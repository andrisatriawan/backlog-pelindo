<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Lha;
use App\Models\Tindaklanjut;
use App\Models\TindaklanjutHasFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TindakLanjutController extends Controller
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
            $tindaklanjut = Tindaklanjut::where('deleted', '!=', '1');

            if ($request->has('keyword')) {
                $keyword = strtolower($request->keyword);
                $tindaklanjut->whereRaw('LOWER(nomor) LIKE ?', ["%{$keyword}%"]);
            }

            $tindaklanjut->orderBy('id', 'ASC');
            $data = $tindaklanjut->paginate($length);

            $customData = collect($data->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'rekomendasi_id' => $item->rekomendasi_id,
                    'deskripsi' => $item->deskripsi,
                    'tanggal' => $item->tanggal,
                    'file' => $item->file()->where('deleted', '0')->get(),
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
            $tindaklanjut = Tindaklanjut::find($id);
            if (!$tindaklanjut || $tindaklanjut->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tindaklanjut tidak ditemukan'
                ], 404);
            }
            $customData = [
                'id' => $tindaklanjut->id,
                'rekomendasi_id' => $tindaklanjut->rekomendasi_id,
                'deskripsi' => $tindaklanjut->deskripsi,
                'tanggal' => $tindaklanjut->tanggal,
                'file' => $tindaklanjut->file()->where('deleted', '0')->get(),
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

    public function findByRekomendasiId(Request $request, $id)
    {
        try {
            $length = 10;
            if ($request->has('page_size')) {
                $length = $request->page_size;
            }
            $tindaklanjut = Tindaklanjut::where('rekomendasi_id', $id);

            $tindaklanjut->where('deleted', '0');
            if ($request->has('keyword')) {
                $keyword = strtolower($request->keyword);
                $tindaklanjut->whereRaw('LOWER(nomor) LIKE ?', ["%{$keyword}%"]);
            }
            $data = $tindaklanjut->paginate($length);

            $customData = collect($data->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'rekomendasi_id' => $item->rekomendasi_id,
                    'deskripsi' => $item->deskripsi,
                    'tanggal' => $item->tanggal,
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
                'deskripsi' => 'required',
                'tanggal' => 'required',
                'rekomendasi_id' => [
                    'required',
                    'integer',
                    Rule::exists('rekomendasi', 'id')->where(function ($query) {
                        $query->where('deleted', '0');
                    }),
                ],
            ]);

            $tindaklanjut = new Tindaklanjut();

            $tindaklanjut->rekomendasi_id = $request->rekomendasi_id;
            $tindaklanjut->deskripsi = $request->deskripsi;
            $tindaklanjut->tanggal = $request->tanggal;


            $tindaklanjut->save();

            return response()->json([
                'status' => true,
                'message' => 'Tindak lanjut berhasil ditambahkan',
                'data' => $tindaklanjut
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
            $tindaklanjut = Tindaklanjut::findOrFail($id);
            if (!$tindaklanjut || $tindaklanjut->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tindaklanjut tidak ditemukan'
                ], 404);
            }

            $validated = $request->validate([
                'deskripsi' => 'required',
                'tanggal' => 'required',
                'rekomendasi_id' => [
                    'required',
                    'integer',
                    Rule::exists('rekomendasi', 'id')->where(function ($query) {
                        $query->where('deleted', '0');
                    }),
                ],
            ]);

            $tindaklanjut->rekomendasi_id = $request->rekomendasi_id;
            $tindaklanjut->deskripsi = $request->deskripsi;
            $tindaklanjut->tanggal = $request->tanggal;


            $tindaklanjut->save();

            return response()->json([
                'status' => true,
                'message' => 'Tindaklanjut berhasil diubah',
                'data' => $tindaklanjut
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
            $tindaklanjut = Tindaklanjut::find($id);
            if (!$tindaklanjut || $tindaklanjut->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tindaklanjut tidak ditemukan'
                ], 404);
            }

            $tindaklanjut->update(['deleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'Tindaklanjut berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Restore Deleted Tindaklanjut (RESTORE)
    public function restore($id)
    {
        try {
            $tindaklanjut = Tindaklanjut::find($id);
            if (!$tindaklanjut || $tindaklanjut->deleted == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tindaklanjut tidak ditemukan atau sudah aktif'
                ], 404);
            }

            $tindaklanjut->update(['deleted' => 0]);

            return response()->json([
                'status' => true,
                'message' => 'Tindaklanjut berhasil diaktifkan kembali',
                'data' => $tindaklanjut
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
