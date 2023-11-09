<?php

namespace App\Http\Resources\MealSheet;

use App\Http\Resources\Location\LocationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealSheetMonthlyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'month' => $this->month,
            'year' => $this->year,
            'meal_sheet_group' => [
                'id' => $this->meal_sheet_group->id,
                'location' => new LocationResource($this->meal_sheet_group->location),
                'client' => MealSheetClientResource::collection($this->meal_sheet_group->meal_sheet_client),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
