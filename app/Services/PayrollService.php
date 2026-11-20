<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class PayrollService 
{
    /**
     * Menghitung rincian gaji bulanan karyawan secara otomatis.
     */
    public function calculateMonthlySalary($user, $month, $year)
    {
        // Pastikan relasi position sudah dimuat
        $user->loadMissing('position');

        // 1. Hitung Total Kehadiran (Hari)
        $attendanceCount = $user->attendances()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereNotNull('clock_in')
            ->count();

        // 2. Ambil Komponen dari Database (Position & User)
        $dailySalary = $user->position->daily_salary ?? 0;
        $allowance   = $user->position->allowance ?? 0;
        
        // Komponen BPJS & Denda (Poin 4 & 10)
        $latePenaltyPerMinute = $user->late_deduction ?? 5000; // Default 5k jika kolom kosong
        $bpjsKesehatan = $user->bpjs_kesehatan_deduction ?? 0;
        $bpjsKetenagakerjaan = $user->bpjs_ketenagakerjaan_deduction ?? 0;

        // 3. Hitung Denda Keterlambatan (Poin 4)
        $totalLateMinutes = $user->attendances()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('late_minutes');
        
        $totalLatePenalty = $totalLateMinutes * $latePenaltyPerMinute;

        // 4. Kalkulasi Akhir (Poin 12: Gapok dihitung per hari kerja)
        $totalBasicSalary = $attendanceCount * $dailySalary;
        
        // Total Pendapatan Kotor
        $grossSalary = $totalBasicSalary + $allowance;
        
        // Total Potongan
        $totalDeduction = $totalLatePenalty + $bpjsKesehatan + $bpjsKetenagakerjaan;
        
        // Gaji Bersih (Take Home Pay)
        $takeHomePay = $grossSalary - $totalDeduction;

        return [
            'meta' => [
                'name' => $user->name,
                'period' => Carbon::create($year, $month)->translatedFormat('F Y'),
                'position' => $user->position->name ?? 'N/A',
            ],
            'components' => [
                'present_days'       => $attendanceCount,
                'daily_salary_rate'  => $dailySalary,
                'total_basic_salary' => $totalBasicSalary,
                'allowance'          => $allowance,
            ],
            'deductions' => [
                'late_minutes'       => $totalLateMinutes,
                'late_penalty'       => $totalLatePenalty,
                'bpjs_kesehatan'     => $bpjsKesehatan,
                'bpjs_tk'            => $bpjsKetenagakerjaan,
                'total_deduction'    => $totalDeduction,
            ],
            'grand_total' => max(0, $takeHomePay) // Mencegah gaji minus
        ];
    }
}