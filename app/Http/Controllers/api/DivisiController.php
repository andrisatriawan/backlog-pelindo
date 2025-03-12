<?php

namespace App\Http\Controllers\api;

use App\Models\Divisi;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Validation\ValidationException;

class DivisiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $length = 10;
            if ($request->has('page_size')) {
                $length = $request->page_size;
            }

            $divisi = Divisi::where('deleted', '0');

            if ($request->has('keyword')) {
                $keyword = strtolower($request->keyword);
                $divisi->whereRaw('LOWER(nama) LIKE ?', ["%{$keyword}%"]);
            }
            $data = $divisi->paginate($length);

            $customData = collect($data->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama ?? '-',
                    'unit' => $item->unit->nama
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
    // 🔹 Get Single Divisi by ID (READ)
    public function show($id)
    {
        try {

            $divisi = Divisi::find($id);
            if (!$divisi || $divisi->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Divisi tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $divisi
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function findByUnitId(Request $request, $id)
    {
        try {
            $length = 10;
            if ($request->has('page_size')) {
                $length = $request->page_size;
            }

            $divisi = Divisi::where('unit_id', $id);
            $divisi->where('deleted', '0');
            if ($request->has('keyword')) {
                $keyword = strtolower($request->keyword);
                $divisi->whereRaw('LOWER(nama) LIKE ?', ["%{$keyword}%"]);
            }
            $data = $divisi->paginate($length);

            $customData = collect($data->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama ?? '-',
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
    // 🔹 Create New Divisi (CREATE)
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'unit_id' => [
                    'required',
                    'integer',
                    Rule::exists('unit', 'id')->where(function ($query) {
                        $query->where('deleted', 0);
                    }),
                ],

            ]);

            $divisi = Divisi::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Divisi berhasil ditambahkan',
                'data' => $divisi
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

    // 🔹 Update Divisi (UPDATE)
    public function update(Request $request, $id)
    {
        try {
            $divisi = Divisi::findOrFail($id);
            if (!$divisi || $divisi->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Divisi tidak ditemukan'
                ], 404);
            }


            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'unit_id' => [
                    'required',
                    'integer',
                    Rule::exists('unit', 'id')->where(function ($query) {
                        $query->where('deleted', 0);
                    }),
                ],
            ]);

            $divisi->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Divisi berhasil diubah',
                'data' => $divisi
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

    // 🔹 Soft Delete Divisi (DELETE)
    public function destroy($id)
    {
        try {
            $divisi = Divisi::find($id);
            if (!$divisi || $divisi->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Divisi tidak ditemukan'
                ], 404);
            }

            $divisi->update(['deleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'Divisi berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Restore Deleted Divisi (RESTORE)
    public function restore($id)
    {
        try {
            $divisi = Divisi::find($id);
            if (!$divisi || $divisi->deleted == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Divisi tidak ditemukan atau sudah aktif'
                ], 404);
            }

            $divisi->update(['deleted' => 0]);

            return response()->json([
                'status' => true,
                'message' => 'Divisi berhasil diaktifkan kembali',
                'data' => $divisi
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
