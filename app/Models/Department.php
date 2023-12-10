<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use hisorange\BrowserDetect\Parser as Browser;
use Spatie\Activitylog\Traits\LogsActivity;

class Department extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'departments';
    protected $fillable = [
        'department_code',
        'department'
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('department')
        ->setDescriptionForEvent(fn(string $eventName) => "{$eventName} department data")
        ->logOnlyDirty();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->ip = request()->ip();
        $activity->browser = Browser::browserName();
        $activity->os = Browser::platformName();
    }
}
