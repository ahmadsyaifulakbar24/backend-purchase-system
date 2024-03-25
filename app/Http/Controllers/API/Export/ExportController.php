<?php

namespace App\Http\Controllers\API\Export;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function summary(Request $request)
    {
        $request->validate([
            'month' => ['required', 'between:1,12'],
            'year' => ['required', 'integer']
        ]);

        return view('exports.summary_excel');
    }

    public function realisasi_purchase_record(Request $request)
    {
        $request->validate([
            'month' => ['required', 'between:1,12'],
            'year' => ['required', 'integer']
        ]);

        return view('exports.realisasi_purchase_record_excel');
    }
}
