<?php

namespace App\Http\Controllers\api;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        try {
            $response = Unit::where('deleted', '!=', '1')->get();
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
    // 🔹 Get Single Unit by ID (READ)
    public function show($id)
    {
        try {

            $unit = Unit::find($id);
            if (!$unit || $unit->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unit tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $unit
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    // 🔹 Create New Unit (CREATE)
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
            ]);

            $unit = Unit::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Unit berhasil ditambahkan',
                'data' => $unit
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

    // 🔹 Update Unit (UPDATE)
    public function update(Request $request, $id)
    {
        try {
            $unit = Unit::findOrFail($id);
            if (!$unit || $unit->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unit tidak ditemukan'
                ], 404);
            }


            $validated = $request->validate([
                'nama' => 'required|string|max:255',
            ]);

            $unit->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Unit updated successfully',
                'data' => $unit
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

    // 🔹 Soft Delete Unit (DELETE)
    public function destroy($id)
    {
        try {
            $unit = Unit::find($id);
            if (!$unit || $unit->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unit tidak ditemukan'
                ], 404);
            }

            $unit->update(['deleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'Unit berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Restore Deleted Unit (RESTORE)
    public function restore($id)
    {
        try {
            $unit = Unit::find($id);
            if (!$unit || $unit->deleted == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unit tidak ditemukan atau sudah aktif'
                ], 404);
            }

            $unit->update(['deleted' => 0]);

            return response()->json([
                'status' => true,
                'message' => 'Unit berhasil diaktifkan kembali',
                'data' => $unit
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
