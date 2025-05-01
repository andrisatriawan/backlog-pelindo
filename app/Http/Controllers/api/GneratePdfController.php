<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Lha;
use App\Models\StageHasRole;
use App\Models\Temuan;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use iio\libmergepdf\Merger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi as FpdiFpdi;
use setasign\Fpdi\Tcpdf\Fpdi;
use setasign\Fpdi\Tfpdf\Fpdi as TfpdfFpdi;
use TCPDF;
use Throwable;

class GneratePdfController extends Controller
{
    public function temuan($id)
    {
        try {
            $temuan = Temuan::with('rekomendasi.tindaklanjut.file.file')->findOrFail($id);
            $data['data'] = $temuan;
            $data['qrCode'] = null;
            if ($temuan->status == 2 || $temuan->status == 3) {
                $user = $temuan->logStage()->where('stage', 4)->latest()->first();
                $text = "Nama : {$user->user->nama}\n"
                    . "NIP  : {$user->user->nip}\n"
                    . "Unit : " . ($user->user->unit->nama ?? '-') . "\n"
                    . "Divisi : " . ($user->user->divisi ?? '-');

                // dd($text);
                // Generate QR Code
                $qrCode = new QrCode($text, (new Encoding('UTF-8')), ErrorCorrectionLevel::Low, 200, 0);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                // Konversi ke Base64
                $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($result->getString());

                $data['qrCode'] = $qrCodeBase64;
            }
            $files = $temuan->rekomendasi
                ->filter(fn($r) => is_null($r->is_spi) || $r->is_spi == 0)
                ->flatMap(fn($r) => $r->tindaklanjut)
                ->flatMap(fn($tl) => $tl->file)
                ->groupBy('file_id') // gabungkan berdasarkan id file
                ->map(fn($group) => $group->first()) // ambil satu file dari setiap grup
                ->map(fn($thf) => $thf->file);
            $data['files'] = $files;

            $pdf = Pdf::loadView('cetak.temuan', $data)->setPaper('A4');

            $temuanPdf = $pdf->stream('Form tindak lanjut LHA.pdf');

            $mergedPdf = $temuanPdf;

            foreach ($files as $file) {
                try {
                    $pdfPath = public_path('storage/' . $file->direktori . '/' . $file->file);

                    if (file_exists($pdfPath)) {
                        $currentMerger = new Merger();
                        $currentMerger->addRaw($mergedPdf);
                        $currentMerger->addFile($pdfPath);
                        $mergedPdf = $currentMerger->merge();
                    }
                } catch (Throwable $e) {
                    Log::error('Gagal menambahkan file: ' . $file->file . ' | Error: ' . $e->getMessage());

                    continue;
                }
            }

            // Return file PDF hasil merge
            return response($mergedPdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="Tindak lanjut.pdf"');
        } catch (Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function monitoring($id)
    {
        $data = [];
        $roleName = auth()->user()->roles->pluck('name')->toArray();

        $lha = Lha::findOrFail($id);

        $temuan = $lha->temuan()->orderBy('id', 'asc')->get()->groupBy('divisi_id');

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
                    ->orderBy('id', 'asc')->get()
                    ->groupBy('divisi_id');
            } elseif (array_intersect(['penanggungjawab'], $roleName)) {
                $temuan = $lha->temuan()
                    ->where('unit_id', auth()->user()->unit_id)
                    ->where(function ($q) use ($stageIds) {
                        foreach ($stageIds as $stageId) {
                            $q->orWhere('last_stage', '>=', $stageId);
                        }
                    })
                    ->orderBy('id', 'asc')->get()
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
                        'stage_name' => $item->last_stage === 5 && $item->status == 1 ? 'Supervisor' : $item->stage->nama,
                        'closing' => $item->closing,
                        'temuanHasFiles' => $item->temuanHasFiles->map(function ($file) {
                            return [
                                'nama' => $file->file->nama,
                                'url' => url('storage/' . $file->file->direktori . '/' . $file->file->file)
                            ];
                        }),
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
                                        'tanggal' => $item->tanggal,
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

        $data['data'] = $response;

        $pdf = Pdf::loadView('cetak.monitoring', $data)->setPaper('A4', 'landscape');

        return $pdf->stream('Hasil Monitoring.pdf');
    }
}
