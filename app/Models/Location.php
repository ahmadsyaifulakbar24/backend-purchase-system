<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;

class Location extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'locations';
    protected $fillable = [
        'location_code',
        'location',
        'parent_location_id',
    ];
    public $timestamps = false;
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('location')
        ->setDescriptionForEvent(fn(string $eventName) => "{$eventName} location data")
        ->logOnlyDirty();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->ip = request()->ip();
        $activity->browser = Browser::browserName();
        $activity->os = Browser::platformName();
    }

    public function parent_location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_location_id');
    }

    public function mor_month(): HasMany
    {
        return $this->hasMany(MorMonth::class, 'location_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sales::class, 'location_id');
    }

    public function sales_by_month(): HasOne
    {
        return $this->hasOne(Sales::class, 'location_id');
    }
}
