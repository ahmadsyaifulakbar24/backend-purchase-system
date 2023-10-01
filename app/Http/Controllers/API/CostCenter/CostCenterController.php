<?php

namespace App\Http\Controllers\API\CostCenter;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\CostCenter\CostCenterResource;
use App\Models\CostCenter;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'paginate' => ['nullable', 'in:0,1'],
            'search' => ['nullable', 'string'],
        ]);
        $search = $request->search;
        $paginate = $request->paginate;
        $limit = $request->input('limit', 10);

        $cost_center = CostCenter::when($search, function ($query, string $search) {
                                $query->where(function ($sub_query) use ($search) {
                                    $sub_query->where('cost_center', 'like', '%'. $search. '%')
                                        ->orWhere('cost_center_code', 'like', '%'. $search. '%');
                                });
                            })
                            ->orderBy('cost_center', 'ASC');
        
        $result = $paginate ? $cost_center->paginate($limit) : $cost_center->get();

        return ResponseFormatter::success(
            CostCenterResource::collection($result)->response()->getData(true),
            'success get cost center data'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'cost_center_code' => ['required', 'unique:cost_centers,cost_center_code'],
            'cost_center' => ['required', 'unique:cost_centers,cost_center']
        ]);

        $input = $request->all();
        $cost_center = CostCenter::create($input);

        return ResponseFormatter::success(
            new CostCenterResource($cost_center),
            'success create cost center data'
        );
    }

    public function show(CostCenter $cost_center)
    {
        return ResponseFormatter::success(
            new CostCenterResource($cost_center),
            'success show cost center data'
        );
    }

    public function update(CostCenter $cost_center, Request $request)
    {
        $request->validate([
            'cost_center_code' => ['required', 'unique:cost_centers,cost_center_code,'. $cost_center->id],
            'cost_center' => ['required', 'unique:cost_centers,cost_center,'. $cost_center->id]
        ]);

        $input = $request->all();
        $cost_center->update($input);

        return ResponseFormatter::success(
            new CostCenterResource($cost_center),
            'success update cost center data'
        );
    }

    public function destory(CostCenter $cost_center)
    {
        
        $cost_center->delete();

        return ResponseFormatter::success(
            null,
            'success delete cost center data'
        );
    }
}
