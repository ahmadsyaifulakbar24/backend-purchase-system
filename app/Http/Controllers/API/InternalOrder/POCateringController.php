<?php

namespace App\Http\Controllers\API\InternalOrder;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\InternalOrder\POCateringRequest;
use App\Http\Resources\InternalOrder\POCatering\POCateringDetailResource;
use App\Http\Resources\InternalOrder\POCatering\POCateringResource;
use App\Models\POCatering;
use App\Models\SelectItemProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class POCateringController extends Controller
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

        $po_catering = POCatering::when($search, function ($query, string $search) {
                                            $query->where('po_number', 'like', '%'.$search.'%');
                                        })
                                        ->when($status, function ($query, array $status) {
                                            $query->whereIn('status', $status);
                                        })
                                        ->orderBy('created_at', 'DESC');

        $result = $paginate ? $po_catering->paginate($limit) : $po_catering->get();

        return ResponseFormatter::success(
            POCateringResource::collection($result)->response()->getData(true),
            'success get po catering data'
        );
    }

    public function store(POCateringRequest $request) 
    {
        $input = $request->except([
            'item_product'
        ]);

        $last_number = $this->last_number();
        $input['serial_number'] = $last_number;
        $input['po_number'] = $last_number .'/SBL/PRC/' . DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;
        $input['status'] = 'draft';

        // database transaction for po catering and item data
        $result = DB::transaction(function () use ($input, $request) {
            // store po catering data
            $po_catering = POCatering::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\POCatering';
                $item_product['reference_id'] = $po_catering->id;
                SelectItemProduct::create($item_product);
            }

            return $po_catering;
        });

        return ResponseFormatter::success(
            new POCateringDetailResource($result),
            'success create po catering data'
        );
    }

    public function show(POCatering $po_catering)
    {
        return ResponseFormatter::success(
            new POCateringDetailResource($po_catering),
            'success show po catering detail data'
        );
    }    

    public function update(POCateringRequest $request, POCatering $po_catering)
    {
        $input = $request->except([
            'item_product'
        ]);

        // database transaction for po catering and item data
        $result = DB::transaction(function () use ($input, $request, $po_catering) {
            // store po catering data
            $po_catering->update($input);

            // delete po catering item product
            $po_catering->item_product()->delete();

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\POCatering';
                $item_product['reference_id'] = $po_catering->id;
                SelectItemProduct::create($item_product);
            }

            return $po_catering;
        });

        return ResponseFormatter::success(
            new POCateringDetailResource($result),
            'success update po catering data'
        );
    }

    public function update_status(Request $request, POCatering $po_catering)
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
        $po_catering->update($input);

        return ResponseFormatter::success(
            new POCateringDetailResource($po_catering),
            'success update status po catering data'
        );
    }

    public function update_approval_status(Request $request, POCatering $po_catering)
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
            $update = $po_catering->status == 'submit' ? true : false;
        } else if (in_array($status, ['approved1','approved2'])) {
            $update = !empty($po_catering->checked_date) ? true : false;
        }

        if($update) {

            DB::transaction(function () use ($po_catering, $data, $status) {
                $po_catering->update([
                    $data[$status] => Carbon::now()
                ]);

                if(
                    !empty($po_catering->checked_date) && 
                    !empty($po_catering->approved1_date) && 
                    !empty($po_catering->approved2_date)
                ) {
                    $po_catering->update([ 'status' => 'finish' ]);
                }
            });

            return ResponseFormatter::success(
                new POCateringDetailResource($po_catering),
                'success update approval status po catering data'
            );
        } else {
            return ResponseFormatter::errorValidation([
                'po_catering_id' => 'Cannot update this po catering because the status does not match'
            ], 'update status po catering failed', 422);
        }
    }

    public function destroy(POCatering $po_catering)
    {
        DB::transaction(function () use ($po_catering) {
            // delete attachment file
            $files = $po_catering->attachment_file()->pluck('file')->toArray();
            Storage::disk('local')->delete($files);    
            $po_catering->attachment_file()->delete();

            // delete item product
            $po_catering->item_product()->delete();

            // delete catering po
            $po_catering->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete po catering data'
        );
    }

    public function last_number()
    {
         $last_number = POCatering::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
