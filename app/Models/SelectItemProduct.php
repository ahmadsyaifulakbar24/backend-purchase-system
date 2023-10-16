<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectItemProduct extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'select_item_products';
    protected $fillable = [
        'reference_type',
        'reference_id',
        'item_name',
        'item_brand',
        'description',
        'size',
        'weight',
        'unit',
        'quantity',
        'item_price',
        'vat',
        'tnt',
        'remark'
    ];

    public $timestamps = false;
}
