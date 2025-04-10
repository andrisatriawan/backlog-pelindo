<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Temuan;
use Illuminate\Http\Request;

class HasilAuditorController extends Controller
{
    public function index(Request $request)
    {
        $temuan = Temuan::where('deleted', '0')->where('status', '>=', '2')->where('last_stage', '>=', 5);

        if ($request->has('length')) {
            $temuan->paginate($request->length);
        }

        if (!$request->has('length')) {
            $temuan->get();
        }
    }
}
