<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncomingPo extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'incoming_po';
    protected $fillable = [
        'po_number',
        'customer_id',
        'date_received',
        'total',
        'description'
    ];
    protected $casts = [
        'date_received' => 'date'
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

    public function dateReceived(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $date = Carbon::parse($value)->format('Y-m-d');
                $date_timezone = Carbon::createFromFormat('Y-m-d', $date, 'UTC')->setTimezone(config('app.timezone'))->format('Y-m-d');
                return $date_timezone;
            },
        );
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function attachment_file(): HasMany 
    {
        return $this->hasMany(File::class, 'reference_id')->where([
            ['reference_type', 'App\Models\IncomingPo'],
            ['type', 'attachment'],
        ]);
    }
}
