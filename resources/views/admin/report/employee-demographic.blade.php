@section('scripts')
<!-- Pastikan library Chart.js sudah diload di master layout. Jika belum, uncomment baris di bawah ini: -->
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // ====================================================================
        // 1. GRAFIK JENIS KELAMIN (GENDER)
        // ====================================================================
        // Tangkap data dari Controller (menggunakan $genderRatio sesuai compact)
        const rawGenderData = @json($genderRatio ?? []);
        
        // PERBAIKAN: Gunakan .gender (sesuai alias SQL di Controller) bukan .sex
        const genderLabels = rawGenderData.map(data => data.gender);
        const genderTotals = rawGenderData.map(data => data.total);

        const canvasGender = document.getElementById('genderChart');
        if (canvasGender) {
            const ctxGender = canvasGender.getContext('2d');
            new Chart(ctxGender, {
                type: 'doughnut',
                data: {
                    // Fallback jika data kosong
                    labels: genderLabels.length > 0 ? genderLabels : ['Belum Ada Data'],
                    datasets: [{
                        data: genderTotals.length > 0 ? genderTotals : [1],
                        // Tema Warna: Biru Indigo (L), Pink (P), Abu-abu (Belum Diatur)
                        backgroundColor: ['#6366f1', '#ec4899', '#94a3b8'], 
                        hoverBackgroundColor: ['#4f46e5', '#db2777', '#64748b'],
                        borderWidth: 0,
                        hoverOffset: 5 // Efek pop-out saat di-hover
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    cutout: '75%', // Membuat lubang donat lebih tipis dan elegan
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true, // Legend berbentuk bulat, bukan kotak
                                font: { family: "'Inter', sans-serif", size: 12, weight: 'bold' },
                                color: '#64748b'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleFont: { size: 13 },
                            bodyFont: { size: 13, weight: 'bold' },
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    return ` ${label}: ${value} Karyawan`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // ====================================================================
        // 2. GRAFIK TINGKAT PENDIDIKAN (EDUCATION)
        // ====================================================================
        // Tangkap data dari Controller (menggunakan $educationRatio sesuai compact)
        const rawEduData = @json($educationRatio ?? []);
        
        // PERBAIKAN: Gunakan .education (sesuai alias SQL di Controller) bukan .last_education
        const eduLabels = rawEduData.map(data => data.education);
        const eduTotals = rawEduData.map(data => data.total);

        const canvasEdu = document.getElementById('educationChart');
        if (canvasEdu) {
            const ctxEdu = canvasEdu.getContext('2d');
            new Chart(ctxEdu, {
                type: 'doughnut',
                data: {
                    labels: eduLabels.length > 0 ? eduLabels : ['Belum Ada Data'],
                    datasets: [{
                        data: eduTotals.length > 0 ? eduTotals : [1],
                        // Tema Warna Dinamis untuk berbagai tingkat pendidikan
                        backgroundColor: ['#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#94a3b8'], 
                        borderWidth: 0,
                        hoverOffset: 5
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: { family: "'Inter', sans-serif", size: 12, weight: 'bold' },
                                color: '#64748b'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            bodyFont: { size: 13, weight: 'bold' },
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    return ` ${label}: ${value} Karyawan`;
                                }
                            }
                        }
                    }
                }
            });
        }

    });
</script>
@endsection