<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use hisorange\BrowserDetect\Parser as Browser;
use Spatie\Activitylog\Traits\LogsActivity;

class MealSheetMonthly extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'meal_sheet_monthly';
    protected $fillable = [
        'month',
        'year',
        'meal_sheet_group_id',
        'recap_per_day',
        'prepared_by',
        'checked_by',
        'approved_by',
        'acknowladge_by'
    ];

    protected $casts = [
        'recap_per_day' => 'array',
        'prepared_by' => 'array',
        'checked_by' => 'array',
        'approved_by' => 'array',
        'acknowladge_by' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('meal sheet monthly')
        ->setDescriptionForEvent(fn(string $eventName) => "{$eventName} meal sheet monthly data")
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

    public function meal_sheet_group(): BelongsTo
    {
        return $this->belongsTo(MealSheetGroup::class, 'meal_sheet_group_id');
    }
}
