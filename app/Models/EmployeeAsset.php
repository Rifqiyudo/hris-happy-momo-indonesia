<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAsset extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel yang terkait dengan model ini.
     *
     * @var string
     */
    protected $table = 'employee_assets';

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     * Menambahkan kolom finansial dan status terbaru.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 
        'asset_name', 
        'asset_brand', 
        'asset_serial_number', 
        'category', 
        'condition', 
        'purchase_price',   // Tambahan Terbaru
        'purchase_date',    // Tambahan Terbaru
        'handover_date', 
        'status',           // Tambahan Terbaru
        'description'
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data tertentu (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'purchase_date' => 'date',
        'handover_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi ke Model User (Karyawan).
     * Setiap aset dimiliki oleh satu karyawan (Nullable jika aset di gudang).
     * Menggunakan withDefault agar tidak error di halaman Daftar Aset jika pemegang kosong.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'name' => 'Tersedia di Gudang',
            'nik' => '-'
        ]);
    }

    /**
     * Accessor untuk memformat harga beli ke Rupiah secara otomatis.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->purchase_price, 0, ',', '.');
    }
}