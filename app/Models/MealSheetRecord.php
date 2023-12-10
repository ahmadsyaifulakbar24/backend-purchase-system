<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use hisorange\BrowserDetect\Parser as Browser;
use Spatie\Activitylog\Traits\LogsActivity;

class MealSheetRecord extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'meal_sheet_records';
    protected $fillable = [
        'meal_sheet_detail_id',
        'name',
        'position',
        'company',
        'breakfast',
        'lunch',
        'dinner',
        'super',
        'accomodation'
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('meal sheet record')
        ->setDescriptionForEvent(fn(string $eventName) => "{$eventName} meal sheet record data")
        ->logOnlyDirty();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->ip = request()->ip();
        $activity->browser = Browser::browserName();
        $activity->os = Browser::platformName();
    }

    public function meal_sheet_detail(): BelongsTo
    {
        return $this->belongsTo(MealSheetDetail::class, 'meal_sheet_detail_id');
    }
}
