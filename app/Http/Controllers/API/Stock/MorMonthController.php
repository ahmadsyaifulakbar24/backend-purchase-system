<?php

namespace App\Http\Controllers\API\Stock;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Stock\MorMonthDetailResource;
use App\Http\Resources\Stock\MorMonthResource;
use App\Models\MorMonth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MorMonthController extends Controller
{
    
    public function get_month_year(Request $request)
    {
        $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
            'limit' => ['nullable', 'integer']
        ]);
        $location_id = $request->location_id;
        $limit = $request->input('limit', 10);

        $mor_month = MorMonth::where('location_id', $location_id)->paginate($limit);

        return ResponseFormatter::success(
            MorMonthResource::collection($mor_month)->response()->getData(true),
            'success get group mor by month and year'
        );

    }

    public function get(Request $request, MorMonth $mor_month)
    {
        $request->validate([
            'limit' => ['nullable', 'integer']
        ]);
        $limit = $request->input('limit', 10);

        $mor_month_detail = $mor_month->mor_month_detail()->paginate($limit);

        return ResponseFormatter::success([
            'mor_month' => new MorMonthResource($mor_month),
            'detail' => MorMonthDetailResource::collection($mor_month_detail)->response()->getData(true)
        ],
            'success get mor month detail data'
        );
    }
    
    public function upsert(Request $request)
    {
        $request->validate([
            'location_id' => ['required','exists:locations,id'],
            'month' => ['required', 'digits_between:1,12'],
            'year' => ['required', 'integer'],

            'product' => ['required', 'array'],
            'product.*.item_product_id' => [
                'required',
                Rule::exists('item_products', 'id')->where(function ($query) use ($request) {
                    $query->where('location_id', $request->location_id);
                })
            ],
            'product.*.price' => ['required', 'integer'],
            'product.*.last_stock' => ['required', 'integer'],
            'product.*.actual_stock' => ['required', 'integer'],
        ]);
        
        try {
            DB::beginTransaction();
    
            $result = DB::transaction(function () use ($request) {
                $product = $request->product;
                
                $mor_month = MorMonth::where([
                    ['location_id', $request->location_id],
                    ['month', $request->month],
                    ['year', $request->year]
                ])->first();
        
                if(empty($mor_month)) {
                    $mor_month = MorMonth::create([
                        'location_id' => $request->location_id,
                        'month' => $request->month,
                        'year' => $request->year
                    ]);
                }
    
                foreach ($product as $data) {
                    
                    $mor_month_detail = $mor_month->mor_month_detail()->where('item_product_id', $data['item_product_id'])->first();
    
                    if(!empty($mor_month_detail)) {
                        $mor_month_detail->update([
                            'price' => $data['price'],
                            'last_stock' => $data['last_stock'],
                            'actual_stock' => $data['actual_stock'],
                        ]);
                    } else {
                        $mor_month_detail = $mor_month->mor_month_detail()->create($data);
                    }
                }
            
                return ResponseFormatter::success(
                    new MorMonthResource($mor_month),
                    'success upsert mor month data',
                );
    
            });
    
            DB::commit();
    
            return $result;

        } catch (Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error([
                'messaage' => $e->getMessage(),
            ], 'upsert mor month data failed');
        }
    }
}
