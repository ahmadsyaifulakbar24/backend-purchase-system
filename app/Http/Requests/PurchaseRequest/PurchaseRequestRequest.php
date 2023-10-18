<?php

namespace App\Http\Requests\PurchaseRequest;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequestRequest extends FormRequest
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
            'prepared_by' => ['required', 'exists:users,id'],
            'approved1_by' => ['required', 'exists:users,id'],
            'approved2_by' => ['required', 'exists:users,id'],

            'item_product' => ['required', 'array'],
            'item_product.*.item_name' => ['required', 'string'],
            'item_product.*.item_brand' => ['required', 'string'],
            'item_product.*.description' => ['required', 'string'],
            'item_product.*.size' => ['required', 'string'],
            'item_product.*.unit' => ['required', 'string'],
            'item_product.*.item_price' => ['required', 'numeric'],
            'item_product.*.quantity' => ['required', 'string'],
            'item_product.*.vat' => ['required', 'integer'],
            'item_product.*.remark' => ['required', 'string'],
        ];
    }
}
