<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveAllowance extends Model
{
    /**
     * Nama tabel yang didefinisikan di migration.
     */
    protected $table = 'leave_allowances';

    /**
     * Kolom yang dapat diisi secara massal.
     * Mencakup user_id, tahun anggaran, jumlah kuota, dan catatan opsional.
     */
    protected $fillable = [
        'user_id',
        'year',
        'quota',
        'note',
    ];

    /**
     * RELASI: Menghubungkan jatah cuti kembali ke Pemiliknya (Karyawan).
     * Memungkinkan Anda memanggil $allowance->user->name
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * SCOPE: Memudahkan pengambilan kuota untuk tahun berjalan.
     * Penggunaan: LeaveAllowance::currentYear()->where('user_id', $id)->first();
     */
    public function scopeCurrentYear($query)
    {
        return $query->where('year', date('Y'));
    }
}