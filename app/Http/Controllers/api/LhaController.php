<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Lha;
use App\Models\Stage;
use App\Models\StageHasRole;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LhaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $length = 10;

            $filters = $request->query('filters', []);

            $lha = Lha::where('deleted', '0');

            $id = auth()->user()->id;

            $user = User::findOrFail($id);

            $check = !$user->roles()->where('name', 'admin')->exists();

            if ($check) {
                $roleIds = $user->roles()->pluck('id');
                $stageIds = StageHasRole::whereIn('role_id', $roleIds)->pluck('stage_id');
                $roleName = $user->roles()->pluck('name')->toArray();

                $lha->whereHas('temuan', function ($query) use ($stageIds, $roleName) {
                    $query->where(function ($q) use ($stageIds, $roleName) {
                        foreach ($stageIds as $stageId) {
                            $q->orWhere('last_stage', '>=', $stageId);
                            if (array_intersect(['pic'], $roleName)) {
                                $q->where('divisi_id', auth()->user()->divisi_id);
                            }
                        }
                    });
                });
            }

            if ($request->has('page_size')) {
                $length = $request->page_size;
            }

            if ($request->has('keyword')) {
                $keyword = strtolower($request->keyword);
                $lha->whereRaw('LOWER(no_lha) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(judul) LIKE ?', ["%{$keyword}%"]);
            }

            if (!empty($filters)) {
                foreach ($filters as $key => $value) {
                    $lha->where($key, $value);
                }
            }

            $data = $lha->paginate($length);

            $customData = collect($data->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'no_lha' => $item->no_lha,
                    'periode' => $item->periode,
                    'status' => $item->status,
                    // 'last_stage' => $item->last_stage,
                    'status_name' => STATUS_LHA[$item->status],
                    // 'stage_name' => $item->stage->nama ?? 'undefined' // Contoh fungsi tambahan
                ];
            });


            return response()->json([
                'status' => true,
                'message' => 'Data tersedia!',
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
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // 🔹 Get Single LHA by ID (READ)
    public function show($id)
    {
        try {

            $lha = Lha::find($id);
            if (!$lha || $lha->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'LHA tidak ditemukan'
                ], 404);
            }


            $customData = [
                'id' => $lha->id,
                'judul' => $lha->judul,
                'no_lha' => $lha->no_lha,
                'periode' => $lha->periode,
                'deskripsi' => $lha->deskripsi,
                'status' => $lha->status,
                'last_stage' => $lha->last_stage,
                'status_name' => STATUS_LHA[$lha->status] ?? '-',
                'stage_name' => $lha->stage->nama ?? 'undefined'
            ];

            return response()->json([
                'status' => true,
                'data' => $customData
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
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
            $lha->tanggal = $request->tanggal;
            $lha->periode = $request->periode;
            $lha->deskripsi = $request->deskripsi;
            $lha->last_stage = 1;
            $lha->status = 0;
            $lha->user_id = auth()->user()->id;

            $lha->save();

            $lha->logStage()->create([
                'lha_id' => $lha->id,
                'stage' => 2,
                'keterangan' => $request->keterangan ?? 'LHA dibuat'
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan!'
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // 🔹 Update LHA (UPDATE)
    public function update(Request $request, $id)
    {
        try {
            $lha = Lha::findOrFail($id);
            if (!$lha || $lha->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'LHA tidak ditemukan'
                ], 404);
            }

            $validated = $request->validate([
                'no_lha' => 'required',
                'judul' => 'required',
                'tanggal' => 'required',
                'periode' => 'required',
                'deskripsi' => 'required'
            ]);

            $lha->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'LHA updated successfully',
                'data' => $lha
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

    // 🔹 Soft Delete LHA (DELETE)
    public function destroy($id)
    {
        try {
            $lha = Lha::find($id);
            if (!$lha || $lha->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'LHA tidak ditemukan'
                ], 404);
            }

            $lha->update(['deleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'LHA berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Restore Deleted LHA (RESTORE)
    public function restore($id)
    {
        try {
            $lha = Lha::find($id);
            if (!$lha || $lha->deleted == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'LHA tidak ditemukan atau sudah aktif'
                ], 404);
            }

            $lha->update(['deleted' => 0]);

            return response()->json([
                'status' => true,
                'message' => 'LHA berhasil diaktifkan kembali',
                'data' => $lha
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function details($id)
    {
        try {
            $roleName = auth()->user()->roles->pluck('name')->toArray();

            $lha = Lha::findOrFail($id);

            $temuan = $lha->temuan->groupBy('divisi_id');

            if (!in_array('admin', $roleName)) {
                $roleIds = auth()->user()->roles->pluck('id');
                $stageIds = StageHasRole::whereIn('role_id', $roleIds)->pluck('stage_id');
                if ($lha->last_stage >= 2) {
                    $lha = Lha::whereHas('temuan', function ($query) use ($stageIds) {
                        $query->where(function ($q) use ($stageIds) {
                            foreach ($stageIds as $stageId) {
                                $q->orWhere('last_stage', '>=', $stageId);
                            }
                        });
                    })->findOrFail($id);
                }
                if (array_intersect(['pic'], $roleName)) {
                    $temuan = $lha->temuan()
                        ->where('divisi_id', auth()->user()->divisi_id)
                        ->where(function ($q) use ($stageIds) {
                            foreach ($stageIds as $stageId) {
                                $q->orWhere('last_stage', '>=', $stageId);
                            }
                        })
                        ->get()
                        ->groupBy('divisi_id');
                } elseif (array_intersect(['penanggungjawab'], $roleName)) {
                    $temuan = $lha->temuan()
                        ->where('unit_id', auth()->user()->unit_id)
                        ->where(function ($q) use ($stageIds) {
                            foreach ($stageIds as $stageId) {
                                $q->orWhere('last_stage', '>=', $stageId);
                            }
                        })
                        ->get()
                        ->groupBy('divisi_id');
                }
            }

            $temuan = $temuan->map(function ($items, $divisiId) {
                $items = $items->where('deleted', '0');
                return [
                    'divisi_id' => $divisiId,
                    'nama_divisi' => $items->first()->divisi->nama ?? 'Unknown',
                    'data' => $items->map(function ($item) {
                        $rekomendasi = $item->rekomendasi->where('deleted', '0');
                        return [
                            'id' => $item->id,
                            'nomor' => $item->nomor,
                            'judul' => $item->judul,
                            'deskripsi' => $item->deskripsi,
                            'status' => $item->status,
                            'status_name' => STATUS_TEMUAN[$item->status],
                            'last_stage' => $item->last_stage,
                            'stage' => $item->logStage()->where('stage', $item->last_stage)->latest()->first(),
                            'stage_name' => $item->stage->nama,
                            'rekomendasi' => $rekomendasi->map(function ($item) {
                                $tindaklanjut = $item->tindaklanjut->where('deleted', '0');
                                return [
                                    'id' => $item->id,
                                    'nomor' => $item->nomor,
                                    'deskripsi' => $item->deskripsi,
                                    'batas_tanggal' => $item->batas_tanggal,
                                    'tanggal_selesai' => $item->tanggal_selesai,
                                    'status' => $item->status,
                                    'status_name' => STATUS_REKOMENDASI[$item->status] ?? '-',
                                    'tindaklanjut' => $tindaklanjut->map(function ($item) {
                                        return [
                                            'id' => $item->id,
                                            'deskripsi' => $item->deskripsi,
                                            'files' => $item->file->map(function ($file) {

                                                if ($file->file && $file->file->deleted == 0) {
                                                    return [
                                                        'nama' => $file->file->nama,
                                                        'url' => url('storage/' . $file->file->direktori . '/' . $file->file->file)
                                                    ];
                                                }
                                                return null;
                                            })
                                                ->filter() // Hapus nilai null
                                                ->values()
                                        ];
                                    })
                                ];
                            })
                        ];
                    })
                ];
            });
            $response = [
                'id' => $lha->id,
                'judul' => $lha->judul,
                'no_lha' => $lha->no_lha,
                'tanggal' => $lha->tanggal,
                'periode' => $lha->periode,
                'deskripsi' => $lha->deskripsi,
                'status' => $lha->status,
                'last_stage' => $lha->last_stage,
                'stage_name' => $lha->stage->nama ?? 'undefined',
                'status_name' => STATUS_LHA[$lha->status] ?? '-',
                'stage' => $lha->logStage()->where('stage', $lha->last_stage)->latest()->first(),
                'temuan' => $temuan,
            ];

            if ($lha->last_stage > 2) {
                $response['temuan_last_stage'] = $lha->temuan()->where('divisi_id', auth()->user()->divisi_id)->latest('updated_at')->value('last_stage');
            }

            return response()->json([
                'status' => true,
                'data' => $response
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function sendLhaToSpv(Request $request)
    {
        DB::beginTransaction();
        try {
            $lha = Lha::findOrFail($request->lha_id);

            $lha->last_stage = 2;
            $lha->status = 1;

            $lha->save();

            $lha->refresh();

            $lha->temuan()->update([
                'status' => 1,
                'last_stage' => 2
            ]);


            foreach ($lha->temuan as $temuan) {
                $temuan->logStage()->create([
                    'lha_id' => $lha->id,
                    'stage' => 2,
                    'keterangan' => $request->keterangan,
                    'nama' => $lha->stage->nama,
                    'user_id' => auth()->user()->id,
                    'action' => 'submit'
                ]);
            }

            $lha->logStage()->create([
                'lha_id' => $lha->id,
                'stage' => 2,
                'keterangan' => $request->keterangan,
                'nama' => $lha->stage->nama,
                'user_id' => auth()->user()->id,
                'action' => 'submit'
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil dikirim ke Supervisor'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function sendLhaToPic(Request $request)
    {
        DB::beginTransaction();
        try {
            $lha = Lha::findOrFail($request->lha_id);

            $lha->last_stage = 3;

            $lha->save();

            $lha->temuan()->update([
                'last_stage' => 3
            ]);

            $lha->logStage()->create([
                'lha_id' => $lha->id,
                'stage' => 3,
                'keterangan' => $request->keterangan
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil dikirim ke PIC'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function sendLhaToPj(Request $request)
    {
        DB::beginTransaction();
        try {
            $lha = Lha::findOrFail($request->lha_id);

            $lha->temuan()->where('divisi_id', auth()->user()->divisi_id)->update([
                'last_stage' => 4
            ]);

            $lha->refresh();

            $totalTemuan = $lha->temuan()->count();

            $totalLastStage4 = $lha->temuan()->where('last_stage', 4)->count();

            if ($totalTemuan > 0 && $totalTemuan === $totalLastStage4) {
                $lha->last_stage = 4;

                $lha->save();

                $lha->logStage()->create([
                    'lha_id' => $lha->id,
                    'stage' => 4,
                    'keterangan' => $request->keterangan
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil dikirim ke Penanggungjawab'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function sendLhaToAuditor(Request $request)
    {
        DB::beginTransaction();
        try {
            $lha = Lha::findOrFail($request->lha_id);

            $lha->temuan()->where('divisi_id', auth()->user()->divisi_id)->update([
                'last_stage' => 5
            ]);

            $lha->refresh();

            $totalTemuan = $lha->temuan()->count();

            $totalLastStage4 = $lha->temuan()->where('last_stage', 5)->count();

            if ($totalTemuan > 0 && $totalTemuan === $totalLastStage4) {
                $lha->last_stage = 5;

                $lha->save();

                $lha->logStage()->create([
                    'lha_id' => $lha->id,
                    'stage' => 5,
                    'keterangan' => $request->keterangan
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil dikirim ke Auditor'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function rejectLha(Request $request)
    {
        DB::beginTransaction();
        try {
            $lha = Lha::findOrFail($request->lha_id);

            $lha->last_stage = $request->last_stage - 1;
            if ($lha->last_stage == 1) {
                $lha->status = 0;

                $lha->temuan()->update([
                    'status' => 0
                ]);
            }

            $lha->save();

            $lha->logStage()->create([
                'lha_id' => $lha->id,
                'stage' => $lha->last_stage,
                'keterangan' => $request->keterangan
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil ditolak dan dikembalikan'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
