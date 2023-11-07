<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealSheetRecord extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'meal_sheet_records';
    protected $fillable = [
        'meal_sheet_detail_id',
        'name',
        'position',
        'company',
        'breakfast',
        'lunch',
        'dinner',
        'super',
        'accomodation'
    ];

    public $timestamps = false;

    public function meal_sheet_detail(): BelongsTo
    {
        return $this->belongsTo(MealSheetDetail::class, 'meal_sheet_detail_id');
    }
}
