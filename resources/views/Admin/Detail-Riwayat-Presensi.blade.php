@extends('layouts.app')
@section('page_title', 'Riwayat Presensi Siswa')
@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #fcfcfc; min-height: 100vh;">

    {{-- HEADER --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.presensi') }}" class="btn btn-light rounded-circle me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-1">Riwayat Presensi</h4>
                    <p class="text-muted mb-0 small">Detail kehadiran siswa per bulan</p>
                </div>
            </div>
            <a href="{{ route('admin.presensi.export.pdf', ['siswaId' => $siswa->id, 'bulan' => $bulan]) }}" 
               class="btn btn-danger rounded-pill px-4">
                <i class="bi bi-file-pdf me-2"></i>Export PDF
            </a>
        </div>
    </div>

    {{-- PROFIL SISWA --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name={{ $siswa->user->nama_lengkap }}&background=random&size=100" 
                         class="rounded-3 border me-4" width="100">
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1">{{ $siswa->user->nama_lengkap }}</h5>
                        <p class="text-muted mb-2">{{ $siswa->user->email }}</p>
                        <div class="d-flex gap-2">
                            <span class="badge bg-dark">{{ $siswa->sekolah->nama_sekolah ?? 'N/A' }}</span>
                            <span class="badge bg-light text-dark border">Siswa PKL</span>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 d-inline-block">
                            <h2 class="fw-bold text-primary mb-0">{{ $persentaseKehadiran }}%</h2>
                            <small class="text-muted">Kehadiran</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Filter Bulan --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <label class="form-label fw-bold small text-muted mb-2">PILIH BULAN</label>
                <form action="{{ route('admin.presensi.show', $siswa->id) }}" method="GET">
                    <div class="input-group">
                        <input type="month" 
                               name="bulan" 
                               value="{{ $bulan }}" 
                               class="form-control bg-light border-0 rounded-start-3">
                        <button type="submit" class="btn btn-dark rounded-end-3">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
                <small class="text-muted mt-2">Menampilkan: {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</small>
            </div>
        </div>
    </div>

    {{-- STATISTIK BULAN INI --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 d-inline-block mb-2">
                        <i class="bi bi-check-circle-fill text-success fs-3"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ $stats['hadir'] }}</h3>
                    <small class="text-muted">Hari Hadir</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 d-inline-block mb-2">
                        <i class="bi bi-exclamation-circle-fill text-warning fs-3"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $stats['terlambat'] }}</h3>
                    <small class="text-muted">Hari Terlambat</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 p-3 rounded-3 d-inline-block mb-2">
                        <i class="bi bi-file-medical-fill text-info fs-3"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1">{{ $stats['izin'] }}</h3>
                    <small class="text-muted">Hari Izin/Sakit</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-3 d-inline-block mb-2">
                        <i class="bi bi-x-circle-fill text-danger fs-3"></i>
                    </div>
                    <h3 class="fw-bold text-danger mb-1">{{ $stats['alpha'] }}</h3>
                    <small class="text-muted">Hari Alpha</small>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL RIWAYAT --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-calendar-check me-2"></i>
                Riwayat Kehadiran - {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4">No</th>
                            <th class="border-0">Tanggal</th>
                            <th class="border-0">Hari</th>
                            <th class="border-0">Jam Masuk</th>
                            <th class="border-0">Jam Pulang</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Jurnal Kegiatan</th>
                            <th class="border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $index => $r)
                        <tr>
                            <td class="px-4">{{ $riwayat->firstItem() + $index }}</td>
                            <td class="fw-bold small">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-light text-dark border small">
                                    {{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('l') }}
                                </span>
                            </td>
                            <td>
                                @if($r->jam_masuk)
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($r->jam_masuk)->format('H:i') }}
                                </span>
                                @else
                                <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                @if($r->jam_pulang)
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($r->jam_pulang)->format('H:i') }}
                                </span>
                                @else
                                <small class="text-muted">Belum pulang</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColor = match($r->status_kehadiran) {
                                        'hadir' => 'success',
                                        'terlambat' => 'warning',
                                        'izin', 'sakit' => 'info',
                                        'alpha' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">
                                    {{ strtoupper($r->status_kehadiran) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $r->jurnal_kegiatan ? Str::limit($r->jurnal_kegiatan, 40) : 'Belum mengisi' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalDetail{{ $r->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Modal Detail --}}
                        <div class="modal fade" id="modalDetail{{ $r->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-0 bg-dark text-white">
                                        <h6 class="modal-title fw-bold">Detail Presensi - {{ \Carbon\Carbon::parse($r->tanggal)->format('d F Y') }}</h6>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="small fw-bold text-muted">JAM MASUK</label>
                                                <p class="fw-bold">{{ $r->jam_masuk ? \Carbon\Carbon::parse($r->jam_masuk)->format('H:i:s') : '-' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small fw-bold text-muted">JAM PULANG</label>
                                                <p class="fw-bold">{{ $r->jam_pulang ? \Carbon\Carbon::parse($r->jam_pulang)->format('H:i:s') : 'Belum absen pulang' }}</p>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="small fw-bold text-muted">JURNAL KEGIATAN</label>
                                                <div class="bg-light p-3 rounded-3">
                                                    {{ $r->jurnal_kegiatan ?? 'Belum mengisi jurnal kegiatan' }}
                                                </div>
                                            </div>
                                            @if($r->keterangan_izin)
                                            <div class="col-md-12">
                                                <label class="small fw-bold text-muted">KETERANGAN IZIN/SAKIT</label>
                                                <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                                                    {{ $r->keterangan_izin }}
                                                </div>
                                            </div>
                                            @endif
                                            @if($r->foto_masuk)
                                            <div class="col-md-6">
                                                <label class="small fw-bold text-muted">FOTO MASUK</label>
                                                <img src="{{ asset('storage/' . $r->foto_masuk) }}" class="img-fluid rounded-3 border" alt="Foto Masuk">
                                            </div>
                                            @endif
                                            @if($r->bukti_izin)
                                            <div class="col-md-6">
                                                <label class="small fw-bold text-muted">BUKTI IZIN</label>
                                                <img src="{{ asset('storage/' . $r->bukti_izin) }}" class="img-fluid rounded-3 border" alt="Bukti Izin">
                                            </div>
                                            @endif
                                            @if($r->latitude_masuk && $r->longitude_masuk)
                                            <div class="col-md-12">
                                                <label class="small fw-bold text-muted">LOKASI ABSEN</label>
                                                <p class="small">
                                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                                    Lat: {{ $r->latitude_masuk }}, Long: {{ $r->longitude_masuk }}
                                                    <a href="https://www.google.com/maps?q={{ $r->latitude_masuk }},{{ $r->longitude_masuk }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary rounded-pill ms-2">
                                                        <i class="bi bi-map me-1"></i>Lihat di Maps
                                                    </a>
                                                </p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="80" class="opacity-25 mb-3">
                                <p class="text-muted">Tidak ada data presensi untuk bulan ini</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($riwayat->hasPages())
        <div class="card-footer bg-light border-0 py-3">
            {{ $riwayat->links() }}
        </div>
        @endif
    </div>

</div>

<style>
.card {
    border: 1px solid #f0f0f0 !important;
}
.table thead th {
    font-weight: 600;
    font-size: 0.85rem;
    color: #666;
}
</style>
@endsection