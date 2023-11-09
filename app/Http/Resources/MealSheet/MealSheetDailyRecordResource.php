<?php

namespace App\Http\Resources\MealSheet;

use App\Http\Resources\Client\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealSheetDailyRecordResource extends JsonResource
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
            'meal_sheet_daily' => new MealSheetDailyResource($this->meal_sheet_daily),
            'client' => new ClientResource($this->client),
            'mandays' => $this->mandays,  
            'casual_breakfast' => $this->casual_breakfast,  
            'casual_lunch' => $this->casual_lunch,  
            'casual_dinner' => $this->casual_dinner,  
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
