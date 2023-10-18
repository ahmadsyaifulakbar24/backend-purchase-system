<?php

namespace App\Http\Requests\Quotation;

use Illuminate\Foundation\Http\FormRequest;

class QuotationRequest extends FormRequest
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
            'attention' => ['required', 'string'],
            'address' => ['required', 'string'],
            'delivery_date' => ['required', 'date'],
            'vessel' => ['required', 'string'],
            'shipping_address' => ['required', 'string'],
            'shipment_date' => ['required', 'date'],
            'prepared_by' => ['required', 'exists:users,id'],
            'checked_by' => ['required', 'exists:users,id'],
            'approved1_by' => ['required', 'exists:users,id'],
            'approved2_by' => ['required', 'exists:users,id'],
            'term_condition' => ['required', 'string'],

            'item_product' => ['required', 'array'],
            'item_product.*.item_name' => ['required', 'string'],
            'item_product.*.weight' => ['required', 'integer'],
            'item_product.*.unit' => ['required', 'string'],
            'item_product.*.quantity' => ['required', 'string'],
            'item_product.*.item_price' => ['required', 'numeric'],
            'item_product.*.vat' => ['required', 'integer'],
            'item_product.*.tnt' => ['required', 'in:T,NT'],
            'item_product.*.remark' => ['required', 'string'],
        ];
    }
}
