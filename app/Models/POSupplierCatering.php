<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class POSupplierCatering extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'po_supplier_caterings';
    protected $fillable = [
        'created_by',
        'po_catering_id',
        'serial_number',
        'po_number',
        'supplier_id',
        'discount_id',
        'term_condition',
        'status',
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

    public function po_catering(): BelongsTo
    {
        return $this->belongsTo(POCatering::class, 'po_catering_id');
    }

    public function do_catering(): HasOne
    {
        return $this->hasOne(DOCatering::class, 'po_supplier_catering_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }

    public function created_by_data(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function item_product(): HasMany
    {
        return $this->hasMany(SelectItemProduct::class, 'reference_id')->where('reference_type', 'App\Models\POSupplierCatering');
    }

    public function attachment_file(): HasMany
    {
        return $this->hasMany(File::class, 'reference_id')->where([
            ['reference_type', 'App\Models\POSupplierCatering'],
            ['type', 'attachment']
        ]);
    }
}
