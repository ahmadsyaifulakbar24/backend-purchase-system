<?php

namespace App\Http\Controllers\API\Export;

use App\Exports\RealisasiPurchaseRecord;
use App\Exports\RealMORExport;
use App\Exports\SalesExport;
use App\Exports\SummaryExport;
use App\Helpers\DateHelpers;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\MealSheetDetail;
use App\Models\POSupplierCatering;
use App\Models\Sales;
use App\Models\SelectItemProduct;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Query\JoinClause;
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
            'mor_month' => function ($query) use ($request) {
                $query->where([
                    ['month', $request->month],
                    ['year', $request->year],
                ])
                ->with([
                    'mor_month_detail' => function ($query) {
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
        $request->validate([
            'month' => ['required', 'between:1,12'],
            'year' => ['required', 'integer']
        ]);
        $month = $request->month;
        $year = $request->year;

        $data = Location::with([
            'mor_month' => function ($query) use ($request) {
                $query->where([
                    ['month', $request->month],
                    ['year', $request->year],
                ])
                ->with([
                    'mor_month_detail' => function ($query) {
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
        return Excel::download(new SummaryExport($year, $month_name, $data), 'SUMMARY.xlsx');
    }

    public function realisasi_purchase_record_excel(Request $request)
    {
        $request->validate([
            'month' => ['required', 'between:1,12'],
            'year' => ['required', 'integer']
        ]);
        $month = $request->month;
        $year = $request->year;

        $query_data = SelectItemProduct::select(
            'quantity',
            'item_price',
            DB::raw('CAST(quantity * item_price AS UNSIGNED) AS total_item_price'),
            'supplier_id',
            'suppliers.name AS supplier_name',
            'suppliers.code AS supplier_code',
            'pr_caterings.location_id',
            'locations.location',
            'locations.location_code',
            'delivery_date',
            DB::raw('
                CASE
                    WHEN DAY(pr_caterings.delivery_date) BETWEEN 1 AND 7 THEN 1
                    WHEN DAY(pr_caterings.delivery_date) BETWEEN 8 AND 14 THEN 2
                    WHEN DAY(pr_caterings.delivery_date) BETWEEN 15 AND 21 THEN 3
                    WHEN DAY(pr_caterings.delivery_date) BETWEEN 22 AND 28 THEN 4
                    ELSE 5
                END AS week
            '),
        )
        ->join('po_supplier_caterings', 'po_supplier_caterings.id', '=',  'select_item_products.reference_id')
        ->join('po_caterings', 'po_caterings.id', '=', 'po_supplier_caterings.po_catering_id')
        ->join('pr_caterings', 'pr_caterings.id', '=', 'po_caterings.pr_catering_id')
        ->join('locations', 'locations.id', '=', 'pr_caterings.location_id')
        ->join('suppliers', 'suppliers.id', '=', 'po_supplier_caterings.supplier_id')
        ->where('po_supplier_caterings.status', 'submit')
        ->where('select_item_products.reference_type', POSupplierCatering::class)
        ->whereMonth('pr_caterings.delivery_date', $month)
        ->whereYear('pr_caterings.delivery_date', $year)
        ->get();

        $data = $query_data->groupBy('week')->map(function ($week_group, $week) {
            return [
                'week' => $week,
                'supplier' => $week_group->groupBy('supplier_id')->map(function ($supplier_group, $supplier_id) {
                    $supplier = $supplier_group->first();
                    return [
                        'supplier_id' => $supplier_id,
                        'supplier_name' => $supplier['supplier_name'],
                        'supplier_code' => $supplier['supplier_code'],
                        'location' => $supplier_group->groupBy('location_id')->map(function ($location_group, $location_id) {
                            $location = $location_group->first();
                            $sum_total_item_price = $location_group->sum('total_item_price');
                            return [
                                'location_id' => $location_id,
                                'location' => $location['location'],
                                'location_code' => $location['location_code'],
                                'total_item_price' => $sum_total_item_price,
                            ];
                        })->values()->all()
                    ];
                })->values()->all()
            ];
        })->values()->all();

        $locations = Location::orderBy('location', 'ASC')->get();

        $location_sum = collect($data)
        ->pluck('supplier')
        ->flatten(1)
        ->pluck('location')
        ->flatten(1);

        $total_item_price_location = $location_sum
        ->groupBy('location_id')
        ->map(function ($group) {
            return [
                'location_id' => $group->first()['location_id'],
                'location' => $group->first()['location'],
                'location_code' => $group->first()['location_code'],
                'total_item_price' => $group->sum('total_item_price')
            ];
        });

        $month_name = strtoupper(DateHelpers::numericToMonth($month));

        $param_data = [
            'locations' => $locations,
            'year' => $year,
            'month_name' => $month_name,
            'data' => $data,
            'total_item_price_location' => $total_item_price_location,
        ];
        return Excel::download(new RealisasiPurchaseRecord($param_data), 'Realisasi Purchase Record.xlsx');
        // return view('exports.realisasi_purchase_record_excel', compact('locations', 'year', 'month_name', 'data', 'total_item_price_location'));
    }

    public function seles_excel(Request $request)
    {
        $request->validate([
            'month' => ['required', 'between:1,12'],
            'year' => ['required', 'integer']
        ]);
        $month = $request->month;
        $year = $request->year;
        
        $sales = Sales::where([
            ['month', $month],
            ['year', $year],
        ])->get();

        $meal_sheat_month = $this->meal_sheet_month_count($month, $year);

        $month_name = strtoupper(DateHelpers::numericToMonth($month));
        $data = [
            'sales' => $sales,
            'meal_sheat_month' => $meal_sheat_month,
            'year' => $year,
            'month_name' => $month_name,
        ];
        
        // return view('exports.sales_excel', $data);
        return Excel::download(new SalesExport($data), 'Sales.xlsx');
    }

    public function real_mor_excel(Request $request)
    {
        $request->validate([
            'month' => ['required', 'between:1,12'],
            'year' => ['required', 'integer']
        ]);
        $month = $request->month;
        $year = $request->year;

        $query_data = SelectItemProduct::select(
            'quantity',
            'item_price',
            DB::raw('CAST(quantity * item_price AS UNSIGNED) AS total_item_price'),
            'supplier_id',
            'suppliers.name AS supplier_name',
            'suppliers.code AS supplier_code',
            'pr_caterings.location_id',
            'locations.location',
            'locations.location_code',
            'delivery_date',
            DB::raw('
                CASE
                    WHEN DAY(pr_caterings.delivery_date) BETWEEN 1 AND 7 THEN 1
                    WHEN DAY(pr_caterings.delivery_date) BETWEEN 8 AND 14 THEN 2
                    WHEN DAY(pr_caterings.delivery_date) BETWEEN 15 AND 21 THEN 3
                    WHEN DAY(pr_caterings.delivery_date) BETWEEN 22 AND 28 THEN 4
                    ELSE 5
                END AS week
            '),
        )
        ->join('po_supplier_caterings', 'po_supplier_caterings.id', '=',  'select_item_products.reference_id')
        ->join('po_caterings', 'po_caterings.id', '=', 'po_supplier_caterings.po_catering_id')
        ->join('pr_caterings', 'pr_caterings.id', '=', 'po_caterings.pr_catering_id')
        ->join('locations', 'locations.id', '=', 'pr_caterings.location_id')
        ->join('suppliers', 'suppliers.id', '=', 'po_supplier_caterings.supplier_id')
        ->where('po_supplier_caterings.status', 'submit')
        ->where('select_item_products.reference_type', POSupplierCatering::class)
        ->whereMonth('pr_caterings.delivery_date', $month)
        ->whereYear('pr_caterings.delivery_date', $year)
        ->get();

        $data = $query_data->groupBy('week')->map(function ($week_group, $week) {
            return [
                'week' => $week,
                'supplier' => $week_group->groupBy('supplier_id')->map(function ($supplier_group, $supplier_id) {
                    $supplier = $supplier_group->first();
                    return [
                        'supplier_id' => $supplier_id,
                        'supplier_name' => $supplier['supplier_name'],
                        'supplier_code' => $supplier['supplier_code'],
                        'location' => $supplier_group->groupBy('location_id')->map(function ($location_group, $location_id) {
                            $location = $location_group->first();
                            $sum_total_item_price = $location_group->sum('total_item_price');
                            return [
                                'location_id' => $location_id,
                                'location' => $location['location'],
                                'location_code' => $location['location_code'],
                                'total_item_price' => $sum_total_item_price,
                            ];
                        })->values()->all()
                    ];
                })->values()->all()
            ];
        })->values()->all();

        $locations = Location::orderBy('location', 'ASC')->get();

        $location_sum = collect($data)
        ->pluck('supplier')
        ->flatten(1)
        ->pluck('location')
        ->flatten(1);

        $total_item_price_location = $location_sum
        ->groupBy('location_id')
        ->map(function ($group) {
            return [
                'location_id' => $group->first()['location_id'],
                'location' => $group->first()['location'],
                'location_code' => $group->first()['location_code'],
                'total_item_price' => $group->sum('total_item_price')
            ];
        });

        $month_name = strtoupper(DateHelpers::numericToMonth($month));

        $wekk_summary = [];
        foreach($data as $week_data) {
            $week = $week_data['week'];
            foreach($week_data['supplier'] as $supplier) {
                foreach($supplier['location'] as $location) {
                    $location_id = $location['location_id'];
                    $location_name = $location['location'];
                    $total_item_price = $location['total_item_price'];

                    if (!isset($wekk_summary[$week])) {
                        $wekk_summary[$week] = [];
                    }
                    
                    if (!isset($wekk_summary[$week][$location_id])) {
                        $wekk_summary[$week][$location_id] = [
                            'location_name' => $location_name,
                            'total_item_price' => 0
                        ];
                    }
                    
                    $wekk_summary[$week][$location_id]['total_item_price'] += $total_item_price;
                }
            }
        }

        $mics_month = Location::with([
            'mor_month' => function ($query) use ($month, $year) {
                $query->where([
                    ['month', $month],
                    ['year', $year],
                ])
                ->with([
                    'mor_month_detail' => function ($query) {
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
            
        $meal_sheat_month = $this->meal_sheet_month_count($month, $year);
        $sales = Sales::where([
            ['month', $month],
            ['year', $year],
        ])->get();

        $last_stock = $this->last_month($month, $year);
        $last_month = $last_stock['month'];
        $last_year = $last_stock['year'];
        $last_month_name = strtoupper(DateHelpers::numericToMonth($last_month));

        $beginning_stock = Location::with([
            'mor_month' => function ($query) use ($last_month, $last_year) {
                $query->where([
                    ['month', $last_month],
                    ['year', $last_year],
                ])
                ->with([
                    'mor_month_detail' => function ($query) {
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

        $param_data = [
            'locations' => $locations,
            'year' => $year,
            'month_name' => $month_name,
            'data' => $data,
            'wekk_summary' => $wekk_summary,
            'total_item_price_location' => $total_item_price_location,
            'mics_month' => $mics_month,
            'meal_sheat_month' => $meal_sheat_month,
            'sales' => $sales,
            'beginning_stock' => $beginning_stock,
            'last_month_name' => $last_month_name,
            'last_year' => $last_year,
        ];

        // return view('exports.real_mor_excel', $param_data);

        return Excel::download(new RealMORExport($param_data), 'MONTHLY OPERATION REPORT.xlsx');
    }

    public function meal_sheet_month_count($month, $year)
    {
        $sub_query_meal_sheet = MealSheetDetail::select(
            'meal_sheet_details.mandays',
            'meal_sheet_details.casual_breakfast',
            'meal_sheet_details.casual_lunch',
            'meal_sheet_details.casual_dinner',
            'meal_sheet_details.supper',
            'meal_sheet_daily.meal_sheet_date',
            'meal_sheet_groups.location_id',
        )
        ->join('meal_sheet_daily', 'meal_sheet_daily.id', '=', 'meal_sheet_details.meal_sheet_daily_id')
        ->join('meal_sheet_groups', 'meal_sheet_groups.id', '=', 'meal_sheet_daily.meal_sheet_group_id')
        ->whereMonth('meal_sheet_daily.meal_sheet_date', $month)
        ->whereYear('meal_sheet_daily.meal_sheet_date', $year);

        $meal_sheat = Location::orderBy('location_code')
        ->leftJoinSub($sub_query_meal_sheet, 'meal_sheet', function (JoinClause $join) {
            $join->on('locations.id', '=', 'meal_sheet.location_id');
        })
        ->get();

        $meal_sheat_month = $meal_sheat->groupBy('id')->map(function($location_group, $location_id) {
            $location = $location_group->first();
            return [
                'location_id' => $location_id,
                'location' => $location['location'],
                'location_code' => $location['location_code'],
                'mandays' => $location_group->sum('mandays'),
                'casual_breakfast' => $location_group->sum('casual_breakfast'),
                'casual_lunch' => $location_group->sum('casual_lunch'),
                'casual_dinner' => $location_group->sum('casual_dinner'),
                'supper' => $location_group->sum('supper'),
            ];
        })->values()->all();

        return $meal_sheat_month;
    }

    public function last_month($month, $year)
    {
        $last_month = $month - 1;
        $last_year = $last_month == 0 ? $year - 1 : $year;

        return [
            'month' => $last_month,
            'year' => $last_year
        ];
    }
}
