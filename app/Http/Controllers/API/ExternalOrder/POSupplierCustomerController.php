<?php

namespace App\Http\Controllers\API\ExternalOrder;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExternalOrder\POSupplierCustomer\POSupplierCustomerRequest;
use App\Http\Requests\ExternalOrder\POSupplierCustomer\POSupplierCustomerUpdateRequest;
use App\Http\Resources\ExternalOrder\POSupplierCustomer\POSupplierCustomerDetailResource;
use App\Http\Resources\ExternalOrder\POSupplierCustomer\POSupplierCustomerResource;
use App\Models\POSupplierCustomer;
use App\Models\SelectItemProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class POSupplierCustomerController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1'],

            'status' => ['nullable', 'array'],
            'status.*' => ['nullable', 'in:draft,submit']
        ]);
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);
        $status = $request->status;

        $po_supplier_customer = POSupplierCustomer::when($search, function ($query, string $search) {
                                            $query->where('po_number', 'like', '%'.$search.'%');
                                        })
                                        ->when($status, function ($query, array $status) {
                                            $query->whereIn('status', $status);
                                        })
                                        ->orderBy('created_at', 'DESC');

        $result = $paginate ? $po_supplier_customer->paginate($limit) : $po_supplier_customer->get();

        return ResponseFormatter::success(
            POSupplierCustomerResource::collection($result)->response()->getData(true),
            'success get po supplier customer data'
        );
    }

    public function store(POSupplierCustomerRequest $request) 
    {
        $input = $request->except([
            'item_product'
        ]);
        
        // create variable for po supllier customer
        $last_number = $this->last_number();
        $input['created_by'] = Auth::user()->id;
        $input['serial_number'] = $last_number;
        $input['po_number'] = $last_number .'/SBL/PO/CUSTOMER/' . DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;

        // database transaction for po supplier customer and item data
        $result = DB::transaction(function () use ($input, $request) {
            // store po supplier customer data
            $po_supplier_customer = POSupplierCustomer::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\POSupplierCustomer';
                $item_product['reference_id'] = $po_supplier_customer->id;
                SelectItemProduct::create($item_product);
            }

            return $po_supplier_customer;
        });

        return ResponseFormatter::success(
            new POSupplierCustomerDetailResource($result),
            'success create po supplier customer data'
        );
    }    

    public function show(POSupplierCustomer $po_supplier_customer)
    {
        return ResponseFormatter::success(
            new POSupplierCustomerDetailResource($po_supplier_customer),
            'success show po supplier customer detail data'
        );
    }    

    public function update(POSupplierCustomerUpdateRequest $request, POSupplierCustomer $po_supplier_customer)
    {
        $input = $request->except([
            'item_product'
        ]);
        $hard_edit = $request->hard_edit;

        if ($po_supplier_customer->status == 'submit' && $hard_edit == 'no') {
            return ResponseFormatter::errorValidation([
                'po_supplier_customer_id' => ['cannot update this data because the status has already been submitted']
            ], 'update po supplier customer data failed', 422);
        }

        // database transaction for po supplier customer and item data
        $result = DB::transaction(function () use ($input, $request, $po_supplier_customer) {
            // store po supplier customer data
            $po_supplier_customer->update($input);

            // delete po supplier customer item product
            $po_supplier_customer->item_product()->delete();

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\POSupplierCustomer';
                $item_product['reference_id'] = $po_supplier_customer->id;
                SelectItemProduct::create($item_product);
            }

            return $po_supplier_customer;
        });

        return ResponseFormatter::success(
            new POSupplierCustomerDetailResource($result),
            'success update po supplier customer data'
        );
    }

    public function update_status(Request $request, POSupplierCustomer $po_supplier_customer)
    {
        $request->validate([
            'status' => ['required', 'in:draft,submit'],
        ]);

        $input = $request->only('status');
        $po_supplier_customer->update($input);

        return ResponseFormatter::success(
            new POSupplierCustomerDetailResource($po_supplier_customer),
            'success update status po supplier customer data'
        );
    }

    public function destroy(POSupplierCustomer $po_supplier_customer)
    {
        // if ($po_supplier_customer->status == 'submit') {
        //     return ResponseFormatter::errorValidation([
        //         'po_supplier_customer_id' => ['cannot update this data because the status has already been submitted']
        //     ], 'update po supplier customer data failed', 422);
        // }
        
        DB::transaction(function () use ($po_supplier_customer) {

            // delete po supplier customer data 
                // delete attachment file
                $files = $po_supplier_customer->attachment_file()->pluck('file')->toArray();
                Storage::disk('local')->delete($files);    
                $po_supplier_customer->attachment_file()->delete();

                // delete item product
                $po_supplier_customer->item_product()->delete();

                // delete customer po
                $po_supplier_customer->delete();
            // end delete po supplier customer data 
        });

        return ResponseFormatter::success(
            null,
            'success delete po supplier customer data'
        );
    }

    public function last_number()
    {
         $last_number = POSupplierCustomer::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
