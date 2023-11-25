<?php

namespace App\Http\Resources\InternalOrder;

use App\Http\Resources\Location\LocationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PRCateringResource extends JsonResource
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
            'pr_number' => $this->pr_number,
            'location' => new LocationResource($this->location),
            'pr_date' => $this->pr_date,
            'shipment_date' => $this->shipment_date,
            'prepared_by' => [
                'id' => $this->prepared_by_data->id,
                'name' => $this->prepared_by_data->name,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
