<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Param extends Model
{
    use HasFactory, HasUuids;
    
    protected $table = 'params';
    protected $fillable = [
        'parent_id',
        'category',
        'param',
        'slug',
        'order',
    ];

    public $timestamps = false;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Param::class, 'parent_id');
    }
}
