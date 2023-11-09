<?php

namespace App\Http\Controllers\API\MealSheet;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\MealSheet\MealSheetDailyRequest;
use App\Http\Requests\MealSheet\MealSheetDailyUpdateRequest;
use App\Http\Resources\MealSheet\MealSheetDailyDetailResource;
use App\Http\Resources\MealSheet\MealSheetDailyResource;
use App\Models\MealSheetDaily;
use Illuminate\Http\Request;

class MealSheetDailyController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'meal_sheet_group_id' => ['required', 'exists:meal_sheet_groups,id'],
            'meal_sheet_date' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'limit' => ['nullable', 'integer'],
            'paginate' => ['nullable', 'in:0,1'],
        ]);
        $meal_sheet_group_id = $request->meal_sheet_group_id;
        $meal_sheet_date = $request->meal_sheet_date;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);

        $meal_sheet_daily = MealSheetDaily::where('meal_sheet_group_id', $meal_sheet_group_id)
                                    ->when($meal_sheet_date, function ($query, string $meal_sheet_date) {
                                        $query->where('meal_sheet_date', $meal_sheet_date);
                                    })
                                    ->when($start_date, function ($query, string $start_date) {
                                        $query->where('meal_sheet_date', '>=', $start_date);
                                    })
                                    ->when($end_date, function ($query, string $end_date) {
                                        $query->where('meal_sheet_date', '<=', $end_date);
                                    })
                                    ->orderBy('meal_sheet_date', 'DESC');

        $result = $paginate ? $meal_sheet_daily->paginate($limit) : $meal_sheet_daily->get();

        return ResponseFormatter::success(
            MealSheetDailyResource::collection($result)->response()->getData(true),
            'success get meal sheet daily data'
        );
    }

    public function store(MealSheetDailyRequest $request)
    {
        $input = $request->validated();
        $input['status'] = 'unlock';

        $meal_sheet_daily = MealSheetDaily::create($input);
        return ResponseFormatter::success(
            new MealSheetDailyResource($meal_sheet_daily),
            'success create meal sheet daily data'
        );
    }

    public function show(MealSheetDaily $meal_sheet_daily)
    {
        return ResponseFormatter::success(
            new MealSheetDailyDetailResource($meal_sheet_daily),
            'success show meal sheet daily data'
        );
    }

    public function update(MealSheetDailyUpdateRequest $request, MealSheetDaily $meal_sheet_daily)
    {
        $input = $request->validated();

        $meal_sheet_daily->update($input);
        return ResponseFormatter::success(
            new MealSheetDailyResource($meal_sheet_daily),
            'success update meal sheet daily data'
        );        
    }

    public function destroy(MealSheetDaily $meal_sheet_daily)
    {
        $check_data = $meal_sheet_daily->meal_sheet_detail()->count();

        if($check_data < 1) {
            $meal_sheet_daily->delete();
            return ResponseFormatter::success(
                null,
                'success delete meal sheet daily data'
            );
        } else {
            return ResponseFormatter::error([
                'meal_sheet_group_id' => 'this group already has data'
            ], 'failed to delete meal sheet daily data', 422);
        }
    }
}
