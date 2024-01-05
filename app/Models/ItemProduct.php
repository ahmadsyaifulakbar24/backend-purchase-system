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

class ItemProduct extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table  = 'item_products';
    protected $fillable = [
        'code',
        'name',
        'item_category_id',
        'sub_item_category_id',
        'brand',
        'description',
        'size',
        'unit_id',
        'tax',
        'location_id',
        'supplier_id',
        'price',
        'sell_price',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('item product')
        ->setDescriptionForEvent(fn(string $eventName) => "{$eventName} item product data")
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

    public function item_category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }

    public function sub_item_category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'sub_item_category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Param::class, 'unit_id');
    }

    public function price(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => intval($value),
        );
    }

    public function sellPrice(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => intval($value),
        );
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
