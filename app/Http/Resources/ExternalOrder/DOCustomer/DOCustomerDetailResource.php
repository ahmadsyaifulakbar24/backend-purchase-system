<?php

namespace App\Http\Resources\ExternalOrder\DOCustomer;

use App\Http\Resources\File\FileResource;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\SelectItemProduct\SelectItemProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DOCustomerDetailResource extends JsonResource
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
            'do_number' => $this->do_number,
            'approved_by' => [
                'id' => $this->approved_by_data->id,
                'name' => $this->approved_by_data->name,
            ],
            'approved_date' => $this->approved_date,
            'status' => $this->status,
            'note' => $this->note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'item_product' => SelectItemProductResource::collection($this->item_product),
            'attachment_file' => FileResource::collection($this->attachment_file)
        ];
    }
}
