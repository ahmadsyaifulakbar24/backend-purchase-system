<?php

namespace App\Http\Resources\InternalOrder;

use App\Http\Resources\File\FileResource;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\SelectItemProduct\SelectItemProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PRCateringDetailResource extends JsonResource
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
            'description' => $this->description,
            'prepared_by' => [
                'id' => $this->prepared_by_data->id,
                'name' => $this->prepared_by_data->name,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'item_product' => SelectItemProductResource::collection($this->item_product),
            'attachment_file' => FileResource::collection($this->attachment_file)
        ];
    }
}
