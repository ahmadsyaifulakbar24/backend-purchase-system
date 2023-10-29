<?php

namespace App\Http\Controllers\API\PurchaseOrder;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrder\CateringPORequest;
use App\Http\Resources\PurchaseOrder\CateringPO\CateringPODetailResource;
use App\Http\Resources\PurchaseOrder\CateringPO\CateringPOResource;
use App\Models\CateringPo;
use App\Models\Location;
use App\Models\SelectItemProduct;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CateringPOController extends Controller
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

        $catering_po = CateringPo::when($search, function ($query, string $search) {
                                            $query->where('po_number', 'like', '%'.$search.'%');
                                        })
                                        ->when($status, function ($query, array $status) {
                                            $query->whereIn('status', $status);
                                        })
                                        ->orderBy('created_at', 'DESC');

        $result = $paginate ? $catering_po->paginate($limit) : $catering_po->get();

        return ResponseFormatter::success(
            CateringPOResource::collection($result)->response()->getData(true),
            'success get catering po data'
        );
    }

    public function store(CateringPORequest $request) {
        $input = $request->except([
            'item_product'
        ]);

        $last_number = $this->last_number();
        $supplier = Supplier::find($request->supplier_id);
        $location = Location::find($request->location_id);
        $input['serial_number'] = $last_number;
        $input['po_number'] = $last_number .'/SBL/'. $supplier->code .'/'. $location->location_code .'/'. DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;
        $input['status'] = 'draft';

        // database transaction for catering po and item data
        $result = DB::transaction(function () use ($input, $request) {
            // store catering po data
            $catering_po = CateringPo::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\CateringPo';
                $item_product['reference_id'] = $catering_po->id;
                SelectItemProduct::create($item_product);
            }

            return $catering_po;
        });

        return ResponseFormatter::success(
            new CateringPODetailResource($result),
            'success create catering po request data'
        );
    }

    public function show(CateringPo $catering_po)
    {
        return ResponseFormatter::success(
            new CateringPODetailResource($catering_po),
            'success show catering po detail data'
        );
    }

    public function update(CateringPORequest $request, CateringPo $catering_po)
    {
        $input = $request->except([
            'item_product'
        ]);

        // database transaction for catering po and item data
        $result = DB::transaction(function () use ($input, $request, $catering_po) {
            // store catering po data
            $catering_po->update($input);

            // delete catering po item product
            $catering_po->item_product()->delete();

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\CateringPo';
                $item_product['reference_id'] = $catering_po->id;
                SelectItemProduct::create($item_product);
            }

            return $catering_po;
        });

        return ResponseFormatter::success(
            new CateringPODetailResource($result),
            'success update catering po data'
        );
    }

    public function update_status(Request $request, CateringPo $catering_po)
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
        $catering_po->update($input);

        return ResponseFormatter::success(
            new CateringPODetailResource($catering_po),
            'success update status catering po data'
        );
    }

    public function update_approval_status(Request $request, CateringPo $catering_po)
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
            $update = $catering_po->status == 'submit' ? true : false;
        } else if (in_array($status, ['approved1','approved2'])) {
            $update = !empty($catering_po->checked_date) ? true : false;
        }

        if($update) {

            DB::transaction(function () use ($catering_po, $data, $status) {
                $catering_po->update([
                    $data[$status] => Carbon::now()
                ]);

                if(
                    !empty($catering_po->checked_date) && 
                    !empty($catering_po->approved1_date) && 
                    !empty($catering_po->approved2_date)
                ) {
                    $catering_po->update([ 'status' => 'finish' ]);
                }
            });

            return ResponseFormatter::success(
                new CateringPODetailResource($catering_po),
                'success update approval status catering po data'
            );
        } else {
            return ResponseFormatter::errorValidation([
                'catering_po_id' => 'Cannot update this catering po because the status does not match'
            ], 'update status catering po failed', 422);
        }
    }

    public function destroy(CateringPo $catering_po)
    {
        DB::transaction(function () use ($catering_po) {
            // delete attachment file
            $files = $catering_po->attachment_file()->pluck('file')->toArray();
            Storage::disk('local')->delete($files);    
            $catering_po->attachment_file()->delete();

            // delete item product
            $catering_po->item_product()->delete();

            // delete catering po
            $catering_po->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete catering po data'
        );
    }

    public function last_number()
    {
         $last_number = CateringPo::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
