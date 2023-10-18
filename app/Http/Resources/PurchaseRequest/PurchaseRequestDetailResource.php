<?php

namespace App\Http\Resources\PurchaseRequest;

use App\Http\Resources\File\FileResource;
use App\Http\Resources\Location\LocationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseRequestDetailResource extends JsonResource
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
            'checked_by' => [
                'id' => $this->checked_by_data->id,
                'name' => $this->checked_by_data->name,
            ],
            'approved_by' => [
                'id' => $this->approved_by_data->id,
                'name' => $this->approved_by_data->name,
            ],
            'checked_date' => $this->checked_date,
            'approved_date' => $this->approved_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'item_product' => PurchaseRequestItemProductResource::collection($this->item_product),
            'attachment_file' => FileResource::collection($this->attachment_file)
        ];
    }
}
