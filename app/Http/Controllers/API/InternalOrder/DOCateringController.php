<?php

namespace App\Http\Controllers\API\InternalOrder;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\InternalOrder\DOCateringRequest;
use App\Http\Resources\InternalOrder\DOCatering\DOCateringDetailResource;
use App\Http\Resources\InternalOrder\DOCatering\DOCateringResource;
use App\Models\DOCatering;
use App\Models\SelectItemProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DOCateringController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1'],
            'status' => ['nullable', 'in:draft,submit'],
        ]);
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);
        $status = $request->status;

        $do_catering = DOCatering::when($search, function ($query, string $search) {
                                    $query->where(function ($query2) use ($search) {
                                        $query2->where('do_number', 'like', '%'.$search.'%')
                                        ->orWhereHas('po_supplier_catering', function ($sub_query) use ($search) {
                                            $sub_query->where('po_number', 'like', '%'.$search.'%');
                                        });
                                    });
                                })
                                ->when($status, function ($query, string $status) {
                                    $query->where('status', $status);
                                })
                                ->orderBy('created_at', 'DESC');

        $result = $paginate ? $do_catering->paginate($limit) : $do_catering->get();

        return ResponseFormatter::success(
            DOCateringResource::collection($result)->response()->getData(true),
            'success get do catering data'
        );
    }  

    public function show(DOCatering $do_catering)
    {
        return ResponseFormatter::success(
            new DOCateringDetailResource($do_catering),
            'success show do catering detail data'
        );
    }    

    public function update(DOCateringRequest $request, DOCatering $do_catering)
    {
        if($do_catering->status == 'submit')
        {
            return ResponseFormatter::errorValidation([
                'do_catering_id' => ['cannot update this data because the status has already been submitted']
            ], 'update do cateirng data failed', 422);
        }

        // database transaction for do catering and item data
        $result = DB::transaction(function () use ($request, $do_catering) {
            // delete do catering item product
            $do_catering->item_product()->delete();

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\DOCatering';
                $item_product['reference_id'] = $do_catering->id;
                SelectItemProduct::create($item_product);
            }

            return $do_catering;
        });

        return ResponseFormatter::success(
            new DOCateringDetailResource($result),
            'success update do catering data'
        );
    }

    public function update_status(Request $request, DOCatering $do_catering)
    {
        $request->validate([
            'status' => ['required', 'in:draft,submit'],
        ]);

        $input = $request->only('status');
        $do_catering->update($input);
        
        return ResponseFormatter::success(
            new DOCateringDetailResource($do_catering),
            'success update status do catering data'
        );
    }

}
