<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    /**
     * fillable diupdate untuk mendukung fitur Grid Absensi Kolektif, Import Excel
     * serta fitur Kunjungan (Visit) di masa mendatang agar konsisten dengan tabel database.
     */
    protected $fillable = [
        'user_id',
        'date',
        'clock_in',          // Digunakan pada Grid Input Absensi
        'clock_out',         // Jam pulang sudah ditambahkan di sini
        'latitude',
        'longitude',
        'address',           // Alamat lengkap dari GPS (Untuk fitur Visit nanti)
        'category',          // absen, visit, meeting, dll
        'image_in',
        'image_out',
        'image_visit',       // Foto khusus untuk kunjungan
        'late_minutes',
        'is_late',
        'status',            // Hadir, Cuti, Izin, Sakit, Alpha/Mangkir (Digunakan di Import Excel)
    ];

    /**
     * Casting diperketat dengan format datetime:Y-m-d H:i:s
     * Solusi permanen untuk error "Double time specification".
     */
    protected $casts = [
        'date'         => 'date:Y-m-d',
        'is_late'      => 'boolean',
        'late_minutes' => 'integer',
        'created_at'   => 'datetime:Y-m-d H:i:s',
        'updated_at'   => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Relasi ke User (Karyawan)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Karyawan Tidak Ditemukan'
        ]);
    }

    /**
     * Accessor: Deteksi Keterlambatan Real-time Dinamis berdasarkan Shift (Bukan hardcode 08:00 WIB)
     */
    public function getCheckIsLateAttribute()
    {
        if (!$this->clock_in) return false;
        
        // Memastikan clock_in benar-benar berformat waktu (HH:MM / HH:MM:SS) sebelum parsing
        if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)/', $this->clock_in)) {
            return false;
        }
        
        try {
            $clockInTime = Carbon::parse($this->clock_in);
            
            // Ambil jadwal shift karyawan, jika kosong gunakan default 08:00:00
            $shiftIn = ($this->user && $this->user->shift) ? $this->user->shift->start_time : '08:00:00';
            
            // Pastikan deadline menggunakan tanggal yang sama dengan record absensi
            $deadline = Carbon::parse($this->date->format('Y-m-d') . ' ' . $shiftIn);

            return $clockInTime->gt($deadline);
        } catch (\Exception $e) {
            // Jika format clock_in salah ketik oleh HRD (bukan jam), anggap tidak telat
            return false;
        }
    }

    /**
     * Accessor: Hitung Menit Terlambat Secara Otomatis Berdasarkan Shift Pegawai
     */
    public function getLateDurationAttribute()
    {
        if (!$this->check_is_late) return 0;

        try {
            $clockInTime = Carbon::parse($this->clock_in);
            
            $shiftIn = ($this->user && $this->user->shift) ? $this->user->shift->start_time : '08:00:00';
            $deadline = Carbon::parse($this->date->format('Y-m-d') . ' ' . $shiftIn);

            return (int) $clockInTime->diffInMinutes($deadline);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Accessor: Hitung Total Jam Kerja (Antara In dan Out)
     */
    public function getWorkDurationAttribute()
    {
        if (!$this->clock_in || !$this->clock_out) return '0h 0m';

        // Validasi format jam sebelum dihitung untuk mencegah error exception
        if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)/', $this->clock_in) || !preg_match('/^([01]\d|2[0-3]):([0-5]\d)/', $this->clock_out)) {
             return '-';
        }

        try {
            $in = Carbon::parse($this->clock_in);
            $out = Carbon::parse($this->clock_out);
            
            $diff = $in->diff($out);
            return $diff->format('%hh %im');
        } catch (\Exception $e) {
            return 'Invalid Format';
        }
    }

    /**
     * Accessor: Format Jam Masuk & Pulang (H:i)
     */
    public function getInTimeAttribute()
    {
        try {
            return $this->clock_in ? Carbon::parse($this->clock_in)->format('H:i') : '--:--';
        } catch (\Exception $e) {
            return $this->clock_in; // Kembalikan string asli jika bukan format jam
        }
    }

    public function getOutTimeAttribute()
    {
        try {
            return $this->clock_out ? Carbon::parse($this->clock_out)->format('H:i') : '--:--';
        } catch (\Exception $e) {
            return $this->clock_out;
        }
    }

    /**
     * Accessor: Badge Status Dinamis
     */
    public function getStatusBadgeAttribute()
    {
        $currentStatus = ($this->check_is_late && strtolower($this->status) == 'hadir') ? 'telat' : $this->status;

        return match(strtolower($currentStatus)) {
            'hadir', 'present'   => 'bg-emerald-50 text-emerald-600 border-emerald-100',
            'telat', 'late'      => 'bg-rose-50 text-rose-600 border-rose-100',
            'izin', 'permit'     => 'bg-blue-50 text-blue-600 border-blue-100',
            'cuti', 'leave'      => 'bg-amber-50 text-amber-600 border-amber-100',
            'sakit', 'sick'      => 'bg-purple-50 text-purple-600 border-purple-100',
            'alpha', 'mangkir'   => 'bg-slate-100 text-slate-500 border-slate-200',
            default              => 'bg-gray-50 text-gray-400 border-gray-100',
        };
    }

    /**
     * Accessor: Label Kategori (Absen vs Visit)
     */
    public function getCategoryLabelAttribute()
    {
        return match(strtolower($this->category ?? 'absen')) {
            'visit'   => '<span class="text-indigo-600 font-bold"><i class="fas fa-map-marker-alt"></i> VISIT</span>',
            'meeting' => '<span class="text-purple-600 font-bold"><i class="fas fa-handshake"></i> MEETING</span>',
            default   => '<span class="text-slate-400">ABSENSI</span>',
        };
    }

    /**
     * Scope: Filter berdasarkan rentang tanggal
     */
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }

    /**
     * Scope: Filter absensi hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', Carbon::today());
    }
}