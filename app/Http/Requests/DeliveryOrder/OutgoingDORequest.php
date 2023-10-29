<?php

namespace App\Http\Requests\DeliveryOrder;

use Illuminate\Foundation\Http\FormRequest;

class OutgoingDORequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'incoming_po_id' => ['required', 'exists:incoming_po,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'address' => ['required', 'string'],
            'delivery_date' => ['required', 'date'],
            'location_id' => ['required', 'exists:locations,id'],
            'prepared_by' => ['required', 'exists:users,id'],
            'received_by' => ['required', 'exists:users,id'],

            'item_product' => ['required', 'array'],
            'item_product.*.item_product_id' => ['required', 'exists:item_products,id', 'distinct'],
            'item_product.*.description' => ['required', 'string'],
            'item_product.*.quantity' => ['required', 'string'],
        ];
    }
}
