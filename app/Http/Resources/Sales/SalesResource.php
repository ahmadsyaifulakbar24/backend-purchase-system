<?php

namespace App\Http\Resources\Sales;

use App\Http\Resources\Location\LocationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesResource extends JsonResource
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
            'location' => new LocationResource($this->location),
            'year' => $this->year,
            'month' => $this->month,
            'manday' => $this->manday,
            'breakfast' => $this->breakfast,
            'lunch' => $this->lunch,
            'dinner' => $this->dinner,
            'supper' => $this->supper,
            'hk' => $this->hk,
            'minimum' => $this->minimum,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
