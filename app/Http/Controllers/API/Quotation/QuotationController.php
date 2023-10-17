<?php

namespace App\Http\Controllers\API\Quotation;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Quotation\QuotationRequest;
use App\Http\Resources\Quotation\QuotationDetailResource;
use App\Http\Resources\Quotation\QuotationResource;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\SelectItemProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
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

        $quotation = Quotation::when($search, function ($query, string $search) {
                                    $query->where('quotation_number', $search);
                                })
                                ->orderBy('created_at', 'DESC');

        $result = $paginate ? $quotation->paginate($limit) : $quotation->get();

        return ResponseFormatter::success(
            QuotationResource::collection($result)->response()->getData(true),
            'success get quotation data'
        );
    }

    public function store(QuotationRequest $request)
    {
        $input = $request->except([
            'item_product'
        ]);
        $last_number = $this->last_number();
        $customer = Customer::find($request->customer_id);
        $input['serial_number'] = $last_number;
        $input['quotation_number'] = $last_number .'/SBL/Q/'. $customer->code .'/'. DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;

        // database transaction for quotation and item
        $result = DB::transaction(function () use ($input, $request) {
            // store quotation data
            $quotation = Quotation::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App/Models/Quotation';
                $item_product['reference_id'] = $quotation->id;
                SelectItemProduct::create($item_product);
            }

            return $quotation;
        });

        return ResponseFormatter::success(
            new QuotationDetailResource($result),
            'success create quotation data'
        );
    }

    public function show(Quotation $quotation)
    {
        return ResponseFormatter::success(
            new QuotationDetailResource($quotation),
            'success show quotation detail data'
        );
    }

    public function update(QuotationRequest $request, Quotation $quotation)
    {
        $input = $request->except([
            'item_product'
        ]);

        // database transaction for quotation and item
        $result = DB::transaction(function () use ($input, $request, $quotation) {
            // store quotation data
            $quotation->update($input);

            // delete quotation item product
            $quotation->item_product()->delete();
            
            // store item product data
            foreach($request->item_product as $item_product) {
                // store quotation item product
                $item_product['reference_type'] = 'App/Models/Quotation';
                $item_product['reference_id'] = $quotation->id;
                SelectItemProduct::create($item_product);
            }

            return $quotation;
        });

        return ResponseFormatter::success(
            new QuotationDetailResource($result),
            'success create quotation data'
        );
    }

    public function update_status(Request $request, Quotation $quotation)
    {
        $request->validate([
            'status' => ['required', 'in:checked,approved']
        ]);
        
        $data = [
            'checked' => 'checked_date',
            'approved' => 'approved_date',
        ];

        $quotation->update([
            $data[$request->status] => Carbon::now()
        ]);

        return ResponseFormatter::success(
            new QuotationDetailResource($quotation),
            'success update quotation data'
        );
    }

    public function destroy(Quotation $quotation)
    {
        DB::transaction(function () use ($quotation) {
            $quotation->item_product()->delete();
            $quotation->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete quotation data'
        );
    }

    public function last_number()
    {
         $last_number = Quotation::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
