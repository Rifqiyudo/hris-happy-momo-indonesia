<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'amount', 
        'type' // 'daily' (harian) atau 'monthly' (bulanan)
    ];
}