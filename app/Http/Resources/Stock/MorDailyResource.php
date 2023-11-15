<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MorDailyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'location_id' => $this->location_id,
            'date' => $this->date,
        ];
    }
}
