<?php

namespace App\Http\Resources\ExternalOrder\Quotation;

use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Location\LocationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
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
                'id' => $this->pr_customer->id,
                'pr_number' => $this->pr_customer->pr_number,
                'request_date' => $this->pr_customer->request_date,
                'delivery_date' => $this->pr_customer->delivery_date,
                'location' => new LocationResource($this->pr_customer->location),
            ],
            'quotation_number' => $this->quotation_number,
            'customer' => new CustomerResource($this->customer),
            'prepared_by' => [
                'id' => $this->prepared_by_data->id,
                'name' => $this->prepared_by_data->name,
            ],
            'checked_by' => [
                'id' => $this->checked_by_data->id,
                'name' => $this->checked_by_data->name,
            ],
            'checked_date' => $this->checked_date,
            'status' => $this->status,
            'note' => $this->note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
