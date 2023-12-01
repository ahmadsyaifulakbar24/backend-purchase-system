<?php

namespace App\Http\Resources\InternalOrder\DOCatering;

use App\Http\Resources\Discount\DiscountResource;
use App\Http\Resources\File\FileResource;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\SelectItemProduct\SelectItemProductResource;
use App\Http\Resources\Supplier\SupplierResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DOCateringDetailResource extends JsonResource
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
                'id' => $this->po_supplier_catering->po_catering->pr_catering->id,
                'request_date' => $this->po_supplier_catering->po_catering->pr_catering->request_date,
                'delivery_date' => $this->po_supplier_catering->po_catering->pr_catering->delivery_date,
                'location' => new LocationResource($this->po_supplier_catering->po_catering->pr_catering->location),
            ],
            'po_catering' => [
                'id' => $this->po_supplier_catering->po_catering->id,
                'discount' => new DiscountResource($this->po_supplier_catering->po_catering->discount),
                'term_condition' => $this->po_supplier_catering->po_catering->term_condition,
                'term_payment' => $this->po_supplier_catering->po_catering->term_payment,
            ],
            'po_supplier_catering' => [
                'id' => $this->po_supplier_catering->id,
                'po_number' => $this->po_supplier_catering->po_number,
                'supplier' => new SupplierResource($this->po_supplier_catering->supplier),
            ],
            'do_number' => $this->do_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'item_product' => SelectItemProductResource::collection($this->item_product),
            'attachment_file' => FileResource::collection($this->attachment_file)
        ];
    }
}
