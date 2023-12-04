<?php

namespace App\Http\Requests\InternalOrder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class POSupplierCateringRequest extends FormRequest
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
            'po_catering_id' => [
                'required', 
                Rule::exists('po_caterings', 'id')->where(function ($query) {
                    return $query->where('status', 'finish');
                })
            ],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'discount_id' => ['required', 'exists:discounts,id'],
            'term_condition' => ['required', 'string'],
            'status' => ['required', 'in:draft,submit'],

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
