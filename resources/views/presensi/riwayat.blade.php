@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">📋 Riwayat Presensi</h2>
            <p class="text-muted mb-0">Lihat semua riwayat kehadiran Anda</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    {{-- STATISTIK BULAN INI --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Hadir</p>
                            <h3 class="fw-bold mb-0 text-success">{{ $statsBulanIni['hadir'] }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Terlambat</p>
                            <h3 class="fw-bold mb-0 text-warning">{{ $statsBulanIni['terlambat'] }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                            <i class="bi bi-exclamation-circle-fill text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Izin/Sakit</p>
                            <h3 class="fw-bold mb-0 text-info">{{ $statsBulanIni['izin'] }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-2 rounded">
                            <i class="bi bi-file-medical-fill text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Alpa</p>
                            <h3 class="fw-bold mb-0 text-danger">{{ $statsBulanIni['alpa'] }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-2 rounded">
                            <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD TABEL RIWAYAT --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Daftar Riwayat Presensi</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Tanggal</th>
                            <th>Hari</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presensi as $index => $p)
                        <tr>
                            <td class="ps-4">{{ $presensi->firstItem() + $index }}</td>
                            <td>
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                            <td>
                                @if($p->jam_masuk)
                                    <span class="badge bg-light text-dark border">
                                        {{ \Carbon\Carbon::parse($p->tanggal . ' ' . $p->jam_masuk)->timezone('Asia/Jakarta')->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($p->jam_keluar)
                                    <span class="badge bg-light text-dark border">
                                        {{ \Carbon\Carbon::parse($p->tanggal . ' ' . $p->jam_keluar)->timezone('Asia/Jakarta')->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($p->jam_masuk && $p->jam_keluar)
                                    @php
                                        $masuk = \Carbon\Carbon::parse($p->tanggal . ' ' . $p->jam_masuk, 'Asia/Jakarta');
                                        $keluar = \Carbon\Carbon::parse($p->tanggal . ' ' . $p->jam_keluar, 'Asia/Jakarta');
                                        $durasi = $masuk->diffInHours($keluar);
                                        $menit = $masuk->diff($keluar)->i;
                                    @endphp
                                    <span class="text-muted">{{ $durasi }}j {{ $menit }}m</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusBadge = match($p->status) {
                                        'hadir' => 'bg-success',
                                        'terlambat' => 'bg-warning text-dark',
                                        'izin' => 'bg-info',
                                        'sakit' => 'bg-info',
                                        'alpa' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge }}">
                                    {{ strtoupper($p->status) }}
                                </span>
                            </td>
                            <td>
                                @if($p->keterangan)
                                    <button class="btn btn-sm btn-outline-secondary" 
                                            data-bs-toggle="tooltip" 
                                            title="{{ $p->keterangan }}">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Belum ada riwayat presensi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        @if($presensi->hasPages())
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Menampilkan {{ $presensi->firstItem() }} - {{ $presensi->lastItem() }} dari {{ $presensi->total() }} data
                </div>
                {{ $presensi->links() }}
            </div>
        </div>
        @endif
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="row g-3 mt-4">
        <div class="col-md-4">
            <a href="{{ route('presensi.izin') }}" class="card text-decoration-none border-0 shadow-sm hover-lift">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark-text fs-1 text-warning mb-2"></i>
                    <h6 class="fw-bold mb-1">Ajukan Izin/Sakit</h6>
                    <small class="text-muted">Jika tidak bisa hadir</small>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('dashboard') }}" class="card text-decoration-none border-0 shadow-sm hover-lift">
                <div class="card-body text-center">
                    <i class="bi bi-house-door fs-1 text-primary mb-2"></i>
                    <h6 class="fw-bold mb-1">Dashboard</h6>
                    <small class="text-muted">Kembali ke dashboard</small>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-month fs-1 text-success mb-2"></i>
                    <h6 class="fw-bold mb-1">Bulan Ini</h6>
                    <small class="text-muted">{{ now()->locale('id')->isoFormat('MMMM YYYY') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
}
</style>

<script>
// Enable Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>
@endsection