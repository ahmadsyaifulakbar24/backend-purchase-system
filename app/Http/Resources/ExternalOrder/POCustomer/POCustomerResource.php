<?php

namespace App\Http\Resources\ExternalOrder\POCustomer;

use App\Http\Resources\Discount\DiscountResource;
use App\Http\Resources\Location\LocationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class POCustomerResource extends JsonResource
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
                'id' => $this->quotation->pr_customer->id,
                'pr_number' => $this->quotation->pr_customer->pr_number,
                'request_date' => $this->quotation->pr_customer->request_date,
                'delivery_date' => $this->quotation->pr_customer->delivery_date,
                'location' => new LocationResource($this->quotation->pr_customer->location),
            ],
            'quotation' => [
                'id' => $this->quotation->id,
                'quotation_number' => $this->quotation->quotation_number,
            ],
            'po_number' => $this->po_number,
            'discount' => new DiscountResource($this->discount),
            'term_condition' => $this->term_condition,
            'term_payment' => $this->term_payment,
            'prepared_by' => [
                'id' => $this->prepared_by_data->id,
                'name' => $this->prepared_by_data->name,
            ],
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
