<?php

namespace App\Http\Requests\InternalOrder\POSupplierCatering;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class POSupplierCateringUpdateRequest extends FormRequest
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
        $po_supplier_catering_id = $this->route('po_supplier_catering')->id;
        return [
            'po_catering_id' => [
                'required', 
                Rule::exists('po_caterings', 'id')->where(function ($query) {
                    return $query->where('status', 'finish');
                })
            ],
            'supplier_id' => [
                'required', 
                'exists:suppliers,id',
                Rule::unique('po_supplier_caterings', 'supplier_id')->where(function($query) {
                    $query->where('po_catering_id', $this->po_catering_id);
                })->ignore($po_supplier_catering_id)
            ],
            'discount_id' => ['required', 'exists:discounts,id'],
            'term_condition' => ['required', 'string'],
            'status' => ['required', 'in:draft,submit'],
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
