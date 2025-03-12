<?php

namespace App\Http\Controllers\api;

use App\Models\Jabatan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class JabatanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $length = 10;
            if ($request->has('page_size')) {
                $length = $request->page_size;
            }
            $response = Jabatan::where('deleted', '0');
            if ($request->has('keyword')) {
                $keyword = strtolower($request->keyword);
                $response->whereRaw('LOWER(nama) LIKE ?', ["%{$keyword}%"]);
            }

            $data = $response->paginate($length);
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
    // 🔹 Get Single Jabatan by ID (READ)
    public function show($id)
    {
        try {

            $jabatan = Jabatan::find($id);
            if (!$jabatan || $jabatan->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Jabatan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $jabatan
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    // 🔹 Create New Jabatan (CREATE)
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
            ]);

            $jabatan = Jabatan::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Jabatan berhasil ditambahkan',
                'data' => $jabatan
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

    // 🔹 Update Jabatan (UPDATE)
    public function update(Request $request, $id)
    {
        try {
            $jabatan = Jabatan::findOrFail($id);
            if (!$jabatan || $jabatan->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Jabatan tidak ditemukan'
                ], 404);
            }


            $validated = $request->validate([
                'nama' => 'required|string|max:255',
            ]);

            $jabatan->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Jabatan updated successfully',
                'data' => $jabatan
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

    // 🔹 Soft Delete Jabatan (DELETE)
    public function destroy($id)
    {
        try {
            $jabatan = Jabatan::find($id);
            if (!$jabatan || $jabatan->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Jabatan tidak ditemukan'
                ], 404);
            }

            $jabatan->update(['deleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'Jabatan berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Restore Deleted Jabatan (RESTORE)
    public function restore($id)
    {
        try {
            $jabatan = Jabatan::find($id);
            if (!$jabatan || $jabatan->deleted == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Jabatan tidak ditemukan atau sudah aktif'
                ], 404);
            }

            $jabatan->update(['deleted' => 0]);

            return response()->json([
                'status' => true,
                'message' => 'Jabatan berhasil diaktifkan kembali',
                'data' => $jabatan
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
