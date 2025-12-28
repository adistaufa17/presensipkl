@extends('layouts.app')

@section('page_title', 'Dashboard Admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root {
        --primary-color: #213448;
        --border-color: #b3b9c4ff;
        --radius: 16px;
    }

    .dashboard-wrapper {
        padding: 1.5rem; 
        max-width: 100%;
    }

    .dashboard-content-custom {
        padding: 0 12px; 
    }

    .stats-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 20px;
        transition: transform 0.2s ease;
    }

    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stats-number {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 2px;
    }
    
    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #212529;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .content-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        overflow: hidden;
        height: 100%;
    }

    .content-card-header {
        padding: 16px 24px; 
        border-bottom: 1px solid var(--border-color);
        background-color: #fafbfc;
    }

    .content-card-body {
        padding: 20px;
    }

    .activity-item {
        padding: 16px;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s ease;
    }

    .activity-item:hover {
        background-color: #f8f9fa;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .btn-action {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-action-accept {
        background: #28a745;
        color: white;
    }

    .btn-action-accept:hover {
        background: #218838;
    }

    .btn-action-reject {
        background: #dc3545;
        color: white;
    }

    .btn-action-reject:hover {
        background: #c82333;
    }

    .table-custom {
        width: 100%;
    }

    .table-custom thead {
        background: #f8f9fa;
    }

    .table-custom th {
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 600;
        color: #495057;
        border: none;
    }

    .table-custom td {
        padding: 16px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
    }

    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }

    .modal-custom .modal-content {
        border: none;
        border-radius: var(--radius);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    }

    .modal-custom .modal-body {
        padding: 32px;
    }

    .form-control-custom {
        padding: 12px 16px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
    }

    .form-control-custom:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(33, 52, 72, 0.1);
    }

    .custom-row-gap {
        --bs-gutter-x: 24px;
        --bs-gutter-y: 24px;
    }
</style>

<div class="container-fluid px-4 py-3">
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="content-card">
                <div class="content-card-header d-flex justify-content-between align-items-center">
                    <h6 class="section-title mb-0">
                        <i class="bi bi-calendar-check"></i>
                        Statistik Presensi Hari Ini
                    </h6>
                    
                    {{-- Tombol Generate Alpa --}}
                    <form action="{{ route('admin.presensi.alpha') }}" method="POST" id="formAlpha" class="m-0">
                        @csrf
                        <button type="button" onclick="confirmAlpha()" 
                            class="btn btn-sm {{ $alpaHariIni > 0 ? 'btn-light text-muted' : 'btn-outline-danger' }} fw-bold" 
                            style="border-radius: 8px; font-size: 11px;">
                            <i class="bi {{ $alpaHariIni > 0 ? 'bi-arrow-clockwise' : 'bi-person-x' }}"></i> 
                            {{ $alpaHariIni > 0 ? 'RE-SCAN ALPA' : 'CEK SISWA ALPA' }}
                        </button>
                    </form>
                </div>
                <div class="content-card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <p class="text-muted mb-1" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Total Siswa</p>
                            <h3 class="fw-bold mb-0" style="font-size: 28px; color: #333;">{{ $totalSiswa ?? 0 }}</h3>                        </div>

                        <div class="col-6 col-md-4">
                            <p class="text-muted mb-1" style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #28a745;">Hadir</p>
                            <h3 class="fw-bold mb-0" style="font-size: 28px; color: #28a745;">{{ $hadirHariIni ?? 0 }}</h3>
                        </div>

                        <div class="col-6 col-md-4">
                            <p class="text-muted mb-1" style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #ffc107;">Telat</p>
                            <h3 class="fw-bold mb-0" style="font-size: 28px; color: #ffc107;">{{ $terlambatHariIni ?? 0 }}</h3>
                        </div>

                        <div class="col-6 col-md-4">
                            <p class="text-muted mb-1" style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #17a2b8;">Izin</p>
                            <h3 class="fw-bold mb-0" style="font-size: 28px; color: #17a2b8;">{{ $izinSakitHariIni ?? 0 }}</h3>
                        </div>

                        <div class="col-6 col-md-4">
                            <p class="text-muted mb-1" style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #dc3545;">Alpa</p>
                            <h3 class="fw-bold mb-0" style="font-size: 28px; color: #dc3545;">{{ $alpaHariIni ?? 0 }}</h3>
                        </div>

                        <div class="col-6 col-md-4">
                            <p class="text-muted mb-1" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Kehadiran %</p>
                            <h3 class="fw-bold mb-0" style="font-size: 28px; color: #6f42c1;">
                                {{ $totalSiswa > 0 ? number_format((($hadirHariIni + $terlambatHariIni) / $totalSiswa) * 100, 1) : 0 }}%
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="content-card">
                <div class="content-card-header">
                    <h6 class="section-title mb-0">
                        <i class="bi bi-wallet2"></i>
                        Statistik Pembayaran Bulan Ini
                    </h6>
                </div>
                <div class="content-card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-1" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Total Tagihan</p>
                                    <h3 class="fw-bold mb-1" style="font-size: 28px;">{{ $totalTagihan ?? 0 }}</h3>
                                    <small class="text-muted" style="font-size: 12px;">Bulan ini</small>
                                </div>
                                <div class="stats-icon" style="background: #f0f0f0;">
                                    <i class="bi bi-receipt" style="color: #6c757d;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-1" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Lunas</p>
                                    <h3 class="fw-bold mb-1" style="font-size: 28px; color: #28a745;">{{ $tagihanLunas ?? 0 }}</h3>
                                    <small style="font-size: 12px; color: #28a745;">
                                        {{ $totalTagihan > 0 ? number_format(($tagihanLunas / $totalTagihan) * 100, 1) : 0 }}% terbayar
                                    </small>
                                </div>
                                <div class="stats-icon" style="background: #d4edda;">
                                    <i class="bi bi-check2-circle" style="color: #28a745;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-1" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Perlu Konfirmasi</p>
                                    <h3 class="fw-bold mb-1" style="font-size: 28px; color: #ffc107;">{{ $tagihanMenunggu ?? 0 }}</h3>
                                    <small style="font-size: 12px; color: #ffc107;">Bukti masuk</small>
                                </div>
                                <div class="stats-icon" style="background: #fff3cd;">
                                    <i class="bi bi-hourglass-split" style="color: #ffc107;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-1" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Belum Bayar</p>
                                    <h3 class="fw-bold mb-1" style="font-size: 28px; color: #dc3545;">{{ $tagihanBelumBayar ?? 0 }}</h3>
                                    <small style="font-size: 12px; color: #dc3545;">Perlu ditagih</small>
                                </div>
                                <div class="stats-icon" style="background: #f8d7da;">
                                    <i class="bi bi-x-circle-fill" style="color: #dc3545;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Grafik Presensi -->
        <div class="col-lg-4">
            <div class="content-card">
                <div class="content-card-header">
                    <h6 class="fw-bold mb-1" style="font-size: 15px;">Grafik Presensi Bulanan</h6>
                    <small class="text-muted" style="font-size: 12px;">Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</small>
                </div>
                <div class="content-card-body">
                    <canvas id="attendanceChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Pembayaran -->
        <div class="col-lg-4">
            <div class="content-card">
                <div class="content-card-header">
                    <h6 class="fw-bold mb-1" style="font-size: 15px;">Status Pembayaran</h6>
                    <small class="text-muted" style="font-size: 12px;">Overview status tagihan</small>
                </div>
                <div class="content-card-body d-flex align-items-center justify-content-center">
                    <canvas id="paymentChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Pembagian Sekolah -->
        <div class="col-lg-4">
            <div class="content-card">
                <div class="content-card-header">
                    <h6 class="fw-bold mb-1" style="font-size: 15px;">Pembagian Per Sekolah</h6>
                    <small class="text-muted" style="font-size: 12px;">Distribusi siswa PKL</small>
                </div>
                <div class="content-card-body d-flex align-items-center justify-content-center">
                    <canvas id="schoolChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Aktivitas Terbaru -->
        <div class="col-lg-6">
            <div class="content-card">
                <div class="content-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1" style="font-size: 15px;">Aktivitas Terbaru</h6>
                        <small class="text-muted" style="font-size: 12px;">Real-time monitoring</small>
                    </div>
                    <span class="status-badge" style="background: #e7f1ff; color: #0d6efd;">
                        {{ count($recentActivities ?? []) }}
                    </span>
                </div>
                
                <div style="max-height: 400px; overflow-y: auto;">
                    @forelse($recentActivities ?? [] as $item)
                    <div class="activity-item">
                        <div class="d-flex align-items-start gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($item->siswa->user->nama_lengkap ?? 'User') }}&background=0D6EFD&color=fff" 
                                 class="rounded-circle" 
                                 width="40" height="40">
                            
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 fw-bold" style="font-size: 14px;">{{ $item->siswa->user->nama_lengkap ?? 'User' }}</h6>
                                    @php
                                        $statusColor = match($item->status_kehadiran ?? '') {
                                            'hadir' => ['bg' => '#d4edda', 'color' => '#28a745'],
                                            'telat' => ['bg' => '#fff3cd', 'color' => '#ffc107'],
                                            'izin', 'sakit' => ['bg' => '#d1ecf1', 'color' => '#17a2b8'],
                                            'alpa' => ['bg' => '#f8d7da', 'color' => '#dc3545'],
                                            default => ['bg' => '#f0f0f0', 'color' => '#6c757d']
                                        };
                                    @endphp
                                    <span class="status-badge" style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['color'] }};">
                                        {{ strtoupper($item->status_kehadiran ?? '-') }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted" style="font-size: 12px;">
                                        <i class="bi bi-clock me-1"></i>
                                        @if($item->status_kehadiran == 'alpa')
                                            Tidak Ada Catatan
                                        @else
                                            Absen Jam: {{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}
                                        @endif
                                    </small>
                                    <small class="text-primary" style="font-size: 11px; font-weight: 600;">
                                        {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="bi bi-clock-history text-muted" style="font-size: 48px;"></i>
                        <h6 class="fw-bold text-muted mt-3 mb-1">Belum ada aktivitas</h6>
                        <small class="text-muted" style="font-size: 12px;">Data akan muncul saat ada aktivitas</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Pembayaran Perlu Konfirmasi (Preview) -->
        <div class="col-lg-6">
            @if(($tagihanMenunggu ?? 0) > 0)
            <div class="content-card">
                <div class="content-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1" style="font-size: 15px;">
                            <i class="bi bi-bell-fill text-warning me-2"></i>
                            Pembayaran Menunggu
                        </h6>
                        <small class="text-muted" style="font-size: 12px;">Perlu konfirmasi segera</small>
                    </div>
                    <a href="{{ route('admin.pembayaran.index') }}" class="btn-action" style="background: var(--primary-color); color: white; text-decoration: none;">
                        Lihat Semua
                    </a>
                </div>
                <div style="max-height: 400px; overflow-y: auto;">
                    @foreach(($pembayaranMenunggu ?? [])->take(5) as $p)
                    <div class="activity-item">
                        <div class="d-flex align-items-start gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($p->siswa->user->nama_lengkap) }}&background=FFC107&color=fff" 
                                 class="rounded-circle" width="40" height="40">
                            
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <h6 class="mb-0 fw-bold" style="font-size: 14px;">{{ $p->siswa->user->nama_lengkap }}</h6>
                                        <small class="text-muted" style="font-size: 12px;">{{ $p->tagihan->nama_tagihan }} - Bulan ke-{{ $p->bulan_ke }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold" style="font-size: 14px; color: var(--primary-color);">
                                            Rp{{ number_format($p->tagihan->nominal, 0, ',', '.') }}
                                        </div>
                                        <small class="text-muted" style="font-size: 11px;">
                                            {{ \Carbon\Carbon::parse($p->updated_at)->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="{{ asset('storage/' . $p->bukti_pembayaran) }}" 
                                       target="_blank" 
                                       class="btn-action flex-grow-1 text-center" 
                                       style="background: #e7f1ff; color: #0d6efd; text-decoration: none; font-size: 12px;">
                                        <i class="bi bi-image me-1"></i>Lihat Bukti
                                    </a>
                                    <form action="{{ route('admin.pembayaran.konfirmasi', $p->id) }}" method="POST" class="flex-grow-1">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="dibayar">
                                        <button type="submit" class="btn-action btn-action-accept w-100" style="font-size: 12px;">
                                            <i class="bi bi-check-lg me-1"></i>Terima
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="content-card">
                <div class="content-card-header">
                    <h6 class="fw-bold mb-1" style="font-size: 15px;">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Pembayaran Menunggu
                    </h6>
                    <small class="text-muted" style="font-size: 12px;">Status pembayaran</small>
                </div>
                <div class="content-card-body text-center py-5">
                    <i class="bi bi-check-circle text-success" style="font-size: 48px;"></i>
                    <h6 class="fw-bold text-success mt-3 mb-1">Semua Pembayaran Terkonfirmasi</h6>
                    <small class="text-muted" style="font-size: 12px;">Tidak ada pembayaran yang perlu dikonfirmasi</small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
const ctxAttendance = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(ctxAttendance, {
    type: 'bar',
    data: {
        labels: ['Hadir', 'Telat', 'Izin/Sakit', 'Alpa'],
        datasets: [{
            label: 'Jumlah Siswa',
            data: [
                {{ $hadirHariIni ?? 0 }}, 
                {{ $terlambatHariIni ?? 0 }}, 
                {{ $izinSakitHariIni ?? 0 }}, 
                {{ $alpaHariIni ?? 0 }}
            ],
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(23, 162, 184, 0.8)',
                'rgba(220, 53, 69, 0.8)'
            ],
            borderColor: [
                '#28a745',
                '#ffc107',
                '#17a2b8',
                '#dc3545'
            ],
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { 
                    stepSize: 1,
                    font: { size: 11 }
                },
                grid: { color: '#f0f0f0' }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 } }
            }
        },
        plugins: {
            legend: { display: false }
        }
    }
});

const ctxPayment = document.getElementById('paymentChart').getContext('2d');
const paymentChart = new Chart(ctxPayment, {
    type: 'doughnut',
    data: {
        labels: ['Lunas', 'Menunggu', 'Belum Bayar'],
        datasets: [{
            data: [
                {{ $tagihanLunas ?? 0 }}, 
                {{ $tagihanMenunggu ?? 0 }}, 
                {{ $tagihanBelumBayar ?? 0 }}
            ],
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(220, 53, 69, 0.8)'
            ],
            borderColor: '#fff',
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 11 }
                }
            }
        }
    }
});

const ctxSchool = document.getElementById('schoolChart').getContext('2d');

const schoolData = @json($sekolahData ?? []);
const schoolLabels = schoolData.map(s => s.nama_sekolah || 'Sekolah');
const schoolCounts = schoolData.map(s => s.siswa_count || 0);

const colors = [
    '#0d6efd', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
    '#6f42c1', '#fd7e14', '#20c997', '#6610f2', '#e83e8c'
];

const schoolChart = new Chart(ctxSchool, {
    type: 'doughnut',
    data: {
        labels: schoolLabels.length > 0 ? schoolLabels : ['Belum ada data'],
        datasets: [{
            data: schoolCounts.length > 0 ? schoolCounts : [1],
            backgroundColor: schoolLabels.length > 0 
                ? schoolLabels.map((_, i) => colors[i % colors.length] + 'CC')
                : ['#e0e0e0'],
            borderColor: '#fff',
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 12,
                    font: { size: 10 },
                    boxWidth: 12
                }
            }
        }
    }
});

function confirmAlpha() {
    const sekarang = new Date();
    const jam = sekarang.getHours();

    if (jam < 9) {
        if (!confirm('Peringatan: Ini masih pagi (sebelum jam 09:00). Jika Anda klik sekarang, siswa yang datang telat nanti harus diubah manual. Yakin?')) {
            return;
        }
    }

    if (confirm('Sistem akan menandai semua siswa yang belum absen hari ini sebagai "Alpha". Lanjutkan?')) {
        document.getElementById('formAlpha').submit();
    }
}
</script>
@endsection