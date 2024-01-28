<?php

namespace App\Http\Controllers\API\InternalOrder;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\InternalOrder\POSupplierCatering\POSupplierCateringRequest;
use App\Http\Requests\InternalOrder\POSupplierCatering\POSupplierCateringUpdateRequest;
use App\Http\Resources\InternalOrder\POSupplierCatering\POSupplierCateringDetailResource;
use App\Http\Resources\InternalOrder\POSupplierCatering\POSupplierCateringResource;
use App\Models\DOCatering;
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
            'status.*' => ['nullable', 'in:draft,submit']
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
        
        // create variable for po supllier caterint
        $last_number = $this->last_number();
        $input['created_by'] = Auth::user()->id;
        $input['serial_number'] = $last_number;
        $input['po_number'] = $last_number .'/SBL/POSC/' . DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;

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

            // store do catering
            $this->store_do_catering($po_supplier_catering, $request);

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

    public function update(POSupplierCateringUpdateRequest $request, POSupplierCatering $po_supplier_catering)
    {
        $input = $request->except([
            'item_product'
        ]);
        $hard_edit = $request->hard_edit;

        if ($po_supplier_catering->status == 'submit' && $hard_edit == 'no') {
            return ResponseFormatter::errorValidation([
                'po_supplier_catering_id' => ['cannot update this data because the status has already been submitted']
            ], 'update po supplier cateirng data failed', 422);
        }

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

            // update do catering data
            $this->update_do_catering($po_supplier_catering, $request);

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
            'status' => ['required', 'in:draft,submit'],
        ]);

        $input = $request->only('status');
        $po_supplier_catering->update($input);

        return ResponseFormatter::success(
            new POSupplierCateringDetailResource($po_supplier_catering),
            'success update status po supplier catering data'
        );
    }

    public function destroy(POSupplierCatering $po_supplier_catering)
    {
        // if ($po_supplier_catering->status == 'submit') {
        //     return ResponseFormatter::errorValidation([
        //         'po_supplier_catering_id' => ['cannot update this data because the status has already been submitted']
        //     ], 'update po supplier cateirng data failed', 422);
        // }
        
        DB::transaction(function () use ($po_supplier_catering) {

            // delete do catering data 
                // delete attachament file do catering 
                $files = $po_supplier_catering->do_catering->attachment_file()->pluck('file')->toArray();
                Storage::disk('local')->delete($files);
                $po_supplier_catering->do_catering->attachment_file()->delete();

                // delete item product do catering
                $po_supplier_catering->do_catering->item_product()->delete();

                // delete do catering
                $po_supplier_catering->do_catering()->delete();
            // end delete do catering data

            // delete po supploer catering data 
                // delete attachment file
                $files = $po_supplier_catering->attachment_file()->pluck('file')->toArray();
                Storage::disk('local')->delete($files);    
                $po_supplier_catering->attachment_file()->delete();

                // delete item product
                $po_supplier_catering->item_product()->delete();

                // delete catering po
                $po_supplier_catering->delete();
            // end delete po supploer catering data 
        });

        return ResponseFormatter::success(
            null,
            'success delete po supplier catering data'
        );
    }

    public function store_do_catering(POSupplierCatering $po_supplier_catering, $request)
    {
        // create variable for do catering
        $last_number_do_catering = $this->last_number_do_catering();
        $input_do_catering['created_by'] = Auth::user()->id;
        $input_do_catering['serial_number'] = $last_number_do_catering;
        $input_do_catering['do_number'] = $last_number_do_catering .'/SBL/DOC/' . DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;
        $input_do_catering['status'] = 'draft';

        // store do catering data
        $do_catering = $po_supplier_catering->do_catering()->create($input_do_catering);

        // store item product data
        foreach($request->item_product as $item_product) {
            $item_product['reference_type'] = 'App\Models\DOCatering';
            $item_product['reference_id'] = $do_catering->id;
            SelectItemProduct::create($item_product);
        }
    }


    public function update_do_catering(POSupplierCatering $po_supplier_catering, $request)
    {
        $do_catering = $po_supplier_catering->do_catering;
        // delete do catering item product
        $do_catering->item_product()->delete();

        // store item product data
        foreach($request->item_product as $item_product) {
            $item_product['reference_type'] = 'App\Models\DOCatering';
            $item_product['reference_id'] = $do_catering->id;
            SelectItemProduct::create($item_product);
        }
    }

    public function last_number()
    {
         $last_number = POSupplierCatering::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }

    public function last_number_do_catering()
    {
         $last_number = DOCatering::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
