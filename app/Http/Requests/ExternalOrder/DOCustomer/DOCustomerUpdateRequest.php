<?php

namespace App\Http\Requests\ExternalOrder\DOCustomer;

use Illuminate\Foundation\Http\FormRequest;

class DOCustomerUpdateRequest extends FormRequest
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
            'approved_by' => ['required', 'exists:users,id'],
            'hard_edit' => ['required', 'in:yes,no'],
            
            'item_product' => ['required', 'array'],
            'item_product.*.item_product_id' => ['required', 'exists:item_products,id', 'distinct'],
            'item_product.*.description' => ['required', 'string'],
            'item_product.*.item_price' => ['required', 'numeric'],
            'item_product.*.quantity' => ['required', 'string'],
            'item_product.*.vat' => ['required', 'integer'],
            'item_product.*.remark' => ['nullable', 'string'],
        ];
    }
}
