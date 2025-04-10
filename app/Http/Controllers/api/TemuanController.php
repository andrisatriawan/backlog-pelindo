<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Lha;
use App\Models\Stage;
use App\Models\Temuan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TemuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // try {
        $length = 10;
        if ($request->has('page_size')) {
            $length = $request->page_size;
        }
        $temuan = Temuan::where('deleted', '!=', '1');

        if ($request->has('keyword')) {
            $keyword = strtolower($request->keyword);
            $temuan->whereRaw('LOWER(nomor) LIKE ?', ["%{$keyword}%"]);
        }


        $roleName = auth()->user()->roles->map(function ($item) {
            return $item->name;
        })->toArray();

        if (!in_array('admin', $roleName)) {
            $temuan->where('status', '!=', '0');
        }

        if (in_array('pic', $roleName)) {
            $temuan->where('divisi_id', auth()->user()->divisi_id);
        }

        $roleIds = auth()->user()->roles->pluck('id');

        foreach ($roleIds as $id) {
            $temuan->where('last_stage', '>=', $id);
        }

        $temuan->orderBy('id', 'ASC');
        $data = $temuan->paginate($length);


        $customData = collect($data->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'lha_id' => $item->lha_id,
                'unit_id' => $item->unit_id,
                'divisi_id' => $item->divisi_id,
                'departemen_id' => $item->departemen_id,
                'unit' => $item->unit->nama ?? '-',
                'divisi' => $item->divisi->nama ?? '-',
                'departemen' => $item->departemen->nama ?? '-',
                'judul' => $item->judul,
                'nomor' => $item->nomor,
                'deskripsi' => $item->deskripsi,
                'status' => $item->status,
                'status_name' => STATUS_TEMUAN[$item->status] ?? '-',
                'last_stage' => $item->last_stage,
                'stage_name' => $item->last_stage === '5' && $item->status == '1' ? 'Supervisor' :  $item->stage->nama
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
        // } catch (\Throwable $e) {
        //     $code = 500;
        //     if ($e->getCode()) {
        //         $code = $e->getCode();
        //     }
        //     return response()->json([
        //         'status' => false,
        //         'message' => $e->getMessage()
        //     ], $code);
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $temuan = Temuan::find($id);
            if (!$temuan || $temuan->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Temuan tidak ditemukan'
                ], 404);
            }
            $customData = [
                'id' => $temuan->id,
                'lha_id' => $temuan->lha_id,
                'unit_id' => $temuan->unit_id,
                'divisi_id' => $temuan->divisi_id,
                'departemen_id' => $temuan->departemen_id,
                'unit' => $temuan->unit->nama ?? '-',
                'lha' => $temuan->lha->judul ?? '-',
                'divisi' => $temuan->divisi->nama ?? '-',
                'departemen' => $temuan
                    ->departemen->nama ?? '-',
                'judul' => $temuan->judul,
                'nomor' => $temuan->nomor,
                'deskripsi' => $temuan->deskripsi,
                'status' => $temuan->status,
                'status_name' => STATUS_TEMUAN[$temuan->status],
                'last_stage' => $temuan->last_stage,
                'stage_name' => $temuan->stage->nama,
                'rekomendasi' => $temuan->rekomendasi()->where('deleted', '0')->get()->map(function ($item) {
                    $data = $item->toArray();
                    $data['status_name'] = STATUS_REKOMENDASI[$item->status] ?? 'Unknown';
                    return $data;
                })
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

    public function findByLhaId(Request $request, $id)
    {
        try {
            $length = 10;
            if ($request->has('page_size')) {
                $length = $request->page_size;
            }
            $temuan = Temuan::where('lha_id', $id);

            $temuan->where('deleted', '0');
            if ($request->has('keyword')) {
                $keyword = strtolower($request->keyword);
                $temuan->whereRaw('LOWER(judul) LIKE ?', ["%{$keyword}%"]);
            }

            $roleName = auth()->user()->roles->map(function ($item) {
                return $item->name;
            })->toArray();

            if (!in_array('admin', $roleName)) {
                $temuan->where('status', '!=', '0');
            }

            if (in_array('pic', $roleName)) {
                $temuan->where('divisi_id', auth()->user()->divisi_id);
            }

            $data = $temuan->paginate($length);

            $customData = collect($data->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'unit' => $item->unit->nama ?? '-',
                    'divisi' => $item->divisi->nama ?? '-',
                    'departemen' => $item->departemen->nama ?? '-',
                    'judul' => $item->judul,
                    'nomor' => $item->nomor,
                    'deskripsi' => $item->deskripsi,
                    'status' => $item->status,
                    'status_name' => STATUS_LHA[$item->status],
                    'last_stage' => $item->last_stage,
                    'stage_name' => $item->stage->nama
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nomor' => 'required',
                'lha_id' => [
                    'required',
                    // 'integer',
                    Rule::exists('lha', 'id')->where(function ($query) {
                        $query->where('deleted', '0')->where('status', '0');
                    }),
                ],
            ]);

            $temuan = new Temuan();

            $temuan->lha_id = $request->lha_id;
            $temuan->unit_id = $request->unit_id;
            $temuan->divisi_id = $request->divisi_id;
            $temuan->departemen_id = $request->departemen_id;
            $temuan->nomor = $request->nomor;
            $temuan->judul = $request->judul;
            $temuan->deskripsi = $request->deskripsi;
            $temuan->status = '0';

            $temuan->save();

            $temuan->refresh();

            $temuan->logStage()->create([
                'stage' => 1,
                'keterangan' => 'Temuan dibuat.',
                'nama' => $temuan->stage->nama,
                'action' => 'draf',
                'user_id' => auth()->user()->id,
                'action_name' => 'Draf temuan'
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Temuan berhasil ditambahkan',
                'data' => $temuan
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $temuan = Temuan::findOrFail($id);
            if (!$temuan || $temuan->deleted == 1) {
                return response()->json([
                    // Rollback the transaction
                    'status' => false,

                    // Return a JSON response
                    'message' => 'Temuan tidak ditemukan'
                ], 404);
            }

            $validated = $request->validate([
                'nomor' => 'required',
                'lha_id' => [
                    'required',
                    // 'integer',
                    Rule::exists('lha', 'id')->where(function ($query) {
                        $query->where('deleted', '0');
                    }),
                ],
            ]);

            $temuan->lha_id = $request->lha_id;
            $temuan->unit_id = $request->unit_id;
            $temuan->divisi_id = $request->divisi_id;
            $temuan->departemen_id = $request->departemen_id;
            $temuan->nomor = $request->nomor;
            $temuan->judul = $request->judul;
            $temuan->deskripsi = $request->deskripsi;

            $temuan->save();

            return response()->json([
                'status' => true,
                'message' => 'Temuan berhasil diubah',
                'data' => $temuan
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $temuan = Temuan::find($id);
            if (!$temuan || $temuan->deleted == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Temuan tidak ditemukan'
                ], 404);
            }

            $temuan->update(['deleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'Temuan berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Restore Deleted Temuan (RESTORE)
    public function restore($id)
    {
        try {
            $temuan = Temuan::find($id);
            if (!$temuan || $temuan->deleted == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Temuan tidak ditemukan atau sudah aktif'
                ], 404);
            }

            $temuan->update(['deleted' => 0]);

            return response()->json([
                'status' => true,
                'message' => 'Temuan berhasil diaktifkan kembali',
                'data' => $temuan
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendToPIC(Request $request)
    {
        DB::beginTransaction();
        try {
            $temuan = Temuan::findOrFail($request->temuan_id);
            $stageNow = $temuan->stage->nama;
            $temuan->last_stage = 3;
            $temuan->save();

            $temuan->refresh();

            $temuan->logStage()->create([
                'stage' => 3,
                'keterangan' => $request->keterangan,
                'nama' => $temuan->stage->nama,
                'user_id' => auth()->user()->id,
                'action' => 'diterima',
                'stage_before' => $temuan->last_stage - 1,
                'action_name' => 'Approved ' . $stageNow
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil di teruskan.'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function submitTemuan(Request $request)
    {
        DB::beginTransaction();
        try {
            $temuan = Temuan::findOrFail($request->temuan_id);
            $temuan->last_stage += 1;
            $temuan->save();

            $temuan->refresh();

            $temuan->logStage()->create([
                'stage' => $temuan->last_stage,
                'keterangan' => $request->keterangan,
                'nama' => $temuan->stage->nama,
                'user_id' => auth()->user()->id,
                'action' => 'submit',
                'stage_before' => $temuan->last_stage - 1,
                'action_name' => 'Proses approval ' . $temuan->stage->nama
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil dikirim.'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function acceptTemuan(Request $request)
    {
        DB::beginTransaction();
        try {
            $action = 'diterima';
            $temuan = Temuan::findOrFail($request->temuan_id);
            $stageNow = $temuan->stage->nama;

            $temuan->last_stage += 1;

            $temuan->save();

            $temuan->refresh();

            $temuan->logStage()->create([
                'stage' => $temuan->last_stage,
                'keterangan' => $request->keterangan,
                'nama' => $temuan->stage->nama,
                'user_id' => auth()->user()->id,
                'action' => $action,
                'stage_before' => $temuan->last_stage - 1,
                'action_name' => 'Approved ' . $stageNow
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil diterima.'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function rejectTemuan(Request $request)
    {
        DB::beginTransaction();
        try {
            $temuan = Temuan::findOrFail($request->temuan_id);
            $stageNow = $temuan->stage->nama;
            $temuan->last_stage -= 1;;
            $temuan->save();

            $temuan->refresh();

            $temuan->logStage()->create([
                'stage' => $temuan->last_stage,
                'keterangan' => $request->keterangan,
                'nama' => $temuan->stage->nama,
                'user_id' => auth()->user()->id,
                'action' => 'ditolak',
                'stage_before' => $temuan->last_stage + 1,
                'action_name' => 'Rejected ' . $stageNow
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil ditolak.'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logStage($id)
    {
        try {
            $temuan = Temuan::findOrFail($id);
            $log = $temuan->logStage()->orderBy('created_at', 'desc')->get();
            $data = $log->map(function ($item) use ($temuan) {

                return [
                    "stage" => $item->stage,
                    "keterangan" => $item->keterangan,
                    "created_at" => $item->created_at->toISOString(),
                    "nama" => $item->nama,
                    "action" => $item->action,
                    "user" => $item->user->nama ?? 'user not found',
                    "stage_before" => Stage::find($item->stage_before)?->nama ?? null,
                    'action_name' => $item->action_name
                ];
            });
            return response()->json([
                'status' => true,
                'message' => 'Log Stage temuan.',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function tolakSelesaiInternal(Request $request)
    {
        DB::beginTransaction();
        try {
            $temuan = Temuan::findOrFail($request->temuan_id);
            $temuan->last_stage = 3;
            $temuan->save();

            $temuan->refresh();

            $temuan->logStage()->create([
                'stage' => $temuan->last_stage,
                'keterangan' => $request->keterangan,
                'nama' => $temuan->stage->nama,
                'user_id' => auth()->user()->id,
                'action' => 'ditolak',
                'stage_before' => 2,
                'action_name' => 'Rejected Supervisor'
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil ditolak.'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function selesaiInternal(Request $request)
    {
        DB::beginTransaction();
        try {
            $temuan = Temuan::findOrFail($request->temuan_id);

            $temuan->status = 2;
            $temuan->save();

            $temuan->refresh();

            $temuan->logStage()->create([
                'stage' => $temuan->last_stage,
                'keterangan' => $request->keterangan,
                'nama' => $temuan->stage->nama,
                'user_id' => auth()->user()->id,
                'action' => 'selesai',
                'stage_before' => 2,
                'action_name' => 'Approved Supervisor'
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil diterima.'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function hasilAuditor(Request $request, $id)
    {
        // try {

        $temuan = Temuan::where('deleted', '!=', '1')->where('last_stage', '>=', 5)->where('lha_id', $id);

        $temuan->orderBy('id', 'ASC');

        $data = $temuan->get();


        $customData = collect($data)->map(function ($item) {
            return [
                'id' => $item->id,
                'lha_id' => $item->lha_id,
                'unit_id' => $item->unit_id,
                'divisi_id' => $item->divisi_id,
                'departemen_id' => $item->departemen_id,
                'unit' => $item->unit->nama ?? '-',
                'divisi' => $item->divisi->nama ?? '-',
                'departemen' => $item->departemen->nama ?? '-',
                'judul' => $item->judul,
                'nomor' => $item->nomor,
                'deskripsi' => $item->deskripsi,
                'status' => $item->status,
                'status_name' => STATUS_TEMUAN[$item->status],
                'last_stage' => $item->last_stage,
                'stage_name' => $item->stage->nama,
                'rekomendasi' => $item->rekomendasi
                    ->filter(function ($rek) {
                        return !$rek->is_spi;
                    })
                    ->map(function ($item) {
                        $item->status_name = STATUS_REKOMENDASI[$item->status] ?? 'undefined';

                        return $item;
                    })->values()
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $customData
        ]);
        // } catch (\Throwable $e) {
        //     $code = 500;
        //     if ($e->getCode()) {
        //         $code = $e->getCode();
        //     }
        //     return response()->json([
        //         'status' => false,
        //         'message' => $e->getMessage()
        //     ], $code);
        // }
    }

    public function tolakAuditor(Request $request)
    {
        DB::beginTransaction();
        try {
            $temuan = Temuan::findOrFail($request->temuan_id);
            $temuan->status = 0;
            $temuan->last_stage = 1;
            $temuan->save();

            $temuan->refresh();

            $temuan->logStage()->create([
                'stage' => $temuan->last_stage,
                'keterangan' => $request->keterangan,
                'nama' => $temuan->stage->nama,
                'user_id' => auth()->user()->id,
                'action' => 'ditolak',
                'stage_before' => 5
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil ditolak.'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function terimaAuditor(Request $request)
    {
        DB::beginTransaction();
        try {
            $temuan = Temuan::findOrFail($request->temuan_id);
            $temuan->status = 3;
            $temuan->last_stage = 6;
            $temuan->save();

            $temuan->refresh();

            $temuan->logStage()->create([
                'stage' => $temuan->last_stage,
                'keterangan' => $request->keterangan,
                'nama' => $temuan->stage->nama,
                'user_id' => auth()->user()->id,
                'action' => 'selesai',
                'stage_before' => 5
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Temuan berhasil disimpan ke status selesai.'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function inputHasilAuditor(Request $request)
    {
        DB::beginTransaction();
        try {
            $temuan = Temuan::findOrFail($request->temuan_id);

            $allSelesai = $temuan->rekomendasi
                ->where('deleted', '!=', '1')
                ->where('is_spi', null)
                ->every(function ($rek) {
                    return $rek->status == 2;
                });

            $temuan->status = 1;
            $temuan->last_stage = 1;

            if ($allSelesai) {
                $temuan->status = 3;
                $temuan->last_stage = 6;
            }

            $temuan->save();

            foreach ($request->files as $row) {
                $temuan->temuanHasFiles()->create([
                    'temuan_id' => $temuan->id,
                    'file_id' => $row
                ]);
            }

            $temuan->refresh();

            $temuan->rekomendasi()->update([
                'is_spi' => 1
            ]);

            $temuan->logStage()->create([
                'stage' => $temuan->last_stage,
                'keterangan' => $request->keterangan,
                'nama' => $temuan->stage->nama,
                'user_id' => auth()->user()->id,
                'action' => $allSelesai ? 'selesai' : 'ditolak',
                'action_name' => 'Hasil SPI telah diinput',
                'stage_before' => 5
            ]);

            $lha = Lha::findOrFail($temuan->lha_id);
            $selesaiAllLha = $lha->temuan
                ->where('deleted', '!=', '1')
                ->every(function ($rek) {
                    return $rek->status == 2;
                });

            if ($selesaiAllLha) {
                $lha->status = 3;
                $lha->save();
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Temuan berhasil disimpan ke status selesai.'
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
