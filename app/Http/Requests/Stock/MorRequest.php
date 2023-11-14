<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

class MorRequest extends FormRequest
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

            'item_product' => ['required', 'array'],
            'item_product.*.date' => ['required', 'date'],
            'item_product.*.item_product_id' => ['required', 'exists:item_products,id'],
            'item_product.*.item_price' => ['required', 'numeric'],
            'item_product.*.quantity' => ['required', 'string'],
        ];

        
    }
}
