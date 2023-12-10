<?php

namespace App\Http\Controllers\API\ExternalOrder;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExternalOrder\POCustomer\POCustomerRequest;
use App\Http\Requests\ExternalOrder\POCustomer\POCustomerUpdateRequest;
use App\Http\Resources\ExternalOrder\POCustomer\POCustomerDetailResource;
use App\Http\Resources\ExternalOrder\POCustomer\POCustomerResource;
use App\Models\POCustomer;
use App\Models\SelectItemProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class POCustomerController extends Controller
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

        $po_customer = POCustomer::when($search, function ($query, string $search) {
                                            $query->where('po_number', 'like', '%'.$search.'%');
                                        })
                                        ->when($status, function ($query, array $status) {
                                            $query->whereIn('status', $status);
                                        })
                                        ->orderBy('created_at', 'DESC');

        $result = $paginate ? $po_customer->paginate($limit) : $po_customer->get();

        return ResponseFormatter::success(
            POCustomerResource::collection($result)->response()->getData(true),
            'success get po customer data'
        );
    }

    public function store(POCustomerRequest $request) 
    {
        $input = $request->except([
            'item_product'
        ]);

        $last_number = $this->last_number();
        $input['serial_number'] = $last_number;
        $input['po_number'] = $last_number .'/SBL/PO/CUSTOMER/' . DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;
        $input['status'] = 'draft';

        // database transaction for po customer and item data
        $result = DB::transaction(function () use ($input, $request) {
            // store po customer data
            $po_customer = POCustomer::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\POCustomer';
                $item_product['reference_id'] = $po_customer->id;
                SelectItemProduct::create($item_product);
            }

            return $po_customer;
        });

        return ResponseFormatter::success(
            new POCustomerDetailResource($result),
            'success create po customer data'
        );
    }

    public function show(POCustomer $po_customer)
    {
        return ResponseFormatter::success(
            new POCustomerDetailResource($po_customer),
            'success show po customer detail data'
        );
    }    

    public function update(POCustomerUpdateRequest $request, POCustomer $po_customer)
    {
        $input = $request->except([
            'item_product'
        ]);

        // database transaction for po customer and item data
        $result = DB::transaction(function () use ($input, $request, $po_customer) {
            // store po customer data
            $po_customer->update($input);

            // delete po customer item product
            $po_customer->item_product()->delete();

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\POCustomer';
                $item_product['reference_id'] = $po_customer->id;
                SelectItemProduct::create($item_product);
            }

            return $po_customer;
        });

        return ResponseFormatter::success(
            new POCustomerDetailResource($result),
            'success update po customer data'
        );
    }

    public function update_status(Request $request, POCustomer $po_customer)
    {
        $request->validate([
            'status' => ['required', 'in:draft,submit'],
        ]);

        $input = $request->only('status');
        $po_customer->update($input);

        return ResponseFormatter::success(
            new POCustomerDetailResource($po_customer),
            'success update status po customer data'
        );
    }

    public function destroy(POCustomer $po_customer)
    {
        DB::transaction(function () use ($po_customer) {
            // delete attachment file
            $files = $po_customer->attachment_file()->pluck('file')->toArray();
            Storage::disk('local')->delete($files);    
            $po_customer->attachment_file()->delete();

            // delete item product
            $po_customer->item_product()->delete();

            // delete catering po
            $po_customer->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete po customer data'
        );
    }

    public function last_number()
    {
         $last_number = POCustomer::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
