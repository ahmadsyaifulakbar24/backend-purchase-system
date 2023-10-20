<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelectItemProduct extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'select_item_products';
    protected $fillable = [
        'reference_type',
        'reference_id',
        'item_product_id',
        'description',
        'weight',
        'quantity',
        'item_price',
        'vat',
        'tnt',
        'remark'
    ];

    public $timestamps = false;

    public function item_product(): BelongsTo
    {
        return $this->belongsTo(ItemProduct::class, 'item_product_id');
    }
}
