<?php

namespace App\Http\Controllers\API\MealSheet;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\MealSheet\MealSheetGroupRequest;
use App\Http\Resources\MealSheet\MealSheetGroupResource;
use App\Models\MealSheetGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MealSheetGroupController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'paginate' => ['nullable', 'in:0,1'],
        ]);
        $location_id = $request->location_id;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);

        $meal_sheet_group = MealSheetGroup::when($location_id, function($query, string $location_id) {
                                        $query->where('location_id', $location_id);
                                    })
                                    ->orderBy('created_at', 'DESC');

        $result = $paginate ? $meal_sheet_group->paginate($limit) : $meal_sheet_group->get();

        return ResponseFormatter::success(
            MealSheetGroupResource::collection($result)->response()->getData(true),
            'success get meal sheet data'
        );
    }

    public function store(MealSheetGroupRequest $request)
    {

        $result = DB::transaction(function () use ($request) {
            $input = $request->safe()->except([
                'client_id'
            ]);

            $client_data = $request->client_id;

            $meal_sheet_group = MealSheetGroup::create($input);
            $meal_sheet_group->meal_sheet_client()->attach($client_data);

            return $meal_sheet_group;
        });

        return ResponseFormatter::success(
            new MealSheetGroupResource($result),
            'success create client group data'
        );
        
    }

    public function show(MealSheetGroup $meal_sheet_group)
    {
        return ResponseFormatter::success(
            new MealSheetGroupResource($meal_sheet_group),
            'success show meal sheet group data'
        );
    }

    public function update(MealSheetGroupRequest $request, MealSheetGroup $meal_sheet_group)
    {
        $result = DB::transaction(function () use ($request, $meal_sheet_group) {
            $input = $request->safe()->except([
                'client_id'
            ]);

            $client_data = $request->client_id;

            $meal_sheet_group->update($input);
            $meal_sheet_group->meal_sheet_client()->sync($client_data);

            return $meal_sheet_group;
        });

        return ResponseFormatter::success(
            new MealSheetGroupResource($result),
            'success update client group data'
        );
    }

    public function destroy(MealSheetGroup $meal_sheet_group)
    {

        $check_data = $meal_sheet_group->meal_sheet_day()->count();
        if($check_data < 1) {
            $meal_sheet_group->delete();
            return ResponseFormatter::success(
                null,
                'success delete this data'
            );
        } else {
            return ResponseFormatter::error([
                'meal_sheet_group_id' => 'this group already has data'
            ], 'cannot delete this data', 422);
        }

    }
}
