<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use hisorange\BrowserDetect\Parser as Browser;
use Spatie\Activitylog\Traits\LogsActivity;

class DOCustomer extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'do_customers';
    protected $fillable = [
        'created_by',
        'po_customer_id',
        'serial_number',
        'do_number',
        'approved_by',
        'approved_date',
        'status',
        'note',
    ];

    protected $casts = [
        'approved_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('do customer')
        ->setDescriptionForEvent(fn(string $eventName) => "{$eventName} do customer data")
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

    public function approvedDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if(!empty($value)) {
                    $date = Carbon::parse($value)->format('Y-m-d H:i:s');
                    $date_timezone = Carbon::createFromFormat('Y-m-d H:i:s', $date, 'UTC')->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
                    return $date_timezone;
                } else {
                    return $value;
                }
            },
        );
    }

    public function approved_by_data(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function po_customer(): BelongsTo
    {
        return $this->belongsTo(POCustomer::class, 'po_customer_id');
    }

    public function item_product(): HasMany
    {
        return $this->hasMany(SelectItemProduct::class, 'reference_id')->where('reference_type', 'App\Models\DOCustomer');
    }

    public function attachment_file(): HasMany
    {
        return $this->hasMany(File::class, 'reference_id')->where([
            ['reference_type', 'App\Models\DOCustomer'],
            ['type', 'attachment']
        ]);
    }
}
