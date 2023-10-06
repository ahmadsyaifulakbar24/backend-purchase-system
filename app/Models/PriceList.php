<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceList extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'price_lists';
    protected $fillable = [
        'location_id',
        'supplier_id',
        'item_product_id',
        'price'
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

    public function price(): Attribute
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

    public function item_product(): BelongsTo
    {
        return $this->belongsTo(ItemProduct::class, 'item_product_id');
    }
}
