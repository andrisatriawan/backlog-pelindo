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
    public function logStage(Request $request)
    {
        $query = LogStage::where('model_type', Temuan::class);

        if ($request->has('lha_id')) {
            $temuanIds = Temuan::where('lha_id', $request->lha_id)->pluck('id');

            $query->whereIn('model_id', $temuanIds);
        }

        $log = $query->orderBy('created_at', 'desc')->limit(5)->get();

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

    public function dashboardSummary(Request $request)
    {
        $totalLha = Lha::where('deleted', '!=', '1')->count();
        $totalTemuan = Temuan::where('deleted', '!=', '1')->count();
        $temuanSelesai = Temuan::where('deleted', '!=', '1')->where('status', '3')->count();
        $temuanAktif = Temuan::where('deleted', '!=', '1')->whereIn('status', ['1', '4'])->count();
        $temuanAuditor = Temuan::where('deleted', '!=', '1')->where('status', '2')->count();

        if ($request->has('lha_id')) {
            $lha = Lha::find($request->lha_id);
            if (!$lha) {
                return response()->json([
                    'status' => false,
                    'message' => 'LHA not found'
                ]);
            }
            $totalLha = Lha::where('deleted', '!=', '1')->where('id', $request->lha_id)->count();
            $totalTemuan = Temuan::where('deleted', '!=', '1')->where('lha_id', $request->lha_id)->count();
            $temuanSelesai = Temuan::where('deleted', '!=', '1')->where('lha_id', $request->lha_id)->where('status', '3')->count();
            $temuanAktif = Temuan::where('deleted', '!=', '1')->where('lha_id', $request->lha_id)->whereIn('status', ['1', '4'])->count();
            $temuanAuditor = Temuan::where('deleted', '!=', '1')->where('lha_id', $request->lha_id)->where('status', '2')->count();
        }

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
