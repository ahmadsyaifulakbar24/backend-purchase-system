<?php

namespace App\Http\Controllers\API\Location;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Location\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1'],
            'only_parent' => ['nullable', 'in:0,1'],
            'parent_location_id' => [
                'nullable',
                Rule::exists('locations', 'id')->where(function($query) {
                    $query->whereNull('parent_location_id');
                })
            ]
        ]);
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);
        $only_parent = $request->only_parent;
        $parent_location_id = $request->parent_location_id;

        $location = Location::when($search, function ($query, string $search) {
                                $query->where(function ($sub_query) use ($search) {
                                    $sub_query->where('location', 'like', '%'. $search. '%')
                                        ->orWhere('location_code', 'like', '%'. $search. '%');
                                });
                            })
                            ->when($parent_location_id, function($query, string $parent_location_id) {
                                $query->where('parent_location_id', $parent_location_id);
                            })
                            ->when($only_parent, function($query) {
                                $query->whereNull('parent_location_id');
                            })
                            ->orderBy('location', 'ASC');
        
        $result = $paginate ? $location->paginate($limit) : $location->get();

        return ResponseFormatter::success(
            LocationResource::collection($result)->response()->getData(true),
            'success get location data'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_code' => ['required', 'unique:locations,location_code'],
            'location' => ['required', 'unique:locations,location'],
            'parent_location_id' => ['nullable', 'exists:locations,id'],
        ]);

        $input = $request->all();
        $location = Location::create($input);

        return ResponseFormatter::success(
            new LocationResource($location),
            'success create location data'
        );
    }

    public function show(Location $location)
    {
        return ResponseFormatter::success(
            new LocationResource($location),
            'success show department data'
        );
    }
    
    public function update(Location $location, Request $request)
    {
        $request->validate([
            'location_code' => ['required', 'unique:locations,location_code,' . $location->id],
            'location' => ['required', 'unique:locations,location,'  . $location->id],
            'parent_location_id' => ['nullable', 'exists:locations,id'],
        ]);

        $input = $request->all();
        $location->update($input);

        return ResponseFormatter::success(
            new LocationResource($location),
            'success update location data'
        );
    }

    public function destroy(Location $location)
    {
        $location->delete();
     
        return ResponseFormatter::success(
            null,
            'success delete location data'
        );
    }
}
