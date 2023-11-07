<?php

namespace App\Http\Resources\MealSheet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealSheetDailyResource extends JsonResource
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
            'meal_sheet_group_id' => $this->meal_sheet_group_id,
            'meal_sheet_date' => $this->meal_sheet_date,
            'contract_value' => $this->contract_value,
            'status' => $this->status,
        ];
    }
}
