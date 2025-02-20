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
            $response = Divisi::where('deleted', '!=', '1')->get();
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

    public function findByUnitId($id)
    {
        try {
            $unit = Unit::findOrfail($id);
            if (!$unit || $unit->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unit tidak ditemukan'
                ], 404);
            }

            $divisi = $unit->divisi()->where('deleted', 0)->get();

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
