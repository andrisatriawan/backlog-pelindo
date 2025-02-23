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

            if ($request->has('length')) {
                $length = $request->length;
            }

            $data = $lha->paginate($length);

            return response()->json([
                'status' => true,
                'message' => 'Data tersedia!',
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
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
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

    // 🔹 Update Unit (UPDATE)
    public function update(Request $request, $id)
    {
        try {
            $unit = Lha::findOrFail($id);
            if (!$unit || $unit->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unit tidak ditemukan'
                ], 404);
            }

            $validated = $request->validate([
                'no_lha' => 'required',
                'judul' => 'required',
                'periode' => 'required',
                'deskripsi' => 'required'
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
}
