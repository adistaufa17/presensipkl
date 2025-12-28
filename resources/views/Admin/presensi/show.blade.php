@extends('layouts.app')

@section('page_title', 'Riwayat Presensi Siswa')

@section('content')
<div class="container-fluid">

    {{-- HEADER & KEMBALI --}}
    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid #e0e0e0 !important; border-radius: 16px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.presensi') }}" class="btn btn-light rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h5 class="fw-bold mb-1">Riwayat Presensi</h5>
                        <p class="text-muted mb-0 small">Detail kehadiran siswa per bulan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PROFIL SISWA & EXPORT --}}
    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid #e0e0e0 !important; border-radius: 16px;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($siswa->user->nama_lengkap) }}&background=0D6EFD&color=fff&size=80" 
                     class="rounded-3 me-4" width="80" height="80" alt="Avatar">
                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-1">{{ $siswa->user->nama_lengkap }}</h5>
                    <p class="text-muted mb-2 small">
                        <span class="badge bg-light text-dark border me-2 small">{{ $siswa->nisn }}</span>
                        {{ $siswa->sekolah->nama_sekolah }}
                    </p>
                </div>
                <div class="text-end">
                    <form action="{{ route('admin.presensi.export.pdf', $siswa->id) }}" method="GET" class="d-flex gap-2 align-items-center">
                        <input type="month" 
                               name="bulan" 
                               value="{{ $bulan }}" 
                               class="form-control border-0" 
                               style="background-color: #f8f9fa; border-radius: 8px; width: auto;">
                        <button type="submit" class="btn btn-danger" style="border-radius: 8px;">
                            <i class="bi bi-file-pdf me-1"></i> Export PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIK BULAN INI --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid #e0e0e0 !important; border-radius: 12px;">
                <div class="card-body text-center py-4">
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-check-circle-fill text-success fs-3"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-success mb-1">{{ $stats['hadir'] }}</h2>
                    <p class="text-muted mb-0 small">Hari Hadir</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid #e0e0e0 !important; border-radius: 12px;">
                <div class="card-body text-center py-4">
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-exclamation-circle-fill text-warning fs-3"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-warning mb-1">{{ $stats['telat'] }}</h2>
                    <p class="text-muted mb-0 small">Hari Terlambat</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid #e0e0e0 !important; border-radius: 12px;">
                <div class="card-body text-center py-4">
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-file-medical-fill text-info fs-3"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-info mb-1">{{ $stats['izin'] }}</h2>
                    <p class="text-muted mb-0 small">Hari Izin/Sakit</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid #e0e0e0 !important; border-radius: 12px;">
                <div class="card-body text-center py-4">
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-x-circle-fill text-danger fs-3"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-danger mb-1">{{ $stats['alpha'] }}</h2>
                    <p class="text-muted mb-0 small">Hari Alpha</p>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL RIWAYAT --}}
    <div class="card border-0 shadow-sm" style="border: 1px solid #e0e0e0 !important; border-radius: 16px;">
        <div class="card-header bg-white border-0 py-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="bi bi-calendar-check-fill me-2 text-primary"></i>
                    <h5 class="fw-bold mb-0">Riwayat Presensi - {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</h5>
                </div>
                <form action="{{ route('admin.presensi.show', $siswa->id) }}" method="GET">
                    <input type="month" 
                           name="bulan" 
                           value="{{ $bulan }}" 
                           class="form-control border-0" 
                           style="background-color: #f8f9fa; border-radius: 8px; width: 200px;"
                           onchange="this.form.submit()">
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted small">TANGGAL</th>
                            <th class="border-0 py-3 fw-semibold text-muted small">JAM MASUK</th>
                            <th class="border-0 py-3 fw-semibold text-muted small">JAM PULANG</th>
                            <th class="border-0 py-3 fw-semibold text-muted small">STATUS</th>
                            <th class="border-0 py-3 fw-semibold text-muted small">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td class="px-4 py-3">
                                <small class="text-dark fw-semibold">{{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d M Y') }}</small>
                            </td>
                            <td class="py-3">
                                @if($r->jam_masuk)
                                <span class="badge bg-light text-dark border small">
                                    <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($r->jam_masuk)->format('H:i') }}
                                </span>
                                @else
                                <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($r->jam_pulang)
                                <span class="badge bg-light text-dark border small">
                                    <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($r->jam_pulang)->format('H:i') }}
                                </span>
                                @elseif(in_array($r->status_kehadiran, ['hadir', 'telat']))
                                <small class="text-warning">Belum pulang</small>
                                @else
                                <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td class="py-3">
                                @php
                                    $statusColor = match($r->status_kehadiran) {
                                        'hadir' => 'success',
                                        'telat' => 'warning',
                                        'izin', 'sakit' => 'info',
                                        'alpha' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }} small">
                                    {{ strtoupper($r->status_kehadiran) }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if($r->keterangan_izin && !in_array($r->status_kehadiran, ['hadir', 'telat']))
                                    <small class="text-muted">{{ Str::limit($r->keterangan_izin, 50) }}</small>
                                @elseif(in_array($r->status_kehadiran, ['hadir', 'telat']))
                                    <small class="text-muted">-</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-3"></i>
                                <p class="text-muted fw-semibold mb-1">Tidak ada data presensi untuk bulan ini</p>
                                <small class="text-muted">Coba pilih bulan yang berbeda</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($riwayat->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $riwayat->links() }}
        </div>
        @endif
    </div>

</div>

<style>
.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
    background-color: #f0f0f0 !important;
    border-color: #b3b9c4;
}

.btn:focus {
    box-shadow: none;
}

.table > :not(caption) > * > * {
    padding: 0.75rem 1rem;
}
</style>
@endsection