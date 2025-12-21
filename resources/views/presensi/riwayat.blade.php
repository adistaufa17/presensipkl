@extends('layouts.app')

@section('page_title', 'Riwayat')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                            <th>Foto</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th>Jurnal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presensi as $index => $p)
                        <tr>
                            <td class="ps-4">{{ $presensi->firstItem() + $index }}</td>
                            <td>
                                <div>
                                    <span class="fw-bold d-block">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($p->tanggal)->locale('id')->isoFormat('dddd') }}</small>
                                </div>
                            </td>
                            <td>
                                @if($p->foto_masuk)
                                    <img src="{{ asset('storage/' . $p->foto_masuk) }}" 
                                         class="rounded-circle border border-2 shadow-sm" 
                                         width="45" 
                                         height="45"
                                         style="object-fit: cover; cursor: pointer;"
                                         onclick="previewFoto('{{ asset('storage/' . $p->foto_masuk) }}', '{{ $p->tanggal }}')"
                                         title="Klik untuk memperbesar">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 45px; height: 45px;">
                                        <i class="bi bi-camera-video-off text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($p->jam_masuk)
                                    <span class="badge bg-success">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                        {{ \Carbon\Carbon::parse($p->tanggal . ' ' . $p->jam_masuk)->timezone('Asia/Jakarta')->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($p->jam_keluar)
                                    <span class="badge bg-danger">
                                        <i class="bi bi-box-arrow-right"></i>
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
                                    <span class="text-muted fw-bold">{{ $durasi }}j {{ $menit }}m</span>
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
                                @if($p->jurnal_kegiatan)
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="showJurnal('{{ addslashes($p->jurnal_kegiatan) }}', '{{ $p->tanggal }}')">
                                        <i class="bi bi-journal-text"></i> Lihat
                                    </button>
                                @elseif($p->keterangan)
                                    <button class="btn btn-sm btn-outline-secondary" 
                                            onclick="showKeterangan('{{ addslashes($p->keterangan) }}')">
                                        <i class="bi bi-info-circle"></i> Info
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-info" 
                                        onclick="showDetail({{ json_encode($p) }})">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
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
.table tbody tr {
    vertical-align: middle;
}
</style>

<script>
// ==================== PREVIEW FOTO ====================
function previewFoto(url, tanggal) {
    Swal.fire({
        title: `📸 Foto Selfie - ${new Date(tanggal).toLocaleDateString('id-ID', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        })}`,
        imageUrl: url,
        imageAlt: 'Foto Selfie Presensi',
        width: 700,
        showCloseButton: true,
        showConfirmButton: false,
        background: '#fff',
        customClass: {
            image: 'img-fluid rounded shadow'
        }
    });
}

// ==================== SHOW JURNAL ====================
function showJurnal(jurnal, tanggal) {
    Swal.fire({
        title: `📝 Jurnal Kegiatan`,
        html: `
            <div class="text-start mb-3">
                <span class="badge bg-primary">${new Date(tanggal).toLocaleDateString('id-ID', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                })}</span>
            </div>
            <div class="text-start bg-light p-4 rounded border" style="white-space: pre-line; max-height: 400px; overflow-y: auto;">
                ${jurnal}
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Tutup',
        width: 700,
        customClass: {
            popup: 'text-start'
        }
    });
}

// ==================== SHOW KETERANGAN ====================
function showKeterangan(keterangan) {
    Swal.fire({
        title: 'ℹ️ Keterangan',
        html: `<div class="text-start bg-light p-3 rounded">${keterangan}</div>`,
        icon: 'info',
        confirmButtonText: 'Tutup',
        width: 600
    });
}

// ==================== SHOW DETAIL LENGKAP ====================
function showDetail(data) {
    const tanggal = new Date(data.tanggal).toLocaleDateString('id-ID', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    
    let durasi = '-';
    if (data.jam_masuk && data.jam_keluar) {
        const masuk = new Date(`${data.tanggal} ${data.jam_masuk}`);
        const keluar = new Date(`${data.tanggal} ${data.jam_keluar}`);
        const diff = keluar - masuk;
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        durasi = `${hours} jam ${minutes} menit`;
    }
    
    const statusBadge = {
        'hadir': '<span class="badge bg-success">HADIR</span>',
        'terlambat': '<span class="badge bg-warning text-dark">TERLAMBAT</span>',
        'izin': '<span class="badge bg-info">IZIN</span>',
        'sakit': '<span class="badge bg-info">SAKIT</span>',
        'alpa': '<span class="badge bg-danger">ALPA</span>'
    };
    
    let fotoHTML = '';
    if (data.foto_masuk) {
        const fotoUrl = `/storage/${data.foto_masuk}`;
        fotoHTML = `
            <div class="mb-3 text-center">
                <img src="${fotoUrl}" class="img-fluid rounded shadow" style="max-height: 300px;">
                <p class="text-muted small mt-2">📸 Foto Selfie Saat Masuk</p>
            </div>
        `;
    }
    
    let jurnalHTML = '';
    if (data.jurnal_kegiatan) {
        jurnalHTML = `
            <div class="alert alert-info text-start">
                <h6 class="fw-bold mb-2">📝 Jurnal Kegiatan:</h6>
                <div style="white-space: pre-line;">${data.jurnal_kegiatan}</div>
            </div>
        `;
    }
    
    let keteranganHTML = '';
    if (data.keterangan) {
        keteranganHTML = `
            <div class="alert alert-secondary text-start">
                <h6 class="fw-bold mb-2">ℹ️ Keterangan:</h6>
                <p class="mb-0">${data.keterangan}</p>
            </div>
        `;
    }
    
    Swal.fire({
        title: `Detail Presensi`,
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <span class="badge bg-primary mb-2">${tanggal}</span>
                    <div>${statusBadge[data.status]}</div>
                </div>
                
                ${fotoHTML}
                
                <table class="table table-sm table-bordered">
                    <tr>
                        <th width="40%">Jam Masuk</th>
                        <td>${data.jam_masuk ? data.jam_masuk.substring(0, 5) : '-'}</td>
                    </tr>
                    <tr>
                        <th>Jam Keluar</th>
                        <td>${data.jam_keluar ? data.jam_keluar.substring(0, 5) : '-'}</td>
                    </tr>
                    <tr>
                        <th>Durasi Kerja</th>
                        <td><strong>${durasi}</strong></td>
                    </tr>
                </table>
                
                ${jurnalHTML}
                ${keteranganHTML}
            </div>
        `,
        width: 800,
        showCloseButton: true,
        confirmButtonText: 'Tutup',
        customClass: {
            popup: 'text-start'
        }
    });
}
</script>
@endsection