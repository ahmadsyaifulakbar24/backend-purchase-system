<?php

namespace App\Http\Requests\MealSheet;

use Illuminate\Foundation\Http\FormRequest;

class MealSheetGroupRequest extends FormRequest
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

            'client_id' => ['required', 'array'],
            'client_id.*' => ['required', 'exists:clients,id', 'distinct'],

            'prepared_by' => ['required', 'array'],
            'prepared_by.name' => ['required', 'string'],
            'prepared_by.position' => ['required', 'string'],

            'checked_by' => ['required', 'array'],
            'checked_by.name' => ['required', 'string'],
            'checked_by.position' => ['required', 'string'],

            'approved_by' => ['required', 'array'],
            'approved_by.name' => ['required', 'string'],
            'approved_by.position' => ['required', 'string'],

            'acknowladge_by' => ['required', 'array'],
            'acknowladge_by.name' => ['required', 'string'],
            'acknowladge_by.position' => ['required', 'string'],
        ];
    }
}
