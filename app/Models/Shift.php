<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'day_number', // 1=Senin, 6=Sabtu
        'clock_in',
        'clock_out',
        'is_holiday'
    ];

    // Helper: Ambil Shift hari ini otomatis
    public static function getCurrentShift()
    {
        // Carbon::now()->dayOfWeekIso mengembalikan 1 (Senin) s/d 7 (Minggu)
        $today = Carbon::now()->dayOfWeekIso; 
        
        return self::where('day_number', $today)->first();
    }
}