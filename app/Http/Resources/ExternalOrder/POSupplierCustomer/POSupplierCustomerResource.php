<?php

namespace App\Http\Resources\ExternalOrder\POSupplierCustomer;

use App\Http\Resources\Discount\DiscountResource;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\Supplier\SupplierResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class POSupplierCustomerResource extends JsonResource
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
            'pr_customer' => [
                'id' => $this->po_customer->quotation->pr_customer->id,
                'pr_number' => $this->po_customer->quotation->pr_customer->pr_number,
                'request_date' => $this->po_customer->quotation->pr_customer->request_date,
                'delivery_date' => $this->po_customer->quotation->pr_customer->delivery_date,
                'location' => new LocationResource($this->po_customer->quotation->pr_customer->location),
            ],
            'quotation' => [
                'id' => $this->po_customer->quotation->id,
                'quotation_number' => $this->po_customer->quotation->quotation_number,
            ],
            'po_customer' => [
                'id' => $this->po_customer->id,
                'po_number' => $this->po_customer->po_number,
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
