<?php

namespace App\Http\Controllers\API\Stock;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\MorRequest;
use App\Http\Resources\Stock\MorDailyResource;
use App\Http\Resources\Stock\MorResource;
use App\Models\Mor;
use App\Repository\ProductStockRepository;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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
}
