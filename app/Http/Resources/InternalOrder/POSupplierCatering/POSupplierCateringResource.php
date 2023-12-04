<?php

namespace App\Http\Resources\InternalOrder\POSupplierCatering;

use App\Http\Resources\Discount\DiscountResource;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\Supplier\SupplierResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class POSupplierCateringResource extends JsonResource
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
            'pr_catering' => [
                'id' => $this->po_catering->pr_catering->id,
                'request_date' => $this->po_catering->pr_catering->request_date,
                'delivery_date' => $this->po_catering->pr_catering->delivery_date,
                'location' => new LocationResource($this->po_catering->pr_catering->location),
            ],
            'po_catering' => [
                'id' => $this->po_catering->id,
                'po_number' => $this->po_catering->po_number,
            ],
            'po_number' => $this->po_number,
            'supplier' => new SupplierResource($this->supplier),
            'discount' => new DiscountResource($this->discount),
            'term_condition' => $this->term_condition,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
