<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Temuan;
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
                $temuan->whereRaw('LOWER(nama) LIKE ?', ["%{$keyword}%"]);
            }

            $temuan->orderBy('id', 'ASC');
            $data = $temuan->paginate($length);

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
     * Show the form for editing the specified resource.
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
            return response()->json([
                'status' => true,
                'data' => $temuan
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function findByUnitId($id)
    {
        try {
            $temuan = Temuan::findOrfail($id);
            if (!$temuan || $temuan->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Temuan tidak ditemukan'
                ], 404);
            }

            $lha = $temuan->lha()->where('deleted', 0)->get();

            return response()->json([
                'status' => true,
                'data' => $lha
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
                        $query->where('deleted', 0);
                    }),
                ],
            ]);

            $temuan = Temuan::create($validated);

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
     * Display the specified resource.
     */

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
                'unit_id' => [
                    'required',
                    'integer',
                    Rule::exists('unit', 'id')->where(function ($query) {
                        $query->where('deleted', 0);
                    }),
                ],
            ]);

            $temuan->update($validated);

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
