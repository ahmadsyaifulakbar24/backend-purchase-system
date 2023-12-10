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

class ItemCategory extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'item_categories';
    protected $fillable = [
        'category_code',
        'category',
        'parent_category_id'
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('item category')
        ->setDescriptionForEvent(fn(string $eventName) => "{$eventName} item category data")
        ->logOnlyDirty();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->ip = request()->ip();
        $activity->browser = Browser::browserName();
        $activity->os = Browser::platformName();
    }

    public function parent_category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'parent_category_id');
    }
}
