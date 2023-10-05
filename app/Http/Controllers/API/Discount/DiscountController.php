<?php

namespace App\Http\Controllers\API\Discount;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Discount\DiscountResource;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1'],
        ]);
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);

        $discount = Discount::when($search, function ($query, string $search) {
                                $query->where('discount', 'like', '%'. $search. '%');
                            })
                            ->orderBy('discount', 'ASC');
                        
        $result = $paginate ? $discount->paginate($limit) : $discount->get();                            

        return ResponseFormatter::success(
            DiscountResource::collection($result)->response()->getData(true),
            'success get discount data'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'discount' => ['required', 'unique:discounts,discount', 'integer', 'max:100']
        ]);

        $input = $request->all();
        $discount = Discount::create($input);

        return ResponseFormatter::success(
            new DiscountResource($discount),
            'success create discount data'
        );
    }

    public function show(Discount $discount)
    {
        return ResponseFormatter::success(
            new DiscountResource($discount),
            'success show discount data'
        );
    }

    public function update(Discount $discount, Request $request)
    {
        $request->validate([
            'discount' => ['required', 'unique:discounts,discount,' . $discount->id, 'integer', 'max:100']
        ]);

        $input = $request->all();
        $discount->update($input);

        return ResponseFormatter::success(
            new DiscountResource($discount),
            'success update discount data'
        );
    }

    public function destroy(Discount $discount)
    {
        
        $discount->delete();

        return ResponseFormatter::success(
            null,
            'success delete discount data'
        );
    }
}
