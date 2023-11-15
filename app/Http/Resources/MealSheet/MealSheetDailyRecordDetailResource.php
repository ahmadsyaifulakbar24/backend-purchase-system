<?php

namespace App\Http\Resources\MealSheet;

use App\Http\Resources\Client\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealSheetDailyRecordDetailResource extends JsonResource
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
            'meal_sheet_daily' => new MealSheetDailyDetailResource($this->meal_sheet_daily),
            'client' => new ClientResource($this->client),
            'mandays' => $this->mandays,  
            'casual_breakfast' => $this->casual_breakfast,  
            'casual_lunch' => $this->casual_lunch,  
            'casual_dinner' => $this->casual_dinner,  
            'prepared_by' => $this->prepared_by,  
            'checked_by' => $this->checked_by,  
            'approved_by' => $this->approved_by,  
            'acknowladge_by' => $this->acknowladge_by,  
            'meal_sheet_record' => MealSheetRecordResource::collection($this->meal_sheet_record),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
