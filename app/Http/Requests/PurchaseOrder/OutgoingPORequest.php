<?php

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class OutgoingPORequest extends FormRequest
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
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'attn_name' => ['required', 'string'],
            'request_date' => ['required', 'date'],
            'delivery_date' => ['required', 'date'],
            'location_id' => ['required', 'exists:locations,id'],
            'discount_id' => ['required', 'exists:discounts,id'],
            'term_condition' => ['required', 'string'],
            'prepared_by' => ['required', 'exists:users,id'],
            'checked_by' => ['required', 'exists:users,id'],
            'approved1_by' => ['required', 'exists:users,id'],
            'approved2_by' => ['required', 'exists:users,id'],

            'item_product' => ['required', 'array'],
            'item_product.*.item_product_id' => ['required', 'exists:item_products,id', 'distinct'],
            'item_product.*.weight' => ['required', 'string'],
            'item_product.*.item_price' => ['required', 'numeric'],
            'item_product.*.quantity' => ['required', 'string'],
            'item_product.*.vat' => ['required', 'integer'],
            'item_product.*.tnt' => ['required', 'in:T,NT'],
        ];
    }
}
