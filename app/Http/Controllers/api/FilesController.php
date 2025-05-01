<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Files;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FilesController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'file' => 'required|mimes:pdf|max:2048',
                    'nama' => 'required',
                    'lha_id' => [
                        'required',
                        Rule::exists('lha', 'id')->where(function ($query) {
                            $query->where('deleted', '0');
                        }),
                    ],
                ],
                [
                    'file.mimes' => 'File harus berupa PDF.',
                    'file.max'   => 'Ukuran file maksimal 2MB.',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first()
                ], 422);
            }

            if (!$request->hasFile('file')) {
                throw new Exception('Tidak ada file yang diupload.');
            }
            $user = auth()->user();
            $divisi_id = $user->divisi_id ?? null;
            $lha_id = $request->lha_id;
            $nama = $request->nama;
            $file = $request->file('file');
            $directory = 'lha-' . $lha_id;
            $filename = Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/' . $directory, $filename);

            $fileModel = new Files();

            $fileModel->divisi_id = $divisi_id;
            $fileModel->lha_id = $lha_id;
            $fileModel->nama = $nama;
            $fileModel->file = $filename;
            $fileModel->direktori = $directory;
            if ($request->has('is_spi')) {
                $fileModel->is_spi = $request->is_spi ? 1 : 0;
            }
            $fileModel->save();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil diupload',
                'data' => $fileModel
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function findByLha($lha_id)
    {
        try {
            $files = Files::where('lha_id', $lha_id)->where('deleted', '0')->get();
            foreach ($files as $file) {
                $filePath = 'public/' . $file->direktori . '/' . $file->file;

                if (Storage::exists($filePath)) {
                    $file->url_file = url('storage/' . $file->direktori . '/' . $file->file);
                } else {
                    $file->url_file = null;
                }
            }
            $response = $files->map(
                function ($file) {
                    return [
                        'id' => $file->id,
                        'nama' => $file->nama,
                        'url_file' => $file->url_file,
                    ];
                }
            );
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengambil files.',
                'data' => $response
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function findByDivisi(Request $request, $divisi_id)
    {
        try {
            $files = Files::where('divisi_id', $divisi_id)->where('lha_id', $request->lha_id)->where('deleted', '0')->get();
            foreach ($files as $file) {
                $filePath = 'public/' . $file->direktori . '/' . $file->file;

                if (Storage::exists($filePath)) {
                    $file->url_file = url('storage/' . $file->direktori . '/' . $file->file);
                } else {
                    $file->url_file = null;
                }
            }
            $response = $files->map(
                function ($file) {
                    return [
                        'id' => $file->id,
                        'nama' => $file->nama,
                        'url_file' => $file->url_file,
                    ];
                }
            );
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengambil files.',
                'data' => $response
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function find($id)
    {
        try {
            $file = Files::find($id);
            if ($file) {
                $path = 'public/' . $file->direktori . '/' . $file->file;

                if (!Storage::exists($path)) {
                    return response()->json([
                        'status' => false,
                        'messsage' => 'File not found'
                    ], 404);
                }

                return Storage::download($path, $file->nama);
            }
            return response()->json([
                'status' => false,
                'messsage' => 'File not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $file = Files::findOrFail($id);

            $file->deleted = '1';
            $file->deleted_at = Carbon::now()->toDateTimeLocalString();

            $file->save();

            return response()->json([
                'status' => true,
                'message' => 'File berhasil dihapus.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function findByLhaSpi($lha_id)
    {
        try {
            $files = Files::where('lha_id', $lha_id)->where('deleted', '0')->where('is_spi', 1)->get();
            foreach ($files as $file) {
                $filePath = 'public/' . $file->direktori . '/' . $file->file;

                if (Storage::exists($filePath)) {
                    $file->url_file = url('storage/' . $file->direktori . '/' . $file->file);
                } else {
                    $file->url_file = null;
                }
            }
            $response = $files->map(
                function ($file) {
                    return [
                        'id' => $file->id,
                        'nama' => $file->nama,
                        'url_file' => $file->url_file,
                    ];
                }
            );
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengambil files.',
                'data' => $response
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
