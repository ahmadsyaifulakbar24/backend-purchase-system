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

class POCatering extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'po_caterings';
    protected $fillable = [
        'pr_catering_id',
        'serial_number',
        'po_number',
        'discount_id',
        'term_condition',
        'term_payment',
        'prepared_by',
        'checked_by',
        'approved1_by',
        'approved2_by',
        'checked_date',
        'approved1_date',
        'approved2_date',
        'status',
        'note',
    ];

    protected $casts = [
        'checked_date' => 'date', 
        'approved1_date' => 'date',  
        'approved2_date' => 'date',  
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('po catering')
        ->setDescriptionForEvent(fn(string $eventName) => "{$eventName} po catering data")
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

    public function checkedDate(): Attribute
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

    public function approved1Date(): Attribute
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

    public function approved2Date(): Attribute
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

    public function pr_catering(): BelongsTo
    {
        return $this->belongsTo(PRCatering::class, 'pr_catering_id');
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }

    public function prepared_by_data(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function checked_by_data(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function approved1_by_data(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved1_by');
    }

    public function approved2_by_data(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved2_by');
    }

    public function join_item_product(): HasMany
    {
        return $this->hasMany(SelectItemProduct::class, 'reference_id')
                ->join('item_products', 'select_item_products.item_product_id', 'item_products.id')
                ->where('reference_type', 'App\Models\POCatering');
    }

    public function item_product(): HasMany
    {
        return $this->hasMany(SelectItemProduct::class, 'reference_id')->where('reference_type', 'App\Models\POCatering');
    }

    public function attachment_file(): HasMany
    {
        return $this->hasMany(File::class, 'reference_id')->where([
            ['reference_type', 'App\Models\POCatering'],
            ['type', 'attachment']
        ]);
    }
}
