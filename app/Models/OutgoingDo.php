<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutgoingDo extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'outgoing_do';
    protected $fillable = [
        'serial_number',
        'do_number',
        'incoming_po_id',
        'customer_id',
        'address',
        'delivery_date',
        'location_id',
        'prepared_by',
        'received_by',
    ];

    protected $casts = [
        'delivery_date' => 'date',
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

    public function deliveryDate(): Attribute
    {
        return Attribute::make(
            get: function($value) {
                $date = Carbon::parse($value)->format('Y-m-d');
                $date_timezone = Carbon::createFromFormat('Y-m-d', $date, 'UTC')->setTimezone(config('app.timezone'))->format('Y-m-d');
                return $date_timezone;
            },
        );
    }

    public function incoming_po(): BelongsTo
    {
        return $this->belongsTo(IncomingPo::class, 'incoming_po_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function prepared_by_data(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function received_by_data(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function item_product(): HasMany
    {
        return $this->hasMany(SelectItemProduct::class, 'reference_id')->where('reference_type', 'App\Models\OutgoingDo');
    }
}
