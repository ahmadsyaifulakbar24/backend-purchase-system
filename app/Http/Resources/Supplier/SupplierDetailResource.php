<?php

namespace App\Http\Resources\Supplier;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierDetailResource extends JsonResource
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
            'code' => $this->code,
            'type' => $this->type,
            'name' => $this->name,
            'category' => $this->category,
            'npwp' => $this->npwp,
            'contact_person' => $this->contact_person,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
