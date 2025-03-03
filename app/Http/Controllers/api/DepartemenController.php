<?php

namespace App\Http\Controllers\api;

use App\Models\Departemen;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\Divisi;
use Illuminate\Validation\ValidationException;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        try {
            $response = Departemen::where('deleted', '!=', '1')->get();
            return response()->json([
                'status' => true,
                'data' => $response,
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
    // 🔹 Get Single Departemen by ID (READ)
    public function show($id)
    {
        try {

            $departemen = Departemen::find($id);
            if (!$departemen || $departemen->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Departemen tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $departemen
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function findByDivisiId(Request $request, $id)
    {
        try {
            $length = 10;
            if ($request->has('page_size')) {
                $length = $request->page_size;
            }
            $departemen = Departemen::where('divisi_id', $id);
            $departemen->where('deleted', '0');
            if ($request->has('keyword')) {
                $keyword = strtolower($request->keyword);
                $departemen->whereRaw('LOWER(nama) LIKE ?', ["%{$keyword}%"]);
            }

            $data = $departemen->paginate($length);

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
    // 🔹 Create New Departemen (CREATE)
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'divisi_id' => [
                    'required',
                    'integer',
                    Rule::exists('divisi', 'id')->where(function ($query) {
                        $query->where('deleted', 0);
                    }),
                ],

            ]);

            $departemen = Departemen::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Departemen berhasil ditambahkan',
                'data' => $departemen
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

    // 🔹 Update Departemen (UPDATE)
    public function update(Request $request, $id)
    {
        try {
            $departemen = Departemen::findOrFail($id);
            if (!$departemen || $departemen->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Departemen tidak ditemukan'
                ], 404);
            }


            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'divisi_id' => [
                    'required',
                    'integer',
                    Rule::exists('divisi', 'id')->where(function ($query) {
                        $query->where('deleted', 0);
                    }),
                ],
            ]);

            $departemen->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Departemen berhasil diubah',
                'data' => $departemen
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

    // 🔹 Soft Delete Departemen (DELETE)
    public function destroy($id)
    {
        try {
            $departemen = Departemen::find($id);
            if (!$departemen || $departemen->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Departemen tidak ditemukan'
                ], 404);
            }

            $departemen->update(['deleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'Departemen berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Restore Deleted Departemen (RESTORE)
    public function restore($id)
    {
        try {
            $departemen = Departemen::find($id);
            if (!$departemen || $departemen->deleted == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Departemen tidak ditemukan atau sudah aktif'
                ], 404);
            }

            $departemen->update(['deleted' => 0]);

            return response()->json([
                'status' => true,
                'message' => 'Departemen berhasil diaktifkan kembali',
                'data' => $departemen
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
