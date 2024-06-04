<?php

namespace App\Http\Resources\MealSheet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealSheetRecordResource extends JsonResource
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
            'name' => $this->name,
            'position' => $this->position,
            'company' => $this->company,
            'breakfast' => $this->breakfast,
            'lunch' => $this->lunch,
            'dinner' => $this->dinner,
            'supper' => $this->supper,
            'accomodation' => $this->accomodation,
        ];
    }
}
