@extends('layouts.app')

@section('content')
{{-- Load Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Dashboard Admin</h2>
            <p class="text-muted mb-0">Overview kehadiran siswa PKL hari ini.</p>
        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark border px-3 py-2">{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    {{-- BARIS 1: KARTU STATISTIK --}}
    <div class="row g-4 mb-4">
        {{-- Total Siswa --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Total Siswa</p>
                            <h3 class="fw-bold mb-0">{{ $totalSiswa }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="bi bi-people-fill text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hadir Hari Ini --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Hadir Hari Ini</p>
                            <h3 class="fw-bold mb-0 text-success">{{ $hadirHariIni }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Terlambat --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Terlambat</p>
                            <h3 class="fw-bold mb-0 text-warning">{{ $terlambatHariIni }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                            <i class="bi bi-exclamation-circle-fill text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Izin / Sakit --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Izin / Sakit</p>
                            <h3 class="fw-bold mb-0 text-info">{{ $izinSakitHariIni }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-2 rounded">
                            <i class="bi bi-file-medical-fill text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS 2: GRAFIK & LIVE TRACKING --}}
    <div class="row g-4">
        
        {{-- KOLOM KIRI: GRAFIK BULANAN --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">Ringkasan Kehadiran Bulan Ini</h6>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" height="120"></canvas>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: AKTIVITAS TERBARU (MONITORING DETAIL) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0">Aktivitas Terbaru</h6>
                        <small class="text-muted">Pantauan Real-time Hari Ini</small>
                    </div>
                    <span class="badge bg-primary rounded-pill">{{ count($recentActivities) }} Item</span>
                </div>
                
                {{-- List Aktivitas dengan Scroll --}}
                <div class="card-body p-0" style="max-height: 450px; overflow-y: auto;">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivities as $item)
                        <div class="list-group-item px-4 py-3 border-bottom">
                            <div class="d-flex align-items-center">
                                {{-- 1. FOTO PROFIL --}}
                                <div class="position-relative">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($item->user->name ?? 'User') }}&background=random&color=fff&bold=true" 
                                         class="rounded-circle me-3 border border-2 border-white shadow-sm" 
                                         width="48" height="48" alt="Avatar">
                                    {{-- Indikator Online Hijau --}}
                                    <span class="position-absolute bottom-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle"></span>
                                </div>
                                
                                {{-- 2. DETAIL USER (GABUNGAN MONITORING) --}}
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="mb-0 fw-bold text-truncate text-dark">{{ $item->user->name ?? 'User Terhapus' }}</h6>
                                    </div>
                                    
                                    <div class="d-flex align-items-center gap-2">
                                        {{-- Badge Role --}}
                                        @php
                                            $roleBadge = match($item->user->role ?? '') {
                                                'admin' => 'bg-dark',
                                                'pembimbing' => 'bg-primary',
                                                'siswa' => 'bg-secondary',
                                                default => 'bg-light text-dark border'
                                            };
                                        @endphp
                                        <span class="badge {{ $roleBadge }}" style="font-size: 9px;">{{ strtoupper($item->user->role ?? 'USER') }}</span>
                                        
                                        {{-- Email Kecil --}}
                                        <small class="text-muted text-truncate" style="max-width: 120px; font-size: 11px;">
                                            {{ $item->user->email ?? '-' }}
                                        </small>
                                    </div>
                                </div>

                                {{-- 3. STATUS & WAKTU --}}
                                <div class="text-end ms-2">
                                    @php
                                        $statusColor = match($item->status) {
                                            'hadir' => 'success',
                                            'terlambat' => 'warning',
                                            'izin' => 'info',
                                            'sakit' => 'info',
                                            'alpa' => 'danger',
                                            default => 'secondary'
                                        };
                                        
                                        // Deteksi jam terakhir (Masuk atau Pulang)
                                        $waktuTampil = $item->jam_keluar ? $item->jam_keluar : $item->jam_masuk;
                                        $ketWaktu = $item->jam_keluar ? 'Pulang' : 'Masuk';
                                    @endphp
                                    
                                    <span class="badge bg-{{ $statusColor }} mb-1">{{ strtoupper($item->status) }}</span>
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="fw-bold text-dark" style="font-size: 13px;">
                                            {{ \Carbon\Carbon::parse($waktuTampil)->format('H:i') }}
                                        </span>
                                        <small class="text-muted" style="font-size: 9px;">{{ $ketWaktu }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center p-5">
                            <i class="bi bi-clock-history text-muted fs-1 mb-3"></i>
                            <h6 class="fw-bold text-muted">Belum ada aktivitas</h6>
                            <small class="text-muted">Data akan muncul saat ada yang absen hari ini.</small>
                        </div>
                        @endforelse
                    </div>
                </div>
                
                <div class="card-footer bg-light text-center py-2">
                    <a href="#" class="text-decoration-none small fw-bold text-primary">Lihat Semua Riwayat</a>
                </div>
            </div>
        </div>

{{-- SCRIPT CHART.JS --}}
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Hadir', 'Terlambat', 'Izin/Sakit', 'Alpa'],
            datasets: [{
                label: 'Jumlah Siswa',
                data: [{{ $rekapHadir }}, {{ $rekapTerlambat }}, {{ $rekapIzin }}, {{ $rekapAlpa }}],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',  // Hijau (Hadir)
                    'rgba(255, 193, 7, 0.7)',   // Kuning (Terlambat)
                    'rgba(23, 162, 184, 0.7)',  // Biru (Izin)
                    'rgba(220, 53, 69, 0.7)'    // Merah (Alpa)
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 } // Agar angka bulat (orang)
                }
            },
            plugins: {
                legend: { display: false } // Sembunyikan legenda atas karena warna sudah jelas
            }
        }
    });

</script>
@endsection