<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Lha;
use App\Models\Temuan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TemuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $length = 10;
            if ($request->has('page_size')) {
                $length = $request->page_size;
            }
            $temuan = Temuan::where('deleted', '!=', '1');

            if ($request->has('keyword')) {
                $keyword = strtolower($request->keyword);
                $temuan->whereRaw('LOWER(nomor) LIKE ?', ["%{$keyword}%"]);
            }

            $temuan->orderBy('id', 'ASC');
            $data = $temuan->paginate($length);

            $customData = collect($data->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'lha_id' => $item->lha_id,
                    'unit_id' => $item->unit_id,
                    'divisi_id' => $item->divisi_id,
                    'departemen_id' => $item->departemen_id,
                    'unit' => $item->unit->nama ?? '-',
                    'divisi' => $item->divisi->nama ?? '-',
                    'departemen' => $item->departemen->nama ?? '-',
                    'judul' => $item->judul,
                    'nomor' => $item->nomor,
                    'deskripsi' => $item->deskripsi,
                    'status' => $item->status,
                    'status_name' => STATUS_LHA[$item->status],
                ];
            });

            return response()->json([
                'status' => true,
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
        } catch (\Throwable $e) {
            $code = 500;
            if ($e->getCode()) {
                $code = $e->getCode();
            }
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], $code);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $temuan = Temuan::find($id);
            if (!$temuan || $temuan->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Temuan tidak ditemukan'
                ], 404);
            }
            $customData = [
                'id' => $temuan->id,
                'lha_id' => $temuan->lha_id,
                'unit_id' => $temuan->unit_id,
                'divisi_id' => $temuan->divisi_id,
                'departemen_id' => $temuan->departemen_id,
                'unit' => $temuan->unit->nama ?? '-',
                'lha' => $temuan->lha->judul ?? '-',
                'divisi' => $temuan->divisi->nama ?? '-',
                'departemen' => $temuan
                    ->departemen->nama ?? '-',
                'judul' => $temuan->judul,
                'nomor' => $temuan->nomor,
                'deskripsi' => $temuan->deskripsi,
                'status' => $temuan->status,
                'status_name' => STATUS_LHA[$temuan->status],
                'rekomendasi' => $temuan->rekomendasi()->where('deleted', 0)->get()
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

    public function findByLhaId(Request $request, $id)
    {
        try {
            $length = 10;
            if ($request->has('page_size')) {
                $length = $request->page_size;
            }
            $temuan = Temuan::where('lha_id', $id);

            $temuan->where('deleted', '0');
            if ($request->has('keyword')) {
                $keyword = strtolower($request->keyword);
                $temuan->whereRaw('LOWER(judul) LIKE ?', ["%{$keyword}%"]);
            }
            $data = $temuan->paginate($length);

            $customData = collect($data->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'unit' => $item->unit->nama ?? '-',
                    'divisi' => $item->divisi->nama ?? '-',
                    'departemen' => $item->departemen->nama ?? '-',
                    'judul' => $item->judul,
                    'nomor' => $item->nomor,
                    'deskripsi' => $item->deskripsi,
                    'status' => $item->status,
                    'status_name' => STATUS_LHA[$item->status],
                ];
            });

            return response()->json([
                'status' => true,
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
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nomor' => 'required',
                'lha_id' => [
                    'required',
                    'integer',
                    Rule::exists('lha', 'id')->where(function ($query) {
                        $query->where('deleted', '0')->where('status', '0');
                    }),
                ],
            ]);

            $temuan = new Temuan();

            $temuan->lha_id = $request->lha_id;
            $temuan->unit_id = $request->unit_id;
            $temuan->divisi_id = $request->divisi_id;
            $temuan->departemen_id = $request->departemen_id;
            $temuan->nomor = $request->nomor;
            $temuan->judul = $request->judul;
            $temuan->deskripsi = $request->deskripsi;

            $temuan->save();

            return response()->json([
                'status' => true,
                'message' => 'Temuan berhasil ditambahkan',
                'data' => $temuan
            ], 201);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $temuan = Temuan::findOrFail($id);
            if (!$temuan || $temuan->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Temuan tidak ditemukan'
                ], 404);
            }

            $validated = $request->validate([
                'nomor' => 'required',
                'lha_id' => [
                    'required',
                    'integer',
                    Rule::exists('lha', 'id')->where(function ($query) {
                        $query->where('deleted', 0);
                    }),
                ],
            ]);

            $temuan->lha_id = $request->lha_id;
            $temuan->unit_id = $request->unit_id;
            $temuan->divisi_id = $request->divisi_id;
            $temuan->departemen_id = $request->departemen_id;
            $temuan->nomor = $request->nomor;
            $temuan->judul = $request->judul;
            $temuan->deskripsi = $request->deskripsi;

            $temuan->save();

            return response()->json([
                'status' => true,
                'message' => 'Temuan berhasil diubah',
                'data' => $temuan
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $temuan = Temuan::find($id);
            if (!$temuan || $temuan->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Temuan tidak ditemukan'
                ], 404);
            }

            $temuan->update(['deleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'Temuan berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Restore Deleted Temuan (RESTORE)
    public function restore($id)
    {
        try {
            $temuan = Temuan::find($id);
            if (!$temuan || $temuan->deleted == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Temuan tidak ditemukan atau sudah aktif'
                ], 404);
            }

            $temuan->update(['deleted' => 0]);

            return response()->json([
                'status' => true,
                'message' => 'Temuan berhasil diaktifkan kembali',
                'data' => $temuan
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
