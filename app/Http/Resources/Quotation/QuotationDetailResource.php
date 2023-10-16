<?php

namespace App\Http\Resources\Quotation;

use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationDetailResource extends JsonResource
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
            'quotation_number' => $this->quotation_number,
            'customer' => new CustomerResource($this->customer),
            'attention' => $this->attention,
            'address' => $this->address,
            'delivery_date' => $this->delivery_date,
            'vessel' => $this->vessel,
            'shipping_address' => $this->shipping_address,
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
            'term_condition' => $this->term_condition,
            'checked_date' => $this->checked_date,
            'approved_date' => $this->approved_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'item_product' => QuotationItemProductResource::collection($this->item_product),
        ];
    }
}
