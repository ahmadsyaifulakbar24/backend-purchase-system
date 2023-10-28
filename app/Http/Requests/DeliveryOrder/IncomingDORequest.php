<?php

namespace App\Http\Requests\DeliveryOrder;

use Illuminate\Foundation\Http\FormRequest;

class IncomingDORequest extends FormRequest
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
            $incoming_do_id = $this->route('incoming_do')->id;
            $do_number_validation = ['required', 'unique:incoming_do,do_number,' . $incoming_do_id];
        } else {
            $do_number_validation = ['required', 'unique:incoming_do,do_number'];
        }
        return [
            'do_number' => $do_number_validation,
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'delivery_date' => ['required', 'date'],
            'received_date' => ['required', 'date'],
            'total' => ['required', 'integer'],
            'description' => ['nullable', 'string'],
        ];
    }
}
