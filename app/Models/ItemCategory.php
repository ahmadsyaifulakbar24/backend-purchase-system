<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemCategory extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'item_categories';
    protected $fillable = [
        'category_code',
        'category',
        'parent_category_id'
    ];

    public $timestamps = false;

    public function parent_category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'parent_category_id');
    }
}
