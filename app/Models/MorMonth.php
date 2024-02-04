<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MorMonth extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'mor_months';
    protected $fillable = [
        'location_id',
        'month',
        'year'
    ];

    public $timestamps = false;

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function mor_month_detail(): HasMany
    {
        return $this->hasMany(MorMonthDetail::class, 'mor_month_id');
    }
}
