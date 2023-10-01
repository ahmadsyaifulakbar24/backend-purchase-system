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
            'search' => ['nullable', 'string'],
        ]);
        $search = $request->search;
        $limit = $request->input('limit', 10);

        $cost_center = CostCenter::when($search, function ($query, string $search) {
                                $query->where(function ($sub_query) use ($search) {
                                    $sub_query->where('cost_center', 'like', '%'. $search. '%')
                                        ->orWhere('cost_center_code', 'like', '%'. $search. '%');
                                });
                            })
                            ->orderBy('cost_center', 'ASC')
                            ->paginate($limit);

        return ResponseFormatter::success(
            CostCenterResource::collection($cost_center)->response()->getData(true),
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

    public function destroy(CostCenter $cost_center)
    {
        
        $cost_center->delete();

        return ResponseFormatter::success(
            null,
            'success delete cost center data'
        );
    }
}
