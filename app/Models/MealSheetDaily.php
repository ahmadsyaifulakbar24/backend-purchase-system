<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use hisorange\BrowserDetect\Parser as Browser;
use Spatie\Activitylog\Traits\LogsActivity;

class MealSheetDaily extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'meal_sheet_daily';
    protected $fillable = [
        'meal_sheet_group_id',
        'meal_sheet_date',
        'contract_value',
        'status'
    ];

    protected $casts = [
        'meal_sheet_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('meal sheet daily')
        ->setDescriptionForEvent(fn(string $eventName) => "{$eventName} meal sheet daily data")
        ->logOnlyDirty();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->ip = request()->ip();
        $activity->browser = Browser::browserName();
        $activity->os = Browser::platformName();
    }

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

    public function mealSheetDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $date = Carbon::parse($value)->format('Y-m-d');
                $date_timezone = Carbon::createFromFormat('Y-m-d', $date, 'UTC')->setTimezone(config('app.timezone'))->format('Y-m-d');
                return $date_timezone;
            },
        );
    }

    public function meal_sheet_group(): BelongsTo
    {
        return $this->belongsTo(MealSheetGroup::class, 'meal_sheet_group_id');
    }

    public function meal_sheet_detail(): HasMany
    {
        return $this->hasMany(MealSheetDetail::class, 'meal_sheet_daily_id');
    }
}
