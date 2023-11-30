<?php

namespace App\Http\Resources\InternalOrder\POSupplierCatering;

use App\Http\Resources\Discount\DiscountResource;
use App\Http\Resources\File\FileResource;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\SelectItemProduct\SelectItemProductResource;
use App\Http\Resources\Supplier\SupplierResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class POSupplierCateringDetailResource extends JsonResource
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
            'po_catering' => [
                'id' => $this->po_catering->id,
                'po_number' => $this->po_catering->po_number,
                'request_date' => $this->po_catering->pr_catering->request_date,
                'delivery_date' => $this->po_catering->pr_catering->delivery_date,
                'location' => new LocationResource($this->po_catering->pr_catering->location),
            ],
            'po_number' => $this->po_number,
            'supplier' => new SupplierResource($this->supplier),
            'discount' => new DiscountResource($this->discount),
            'term_condition' => $this->term_condition,
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
            'item_product' => SelectItemProductResource::collection($this->item_product),
            'attachment_file' => FileResource::collection($this->attachment_file)
        ];
    }
}
