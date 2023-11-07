<?php

namespace App\Http\Requests\MealSheet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MealSheetDailyRequest extends FormRequest
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
            'meal_sheet_group_id' => ['required', 'exists:meal_sheet_groups,id'],
            'meal_sheet_date' => [
                'required', 
                'date',
                Rule::unique('meal_sheet_daily', 'meal_sheet_date')->where(function ($query) {
                    $query->where('meal_sheet_group_id', $this->meal_sheet_group_id);
                })
            ],
            'contract_value' => ['required', 'integer'],
        ];
    }
}
