<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    protected $dates = ['deleted_at'];
}
