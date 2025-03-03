<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Lha;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LhaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $length = 10;
            $lha = Lha::where('deleted', '0');
            // if ($request->has('no_lha')) {
            // }

            if ($request->has('page_size')) {
                $length = $request->page_size;
            }

            if ($request->has('keyword')) {
                $keyword = strtolower($request->keyword);
                $lha->whereRaw('LOWER(no_lha) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(judul) LIKE ?', ["%{$keyword}%"]);
            }

            $data = $lha->paginate($length);

            $customData = collect($data->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'no_lha' => $item->no_lha,
                    'periode' => $item->periode,
                    'status' => $item->status,
                    'last_stage' => $item->last_stage,
                    'status_name' => STATUS_LHA[$item->status],
                    'stage_name' => '' // Contoh fungsi tambahan
                ];
            });


            return response()->json([
                'status' => true,
                'message' => 'Data tersedia!',
                'data' => $customData,
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'total' => $data->total(),
                    'per_page' => $data->perPage(),
                    'last_page' => $data->lastPage(),
                    'next_page_url' => $data->nextPageUrl(),
                    'prev_page_url' => $data->previousPageUrl(),
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // 🔹 Get Single LHA by ID (READ)
    public function show($id)
    {
        try {

            $lha = Lha::find($id);
            if (!$lha || $lha->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'LHA tidak ditemukan'
                ], 404);
            }


            $customData = [
                'id' => $lha->id,
                'judul' => $lha->judul,
                'no_lha' => $lha->no_lha,
                'periode' => $lha->periode,
                'deskripsi' => $lha->deskripsi,
                'status' => $lha->status,
                'last_stage' => $lha->last_stage,
                'status_name' => STATUS_LHA[$lha->status] ?? '-',
                'stage_name' => '-'
            ];

            return response()->json([
                'status' => true,
                'data' => $customData
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function save(Request $request)
    {
        try {
            $validate = $request->validate([
                'no_lha' => 'required',
                'judul' => 'required',
                'periode' => 'required',
                'deskripsi' => 'required'
            ]);

            $lha = new Lha();

            $lha->no_lha = $request->no_lha;
            $lha->judul = $request->judul;
            $lha->tanggal = $request->tanggal;
            $lha->periode = $request->periode;
            $lha->deskripsi = $request->deskripsi;
            $lha->last_stage = 0;
            $lha->user_id = auth()->user()->id;

            $lha->save();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan!'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // 🔹 Update LHA (UPDATE)
    public function update(Request $request, $id)
    {
        try {
            $lha = Lha::findOrFail($id);
            if (!$lha || $lha->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'LHA tidak ditemukan'
                ], 404);
            }

            $validated = $request->validate([
                'no_lha' => 'required',
                'judul' => 'required',
                'tanggal' => 'required',
                'periode' => 'required',
                'deskripsi' => 'required'
            ]);

            $lha->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'LHA updated successfully',
                'data' => $lha
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Soft Delete LHA (DELETE)
    public function destroy($id)
    {
        try {
            $lha = Lha::find($id);
            if (!$lha || $lha->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'LHA tidak ditemukan'
                ], 404);
            }

            $lha->update(['deleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'LHA berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Restore Deleted LHA (RESTORE)
    public function restore($id)
    {
        try {
            $lha = Lha::find($id);
            if (!$lha || $lha->deleted == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'LHA tidak ditemukan atau sudah aktif'
                ], 404);
            }

            $lha->update(['deleted' => 0]);

            return response()->json([
                'status' => true,
                'message' => 'LHA berhasil diaktifkan kembali',
                'data' => $lha
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function details($id)
    {
        try {
            $lha = Lha::findOrFail($id);

            $temuan = $lha->temuan->groupBy('divisi_id');
            $temuan = $temuan->map(function ($items, $divisiId) {
                return [
                    'divisi_id' => $divisiId,
                    'nama_divisi' => $items->first()->divisi->nama ?? 'Unknown',
                    'data' => $items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'nomor' => $item->nomor,
                            'judul' => $item->judul,
                            'deskripsi' => $item->deskripsi,
                            'status' => $item->status,
                            'rekomendasi' => $item->rekomendasi->map(function ($item) {
                                return [
                                    'id' => $item->id,
                                    'nomor' => $item->nomor,
                                    'deskripsi' => $item->deskripsi,
                                    'batas_tanggal' => $item->batas_tanggal,
                                    'status' => $item->status,
                                    'status_name' => STATUS_REKOMENDASI[$item->status] ?? '-',
                                ];
                            })
                        ];
                    })
                ];
            });
            $response = [
                'id' => $lha->id,
                'judul' => $lha->judul,
                'no_lha' => $lha->no_lha,
                'tanggal' => $lha->tanggal,
                'periode' => $lha->periode,
                'deskripsi' => $lha->deskripsi,
                'status' => $lha->status,
                'last_stage' => $lha->last_stage,
                'status_name' => STATUS_LHA[$lha->status] ?? '-',
                'stage_name' => '-',
                'temuan' => $temuan
            ];

            return response()->json([
                'status' => true,
                'data' => $response
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
