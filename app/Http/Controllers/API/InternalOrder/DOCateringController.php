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
        ]);
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);

        $do_catering = DOCatering::when($search, function ($query, string $search) {
                                            $query->where('do_number', 'like', '%'.$search.'%');
                                        })
                                        ->orderBy('created_at', 'DESC');

        $result = $paginate ? $do_catering->paginate($limit) : $do_catering->get();

        return ResponseFormatter::success(
            DOCateringResource::collection($result)->response()->getData(true),
            'success get do catering data'
        );
    }

    public function store(DOCateringRequest $request) 
    {
        $input = $request->except([
            'item_product'
        ]);
        
        $last_number = $this->last_number();
        $input['created_by'] = Auth::user()->id;
        $input['serial_number'] = $last_number;
        $input['do_number'] = $last_number .'/SBL/DOC/' . DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;

        // database transaction for do catering and item data
        $result = DB::transaction(function () use ($input, $request) {
            // store do catering data
            $do_catering = DOCatering::create($input);

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
            'success create do catering data'
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
        $input = $request->except([
            'item_product'
        ]);

        // database transaction for do catering and item data
        $result = DB::transaction(function () use ($input, $request, $do_catering) {
            // store do catering data
            $do_catering->update($input);

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

    public function destroy(DOCatering $do_catering)
    {
        DB::transaction(function () use ($do_catering) {
            // delete attachment file
            $files = $do_catering->attachment_file()->pluck('file')->toArray();
            Storage::disk('local')->delete($files);    
            $do_catering->attachment_file()->delete();

            // delete item product
            $do_catering->item_product()->delete();

            // delete catering po
            $do_catering->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete do catering data'
        );
    }

    public function last_number()
    {
         $last_number = DOCatering::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
