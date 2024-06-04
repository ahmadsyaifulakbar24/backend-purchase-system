<?php

namespace App\Http\Requests\MealRate;

use Illuminate\Foundation\Http\FormRequest;

class MealRateRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'manday' => ['required', 'numeric'],
            'breakfast' => ['required', 'numeric'],
            'lunch' => ['required', 'numeric'],
            'dinner' => ['required', 'numeric'],
            'supper' => ['required', 'numeric'],
            'hk' => ['required', 'numeric'],
            'minimum' => ['required', 'numeric'],
        ];
    }
}
