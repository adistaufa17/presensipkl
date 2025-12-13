@extends('layouts.app')
@section('content')
{{-- Load Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid px-4">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Dashboard Admin & Pembimbing</h2>
            <p class="text-muted mb-0">Monitoring kehadiran dan keuangan siswa PKL</p>
            <a href="{{ route('pembimbing.siswa.create') }}"
   class="btn btn-primary mb-3">
    ➕ Tambah Siswa
</a>

        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark border px-3 py-2">{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    {{-- TAB NAVIGATION --}}
    <ul class="nav nav-pills mb-4" id="dashboardTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="kehadiran-tab" data-bs-toggle="pill" data-bs-target="#kehadiran" type="button">
                <i class="bi bi-calendar-check"></i> Monitoring Kehadiran
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="keuangan-tab" data-bs-toggle="pill" data-bs-target="#keuangan" type="button">
                <i class="bi bi-cash-stack"></i> Monitoring Keuangan
            </button>
        </li>
    </ul>

    {{-- TAB CONTENT --}}
    <div class="tab-content" id="dashboardTabContent">
        
        {{-- ========================================= --}}
        {{-- TAB 1: MONITORING KEHADIRAN --}}
        {{-- ========================================= --}}
        <div class="tab-pane fade show active" id="kehadiran" role="tabpanel">
            
            {{-- BARIS 1: KARTU STATISTIK KEHADIRAN --}}
            <div class="row g-4 mb-4">
                {{-- Total Siswa --}}
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small text-uppercase fw-bold">Total Siswa</p>
                                    <h3 class="fw-bold mb-0">{{ $totalSiswa ?? 0 }}</h3>
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
                                    <h3 class="fw-bold mb-0 text-success">{{ $hadirHariIni ?? 0 }}</h3>
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
                                    <h3 class="fw-bold mb-0 text-warning">{{ $terlambatHariIni ?? 0 }}</h3>
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
                                    <h3 class="fw-bold mb-0 text-info">{{ $izinSakitHariIni ?? 0 }}</h3>
                                </div>
                                <div class="bg-info bg-opacity-10 p-2 rounded">
                                    <i class="bi bi-file-medical-fill text-info fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BARIS 2: GRAFIK & AKTIVITAS --}}
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

                {{-- KOLOM KANAN: AKTIVITAS TERBARU --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-0">Aktivitas Terbaru</h6>
                                <small class="text-muted">Pantauan Real-time Hari Ini</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ count($recentActivities ?? []) }} Item</span>
                        </div>
                        
                        {{-- List Aktivitas dengan Scroll --}}
                        <div class="card-body p-0" style="max-height: 450px; overflow-y: auto;">
                            <div class="list-group list-group-flush">
                                @forelse($recentActivities ?? [] as $item)
                                <div class="list-group-item px-4 py-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        {{-- FOTO PROFIL --}}
                                        <div class="position-relative">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($item->user->name ?? 'User') }}&background=random&color=fff&bold=true" 
                                                 class="rounded-circle me-3 border border-2 border-white shadow-sm" 
                                                 width="48" height="48" alt="Avatar">
                                            {{-- Indikator Online --}}
                                            <span class="position-absolute bottom-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle"></span>
                                        </div>
                                        
                                        {{-- DETAIL USER --}}
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
                                                
                                                {{-- Email --}}
                                                <small class="text-muted text-truncate" style="max-width: 120px; font-size: 11px;">
                                                    {{ $item->user->email ?? '-' }}
                                                </small>
                                            </div>
                                        </div>

                                        {{-- STATUS & WAKTU --}}
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

            </div>
        </div>

        {{-- ========================================= --}}
        {{-- TAB 2: MONITORING KEUANGAN --}}
        {{-- ========================================= --}}
        <div class="tab-pane fade" id="keuangan" role="tabpanel">
            
            {{-- RINGKASAN STATISTIK KEUANGAN --}}
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="card-title">Total Tagihan</h6>
                            <h2 class="mb-0">{{ $totalTagihan ?? 0 }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-secondary text-white shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="card-title">Belum Bayar</h6>
                            <h2 class="mb-0">{{ $belumBayar ?? 0 }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-warning text-dark shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="card-title">Pending</h6>
                            <h2 class="mb-0">{{ $pending ?? 0 }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-success text-white shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="card-title">Diterima</h6>
                            <h2 class="mb-0">{{ $diterima ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOTAL NOMINAL DITERIMA --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-light shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Total Nominal Pembayaran Diterima</h6>
                            <h1 class="text-success fw-bold mb-0">Rp {{ number_format($totalNominalDiterima ?? 0, 0, ',', '.') }}</h1>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MENU NAVIGASI --}}
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <a href="{{ route('tagihan.create') }}" class="text-decoration-none">
                        <div class="card shadow-sm border-primary h-100 hover-lift">
                            <div class="card-body text-center">
                                <h1 class="mb-3">➕</h1>
                                <h5>Buat Tagihan Baru</h5>
                                <p class="text-muted mb-0">Tambah tagihan untuk semua siswa</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 mb-3">
                    <a href="{{ route('tagihan.index') }}" class="text-decoration-none">
                        <div class="card shadow-sm border-info h-100 hover-lift">
                            <div class="card-body text-center">
                                <h1 class="mb-3">📋</h1>
                                <h5>Kelola Tagihan</h5>
                                <p class="text-muted mb-0">Lihat dan kelola semua tagihan</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 mb-3">
                    <a href="{{ route('pembayaran.semua') }}" class="text-decoration-none">
                        <div class="card shadow-sm border-success h-100 hover-lift">
                            <div class="card-body text-center">
                                <h1 class="mb-3">💰</h1>
                                <h5>Lihat Pembayaran</h5>
                                <p class="text-muted mb-0">Monitor semua pembayaran siswa</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- PEMBAYARAN PENDING TERBARU --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">⏳ Pembayaran Menunggu Konfirmasi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Siswa</th>
                                    <th>Tagihan</th>
                                    <th>Nominal</th>
                                    <th>Metode</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingPayments ?? [] as $p)
                                <tr>
                                    <td><strong>{{ $p->user->name }}</strong></td>
                                    <td>{{ $p->nama_tagihan }}</td>
                                    <td>Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($p->metode ?? 'N/A') }}</span>
                                    </td>
                                    <td>
                                        @if($p->tanggal_bayar)
                                            {{ $p->tanggal_bayar->format('d M Y H:i') }}
                                        @elseif($p->updated_at)
                                            {{ $p->updated_at->format('d M Y H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('pembayaran.detail', $p->id) }}" class="btn btn-sm btn-primary">
                                            👁️ Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        ✅ Tidak ada pembayaran pending
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- SISWA YANG TELAT BAYAR --}}
            @if(isset($siswaTelat) && $siswaTelat->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">⚠️ Siswa dengan Tagihan Telat</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Jumlah Tagihan Telat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($siswaTelat as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $data['user']->name }}</strong></td>
                                    <td>
                                        <span class="badge bg-danger">{{ $data['jumlah'] }} Tagihan</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('pembayaran.by_siswa', $data['user']->id) }}" class="btn btn-sm btn-info">
                                            📋 Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>

    </div>
</div>

{{-- STYLES --}}
<style>
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
.nav-pills .nav-link {
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
}
.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

{{-- SCRIPT CHART.JS --}}
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Hadir', 'Terlambat', 'Izin/Sakit', 'Alpa'],
            datasets: [{
                label: 'Jumlah Siswa',
                data: [
                    {{ $rekapHadir ?? 0 }}, 
                    {{ $rekapTerlambat ?? 0 }}, 
                    {{ $rekapIzin ?? 0 }}, 
                    {{ $rekapAlpa ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(23, 162, 184, 0.7)',
                    'rgba(220, 53, 69, 0.7)'
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
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection