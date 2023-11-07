<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealSheetClient extends Model
{
    use HasFactory;

    protected $table = 'meal_sheet_clients';
    protected $fillable = [
        'meal_sheet_client_id',
        'client_id'
    ];

    public $timestamps = false;
    
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
