<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'departments';
    protected $fillable = [
        'department_code',
        'department'
    ];

    public $timestamps = false;
}
