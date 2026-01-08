<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id',
        'name',
        'amount',
        'type', // 'allowance' (penambah) atau 'deduction' (pengurang)
    ];

    // Relasi balik ke Header
    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }
}