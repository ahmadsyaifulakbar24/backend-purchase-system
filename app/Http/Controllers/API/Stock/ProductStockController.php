<?php

namespace App\Http\Controllers\API\Stock;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\Stock\ProductStockDetailResource;
use App\Http\Resources\Stock\ProductStockHistoryResource;
use App\Http\Resources\Stock\ProductStockResource;
use App\Models\Location;
use App\Models\ProductStock;
use App\Repository\ProductStockRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductStockController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1'],
        ]);
        $location_id = $request->location_id;
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);

        $product_stock = ProductStock::select(
            'product_stocks.id as id', 
            'item_products.id as item_product_id',
            'stock',
            'product_stocks.location_id',
            'product_stocks.updated_at as updated_at',
        )
        ->rightJoin('item_products', 'product_stocks.item_product_id', 'item_products.id')
        ->when($search, function ($query, $search) {
            $query->where(function($sub_query) use ($search) {
                $sub_query->where('item_products.name', 'like', '%'. $search .'%')
                    ->orWhere('item_products.code', 'like', '%'. $search .'%');
            });
        })
        ->where(function ($query) use ($location_id) {
            $query->where('product_stocks.location_id', $location_id)
                ->orWhereNull('product_stocks.location_id');
        });

        $result = $paginate ? $product_stock->paginate($limit) : $product_stock->get();

        return ResponseFormatter::success(
            ProductStockResource::collection($result)->response()->getData(true),
            'success get product stock data',
        );
    }

    public function upsert(Request $request)
    {
        $request->validate([
            'item_product_id' => ['required', 'exists:item_products,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'quantity' => ['required', 'integer'],
            'description' => ['required', 'string'],
        ]);

        try {
            DB::beginTransaction();
            $result = DB::transaction(function () use ($request) {
                $item_product_id = $request->item_product_id;
                $location_id = $request->location_id;
                $quantity = $request->quantity;
                $description = $request->description;
        
                $data = [
                    'item_product_id' => $item_product_id,
                    'location_id'  => $location_id,
                    'quantity'  => $quantity,
                    'description'  => $description,
                ];
                
                
                $product_stock = ProductStockRepository::find($data);
        
                if(!empty($product_stock)) {
                    $data['stock'] = $product_stock->stock + $quantity;
                } else {
                    $data['stock'] = $quantity;
                }
    
                $new_product_stock = ProductStockRepository::upsertProductStock($data, $product_stock);
    
                return $new_product_stock;
            });
            
            DB::commit();
            
            return ResponseFormatter::success(
                new ProductStockDetailResource($result),
                'success update product stock data'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return ResponseFormatter::errorValidation([
                'product_stock' => [$e->getMessage()],
            ], 'upsert product stock success');
        }
        
    }

    public function show(Request $request)
    {
        $request->validate([
            'item_product_id' => ['required', 'exists:item_products,id'],
            'location_id' => ['required', 'exists:locations,id'],
        ]);
        $item_product_id = $request->item_product_id;
        $location_id = $request->location_id;

        $product_stock = ProductStock::select(
            'product_stocks.id as id', 
            'item_products.id as item_product_id',
            'stock',
            'product_stocks.location_id',
            'product_stocks.updated_at as updated_at',
        )
        ->rightJoin('item_products', 'product_stocks.item_product_id', 'item_products.id')
        ->where(function ($query) use ($location_id) {
            $query->where('product_stocks.location_id', $location_id)
                ->orWhereNull('product_stocks.location_id');
        })
        ->where('item_products.id', $item_product_id)
        ->first();

        return ResponseFormatter::success(
            [
                'product_stock' => new ProductStockDetailResource($product_stock),
                'location' => new LocationResource(Location::find($location_id)),
            ],
            'success show product stock data',
        );
    }

    public function history(Request $request, ProductStock $product_stock)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'paginate' => ['nullable', 'in:0,1'],
        ]);
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);

        $product_stock_history = $product_stock->product_stock_history()->orderBy('created_at', 'DESC');
        $result = $paginate ? $product_stock_history->paginate($limit) : $product_stock_history->get();

        return ResponseFormatter::success(
            ProductStockHistoryResource::collection($result)->response()->getData(true),
            'success get prodduct stock history data'
        );
    }
}
