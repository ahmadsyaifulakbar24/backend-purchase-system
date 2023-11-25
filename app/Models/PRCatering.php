<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PRCatering extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pr_caterings';
    protected $fillable = [
        'serial_number',
        'pr_number',
        'location_id',
        'pr_date',
        'shipment_date',
        'description',
        'prepared_by',
    ];

    protected $casts = [
        'pr_date' => 'date',
        'shipment_date' => 'date',
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

    public function prDate(): Attribute
    {
        return Attribute::make(
            get: function($value) {
                $date = Carbon::parse($value)->format('Y-m-d');
                $date_timezone = Carbon::createFromFormat('Y-m-d', $date, 'UTC')->setTimezone(config('app.timezone'))->format('Y-m-d');
                return $date_timezone;
            },
        );
    }

    public function shipmentDate(): Attribute
    {
        return Attribute::make(
            get: function($value) {
                $date = Carbon::parse($value)->format('Y-m-d');
                $date_timezone = Carbon::createFromFormat('Y-m-d', $date, 'UTC')->setTimezone(config('app.timezone'))->format('Y-m-d');
                return $date_timezone;
            },
        );
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function prepared_by_data(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function item_product(): HasMany
    {
        return $this->hasMany(SelectItemProduct::class, 'reference_id')->where('reference_type', 'App\Models\PRCatering');
    }

    public function attachment_file(): HasMany
    {
        return $this->hasMany(File::class, 'reference_id')->where([
            ['reference_type', 'App\Models\PRCatering'],
            ['type', 'attachment']
        ]);
    }
}
