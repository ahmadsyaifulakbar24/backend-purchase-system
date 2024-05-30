<?php

namespace App\Http\Controllers\API\MealRate;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
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
        });

        $result = $paginate ? $meal_rate->paginate($limit) : $meal_rate->get();

        return ResponseFormatter::success(
            MealRateResource::collection($result)->response()->getData(true),
            'success get meal rate data'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_id' => ['required', 'unique:meal_rates,location_id', 'exists:locations,id'],
            'manday' => ['required', 'numeric'],
            'breakfast' => ['required', 'numeric'],
            'lunch' => ['required', 'numeric'],
            'dinner' => ['required', 'numeric'],
            'supper' => ['required', 'numeric'],
            'hk' => ['required', 'numeric'],
        ]);

        $input = $request->all();

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

    public function update(Request $request, MealRate $meal_rate)
    {
        $request->validate([
            'location_id' => [
                'required', 
                Rule::unique('meal_rates', 'location_id')->ignore($meal_rate->id), 
                'exists:locations,id'
            ],
            'manday' => ['required', 'numeric'],
            'breakfast' => ['required', 'numeric'],
            'lunch' => ['required', 'numeric'],
            'dinner' => ['required', 'numeric'],
            'supper' => ['required', 'numeric'],
            'hk' => ['required', 'numeric'],
        ]);
        $input = $request->all();

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
