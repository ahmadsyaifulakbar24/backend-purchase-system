<?php

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class IncomingPORequest extends FormRequest
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

        if ($this->isMethod('patch') || $this->isMethod('put')) {
            $incoming_po_id = $this->route('incoming_po')->id;
            $po_number_validation = ['required', 'unique:incoming_po,po_number,' . $incoming_po_id];
        } else {
            $po_number_validation = ['required', 'unique:incoming_po,po_number'];
        }

        $rules = [
            'po_number' => $po_number_validation,
            'customer_id' => ['required', 'exists:customers,id'],
            'received_date' => ['required', 'date'],
            'total' => ['required', 'integer'],
            'description' => ['nullable', 'string'],
        ];

        return $rules;
    }
}
