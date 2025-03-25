<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Lha;
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
            if ($temuan->status == 2) {
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
            $pdf = Pdf::loadView('cetak.temuan', $data)->setPaper('A4');

            $temuanPdf = $pdf->stream('Form tindak lanjut LHA.pdf');

            $files = $temuan->rekomendasi
                ->flatMap(fn($r) => $r->tindaklanjut)
                ->flatMap(fn($tl) => $tl->file)
                ->map(fn($thf) => $thf->file);

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
        $lha = Lha::with('temuan')->whereHas('temuan', function ($query) {
            $query->where('unit_id', 1);
        })->findOrFail($id);

        $divisi = $lha->temuan()->get()->groupBy('divisi_id');

        dd($divisi);

        $pdf = Pdf::loadView('cetak.monitoring', $data)->setPaper('A4', 'landscape');

        return $pdf->stream('Hasil Monitoring.pdf');
    }
}
