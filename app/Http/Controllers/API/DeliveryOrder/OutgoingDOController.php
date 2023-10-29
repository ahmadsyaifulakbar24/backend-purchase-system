<?php

namespace App\Http\Controllers\API\DeliveryOrder;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryOrder\OutgoingDORequest;
use App\Http\Resources\DeliveryOrder\OutgoingDO\OutgoingDODetailResource;
use App\Http\Resources\DeliveryOrder\OutgoingDO\OutgoingDOResource;
use App\Models\Customer;
use App\Models\Location;
use App\Models\OutgoingDo;
use App\Models\SelectItemProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutgoingDOController extends Controller
{
    public function get (Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1'],
        ]);
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);

        $outgoing_do = OutgoingDo::when($search, function ($query, string $search) {
                                            $query->where('do_number', 'like', '%'.$search.'%');
                                        })
                                        ->orderBy('created_at', 'DESC');

        $result = $paginate ? $outgoing_do->paginate($limit) : $outgoing_do->get();

        return ResponseFormatter::success(
            OutgoingDOResource::collection($result)->response()->getData(true),
            'success get outgoing do data'
        );
    }

    public function store(OutgoingDORequest $request)
    {
        $input = $request->safe()->except([
            'item_product'
        ]);
        $last_number = $this->last_number();
        $customer = Customer::find($request->customer_id);
        $location = Location::find($request->location_id);
        $input['serial_number'] = $last_number;
        $input['do_number'] = $last_number .'/SBL/'. $customer->code .'/'. $location->location_code .'/'. DateHelpers::monthToRoman(Carbon::now()->month) .'/'. Carbon::now()->year;

        // database transaction for outgoing do and item data
        $result = DB::transaction(function () use ($input, $request) {
            // store outgoing do data
            $outgoing_do = OutgoingDo::create($input);

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\OutgoingDo';
                $item_product['reference_id'] = $outgoing_do->id;
                SelectItemProduct::create($item_product);
            }

            return $outgoing_do;
        });

        return ResponseFormatter::success(
            new OutgoingDODetailResource($result),
            'success create outgoing do data'
        );
    }

    public function show(OutgoingDo $outgoing_do)
    {
        return ResponseFormatter::success(
            new OutgoingDODetailResource($outgoing_do),
            'success show outgoing do detail data'
        );
    }

    public function update(OutgoingDORequest $request, OutgoingDo $outgoing_do)
    {
        $input = $request->safe()->except([
            'item_product'
        ]);

        // database transaction for outgoing do and item data
        $result = DB::transaction(function () use ($input, $request, $outgoing_do) {
            // update outgoing do data
            $outgoing_do->update($input);

            // delete outgoing d item product
            $outgoing_do->item_product()->delete();

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\OutgoingDo';
                $item_product['reference_id'] = $outgoing_do->id;
                SelectItemProduct::create($item_product);
            }

            return $outgoing_do;
        });

        return ResponseFormatter::success(
            new OutgoingDODetailResource($result),
            'success update outgoing do data'
        );
    }

    public function destroy(OutgoingDo $outgoing_do)
    {
        DB::transaction(function () use ($outgoing_do) {
            // delete item product
            $outgoing_do->item_product()->delete();

            // delete outgoing po
            $outgoing_do->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete outgoing do data'
        );
    }

    public function last_number()
    {
         $last_number = OutgoingDo::whereYear('created_at', Carbon::now()->year)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->max('serial_number');
        return $last_number + 1;
    }
}
