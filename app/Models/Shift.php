<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Tambahkan ini

class Shift extends Model
{
    use HasFactory, SoftDeletes;

    // Sesuaikan dengan kolom baru di database
    protected $fillable = [
        'name',
        'code',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'late_tolerance',
        'early_leave_tolerance',
        'is_night_shift',
        'is_holiday',
        'color',
        'description'
    ];

    // Opsional: Casting agar outputnya rapi
    protected $casts = [
        'is_night_shift' => 'boolean',
        'is_holiday' => 'boolean',
        'late_tolerance' => 'integer',
    ];
}