<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealSheetDetail extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'meal_sheet_details';
    protected $fillable = [
        'meal_sheet_day_id',
        'client_id',
        'mandays',
        'casual_breakfast',
        'casual_lunch',
        'casual_dinner',
        'prepared_by',
        'checked_by',
        'approved_by'
    ];

    protected $casts = [
        'prepared_by' => 'array',
        'checked_by' => 'array',
        'approved_by' => 'array',
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: function($value) {
                $date = Carbon::parse($value)->format('Y-m-d H:i:s');
                $date_timezone = Carbon::createFromFormat('Y-m-d H:i:s', $date, 'UTC')->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
                return $date_timezone;
            },
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $date = Carbon::parse($value)->format('Y-m-d H:i:s');
                $date_timezone = Carbon::createFromFormat('Y-m-d H:i:s', $date, 'UTC')->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
                return $date_timezone;
            },
        );
    }
    
}
