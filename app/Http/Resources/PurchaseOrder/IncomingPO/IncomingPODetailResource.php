<?php

namespace App\Http\Resources\PurchaseOrder\IncomingPO;

use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\File\FileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncomingPODetailResource extends JsonResource
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
            'customer' => new CustomerResource($this->customer),
            'received_date' => $this->received_date,
            'total' => $this->total,
            'description' => $this->description,
            'attachment_file' => FileResource::collection($this->attachment_file),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
