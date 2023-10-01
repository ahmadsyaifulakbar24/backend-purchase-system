<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
