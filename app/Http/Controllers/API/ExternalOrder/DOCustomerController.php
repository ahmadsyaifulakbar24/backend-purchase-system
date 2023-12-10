<?php

namespace App\Http\Controllers\API\ExternalOrder;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExternalOrder\DOCustomer\DOCustomerRequest;
use App\Http\Requests\ExternalOrder\DOCustomer\DOCustomerUpdateRequest;
use App\Http\Resources\ExternalOrder\DOCustomer\DOCustomerDetailResource;
use App\Http\Resources\ExternalOrder\DOCustomer\DOCustomerResource;
use App\Models\DOCustomer;
use App\Models\SelectItemProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DOCustomerController extends Controller
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

        $do_customer = DOCustomer::when($search, function ($query, string $search) {
                                            $query->where('do_number', 'like', '%'.$search.'%');
                                        })
                                        ->when($status, function ($query, array $status) {
                                            $query->whereIn('status', $status);
                                        })
                                        ->orderBy('created_at', 'DESC');

        $result = $paginate ? $do_customer->paginate($limit) : $do_customer->get();

        return ResponseFormatter::success(
            DOCustomerResource::collection($result)->response()->getData(true),
            'success get do customer data'
        );
    }

    public function store(DOCustomerRequest $request) 
    {
        $input = $request->except([
            'item_product'
        ]);

        $last_number = $this->last_number();
        $input['created_by'] = Auth::user()->id;
        $input['serial_number'] = $last_number;
        $input['do_number'] = $last_number .'/SBL/DO/CUSTOMER/' . DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;
        $input['status'] = 'draft';

        // database transaction for do customer and item data
        $result = DB::transaction(function () use ($input, $request) {
            // store do customer data
            $do_customer = DOCustomer::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\DOCustomer';
                $item_product['reference_id'] = $do_customer->id;
                SelectItemProduct::create($item_product);
            }

            return $do_customer;
        });

        return ResponseFormatter::success(
            new DOCustomerDetailResource($result),
            'success create do customer data'
        );
    }

    public function show(DOCustomer $do_customer)
    {
        return ResponseFormatter::success(
            new DOCustomerDetailResource($do_customer),
            'success show do customer detail data'
        );
    }    

    public function update(DOCustomerUpdateRequest $request, DOCustomer $do_customer)
    {
        $input = $request->except([
            'item_product'
        ]);

        // database transaction for do customer and item data
        $result = DB::transaction(function () use ($input, $request, $do_customer) {
            // store do customer data
            $do_customer->update($input);

            // delete do customer item product
            $do_customer->item_product()->delete();

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\DOCustomer';
                $item_product['reference_id'] = $do_customer->id;
                SelectItemProduct::create($item_product);
            }

            return $do_customer;
        });

        return ResponseFormatter::success(
            new DOCustomerDetailResource($result),
            'success update do customer data'
        );
    }

    public function update_status(Request $request, DOCustomer $do_customer)
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
            $input['approved_date'] = NULL;
        } else {
            $input['note'] = NULL;
        }
        // return $input;
        $do_customer->update($input);

        return ResponseFormatter::success(
            new DOCustomerDetailResource($do_customer),
            'success update status do customer data'
        );
    }

    public function update_approval_status(Request $request, DOCustomer $do_customer)
    {
        $request->validate([
            'status' => ['required', 'in:approved']
        ]);

        DB::transaction(function () use ($do_customer) {
            $do_customer->update([
                'approved_date' => Carbon::now()
            ]);
            $do_customer->update([ 'status' => 'finish' ]);
        });

        return ResponseFormatter::success(
            new DOCustomerDetailResource($do_customer),
            'success update approval status do customer data'
        );
    }

    public function destroy(DOCustomer $do_customer)
    {
        DB::transaction(function () use ($do_customer) {
            // delete attachment file
            $files = $do_customer->attachment_file()->pluck('file')->toArray();
            Storage::disk('local')->delete($files);    
            $do_customer->attachment_file()->delete();

            // delete item product
            $do_customer->item_product()->delete();

            // delete catering po
            $do_customer->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete do customer data'
        );
    }

    public function last_number()
    {
         $last_number = DOCustomer::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
