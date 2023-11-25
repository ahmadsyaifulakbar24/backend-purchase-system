<?php

namespace App\Http\Requests\InternalOrder;

use Illuminate\Foundation\Http\FormRequest;

class PRCateringRequest extends FormRequest
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
            'location_id' => ['required', 'exists:locations,id'],
            'pr_date' => ['required', 'date'],
            'shipment_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'prepared_by' => ['required', 'exists:users,id'],

            'item_product' => ['required', 'array'],
            'item_product.*.item_product_id' => ['required', 'exists:item_products,id', 'distinct'],
            'item_product.*.description' => ['required', 'string'],
            'item_product.*.item_price' => ['required', 'numeric'],
            'item_product.*.quantity' => ['required', 'string'],
            'item_product.*.vat' => ['required', 'integer'],
            'item_product.*.remark' => ['required', 'string'],
        ];
    }
}
