<?php

namespace App\Http\Controllers\API\PurchaseRequest;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseRequest\PurchaseRequestRequest;
use App\Http\Resources\PurchaseRequest\PurchaseRequestDetailResource;
use App\Http\Resources\PurchaseRequest\PurchaseRequestResource;
use App\Models\Location;
use App\Models\PurchaseRequest;
use App\Models\SelectItemProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1']
        ]);
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);

        $purchase_request = PurchaseRequest::when($search, function ($query, string $search) {
                                            $query->where('pr_number', $search);
                                        })
                                        ->orderBy('created_at', 'DESC');

        $result = $paginate ? $purchase_request->paginate($limit) : $purchase_request->get();

        return ResponseFormatter::success(
            PurchaseRequestResource::collection($result)->response()->getData(true),
            'success get purchase Request data'
        );
    }

    public function store(PurchaseRequestRequest $request, PurchaseRequest $purchase_request)
    {
        $input = $request->except([
            'item_product'
        ]);

        $last_number = $this->last_number();
        $location = Location::find($request->location_id);
        $input['serial_number'] = $last_number;
        $input['pr_number'] = $last_number .'/SBL/PR/'. $location->location_code .'/'. DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;

        // database transaction for purchase request and item data
        $result = DB::transaction(function () use ($input, $request) {
            // store purchase request data
            $purchase_request = PurchaseRequest::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App/Models/PurchaseRequest';
                $item_product['reference_id'] = $purchase_request->id;
                SelectItemProduct::create($item_product);
            }

            return $purchase_request;
        });

        return ResponseFormatter::success(
            new PurchaseRequestDetailResource($result),
            'success create purchase request data'
        );
    }

    public function show(PurchaseRequest $purchase_request)
    {
        return ResponseFormatter::success(
            new PurchaseRequestDetailResource($purchase_request),
            'success show purchase Request detail data'
        );
    }

    public function update(PurchaseRequestRequest $request, PurchaseRequest $purchase_request)
    {
        $input = $request->except([
            'item_product'
        ]);

        // database transaction for purchase request and item
        $result = DB::transaction(function () use ($input, $request, $purchase_request) {
            // store purchase request data
            $purchase_request->update($input);

            // delete purchase request item product
            $purchase_request->item_product()->delete();

            // store item product data
            foreach($request->item_product as $item_product) {
                
                // store purchase request item product
                $item_product['reference_type'] = 'App/Models/PurchaseRequest';
                $item_product['reference_id'] = $purchase_request->id;
                SelectItemProduct::create($item_product);
            }

            return $purchase_request;
        });

        return ResponseFormatter::success(
            new PurchaseRequestDetailResource($result),
            'success create purchase request data'
        );
    }

    public function update_status(Request $request, PurchaseRequest $purchase_request)
    {
        $request->validate([
            'status' => ['required', 'in:checked,approved']
        ]);
        
        $data = [
            'checked' => 'checked_date',
            'approved' => 'approved_date',
        ];

        $purchase_request->update([
            $data[$request->status] => Carbon::now()
        ]);

        return ResponseFormatter::success(
            new PurchaseRequestDetailResource($purchase_request),
            'success update purchase request data'
        );
    }

    public function destroy(PurchaseRequest $purchase_request)
    {
        DB::transaction(function () use ($purchase_request) {
            $purchase_request->item_product()->delete();
            $purchase_request->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete purchase request data'
        );
    }

    public function last_number()
    {
         $last_number = PurchaseRequest::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
