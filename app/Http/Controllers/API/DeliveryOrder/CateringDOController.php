<?php

namespace App\Http\Controllers\API\DeliveryOrder;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryOrder\CateringDORequest;
use App\Http\Resources\DeliveryOrder\CateringDO\CateringDODetailResource;
use App\Http\Resources\DeliveryOrder\CateringDO\CateringDOResource;
use App\Models\CateringDo;
use App\Models\Location;
use App\Models\SelectItemProduct;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CateringDOController extends Controller
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

        $catering_do = CateringDo::when($search, function ($query, string $search) {
                                            $query->where('do_number', 'like', '%'.$search.'%');
                                        })
                                        ->when($status, function ($query, array $status) {
                                            $query->whereIn('status', $status);
                                        })
                                        ->orderBy('created_at', 'DESC');

        $result = $paginate ? $catering_do->paginate($limit) : $catering_do->get();

        return ResponseFormatter::success(
            CateringDOResource::collection($result)->response()->getData(true),
            'success get catering do data'
        );
    }

    public function store(CateringDORequest $request) {
        $input = $request->except([
            'item_product'
        ]);

        $last_number = $this->last_number();
        $supplier = Supplier::find($request->supplier_id);
        $location = Location::find($request->location_id);
        $input['serial_number'] = $last_number;
        $input['do_number'] = $last_number .'/SBL/'. $supplier->code .'/'. $location->location_code .'/'. DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;
        $input['status'] = 'draft';

        // database transaction for catering do and item data
        $result = DB::transaction(function () use ($input, $request) {
            // store catering do data
            $catering_do = CateringDo::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\CateringDo';
                $item_product['reference_id'] = $catering_do->id;
                SelectItemProduct::create($item_product);
            }

            return $catering_do;
        });

        return ResponseFormatter::success(
            new CateringDODetailResource($result),
            'success create catering do request data'
        );
    }

    public function show(CateringDo $catering_do)
    {
        return ResponseFormatter::success(
            new CateringDODetailResource($catering_do),
            'success show catering do detail data'
        );
    }

    public function update(CateringDORequest $request, CateringDo $catering_do)
    {
        $input = $request->except([
            'item_product'
        ]);

        // database transaction for catering do and item data
        $result = DB::transaction(function () use ($input, $request, $catering_do) {
            // store catering do data
            $catering_do->update($input);

            // delete catering do item product
            $catering_do->item_product()->delete();

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\CateringDo';
                $item_product['reference_id'] = $catering_do->id;
                SelectItemProduct::create($item_product);
            }

            return $catering_do;
        });

        return ResponseFormatter::success(
            new CateringDODetailResource($result),
            'success update catering do data'
        );
    }

    public function update_status(Request $request, CateringDo $catering_do)
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
        $catering_do->update($input);

        return ResponseFormatter::success(
            new CateringDODetailResource($catering_do),
            'success update status catering do data'
        );
    }

    public function update_approval_status(Request $request, CateringDo $catering_do)
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
            $update = $catering_do->status == 'submit' ? true : false;
        } else if (in_array($status, ['approved1','approved2'])) {
            $update = !empty($catering_do->checked_date) ? true : false;
        }

        if($update) {

            DB::transaction(function () use ($catering_do, $data, $status) {
                $catering_do->update([
                    $data[$status] => Carbon::now()
                ]);

                if(
                    !empty($catering_do->checked_date) && 
                    !empty($catering_do->approved1_date) && 
                    !empty($catering_do->approved2_date)
                ) {
                    $catering_do->update([ 'status' => 'finish' ]);
                }
            });

            return ResponseFormatter::success(
                new CateringDODetailResource($catering_do),
                'success update approval status catering do data'
            );
        } else {
            return ResponseFormatter::errorValidation([
                'catering_do_id' => 'Cannot update this catering do because the status does not match'
            ], 'update status catering do failed', 422);
        }
    }

    public function destroy(CateringDo $catering_do)
    {
        DB::transaction(function () use ($catering_do) {
            // delete attachment file
            $files = $catering_do->attachment_file()->pluck('file')->toArray();
            Storage::disk('local')->delete($files);    
            $catering_do->attachment_file()->delete();

            // delete item product
            $catering_do->item_product()->delete();

            // delete catering do
            $catering_do->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete catering do data'
        );
    }

    public function last_number()
    {
         $last_number = CateringDo::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
