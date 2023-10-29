<?php

namespace App\Http\Resources\DeliveryOrder\OutgoingDO;

use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\PurchaseOrder\IncomingPO\IncomingPODetailResource;
use App\Http\Resources\SelectItemProduct\SelectItemProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutgoingDODetailResource extends JsonResource
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
            'do_number' => $this->do_number,
            'incoming_po' => new IncomingPODetailResource($this->incoming_po),
            'customer' => new CustomerResource($this->customer),
            'address' => $this->address,
            'delivery_date' => $this->delivery_date,
            'location' => new LocationResource($this->location),
            'prepared_by' => [
                'id' => $this->prepared_by_data->id,
                'name' => $this->prepared_by_data->name,
            ],
            'received_by' => [
                'id' => $this->received_by_data->id,
                'name' => $this->received_by_data->name,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'item_product' => SelectItemProductResource::collection($this->item_product),
        ];
    }
}
