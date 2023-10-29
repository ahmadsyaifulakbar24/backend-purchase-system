<?php

namespace App\Http\Controllers\API\PurchaseOrder;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrder\OutgoingPORequest;
use App\Http\Resources\PurchaseOrder\OutgoingPO\OutgoingPODetailResource;
use App\Http\Resources\PurchaseOrder\OutgoingPO\OutgoingPOResource;
use App\Models\Location;
use App\Models\OutgoingPo;
use App\Models\SelectItemProduct;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OutgoingPOContoller extends Controller
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

        $outgoing_po = OutgoingPo::when($search, function ($query, string $search) {
                                            $query->where('po_number', 'like', '%'.$search.'%');
                                        })
                                        ->when($status, function ($query, array $status) {
                                            $query->whereIn('status', $status);
                                        })
                                        ->orderBy('created_at', 'DESC');

        $result = $paginate ? $outgoing_po->paginate($limit) : $outgoing_po->get();

        return ResponseFormatter::success(
            OutgoingPOResource::collection($result)->response()->getData(true),
            'success get outgoing po data'
        );
    }

    public function store(OutgoingPORequest $request)
    {
        $input = $request->safe()->except([
            'item_product'
        ]);

        $last_number = $this->last_number();
        $supplier = Supplier::find($request->supplier_id);
        $location = Location::find($request->location_id);
        $input['serial_number'] = $last_number;
        $input['po_number'] = $last_number .'/SBL/'. $supplier->code .'/'. $location->location_code .'/'. DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;
        $input['status'] = 'draft';

        // database transaction for outgoing po and item data
        $result = DB::transaction(function () use ($input, $request) {
            // store outgoing po data
            $outgoing_po = OutgoingPo::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\OutgoingPo';
                $item_product['reference_id'] = $outgoing_po->id;
                SelectItemProduct::create($item_product);
            }

            return $outgoing_po;
        });

        return ResponseFormatter::success(
            new OutgoingPODetailResource($result),
            'success create outgoing po data'
        );
    }

    public function show(OutgoingPo $outgoing_po)
    {  
        return ResponseFormatter::success(
            new OutgoingPODetailResource($outgoing_po),
            'success show outgoing po detail data'
        );
    }

    public function update(OutgoingPORequest $request, OutgoingPo $outgoing_po)
    {
        $input = $request->except([
            'item_product'
        ]);

        // database transaction for outgoing po and item data
        $result = DB::transaction(function () use ($input, $request, $outgoing_po) {
            // update outgoing po data
            $outgoing_po->update($input);

            // delete outgoing po item product
            $outgoing_po->item_product()->delete();

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\OutgoingPO';
                $item_product['reference_id'] = $outgoing_po->id;
                SelectItemProduct::create($item_product);
            }

            return $outgoing_po;
        });

        return ResponseFormatter::success(
            new OutgoingPODetailResource($result),
            'success update outgoing po data'
        );
    }

    public function update_status(Request $request, OutgoingPo $outgoing_po)
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
        $outgoing_po->update($input);

        return ResponseFormatter::success(
            new OutgoingPODetailResource($outgoing_po),
            'success update status outgoing po data'
        );
    }

    public function update_approval_status(Request $request, OutgoingPo $outgoing_po)
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
            $update = $outgoing_po->status == 'submit' ? true : false;
        } else if (in_array($status, ['approved1','approved2'])) {
            $update = !empty($outgoing_po->checked_date) ? true : false;
        }

        if($update) {

            DB::transaction(function () use ($outgoing_po, $data, $status) {
                $outgoing_po->update([
                    $data[$status] => Carbon::now()
                ]);

                if(
                    !empty($outgoing_po->checked_date) && 
                    !empty($outgoing_po->approved1_date) && 
                    !empty($outgoing_po->approved2_date)
                ) {
                    $outgoing_po->update([ 'status' => 'finish' ]);
                }
            });

            return ResponseFormatter::success(
                new OutgoingPODetailResource($outgoing_po),
                'success update approval status outgoing po data'
            );
        } else {
            return ResponseFormatter::errorValidation([
                'outgoing_po_id' => 'Cannot update this outgoing po because the status does not match'
            ], 'update status outgoing po failed', 422);
        }
    }

    public function destroy(OutgoingPo $outgoing_po)
    {
        DB::transaction(function () use ($outgoing_po) {
            // delete attachment file
            $files = $outgoing_po->attachment_file()->pluck('file')->toArray();
            Storage::disk('local')->delete($files);    
            $outgoing_po->attachment_file()->delete();

            // delete item product
            $outgoing_po->item_product()->delete();

            // delete outgoing po
            $outgoing_po->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete outgoing po data'
        );
    }

    public function last_number()
    {
         $last_number = OutgoingPo::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }


}
