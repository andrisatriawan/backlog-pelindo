<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FileController extends Controller
{

    public function index(Request $request)
    {
        try {
            $length = 10;
            if ($request->has('page_size')) {
                $length = $request->page_size;
            }
            $file = File::where('deleted', '!=', '1');

            if ($request->has('keyword')) {
                $keyword = strtolower($request->keyword);
                $file->whereRaw('LOWER(nama) LIKE ?', ["%{$keyword}%"]);
            }
            $file->orderBy('id', 'ASC');
            $data = $file->paginate($length);

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
    // 🔹 Get Single File by ID (READ)
    public function show($id)
    {
        try {

            $file = File::find($id);
            if (!$file || $file->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $file
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    // 🔹 Create New File (CREATE)
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'divisi_id' => [
                    'required',
                    // 'integer',
                    Rule::exists('divisi', 'id')->where(function ($query) {
                        $query->where('deleted', '0');
                    }),
                ],
                'lha_id' => [
                    'required',
                    // 'integer',
                    Rule::exists('lha', 'id')->where(function ($query) {
                        $query->where('deleted', '0');
                    }),
                ],
                'file' => 'required|mimes:pdf|max:2048', // Validate that the file is a PDF and its size is not more than 2MB
                'direktori' => 'required|string',
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filePath = $file->store('files', 'public'); // Store the file in the 'files' directory within the 'public' disk

                $validated['file'] = $filePath;
            }

            $file = File::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'File berhasil ditambahkan',
                'data' => $file
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

    // 🔹 Update File (UPDATE)
    public function update(Request $request, $id)
    {
        try {
            $file = File::findOrFail($id);
            if (!$file || $file->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'divisi_id' => [
                    'required',
                    // 'integer',
                    Rule::exists('divisi', 'id')->where(function ($query) {
                        $query->where('deleted', '0');
                    }),
                ],
                'lha_id' => [
                    'required',
                    // 'integer',
                    Rule::exists('lha', 'id')->where(function ($query) {
                        $query->where('deleted', '0');
                    }),
                ],
                'file' => 'nullable|mimes:pdf|max:2048', // Make file nullable for update
                'direktori' => 'required|string',
            ]);

            if ($request->hasFile('file')) {
                $uploadedFile = $request->file('file');
                $filePath = $uploadedFile->store('files', 'public'); // Store the file in the 'files' directory within the 'public' disk
                $validated['file'] = $filePath;
            }

            $file->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'File updated successfully',
                'data' => $file
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

    // 🔹 Soft Delete File (DELETE)
    public function destroy($id)
    {
        try {
            $file = File::find($id);
            if (!$file || $file->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            $file->update(['deleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'File berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Restore Deleted File (RESTORE)
    public function restore($id)
    {
        try {
            $file = File::find($id);
            if (!$file || $file->deleted == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'File tidak ditemukan atau sudah aktif'
                ], 404);
            }

            $file->update(['deleted' => 0]);

            return response()->json([
                'status' => true,
                'message' => 'File berhasil diaktifkan kembali',
                'data' => $file
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
