@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">📊 Rekap Riwayat Presensi Siswa</h2>
            <p class="text-muted mb-0">Monitor kehadiran siswa secara detail</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- FILTER --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('presensi.rekap') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Pilih Siswa</label>
                    <select name="siswa_id" class="form-select">
                        <option value="">-- Semua Siswa --</option>
                        @foreach($siswaList ?? [] as $siswa)
                            <option value="{{ $siswa->id }}" {{ request('siswa_id') == $siswa->id ? 'selected' : '' }}>
                                {{ $siswa->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-bold">Bulan</label>
                    <select name="bulan" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ ($bulan ?? date('n')) == $m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-bold">Tahun</label>
                    <select name="tahun" class="form-select">
                        @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                            <option value="{{ $y }}" {{ ($tahun ?? date('Y')) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('presensi.rekap') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i> Reset Filter
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">✓ Hadir</h6>
                    <h2 class="mb-0">{{ $statsBulanIni['hadir'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">⚠ Terlambat</h6>
                    <h2 class="mb-0">{{ $statsBulanIni['terlambat'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">📄 Izin/Sakit</h6>
                    <h2 class="mb-0">{{ $statsBulanIni['izin'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">✖ Alpa</h6>
                    <h2 class="mb-0">{{ $statsBulanIni['alpa'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL RIWAYAT --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Riwayat Presensi</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presensi ?? [] as $index => $p)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($p->user->name ?? 'User') }}&background=random&color=fff" 
                                         class="rounded-circle me-2" 
                                         width="32" height="32">
                                    <strong>{{ $p->user->name ?? 'N/A' }}</strong>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</td>
                            <td>
                                @if($p->jam_masuk)
                                    <span class="badge bg-success">{{ \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($p->jam_keluar)
                                    <span class="badge bg-danger">{{ \Carbon\Carbon::parse($p->jam_keluar)->format('H:i') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($p->jam_masuk && $p->jam_keluar)
                                    {{ $p->durasi_kerja }} jam
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $p->status_color }}">
                                    {{ strtoupper($p->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('presensi.detail', $p->id) }}" 
                                   class="btn btn-sm btn-primary" 
                                   title="Lihat Detail">
                                    <i class="bi bi-eye-fill"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2">Belum ada data presensi untuk filter ini</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if(isset($presensi) && $presensi->hasPages())
                <div class="mt-4">
                    {{ $presensi->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s;
}
</style>
@endsection