<?php

namespace App\Http\Resources\DeliveryOrder\IncomingDO;

use App\Http\Resources\File\FileResource;
use App\Http\Resources\Supplier\SupplierResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncomingDODetailResource extends JsonResource
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
            'do_number' => $this->do_number,
            'supplier' => new SupplierResource($this->supplier),
            'delivery_date' => $this->delivery_date,
            'received_date' => $this->received_date,
            'total' => $this->total,
            'description' => $this->description,
            'attachment_file' => FileResource::collection($this->attachment_file),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
