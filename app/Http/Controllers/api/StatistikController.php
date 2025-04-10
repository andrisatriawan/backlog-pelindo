<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Lha;
use App\Models\LogStage;
use App\Models\Stage;
use App\Models\Temuan;
use Illuminate\Http\Request;

class StatistikController extends Controller
{
    public function logStage()
    {
        $log = LogStage::where('model_type', Temuan::class)->orderBy('created_at', 'desc')->limit(5)->get();

        $data = $log->map(function ($item) {

            return [
                "stage" => $item->stage,
                "keterangan" => $item->keterangan,
                "created_at" => $item->created_at->format('d-m-Y H:i'),
                "nama" => $item->nama,
                "action" => $item->action,
                "user" => $item->user->nama ?? 'user not found',
                "stage_before" => Stage::find($item->stage_before)?->nama ?? null,
                'action_name' => $item->action_name,
                'temuan' => Temuan::find($item->model_id)
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function dashboardSummary()
    {
        $totalLha = Lha::where('deleted', '!=', '1')->count();
        $totalTemuan = Temuan::where('deleted', '!=', '1')->count();
        $temuanSelesai = Temuan::where('deleted', '!=', '1')->where('status', '3')->count();
        $temuanAktif = Temuan::where('deleted', '!=', '1')->whereIn('status', ['1', '4'])->count();
        $temuanAuditor = Temuan::where('deleted', '!=', '1')->where('status', '2')->count();

        return response()->json([
            'status' => true,
            'data' => [
                'total_lha' => $totalLha,
                'total_temuan' => $totalTemuan,
                'temuan_selesai' => $temuanSelesai,
                'temuan_aktif' => $temuanAktif,
                'temuan_selesai_internal' => $temuanAuditor,
            ]
        ]);
    }
}
