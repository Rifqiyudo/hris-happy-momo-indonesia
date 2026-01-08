<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'generated_date',
        'total_days_worked',
        'total_late_minutes',
        'base_salary',
        'total_allowance',
        'total_deduction',
        'net_salary',
        'status', // 'draft', 'paid'
    ];

    protected $casts = [
        'generated_date' => 'date',
    ];

    // Milik User siapa?
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Punya banyak rincian (details)
    public function details(): HasMany
    {
        return $this->hasMany(PayrollDetail::class);
    }
}