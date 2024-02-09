<?php

namespace App\Http\Controllers\API\Stock;

use App\Exports\MorExport;
use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\MorRequest;
use App\Http\Resources\Stock\MorDailyResource;
use App\Http\Resources\Stock\MorMonthlyResource;
use App\Http\Resources\Stock\MorResource;
use App\Models\ItemProduct;
use App\Models\Location;
use App\Models\Mor;
use App\Models\SelectItemProduct;
use App\Repository\ProductStockRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MorController extends Controller
{
    public function daily (Request $request)
    {
        $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
            'limit' => ['nullable', 'integer'],
            'paginate' => ['nullable', 'in:0,1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);
        $location_id = $request->location_id;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        
        $mor_daily = Mor::where('location_id', $location_id)
                        ->when($start_date, function ($query, $start_date) {
                            $query->where('date', '>=', $start_date);
                        })
                        ->when($end_date, function ($query, $end_date) {
                            $query->where('date', '<=', $end_date);
                        })
                        ->groupBy('date');

        $result = $paginate ? $mor_daily->paginate($limit) : $mor_daily->get();
        return ResponseFormatter::success(
            MorDailyResource::collection($result)->response()->getData(true),
            'success get mor daily data'
        );
    }

    public function get (Request $request)
    {
        $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
            'date' => ['required', 'date'],
            'limit' => ['nullable', 'integer'],
            'paginate' => ['nullable', 'in:0,1'],
        ]);
        $location_id = $request->location_id;
        $date = $request->date;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);

        $mor = Mor::where([
                        ['location_id', $location_id],
                        ['date', $date]
                    ]);

        $result = $paginate ? $mor->paginate($limit) : $mor->get();
        
        return ResponseFormatter::success(
            MorResource::collection($result)->response()->getData(true),
            'success get mor data'
        );
    }

    public function upsert(MorRequest $request)
    {
        try {
            DB::beginTransaction();

            $result = DB::transaction(function () use ($request) {
                $item_products = $request->item_product;
                $location_id = $request->location_id;
            
                foreach ($item_products as $item_product) {
                    $quantity = intval(-$item_product['quantity']);

                    $mor = Mor::where([
                        ['location_id', $location_id],
                        ['item_product_id', $item_product['item_product_id']],
                        ['date', $item_product['date']],
                    ])->first();
        
                    $data = [
                        'item_product_id' => $item_product['item_product_id'],
                        'location_id' => $location_id,
                        'quantity' => $quantity,
                        'description'  => 'Update From MOR',
                    ];

                    // perhitungan stock
                    $product_stock = ProductStockRepository::find($data);
                    if(!empty($product_stock)) {
                        $data['stock'] = $product_stock->stock + $quantity;
                    } else {
                        $data['stock'] = $quantity;
                    }

                    if (!empty($mor)) {
                        // throw new \Exception('data with these location, item product, and date already exists');

                        $data['stock'] = $product_stock->stock + $mor->quantity + $quantity;
                        $data['description']  = 'Edit Quantity MOR from ' . $mor->quantity . ' to ' . $item_product['quantity'];

                        $mor->update([
                            'quantity' => $item_product['quantity'],
                            'item_price' => $item_product['item_price']
                        ]);
                    } else {
                        $input = Arr::prepend($item_product, $location_id, 'location_id');
                        Mor::create($input);
                    }

                    ProductStockRepository::upsertProductStock($data, $product_stock);
        

                }
                
                return ResponseFormatter::success(
                    null,
                    'success upsert mor data',
                );
            });

            DB::commit();
            
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
        
            return ResponseFormatter::errorValidation([
                'mor' => [$e->getMessage()],
            ], 'create mor data failed');
        }
        
    }

    public function export(Request $request)
    {
        $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
            'month' => ['required', 'between:1,12'],
            'year' => ['required', 'integer']
        ]);
        $location_id = $request->location_id;
        $month = $request->month;
        $year = $request->year;

        $location = Location::find($location_id);
        $item_product = ItemProduct::select(
            'id',
            'code',
            'name',
            'item_category_id',
            'sub_item_category_id',
            'brand',
            'size',
            'unit_id',
        )
        ->with([
            'delivery_order' => function ($query) use ($location_id, $month, $year) {
                $query->select(
                    'item_product_id',
                    DB::raw('
                        CASE
                            WHEN DAY(pr_caterings.delivery_date) BETWEEN 1 AND 7 THEN 1
                            WHEN DAY(pr_caterings.delivery_date) BETWEEN 8 AND 14 THEN 2
                            WHEN DAY(pr_caterings.delivery_date) BETWEEN 15 AND 21 THEN 3
                            WHEN DAY(pr_caterings.delivery_date) BETWEEN 22 AND 28 THEN 4
                            ELSE 5
                        END AS week
                    '),
                    DB::raw('SUM(select_item_products.quantity) as total_quantity')
                )
                ->join('do_caterings', 'do_caterings.id', '=', 'select_item_products.reference_id')
                ->join('po_supplier_caterings', 'po_supplier_caterings.id', '=', 'do_caterings.po_supplier_catering_id')
                ->join('po_caterings', 'po_caterings.id', '=', 'po_supplier_caterings.po_catering_id')
                ->join('pr_caterings', 'pr_caterings.id', '=', 'po_caterings.pr_catering_id')
                ->where('select_item_products.reference_type', 'App\Models\DOCatering')
                ->where('do_caterings.status', 'submit')
                ->where('pr_caterings.location_id', $location_id)
                ->whereMonth('pr_caterings.delivery_date', $month)
                ->whereYear('pr_caterings.delivery_date', $year)
                ->groupBy('week')
                ->orderBy('week', 'ASC');
            },
            'mor' => function ($query) use ($location_id, $month, $year) {
                $query->where('location_id', $location_id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year);
            },
            'item_category',
            'sub_item_category',
            'unit',
            'mor_month_detail' => function ($query) use ($location_id, $month, $year) {
                $query->whereHas('mor_month', function ($sub_query) use ($location_id, $month, $year) {
                    $sub_query->where([
                        ['location_id', $location_id],
                        ['month', $month],
                        ['year', $year],
                    ]);
                });
            }
        ])
        ->where('location_id', $location_id)->get();

        // return $item_product;

        $grouped_data = $item_product->groupBy([
            'item_category.category_code',
            'sub_item_category.category_code'
        ]);

        // return view('exports.mor',[
        //     'location' => $location,
        //     'month' => DateHelpers::numericToMonth($month),
        //     'last_date' => Carbon::createFromDate($year, $month)->endOfMonth()->format('d-m-Y'),
        //     'mm' => str_pad($month, 2, '0', STR_PAD_LEFT),
        //     'year' => $year,
        //     'item_product' => $grouped_data
        // ]);

        $return_data = [
            'location' => $location,
            'month' => DateHelpers::numericToMonth($month),
            'mm' => str_pad($month, 2, '0', STR_PAD_LEFT),
            'year' => $year,
            'last_date' => Carbon::createFromDate($year, $month)->endOfMonth()->format('d-m-Y'),
            'item_product' => $grouped_data
        ];

        return Excel::download(new MorExport($return_data), 'MOR.xlsx');
    }
}
