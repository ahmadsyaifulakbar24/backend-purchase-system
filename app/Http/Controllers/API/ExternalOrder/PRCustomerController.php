<?php

namespace App\Http\Controllers\API\ExternalOrder;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExternalOrder\PRCustomer\PRCustomerRequest;
use App\Http\Resources\ExternalOrder\PRCustomer\PRCustomerDetailResource;
use App\Http\Resources\ExternalOrder\PRCustomer\PRCustomerResource;
use App\Models\Location;
use App\Models\PRCustomer;
use App\Models\SelectItemProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PRCustomerController extends Controller
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

        $pr_customer = PRCustomer::when($search, function ($query, string $search) {
                                    $query->where('pr_number', 'like', '%'.$search.'%');
                                })
                                ->when($status, function ($query, array $status) {
                                    $query->whereIn('status', $status);
                                })
                                ->orderBy('created_at', 'DESC');

        $result = $paginate ? $pr_customer->paginate($limit) : $pr_customer->get();

        return ResponseFormatter::success(
            PRCustomerResource::collection($result)->response()->getData(true),
            'success get pr customer data'
        );
    }

    public function store(PRCustomerRequest $request) 
    {
        $input = $request->safe()->except([
            'item_product'
        ]);

        $last_number = $this->last_number();
        $location = Location::find($request->location_id);
        $input['serial_number'] = $last_number;
        $input['pr_number'] = $last_number .'/SBL/PRCTR/'. $location->location_code .'/'. DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;

        // database transaction for create pr customer an product
        $result = DB::transaction(function () use ($input, $request) {
            // store pr customer data
            $pr_customer = PRCustomer::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\PRCustomer';
                $item_product['reference_id'] = $pr_customer->id;
                SelectItemProduct::create($item_product);
            }

            return $pr_customer;
        });

        return ResponseFormatter::success(
            new PRCustomerDetailResource($result),
            'success create pr customer data'
        );
    }

    public function show(PRCustomer $pr_customer) 
    {
        return ResponseFormatter::success(
            new PRCustomerDetailResource($pr_customer),
            'success show pr customer detail data'
        );
    }

    public function update(PRCustomerRequest $request, PRCustomer $pr_customer) 
    {
        $input = $request->except([
            'item_product'
        ]);

        // database transaction for create pr customer an product
        $result = DB::transaction(function () use ($input, $request, $pr_customer) {
            // update pr customer data
            $pr_customer->update($input);

             // delete pr customer item product
             $pr_customer->item_product()->delete();

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\PRCustomer';
                $item_product['reference_id'] = $pr_customer->id;
                SelectItemProduct::create($item_product);
            }

            return $pr_customer;
        });

        return ResponseFormatter::success(
            new PRCustomerDetailResource($result),
            'success update pr customer data'
        );
    }

    public function destroy(PRCustomer $pr_customer)
    {
        DB::transaction(function () use ($pr_customer) {
            // delete attachment file
            $files = $pr_customer->attachment_file()->pluck('file')->toArray();
            Storage::disk('local')->delete($files);    
            $pr_customer->attachment_file()->delete();

            // delete item product
            $pr_customer->item_product()->delete();

            // delete purchase request
            $pr_customer->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete pr customer data'
        );
    }

    public function last_number()
    {
         $last_number = PRCustomer::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
