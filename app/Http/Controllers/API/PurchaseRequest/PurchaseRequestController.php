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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PurchaseRequestController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1'],

            'status' => ['nullable', 'array'],
            'status.*' => ['nullable', 'in:draft,submit,reject,finish']
        ]);
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);
        $status = $request->status;

        $purchase_request = PurchaseRequest::when($search, function ($query, string $search) {
                                            $query->where('pr_number', 'like', '%'.$search.'%');
                                        })
                                        ->when($status, function ($query, array $status) {
                                            $query->whereIn('status', $status);
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
        $input = $request->safe()->except([
            'item_product'
        ]);

        $last_number = $this->last_number();
        $location = Location::find($request->location_id);
        $input['serial_number'] = $last_number;
        $input['pr_number'] = $last_number .'/SBL/PR/'. $location->location_code .'/'. DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;
        $input['status'] = 'draft';

        // database transaction for purchase request and item data
        $result = DB::transaction(function () use ($input, $request) {
            // store purchase request data
            $purchase_request = PurchaseRequest::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\PurchaseRequest';
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
                $item_product['reference_type'] = 'App\Models\PurchaseRequest';
                $item_product['reference_id'] = $purchase_request->id;
                SelectItemProduct::create($item_product);
            }

            return $purchase_request;
        });

        return ResponseFormatter::success(
            new PurchaseRequestDetailResource($result),
            'success update purchase request data'
        );
    }

    public function update_status(Request $request, PurchaseRequest $purchase_request)
    {
        $request->validate([
            'status' => ['required', 'in:submit,reject'],
            'note' => [
                Rule::requiredIf($request->status == 'reject')
            ]
        ]);
        $status = $request->status;

        $input = $request->only('status');
        if($status == 'reject') {
            $input['note'] = $request->note;
            $input['checked_date'] = NULL;
            $input['approved1_date'] = NULL;
            $input['approved2_date'] = NULL;
        } else {
            $input['note'] = NULL;
        }
        // return $input;
        $purchase_request->update($input);

        return ResponseFormatter::success(
            new PurchaseRequestDetailResource($purchase_request),
            'success update status purchase request data'
        );
    }
    
    public function update_approval_status(Request $request, PurchaseRequest $purchase_request)
    {
        $request->validate([
            'status' => ['required', 'in:checked,approved1,approved2']
        ]);
        $status = $request->status;

        $data = [
            'checked' => 'checked_date',
            'approved1' => 'approved1_date',
            'approved2' => 'approved2_date',
        ];

        $update = false;
        if ($status == 'checked') {
            $update = $purchase_request->status == 'submit' ? true : false;
        } else if (in_array($status, ['approved1','approved2'])) {
            $update = !empty($purchase_request->checked_date) ? true : false;
        }

        if($update) {

            DB::transaction(function () use ($purchase_request, $data, $status) {
                $purchase_request->update([
                    $data[$status] => Carbon::now()
                ]);

                if(
                    !empty($purchase_request->checked_date) && 
                    !empty($purchase_request->approved1_date) && 
                    !empty($purchase_request->approved2_date)
                ) {
                    $purchase_request->update([ 'status' => 'finish' ]);
                }
            });

            return ResponseFormatter::success(
                new PurchaseRequestDetailResource($purchase_request),
                'success update approval status purchase request data'
            );
        } else {
            return ResponseFormatter::errorValidation([
                'quotation_id' => 'Cannot update this quote because the status does not match'
            ], 'update status quotation failed', 422);
        }
    }

    public function destroy(PurchaseRequest $purchase_request)
    {
        DB::transaction(function () use ($purchase_request) {
            // delete attachment file
            $files = $purchase_request->attachment_file()->pluck('file')->toArray();
            Storage::disk('local')->delete($files);    
            $purchase_request->attachment_file()->delete();

            // delete item product
            $purchase_request->item_product()->delete();

            // delete purchase request
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
