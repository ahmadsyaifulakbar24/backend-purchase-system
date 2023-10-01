<?php

namespace App\Http\Controllers\API\Location;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Location\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
        ]);
        $search = $request->search;
        $limit = $request->input('limit', 10);

        $location = Location::when($search, function ($query, string $search) {
                                $query->where(function ($sub_query) use ($search) {
                                    $sub_query->where('location', 'like', '%'. $search. '%')
                                        ->orWhere('location_code', 'like', '%'. $search. '%');
                                });
                            })
                            ->orderBy('location', 'ASC')
                            ->paginate($limit);

        return ResponseFormatter::success(
            LocationResource::collection($location)->response()->getData(true),
            'success get department data'
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