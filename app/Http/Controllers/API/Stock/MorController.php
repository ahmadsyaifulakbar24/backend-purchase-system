<?php

namespace App\Http\Controllers\API\Stock;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\MorRequest;
use App\Models\Mor;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class MorController extends Controller
{
    public function get (Request $request)
    {

    }

    public function upsert(MorRequest $request)
    {
        try {
            DB::beginTransaction();

            $result = DB::transaction(function () use ($request) {
                $item_products = $request->item_product;
                $location_id = $request->location_id;
            
                foreach ($item_products as $item_product) {
                    $mor = Mor::where([
                        ['location_id', $location_id],
                        ['item_product_id', $item_product['item_product_id']],
                        ['date', $item_product['date']],
                    ])->first();
        
                    if (!empty($mor)) {
                        throw new \Exception('data with these location, item product, and date already exists');
                    }
        
                    $input = Arr::prepend($item_product, $location_id, 'location_id');
                    Mor::create($input);
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
