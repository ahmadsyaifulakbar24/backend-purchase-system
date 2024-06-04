<?php

namespace App\Http\Controllers\API\MealRate;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\MealRate\MealRateRequest;
use App\Http\Resources\MealRate\MealRateResource;
use App\Models\MealRate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MealRateController extends Controller
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

        $meal_rate = MealRate::when($location_id, function ($query, $location_id) {
            $query->where('location_id', $location_id);
        })
        ->orderBy('created_at', 'DESC');

        $result = $paginate ? $meal_rate->paginate($limit) : $meal_rate->get();

        return ResponseFormatter::success(
            MealRateResource::collection($result)->response()->getData(true),
            'success get meal rate data'
        );
    }

    public function store(MealRateRequest $request)
    {
        $input = $request->validated();

        $meal_rate = MealRate::create($input);

        return ResponseFormatter::success(
            new MealRateResource($meal_rate),
            'success create meal reate data'
        );
    }

    public function show(MealRate $meal_rate)
    {
        return ResponseFormatter::success(
            new MealRateResource($meal_rate),
            'success show meal rate data'
        );
    }

    public function update(MealRateRequest $request, MealRate $meal_rate)
    {
        $input = $request->validated();

        $meal_rate->update($input);

        return ResponseFormatter::success(
            new MealRateResource($meal_rate),
            'success update meal reate data'
        );
    }

    public function destroy(MealRate $meal_rate)
    {
        $meal_rate->delete();

        return ResponseFormatter::success(
            null,
            'success delete meal rate data'
        );
    }
}
