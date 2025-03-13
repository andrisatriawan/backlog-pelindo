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

class TindakLanjutHasFileController extends Controller
{


    public function findByTindakLanjutId($id, Request $request)
    {
        try {

            $length = 10;
            if ($request->has('page_size')) {
                $length = $request->page_size;
            }
            $tindaklanjut = TindaklanjutHasFile::where('deleted', '!=', '1')->where('tindaklanjut_id', $id);

            if ($request->has('keyword')) {
                $keyword = strtolower($request->keyword);
                $tindaklanjut->file()->whereRaw('LOWER(nama) LIKE ?', ["%{$keyword}%"]);
            }

            $tindaklanjut->orderBy('id', 'ASC');
            $data = $tindaklanjut->paginate($length);

            $customData = collect($data->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tindaklanjut_id' => $item->tindaklanjut_id,
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
    //store Tindaklanjut has file
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([

                'file_id' => [
                    'required',
                    'integer',
                    Rule::exists('file', 'id')->where(function ($query) {
                        $query->where('deleted', '0');
                    }),
                ],
                'tindaklanjut_id' => [
                    'required',
                    'integer',
                    Rule::exists('tindaklanjut', 'id')->where(function ($query) {
                        $query->where('deleted', '0');
                    }),
                ],
            ]);

            $tlHasFile = new TindaklanjutHasFile();

            $tlHasFile->tindaklanjut_id = $request->tindaklanjut_id;
            $tlHasFile->file_id = $request->file_id;


            $tlHasFile->save();

            return response()->json([
                'status' => true,
                'message' => 'Tindak lanjut berhasil ditambahkan',
                'data' => $tlHasFile
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

    public function update(Request $request, $id)
    {
        try {
            $tindaklanjut = TindaklanjutHasFile::findOrFail($id);
            if (!$tindaklanjut || $tindaklanjut->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tindaklanjut tidak ditemukan'
                ], 404);
            }

            $validated = $request->validate([

                'file_id' => [
                    'required',
                    'integer',
                    Rule::exists('file', 'id')->where(function ($query) {
                        $query->where('deleted', '0');
                    }),
                ],
                'tindaklanjut_id' => [
                    'required',
                    'integer',
                    Rule::exists('tindaklanjut', 'id')->where(function ($query) {
                        $query->where('deleted', '0');
                    }),
                ],
            ]);

            $tindaklanjut->file_id = $request->file_id;
            $tindaklanjut->tindaklanjut_id = $request->tindaklanjut_id;


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
    public function destroy(string $id)
    {
        try {
            $tlHasfile = TindaklanjutHasFile::find($id);
            if (!$tlHasfile || $tlHasfile->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'File tidak ditemukan tidak ditemukan'
                ], 404);
            }

            $tlHasfile->update(['deleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'File berhasil dihapus dari tindak lanjut ini '
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
            $tlHasFile = TindaklanjutHasFile::find($id);
            if (!$tlHasFile || $tlHasFile->deleted == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tindaklanjut File tidak ditemukan atau sudah aktif'
                ], 404);
            }

            $tlHasFile->update(['deleted' => 0]);

            return response()->json([
                'status' => true,
                'message' => 'Tindaklanjut File berhasil diaktifkan kembali',
                'data' => $tlHasFile
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
