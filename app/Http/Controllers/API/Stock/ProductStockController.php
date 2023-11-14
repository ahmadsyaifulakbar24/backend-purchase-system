<?php

namespace App\Http\Controllers\API\Stock;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Stock\ProductStockDetailResource;
use App\Http\Resources\Stock\ProductStockHistoryResource;
use App\Http\Resources\Stock\ProductStockResource;
use App\Models\ProductStock;
use Illuminate\Http\Request;

class ProductStockController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'location_id' => ['nullable', 'exists:locations,id'],
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
        });
        
        if(!empty($location_id)) {
            $product_stock->where('product_stocks.location_id', $location_id);
        } else {
            $product_stock->whereNull('product_stocks.location_id');
        }

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
            'location_id' => ['nullable', 'exists:locations,id'],
            'quantity' => ['required', 'integer'],
            'description' => ['required', 'string'],
        ]);
        $item_product_id = $request->item_product_id;
        $location_id = $request->location_id;
        $quantity = $request->quantity;
        $description = $request->description;

        if(!empty($location_id)) {
            $query_product_stock = ProductStock::where([
                ['item_product_id', $item_product_id],
                ['location_id', $location_id],
            ]);
        } else {
            $query_product_stock = ProductStock::where('item_product_id', $item_product_id);
        }

        if($query_product_stock->count() > 0) {
            $product_stock = $query_product_stock->first();

            $product_stock->update([
                'item_product_id' => $item_product_id,
                'location_id' => $location_id,
                'stock' => $product_stock->stock + $quantity,
            ]);
        } else {
            $product_stock = ProductStock::create([
                'item_product_id' => $item_product_id,
                'location_id' => $location_id,
                'stock' => $quantity,
            ]);
        }

        $product_stock->product_stock_history()->create([
            'quantity' => $quantity,
            'description' => $description,
        ]);

        return ResponseFormatter::success(
            new ProductStockDetailResource($product_stock),
            'success update product stock data'
        );
    }

    public function show(Request $request)
    {
        $request->validate([
            'item_product_id' => ['required', 'exists:item_products,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
        ]);
        $item_product_id = $request->item_product_id;
        $location_id = $request->location_id;

        if(!empty($location_id)) {
            $query_product_stock = ProductStock::where([
                ['item_product_id', $item_product_id],
                ['location_id', $location_id],
            ]);
        } else {
            $query_product_stock = ProductStock::where('item_product_id', $item_product_id);
        }

        $product_stock = $query_product_stock->first();
        return ResponseFormatter::success(
            new ProductStockDetailResource($product_stock),
            'success show product stock data',
        );
    }

    public function history(ProductStock $product_stock)
    {
        $product_stock_history = $product_stock->product_stock_history;
        return ResponseFormatter::success(
            ProductStockHistoryResource::collection($product_stock_history),
            'success get prodduct stock history data'
        );
    }
}
