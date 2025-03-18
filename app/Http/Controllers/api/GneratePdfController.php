<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Temuan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class GneratePdfController extends Controller
{
    public function temuan($id)
    {
        $data['data'] = Temuan::findOrFail($id);

        $pdf = Pdf::loadView('cetak.temuan', $data)->setPaper('A4');

        return $pdf->stream('Form tindak lanjut LHA.pdf');
    }
}
