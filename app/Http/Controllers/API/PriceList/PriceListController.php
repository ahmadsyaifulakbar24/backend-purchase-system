<?php

namespace App\Http\Controllers\API\PriceList;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\PriceList\PriceListResource;
use App\Models\PriceList;
use Illuminate\Http\Request;

class PriceListController extends Controller
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

        $price_list = PriceList::when($search, function ($query, string $search) {
                                $query->where(function ($sub_query) use ($search) {
                                    $sub_query->whereHas('item_product', function ($sub_query2) use ($search) {
                                        $sub_query2->where('name', 'like', '%'. $search. '%');
                                    });
                                });
                            })
                            ->when($location_id, function ($query, $location_id) {
                                $query->where('location_id', $location_id);
                            })
                            ->orderBy('created_at', 'DESC');
                            
        $result = $paginate ? $price_list->paginate($limit) : $price_list->get();

        return ResponseFormatter::success(
            PriceListResource::collection($result)->response()->getData(true),
            'success get price_list data'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'item_product_id' => ['required', 'exists:item_products,id'],
            'price' => ['required', 'numeric'],
        ]);

        $input = $request->all();
        $price_list = PriceList::create($input);

        return ResponseFormatter::success(
            new PriceListResource($price_list),
            'success create price list data'
        );
    }

    public function show(PriceList $price_list)
    {
        return ResponseFormatter::success(
            new PriceListResource($price_list),
            'success show price list data'
        );
    }

    public function update(Request $request, PriceList $price_list)
    {
        $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'item_product_id' => ['required', 'exists:item_products,id'],
            'price' => ['required', 'numeric'],
        ]);

        $input = $request->all();
        $price_list->update($input);

        return ResponseFormatter::success(
            new PriceListResource($price_list),
            'success update price list data'
        );
    }

    public function destroy(PriceList $price_list)
    {
        $price_list->delete();

        return ResponseFormatter::success(
            null,
            'success delete price list data'
        );
    }
}
