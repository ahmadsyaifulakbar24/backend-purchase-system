<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'locations';
    protected $fillable = [
        'location_code',
        'location',
        'parent_location_id',
    ];
    public $timestamps = false;
}
