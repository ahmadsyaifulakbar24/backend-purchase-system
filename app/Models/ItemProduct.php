<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemProduct extends Model
{
    use HasFactory, HasUuids;

    protected $table  = 'item_products';
    protected $fillable = [
        'code',
        'name',
        'item_category_id',
        'sub_item_category_id',
        'description',
        'size',
        'unit_id',
        'tax'
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
}
