<?php

namespace App\Http\Requests\MealSheet;

use App\Models\MealSheetDaily;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MealSheetDailyRecordUpdateRequest extends FormRequest
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
        $meal_sheet_detail = $this->route('meal_sheet_detail');
        
        return [
            'client_id' => [
                'required',
                Rule::exists('meal_sheet_clients', 'client_id')->where(function ($query) use ($meal_sheet_detail) {
                    $query->where('meal_sheet_group_id', $meal_sheet_detail->meal_sheet_daily->meal_sheet_group_id);
                }),
                Rule::unique('meal_sheet_details', 'client_id')->where(function ($query) use ($meal_sheet_detail) {
                    $query->where('meal_sheet_daily_id', $meal_sheet_detail->meal_sheet_daily_id);
                })->ignore($meal_sheet_detail->id)
            ],
            'formula_id' => ['required', 'exists:formulas,id'],
            
            'prepared_by' => ['required', 'array'],
            'prepared_by.name' => ['required', 'string'],
            'prepared_by.position' => ['required', 'string'],

            'checked_by' => ['required', 'array'],
            'checked_by.name' => ['required', 'string'],
            'checked_by.position' => ['required', 'string'],

            'approved_by' => ['required', 'array'],
            'approved_by.name' => ['required', 'string'],
            'approved_by.position' => ['required', 'string'],

            'acknowladge_by' => ['nullable', 'array'],
            'acknowladge_by.name' => ['required_with:acknowladge_by', 'string'],
            'acknowladge_by.position' => ['required_with:acknowladge_by', 'string'],

            'meal_sheet_record' => ['required', 'array'],
            'meal_sheet_record.*.name' => ['required', 'string'],
            'meal_sheet_record.*.position' => ['required', 'string'],
            'meal_sheet_record.*.company' => ['required', 'string'],
            'meal_sheet_record.*.breakfast' => ['required', 'boolean'],
            'meal_sheet_record.*.lunch' => ['required', 'boolean'],
            'meal_sheet_record.*.dinner' => ['required', 'boolean'],
            'meal_sheet_record.*.supper' => ['required', 'boolean'],
            'meal_sheet_record.*.accomodation' => ['required', 'boolean'],
        ];
    }
}
