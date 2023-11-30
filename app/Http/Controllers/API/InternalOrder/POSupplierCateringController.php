<?php

namespace App\Http\Controllers\API\InternalOrder;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\InternalOrder\POSupplierCateringRequest;
use App\Http\Resources\InternalOrder\POSupplierCatering\POSupplierCateringDetailResource;
use App\Http\Resources\InternalOrder\POSupplierCatering\POSupplierCateringResource;
use App\Models\POSupplierCatering;
use App\Models\SelectItemProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class POSupplierCateringController extends Controller
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

        $po_supplier_catering = POSupplierCatering::when($search, function ($query, string $search) {
                                            $query->where('po_number', 'like', '%'.$search.'%');
                                        })
                                        ->when($status, function ($query, array $status) {
                                            $query->whereIn('status', $status);
                                        })
                                        ->orderBy('created_at', 'DESC');

        $result = $paginate ? $po_supplier_catering->paginate($limit) : $po_supplier_catering->get();

        return ResponseFormatter::success(
            POSupplierCateringResource::collection($result)->response()->getData(true),
            'success get po supplier catering data'
        );
    }

    public function store(POSupplierCateringRequest $request) 
    {
        $input = $request->except([
            'item_product'
        ]);
        
        $last_number = $this->last_number();
        $input['created_by'] = Auth::user()->id;
        $input['serial_number'] = $last_number;
        $input['po_number'] = $last_number .'/SBL/POSC/' . DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;
        $input['status'] = 'draft';

        // database transaction for po supplier catering and item data
        $result = DB::transaction(function () use ($input, $request) {
            // store po supplier catering data
            $po_supplier_catering = POSupplierCatering::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\POSupplierCatering';
                $item_product['reference_id'] = $po_supplier_catering->id;
                SelectItemProduct::create($item_product);
            }

            return $po_supplier_catering;
        });

        return ResponseFormatter::success(
            new POSupplierCateringDetailResource($result),
            'success create po supplier catering data'
        );
    }    

    public function show(POSupplierCatering $po_supplier_catering)
    {
        return ResponseFormatter::success(
            new POSupplierCateringDetailResource($po_supplier_catering),
            'success show po supplier catering detail data'
        );
    }    

    public function update(POSupplierCateringRequest $request, POSupplierCatering $po_supplier_catering)
    {
        $input = $request->except([
            'item_product'
        ]);

        // database transaction for po supplier catering and item data
        $result = DB::transaction(function () use ($input, $request, $po_supplier_catering) {
            // store po supplier catering data
            $po_supplier_catering->update($input);

            // delete po supplier catering item product
            $po_supplier_catering->item_product()->delete();

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\POSupplierCatering';
                $item_product['reference_id'] = $po_supplier_catering->id;
                SelectItemProduct::create($item_product);
            }

            return $po_supplier_catering;
        });

        return ResponseFormatter::success(
            new POSupplierCateringDetailResource($result),
            'success update po supplier catering data'
        );
    }

    public function update_status(Request $request, POSupplierCatering $po_supplier_catering)
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
        $po_supplier_catering->update($input);

        return ResponseFormatter::success(
            new POSupplierCateringDetailResource($po_supplier_catering),
            'success update status po supplier catering data'
        );
    }

    public function update_approval_status(Request $request, POSupplierCatering $po_supplier_catering)
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
            $update = $po_supplier_catering->status == 'submit' ? true : false;
        } else if (in_array($status, ['approved1','approved2'])) {
            $update = !empty($po_supplier_catering->checked_date) ? true : false;
        }

        if($update) {

            DB::transaction(function () use ($po_supplier_catering, $data, $status) {
                $po_supplier_catering->update([
                    $data[$status] => Carbon::now()
                ]);

                if(
                    !empty($po_supplier_catering->checked_date) && 
                    !empty($po_supplier_catering->approved1_date) && 
                    !empty($po_supplier_catering->approved2_date)
                ) {
                    $po_supplier_catering->update([ 'status' => 'finish' ]);
                }
            });

            return ResponseFormatter::success(
                new POSupplierCateringDetailResource($po_supplier_catering),
                'success update approval status po supplier catering data'
            );
        } else {
            return ResponseFormatter::errorValidation([
                'po_catering_id' => 'Cannot update this po supplier catering because the status does not match'
            ], 'update status po supplier catering failed', 422);
        }
    }

    public function destroy(POSupplierCatering $po_supplier_catering)
    {
        DB::transaction(function () use ($po_supplier_catering) {
            // delete attachment file
            $files = $po_supplier_catering->attachment_file()->pluck('file')->toArray();
            Storage::disk('local')->delete($files);    
            $po_supplier_catering->attachment_file()->delete();

            // delete item product
            $po_supplier_catering->item_product()->delete();

            // delete catering po
            $po_supplier_catering->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete po supplier catering data'
        );
    }

    public function last_number()
    {
         $last_number = POSupplierCatering::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
