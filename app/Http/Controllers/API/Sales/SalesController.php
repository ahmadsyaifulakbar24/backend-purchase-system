<?php

namespace App\Http\Controllers\API\Sales;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Sales\SalesResource;
use App\Models\Sales;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'location_id' => ['nullable', 'exists:locations,id'],
            'paginate' => ['nullable', 'in:0,1'],
            'limit' => ['nullable', 'integer'],
        ]);
        $location_id = $request->location_id;
        $limit = $request->input('limit', 10);
        $paginate = $request->input('paginate', 1);

        $sales = Sales::when($location_id, function ($query, $location_id) {
            $query->where('location_id', $location_id);
        })
        ->orderBy('created_at', 'DESC');

        $result = $paginate ? $sales->paginate($limit) : $sales->get();

        return ResponseFormatter::success(
            SalesResource::collection($result)->response()->getData(true),
            'success get sales data'
        );
    }

    public function upsert(Request $request)
    {
        $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
            'year' => ['required', 'integer'],
            'month' => ['required', 'integer'],
            'manday' => ['required', 'numeric'],
            'breakfast' => ['required', 'numeric'],
            'lunch' => ['required', 'numeric'],
            'dinner' => ['required', 'numeric'],
            'supper' => ['required', 'numeric'],
            'hk' => ['required', 'numeric'],
            'minimum' => ['required', 'numeric'],
        ]);
        $location_id = $request->location_id;
        $year = $request->year;
        $month = $request->month;

        $input = $request->all();

        $sales = Sales::where([
            ['location_id', $location_id],
            ['year', $year],
            ['month', $month],
        ])->first();

        if(empty($sales)) {
            $sales = Sales::create($input);
        } else {
            $sales->update($input);
        }

        return ResponseFormatter::success(
            new SalesResource($sales),
            'success update or insert sales data'
        );
    }

    public function show(Sales $sales)
    {
        return ResponseFormatter::success(
            new SalesResource($sales),
            'success show sales data'
        );
    }
}
