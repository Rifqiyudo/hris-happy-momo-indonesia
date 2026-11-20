@extends('admin.layouts.master')
@section('title', 'Demografi Karyawan')

@section('content')
<div class="space-y-6 animate-fadeIn">
    
    {{-- HEADER --}}
    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="font-black text-slate-800 text-xl tracking-tight">Demografi Karyawan</h2>
            <p class="text-xs text-slate-500 mt-1">Analitik dan persebaran data karyawan berdasarkan status, departemen, cabang, jenis kelamin, dan pendidikan.</p>
        </div>
        <button onclick="window.print()" class="hidden md:flex w-10 h-10 rounded-full bg-slate-50 text-slate-500 items-center justify-center hover:bg-indigo-50 hover:text-indigo-600 transition-colors shadow-sm" title="Cetak Halaman">
            <i class="fas fa-print"></i>
        </button>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total Karyawan --}}
        <div class="bg-indigo-50 p-6 rounded-[2rem] border border-indigo-100 shadow-sm flex items-center gap-5 hover:scale-[1.02] transition-transform">
            <div class="w-16 h-16 rounded-[1.25rem] bg-indigo-100 text-indigo-600 flex items-center justify-center text-3xl shadow-sm">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Total Karyawan</p>
                <h3 class="text-4xl font-black text-indigo-900">{{ $totalEmployees ?? 0 }}</h3>
            </div>
        </div>

        {{-- Karyawan Aktif --}}
        <div class="bg-emerald-50 p-6 rounded-[2rem] border border-emerald-100 shadow-sm flex items-center gap-5 hover:scale-[1.02] transition-transform">
            <div class="w-16 h-16 rounded-[1.25rem] bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl shadow-sm">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-1">Karyawan Aktif</p>
                <h3 class="text-4xl font-black text-emerald-900">{{ $activeEmployees ?? 0 }}</h3>
            </div>
        </div>

        {{-- Karyawan Non-Aktif / Resign --}}
        <div class="bg-rose-50 p-6 rounded-[2rem] border border-rose-100 shadow-sm flex items-center gap-5 hover:scale-[1.02] transition-transform">
            <div class="w-16 h-16 rounded-[1.25rem] bg-rose-100 text-rose-600 flex items-center justify-center text-3xl shadow-sm">
                <i class="fas fa-user-times"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-1">Non-Aktif / Resign</p>
                <h3 class="text-4xl font-black text-rose-900">{{ $inactiveEmployees ?? 0 }}</h3>
            </div>
        </div>
    </div>

    {{-- CHART AREA --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Chart Departemen (Doughnut) --}}
        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col">
            <h3 class="font-black text-slate-800 text-base uppercase tracking-widest mb-6 text-center">
                <i class="fas fa-sitemap text-indigo-500 mr-2"></i> Rasio Departemen
            </h3>
            
            <div class="w-full flex-1 min-h-[360px] relative flex justify-center items-center">
                @if(isset($departmentRatio) && count($departmentRatio) > 0)
                    <canvas id="deptChart"></canvas>
                @else
                    <div class="text-center text-slate-400">
                        <i class="fas fa-folder-open text-4xl mb-2 opacity-50"></i>
                        <p class="text-xs font-bold uppercase tracking-widest">Belum Ada Data</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Chart Cabang (Pie) --}}
        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col">
            <h3 class="font-black text-slate-800 text-base uppercase tracking-widest mb-6 text-center">
                <i class="fas fa-building text-indigo-500 mr-2"></i> Rasio Cabang
            </h3>
            
            <div class="w-full flex-1 min-h-[360px] relative flex justify-center items-center">
                @if(isset($branchRatio) && count($branchRatio) > 0)
                    <canvas id="branchChart"></canvas>
                @else
                    <div class="text-center text-slate-400">
                        <i class="fas fa-folder-open text-4xl mb-2 opacity-50"></i>
                        <p class="text-xs font-bold uppercase tracking-widest">Belum Ada Data</p>
                    </div>
                @endif
            </div>
        </div>
        
        {{-- Chart Jenis Kelamin (Pie) --}}
        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col">
            <h3 class="font-black text-slate-800 text-base uppercase tracking-widest mb-6 text-center">
                <i class="fas fa-venus-mars text-indigo-500 mr-2"></i> Jenis Kelamin
            </h3>
            
            <div class="w-full flex-1 min-h-[360px] relative flex justify-center items-center">
                @if(isset($genderRatio) && count($genderRatio) > 0)
                    <canvas id="genderChart"></canvas>
                @else
                    <div class="text-center text-slate-400">
                        <i class="fas fa-folder-open text-4xl mb-2 opacity-50"></i>
                        <p class="text-xs font-bold uppercase tracking-widest">Belum Ada Data</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Chart Pendidikan (Doughnut) --}}
        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col">
            <h3 class="font-black text-slate-800 text-base uppercase tracking-widest mb-6 text-center">
                <i class="fas fa-graduation-cap text-indigo-500 mr-2"></i> Tingkat Pendidikan
            </h3>
            
            <div class="w-full flex-1 min-h-[360px] relative flex justify-center items-center">
                @if(isset($educationRatio) && count($educationRatio) > 0)
                    <canvas id="educationChart"></canvas>
                @else
                    <div class="text-center text-slate-400">
                        <i class="fas fa-folder-open text-4xl mb-2 opacity-50"></i>
                        <p class="text-xs font-bold uppercase tracking-widest">Belum Ada Data</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- CSS untuk Print Mode agar rapi --}}
<style>
    @media print {
        aside, header, form, button, nav { display: none !important; }
        body, main { background: white !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .shadow-sm { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        .bg-indigo-50 { background-color: #f8fafc !important; }
        .bg-emerald-50 { background-color: #f8fafc !important; }
        .bg-rose-50 { background-color: #f8fafc !important; }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Palet warna modern untuk grafik
    const chartColors = [
        '#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', 
        '#06b6d4', '#f97316', '#ec4899', '#14b8a6', '#84cc16'
    ];
    
    // Warna khusus untuk gender (Laki-laki, Perempuan, Belum Diatur)
    const genderColors = ['#3b82f6', '#ec4899', '#94a3b8']; 
    
    // Palet warna khusus pendidikan
    const eduColors = ['#0ea5e9', '#0284c7', '#0369a1', '#14b8a6', '#0f766e', '#64748b'];

    // Konfigurasi Font Global Chart.js
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';
    
    const globalLegend = {
        position: 'bottom',
        labels: {
            font: { weight: 'bold', size: 12 },
            padding: 20,
            usePointStyle: true,
            boxWidth: 10
        }
    };
    
    const globalTooltip = {
        backgroundColor: '#1e293b',
        titleFont: { size: 13 },
        bodyFont: { size: 12, weight: 'bold' },
        padding: 12,
        cornerRadius: 12
    };

    // 1. RENDER CHART DEPARTEMEN
    @if(isset($departmentRatio) && count($departmentRatio) > 0)
        const deptData = @json($departmentRatio);
        const deptLabels = deptData.map(item => item.department ? item.department.name : 'Belum Diatur');
        const deptTotals = deptData.map(item => item.total);

        new Chart(document.getElementById('deptChart'), {
            type: 'doughnut',
            data: {
                labels: deptLabels,
                datasets: [{
                    data: deptTotals,
                    backgroundColor: chartColors,
                    borderWidth: 0,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%', 
                layout: { padding: 10 },
                plugins: {
                    legend: globalLegend,
                    tooltip: globalTooltip
                }
            }
        });
    @endif

    // 2. RENDER CHART CABANG
    @if(isset($branchRatio) && count($branchRatio) > 0)
        const branchData = @json($branchRatio);
        const branchLabels = branchData.map(item => item.branch ? item.branch.name : 'Belum Diatur');
        const branchTotals = branchData.map(item => item.total);
        const reverseColors = [...chartColors].reverse();

        new Chart(document.getElementById('branchChart'), {
            type: 'pie',
            data: {
                labels: branchLabels,
                datasets: [{
                    data: branchTotals,
                    backgroundColor: reverseColors,
                    borderWidth: 0,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: 10 },
                plugins: {
                    legend: globalLegend,
                    tooltip: globalTooltip
                }
            }
        });
    @endif
    
    // 3. RENDER CHART GENDER
    @if(isset($genderRatio) && count($genderRatio) > 0)
        const genderRatioData = @json($genderRatio);
        const genderLabels = genderRatioData.map(item => item.gender);
        const genderTotals = genderRatioData.map(item => item.total);
        
        const mappedGenderColors = genderLabels.map(gender => {
           let g = gender.toLowerCase();
           if(g.includes('laki') || g === 'l' || g === 'm') return genderColors[0];
           if(g.includes('perempuan') || g === 'p' || g === 'f') return genderColors[1];
           return genderColors[2]; // Warna abu-abu untuk 'Belum Diatur'
        });

        new Chart(document.getElementById('genderChart'), {
            type: 'pie',
            data: {
                labels: genderLabels,
                datasets: [{
                    data: genderTotals,
                    backgroundColor: mappedGenderColors,
                    borderWidth: 0,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: 10 },
                plugins: {
                    legend: globalLegend,
                    tooltip: globalTooltip
                }
            }
        });
    @endif
    
    // 4. RENDER CHART PENDIDIKAN
    @if(isset($educationRatio) && count($educationRatio) > 0)
        const eduData = @json($educationRatio);
        const eduLabels = eduData.map(item => item.education);
        const eduTotals = eduData.map(item => item.total);

        new Chart(document.getElementById('educationChart'), {
            type: 'doughnut',
            data: {
                labels: eduLabels,
                datasets: [{
                    data: eduTotals,
                    backgroundColor: eduColors,
                    borderWidth: 0,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%', 
                layout: { padding: 10 },
                plugins: {
                    legend: globalLegend,
                    tooltip: globalTooltip
                }
            }
        });
    @endif
</script>
@endsection