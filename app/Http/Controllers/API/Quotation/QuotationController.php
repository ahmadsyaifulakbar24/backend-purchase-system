<?php

namespace App\Http\Controllers\API\Quotation;

use App\Helpers\DateHelpers;
use App\Helpers\ExcelHelper;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Quotation\QuotationRequest;
use App\Http\Resources\Quotation\QuotationDetailResource;
use App\Http\Resources\Quotation\QuotationResource;
use App\Imports\Quotation\QuotationProductImport;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\SelectItemProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class QuotationController extends Controller
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

        $quotation = Quotation::when($search, function ($query, string $search) {
                                    $query->where('quotation_number', 'like', '%'.$search.'%');
                                })
                                ->when($status, function ($query, array $status) {
                                    $query->whereIn('status', $status);
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
        $input['status'] = 'draft';

        // database transaction for quotation and item
        $result = DB::transaction(function () use ($input, $request) {
            // store quotation data
            $quotation = Quotation::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\Quotation';
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
                $item_product['reference_type'] = 'App\Models\Quotation';
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
        $quotation->update($input);

        return ResponseFormatter::success(
            new QuotationDetailResource($quotation),
            'success update status quotation data'
        );
    }

    public function update_approval_status(Request $request, Quotation $quotation)
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
            $update = $quotation->status == 'submit' ? true : false;
        } else if (in_array($status, ['approved1','approved2'])) {
            $update = !empty($quotation->checked_date) ? true : false;
        }

        if($update) {

            DB::transaction(function () use ($quotation, $data, $status) {
                $quotation->update([
                    $data[$status] => Carbon::now()
                ]);

                if(
                    !empty($quotation->checked_date) && 
                    !empty($quotation->approved1_date) && 
                    !empty($quotation->approved2_date)
                ) {
                    $quotation->update([ 'status' => 'finish' ]);
                }
            });

            return ResponseFormatter::success(
                new QuotationDetailResource($quotation),
                'success update approval status quotation data'
            );
        } else {
            return ResponseFormatter::errorValidation([
                'quotation_id' => 'Cannot update this quote because the status does not match'
            ], 'update status quotation failed', 422);
        }
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

    public function read_excel(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx'],
        ]);
        $file = $request->file;

        return ExcelHelper::read($file);
    }

    public function last_number()
    {
         $last_number = Quotation::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
