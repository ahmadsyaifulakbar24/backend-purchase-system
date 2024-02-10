<?php

namespace App\Http\Requests\ExternalOrder\Quotation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuotationUpdateRequest extends FormRequest
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
            'customer_id' => ['required', 'exists:customers,id'],
            'vessel' => ['required', 'string'],
            'shipping_address' => ['required', 'string'],
            'mark_up' => ['required', 'integer'],
            'prepared_by' => ['required', 'exists:users,id'],
            'checked_by' => ['required', 'exists:users,id'],
            'term_condition' => ['required', 'string'],

            'item_product' => ['required', 'array'],
            'item_product.*.item_product_id' => ['required', 'exists:item_products,id', 'distinct'],
            'item_product.*.weight' => ['required', 'integer'],
            'item_product.*.quantity' => ['required', 'string'],
            'item_product.*.item_price' => ['required', 'numeric'],
            'item_product.*.markup_value' => ['required', 'numeric'],
            'item_product.*.vat' => ['required', 'integer'],
            'item_product.*.tnt' => ['required', 'in:T,NT'],
            'item_product.*.markup_vat' => ['required', 'integer'],
            'item_product.*.remark' => ['nullable', 'string'],
        ];
    }
}
