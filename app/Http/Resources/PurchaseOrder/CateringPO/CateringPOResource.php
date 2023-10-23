<?php

namespace App\Http\Resources\PurchaseOrder\CateringPO;

use App\Http\Resources\Discount\DiscountResource;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\Supplier\SupplierResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CateringPOResource extends JsonResource
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
            'po_number' => $this->po_number,
            'supplier' => new SupplierResource($this->supplier),
            'attn_name' => $this->attn_name,
            'request_date' => $this->request_date,
            'delivery_date' => $this->delivery_date,
            'location' => new LocationResource($this->location),
            'discount' => new DiscountResource($this->discount),
            'term_condition' => $this->term_condition,
            'term_payment' => $this->term_payment,
            'prepared_by' => [
                'id' => $this->prepared_by_data->id,
                'name' => $this->prepared_by_data->name,
            ],
            'checked_by' => [
                'id' => $this->checked_by_data->id,
                'name' => $this->checked_by_data->name,
            ],
            'approved1_by' => [
                'id' => $this->approved1_by_data->id,
                'name' => $this->approved1_by_data->name,
            ],
            'approved2_by' => [
                'id' => $this->approved2_by_data->id,
                'name' => $this->approved2_by_data->name,
            ],
            'checked_date' => $this->checked_date,
            'approved1_date' => $this->approved1_date,
            'approved2_date' => $this->approved2_date,
            'status' => $this->status,
            'note' => $this->note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
