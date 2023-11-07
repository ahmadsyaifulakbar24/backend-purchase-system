<?php

namespace App\Http\Requests\MealSheet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MealSheetDailyUpdateRequest extends FormRequest
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
        $meal_sheet_daily = $this->route('meal_sheet_daily');

        return [
            'meal_sheet_date' => [
                'required', 
                'date',
                Rule::unique('meal_sheet_daily', 'meal_sheet_date')->where(function ($query) use ($meal_sheet_daily) {
                    $query->where('meal_sheet_group_id', $meal_sheet_daily->meal_sheet_group_id);
                })->ignore($meal_sheet_daily->id)
            ],
            'contract_value' => ['required', 'integer'],
        ];
    }
}
