<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealSheetGroup extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'meal_sheet_groups';
    protected $fillable = [
        'location_id',
        'prepared_by',
        'checked_by',
        'approved_by',
        'acknowladge_by'
    ];

    protected $casts = [
        'prepared_by' => 'array',
        'checked_by' => 'array',
        'approved_by' => 'array',
        'acknowladge_by' => 'array',
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

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function meal_sheet_client(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'meal_sheet_clients', 'meal_sheet_group_id', 'client_id');
    }

    public function meal_sheet_daily(): HasMany
    {
        return $this->hasMany(MealSheetDay::class, 'meal_sheet_group_id');
    }

}
