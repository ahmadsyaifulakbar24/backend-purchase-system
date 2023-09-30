<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cost_centers';
    protected $fillable = [
        'cost_center_code',
        'cost_center'
    ];

    public $timestamps = false;
}
