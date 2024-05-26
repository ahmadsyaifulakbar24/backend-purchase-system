<?php

namespace App\Http\Controllers\API\Export;

use App\Exports\RealisasiPurchaseRecord;
use App\Exports\RealMORExport;
use App\Exports\SalesExport;
use App\Exports\SummaryExport;
use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Location;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function summary(Request $request)
    {
        $request->validate([
            'month' => ['required', 'between:1,12'],
            'year' => ['required', 'integer']
        ]);
        $month = $request->month;
        $year = $request->year;

        $data = Location::with([
            'mor_month' => function (Builder $query) use ($request) {
                $query->where([
                    ['month', $request->month],
                    ['year', $request->year],
                ])
                ->with([
                    'mor_month_detail' => function (Builder $query) {
                        $query->select(
                            'mor_month_id',
                            DB::raw("SUM(actual_stock * price) as total_actual_stock_price")
                        )
                        ->groupBy('mor_month_id');
                    }
                ]);
            }
        ])
        ->get();
        
        $month_name = strtoupper(DateHelpers::numericToMonth($month));

        // return view('exports.summary_excel', compact('data', 'year', 'month_name'));
        // return Excel::download(new SummaryExport($year, $month_name, $data), 'SUMMARY.xlsx');
        
        $all_data = [
            'year' => $year,
            'month_name' => $month_name,
            'data' => $data,
        ];
        $pdf = Pdf::loadView('exports.summary_excel', $all_data);
        return $pdf->download('summary.pdf');
    }

    public function realisasi_purchase_record(Request $request)
    {
        $request->validate([
            'month' => ['required', 'between:1,12'],
            'year' => ['required', 'integer']
        ]);

        return view('exports.realisasi_purchase_record_excel');
    }

    public function summary_excel(Request $request)
    {
        return Excel::download(new SummaryExport(), 'SUMMARY.xlsx');
    }

    public function realisasi_purchase_record_excel(Request $request)
    {
        $request->validate([
            'month' => ['required', 'between:1,12'],
            'year' => ['required', 'integer']
        ]);

        return Excel::download(new RealisasiPurchaseRecord(), 'Realisasi Purchase Record.xlsx');
        // return view('exports.realisasi_purchase_record_excel');
    }

    public function seles_excel(Request $request)
    {
        return Excel::download(new SalesExport(), 'Sales.xlsx');
    }

    public function real_mor_excel(Request $request)
    {
        return Excel::download(new RealMORExport(), 'MONTHLY OPERATION REPORT.xlsx');
    }
}
