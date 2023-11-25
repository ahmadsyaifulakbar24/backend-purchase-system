<?php

namespace App\Http\Controllers\API\InternalOrder;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\InternalOrder\PRCateringRequest;
use App\Http\Resources\InternalOrder\PRCateringDetailResource;
use App\Http\Resources\InternalOrder\PRCateringResource;
use App\Models\Location;
use App\Models\PRCatering;
use App\Models\SelectItemProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PRCateringController extends Controller
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
        $status = $request->status;

        $pr_catering = PRCatering::when($search, function ($query, string $search) {
                                    $query->where('pr_number', 'like', '%'.$search.'%');
                                })
                                ->when($status, function ($query, array $status) {
                                    $query->whereIn('status', $status);
                                })
                                ->orderBy('created_at', 'DESC');

        $result = $paginate ? $pr_catering->paginate($limit) : $pr_catering->get();

        return ResponseFormatter::success(
            PRCateringResource::collection($result)->response()->getData(true),
            'success get pr catering data'
        );
    }

    public function store(PRCateringRequest $request) 
    {
        $input = $request->safe()->except([
            'item_product'
        ]);

        $last_number = $this->last_number();
        $location = Location::find($request->location_id);
        $input['serial_number'] = $last_number;
        $input['pr_number'] = $last_number .'/SBL/PR/'. $location->location_code .'/'. DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;

        // database transaction for create pr catering an product
        $result = DB::transaction(function () use ($input, $request) {
            // store pr catering data
            $pr_catering = PRCatering::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\PRCatering';
                $item_product['reference_id'] = $pr_catering->id;
                SelectItemProduct::create($item_product);
            }

            return $pr_catering;
        });

        return ResponseFormatter::success(
            new PRCateringDetailResource($result),
            'success create pr catering data'
        );
    }

    public function show(PRCatering $pr_catering) 
    {
        return ResponseFormatter::success(
            new PRCateringDetailResource($pr_catering),
            'success show pr catering detail data'
        );
    }

    public function update(PRCateringRequest $request, PRCatering $pr_catering) 
    {
        $input = $request->except([
            'item_product'
        ]);

        // database transaction for create pr catering an product
        $result = DB::transaction(function () use ($input, $request, $pr_catering) {
            // update pr catering data
            $pr_catering->update($input);

             // delete pr catering item product
             $pr_catering->item_product()->delete();

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\PRCatering';
                $item_product['reference_id'] = $pr_catering->id;
                SelectItemProduct::create($item_product);
            }

            return $pr_catering;
        });

        return ResponseFormatter::success(
            new PRCateringDetailResource($result),
            'success update pr catering data'
        );
    }

    public function destroy(PRCatering $pr_catering)
    {
        DB::transaction(function () use ($pr_catering) {
            // delete attachment file
            $files = $pr_catering->attachment_file()->pluck('file')->toArray();
            Storage::disk('local')->delete($files);    
            $pr_catering->attachment_file()->delete();

            // delete item product
            $pr_catering->item_product()->delete();

            // delete purchase request
            $pr_catering->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete purchase request data'
        );
    }

    public function last_number()
    {
         $last_number = PRCatering::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
