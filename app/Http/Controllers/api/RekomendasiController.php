<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Rekomendasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RekomendasiController extends Controller
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
            $rekomendasi = Rekomendasi::where('deleted', '!=', '1');

            if ($request->has('keyword')) {
                $keyword = strtolower($request->keyword);
                $rekomendasi->whereRaw('LOWER(nomor) LIKE ?', ["%{$keyword}%"]);
            }

            $rekomendasi->orderBy('id', 'ASC');
            $data = $rekomendasi->paginate($length);

            return response()->json([
                'status' => true,
                'data' => $data->items(),
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
            $rekomendasi = Rekomendasi::find($id);
            if (!$rekomendasi || $rekomendasi->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Rekomendasi tidak ditemukan'
                ], 404);
            }

            $rekomendasi->status_name = STATUS_REKOMENDASI[$rekomendasi->status];
            $rekomendasi->lha_id = $rekomendasi->temuan->lha->id;
            $rekomendasi->lha_judul = $rekomendasi->temuan->lha->judul;
            return response()->json([
                'status' => true,
                'data' => $rekomendasi
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource by temuan_id.
     */
    public function findByTemuanId($id)
    {
        try {
            $rekomendasi = Rekomendasi::where('temuan_id', $id)->where('deleted', 0)->get();

            if (!$rekomendasi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Rekomendasi tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $rekomendasi->map(function ($item) {
                    $item->status_name = STATUS_REKOMENDASI[$item->status] ?? 'undefined';
                    return $item;
                })
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
                'temuan_id' => [
                    'required',
                    // 'integer',
                    Rule::exists('temuan', 'id')->where(function ($query) {
                        $query->where('deleted', 0);
                    }),
                ],
            ]);

            $rekomendasi = new Rekomendasi();

            $rekomendasi->nomor = $validated['nomor'];
            $rekomendasi->temuan_id = $validated['temuan_id'];
            $rekomendasi->deskripsi = $request->deskripsi;
            $rekomendasi->batas_tanggal = $request->batas_tanggal;
            $rekomendasi->tanggal_selesai = $request->tanggal_selesai;
            $rekomendasi->status = $request->status;

            $rekomendasi->save();

            return response()->json([
                'status' => true,
                'message' => 'Rekomendasi berhasil ditambahkan',
                'data' => $rekomendasi
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
    public function update(Request $request, string $id)
    {
        try {
            $rekomendasi = Rekomendasi::findOrFail($id);
            if (!$rekomendasi || $rekomendasi->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Rekomendasi tidak ditemukan'
                ], 404);
            }

            $validated = $request->validate([
                'nomor' => 'required',
                'temuan_id' => [
                    'required',
                    // 'integer',
                    Rule::exists('temuan', 'id')->where(function ($query) {
                        $query->where('deleted', 0);
                    }),
                ],
            ]);

            $rekomendasi->nomor = $validated['nomor'];
            $rekomendasi->temuan_id = $validated['temuan_id'];
            $rekomendasi->deskripsi = $request->deskripsi;
            $rekomendasi->batas_tanggal = $request->batas_tanggal;
            $rekomendasi->tanggal_selesai = $request->tanggal_selesai;
            $rekomendasi->status = $request->status;

            $rekomendasi->save();

            return response()->json([
                'status' => true,
                'message' => 'Rekomendasi berhasil diubah',
                'data' => $rekomendasi
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
            $rekomendasi = Rekomendasi::find($id);
            if (!$rekomendasi || $rekomendasi->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Rekomendasi tidak ditemukan'
                ], 404);
            }

            $rekomendasi->deleted = 1;

            $rekomendasi->save();

            return response()->json([
                'status' => true,
                'message' => 'Rekomendasi berhasil dihapus'
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
            $rekomendasi = Rekomendasi::find($id);
            if (!$rekomendasi || $rekomendasi->deleted == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Rekomendasi tidak ditemukan atau sudah aktif'
                ], 404);
            }

            $rekomendasi->update(['deleted' => 0]);

            return response()->json([
                'status' => true,
                'message' => 'Rekomendasi berhasil diaktifkan kembali',
                'data' => $rekomendasi
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
