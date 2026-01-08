@extends('layouts.app')

@section('page_title', 'Manajemen Absensi')

@section('content')

<style>
    :root {
        --primary-color: #213448;
        --border-color: #b3b9c4ff;
        --radius: 16px;
    }

    .content-wrapper-fixed {
        padding: 0 12px;
    }

    .content-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .content-card-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-color);
        background-color: #fafbfc;
    }

    .stats-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 20px;
        transition: transform 0.2s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .table-custom {
        width: 100%;
    }

    .table-custom thead {
        background: #f8f9fa;
        border-bottom: 1px solid var(--border-color);
    }

    .table-custom th {
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 600;
        color: #717171;
        border: none;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .table-custom td {
        padding: 14px 16px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
        vertical-align: middle;
    }

    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }

    .btn-action {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .form-control-custom, .form-select-custom {
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 13px;
        background-color: #f8f9fa;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        background-color: #f0f0f0;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(33, 52, 72, 0.1);
    }

    .text-nowrap-ellipsis {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }

    .siswa-info {
        max-width: 200px;
    }

    .image-preview-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
    }

    .clickable-image {
        transition: transform 0.3s ease, filter 0.3s ease;
    }

    .clickable-image:hover {
        transform: scale(1.03);
        filter: brightness(0.95);
    }

    .image-overlay {
        position: absolute;
        top: 8px;
        right: 8px;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 10;
    }

    .image-preview-wrapper:hover .image-overlay {
        opacity: 1;
    }

    .image-overlay .btn {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.9) !important;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .image-overlay .btn:hover {
        background-color: rgba(255, 255, 255, 1) !important;
        transform: scale(1.1);
    }

    #modalImagePreview .modal-dialog {
        max-width: 90vw;
    }

    #modalImagePreview .modal-content {
        background: rgba(0, 0, 0, 0.95) !important;
    }

    #modalImagePreview .modal-header,
    #modalImagePreview .modal-footer {
        background: transparent;
    }

    #modalImagePreview img {
        cursor: zoom-in;
        transition: transform 0.3s ease;
    }

    #modalImagePreview img:hover {
        transform: scale(1.02);
    }
</style>

<div class="content-wrapper-fixed">
    {{-- STATISTIK RINGKAS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-check-circle-fill text-success fs-3"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-success mb-1 text-center">{{ $stats['hadir'] ?? 0 }}</h2>
                <p class="text-muted mb-0 small text-center">Hadir</p>
                @if(($stats['total'] ?? 0) > 0)
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: {{ ($stats['hadir']/$stats['total'])*100 }}%"></div>
                </div>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-exclamation-circle-fill text-warning fs-3"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-warning mb-1 text-center">{{ $stats['terlambat'] ?? 0 }}</h2>
                <p class="text-muted mb-0 small text-center">Terlambat</p>
                @if(($stats['total'] ?? 0) > 0)
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar bg-warning" style="width: {{ ($stats['terlambat']/$stats['total'])*100 }}%"></div>
                </div>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-file-medical-fill text-info fs-3"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-info mb-1 text-center">{{ $stats['izin'] ?? 0 }}</h2>
                <p class="text-muted mb-0 small text-center">Izin/Sakit</p>
                @if(($stats['total'] ?? 0) > 0)
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar bg-info" style="width: {{ ($stats['izin']/$stats['total'])*100 }}%"></div>
                </div>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-x-circle-fill text-danger fs-3"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-danger mb-1 text-center">{{ $stats['alpha'] ?? 0 }}</h2>
                <p class="text-muted mb-0 small text-center">Alpha</p>
                @if(($stats['total'] ?? 0) > 0)
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar bg-danger" style="width: {{ ($stats['alpha']/$stats['total'])*100 }}%"></div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="content-card mb-4">
        <div class="content-card-header">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-funnel-fill me-2 text-primary"></i>
                    <h6 class="fw-bold mb-0">Filter Data Presensi</h6>
                </div>
                <button type="button" class="btn btn-success btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalExportRekap">
                    <i class="bi bi-file-pdf me-1"></i>Export PDF
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.presensi') }}" method="GET">
                <div class="row g-3">
                    {{-- Filter Sekolah --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-2">SEKOLAH</label>
                        <select name="sekolah" class="form-select form-select-custom">
                            <option value="all">Semua Sekolah</option>
                            @foreach($sekolahs as $s)
                            <option value="{{ $s->id }}" {{ $sekolah == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_sekolah }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- Filter Tanggal --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-2">TANGGAL</label>
                        <input type="date" 
                               name="tanggal" 
                               value="{{ $tanggal }}" 
                               class="form-control form-control-custom">
                    </div>
                    
                    {{-- Filter Rentang --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-2">DARI</label>
                        <input type="date" 
                               name="tanggal_mulai" 
                               value="{{ $tanggalMulai }}" 
                               class="form-control form-control-custom">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-2">SAMPAI</label>
                        <input type="date" 
                               name="tanggal_akhir" 
                               value="{{ $tanggalAkhir }}" 
                               class="form-control form-control-custom">
                    </div>
                    
                    {{-- Filter Status --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-2">STATUS</label>
                        <select name="status" class="form-select form-select-custom">
                            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="hadir" {{ $status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="terlambat" {{ $status == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                            <option value="izin" {{ $status == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ $status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="alpha" {{ $status == 'alpha' ? 'selected' : '' }}>Alpha</option>
                        </select>
                    </div>
                    
                    {{-- Tombol Filter --}}
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-dark w-100 btn-action">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                
                {{-- Search & Reset --}}
                <div class="row g-3 mt-2">
                    <div class="col-md-10">
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               class="form-control form-control-custom" 
                               placeholder="🔍 Cari nama siswa atau email...">
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.presensi') }}" class="btn btn-light w-100 border btn-action">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL PRESENSI --}}
    <div class="content-card">
        <div class="content-card-header">
            <h6 class="fw-bold mb-0">Data Presensi</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">NO</th>
                        <th style="min-width: 200px;">SISWA</th>
                        <th style="min-width: 150px;">SEKOLAH</th>
                        <th style="width: 100px;">TANGGAL</th>
                        <th style="width: 90px;">MASUK</th>
                        <th style="width: 90px;">PULANG</th>
                        <th style="width: 100px;">STATUS</th>
                        <th style="width: 140px;" class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensis as $index => $p)
                    <tr>
                        <td>{{ $presensis->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center siswa-info">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($p->siswa->user->nama_lengkap) }}&background=e0e0e0&color=333" 
                                     class="rounded-2 me-2" width="36" height="36">
                                <div style="min-width: 0;">
                                    <div class="fw-semibold small text-nowrap-ellipsis" title="{{ $p->siswa->user->nama_lengkap }}">
                                        {{ $p->siswa->user->nama_lengkap }}
                                    </div>
                                    <small class="text-muted text-nowrap-ellipsis d-block" title="{{ $p->siswa->user->email }}">
                                        {{ $p->siswa->user->email }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-nowrap-ellipsis" title="{{ $p->siswa->sekolah->nama_sekolah ?? 'N/A' }}">
                                <span class="badge bg-light text-dark border small" style="font-weight: 500;">
                                    {{ Str::limit($p->siswa->sekolah->nama_sekolah ?? 'N/A', 20) }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <small class="text-dark">{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            @if($p->jam_masuk)
                            <span class="badge bg-light text-dark border small">
                                {{ \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') }}
                            </span>
                            @else
                            <small class="text-muted">-</small>
                            @endif
                        </td>
                        <td>
                            @if($p->jam_pulang)
                            <span class="badge bg-light text-dark border small">
                                {{ \Carbon\Carbon::parse($p->jam_pulang)->format('H:i') }}
                            </span>
                            @else
                            <small class="text-muted">-</small>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusColor = match($p->status_kehadiran) {
                                    'hadir' => 'success',
                                    'terlambat' => 'warning',
                                    'izin', 'sakit' => 'info',
                                    'alpha' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="status-badge bg-{{ $statusColor }} text-white">
                                {{ strtoupper($p->status_kehadiran) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-sm btn-outline-primary btn-action" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalDetail{{ $p->id }}"
                                        title="Detail">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <a href="{{ route('admin.presensi.show', $p->siswa_id) }}" 
                                   class="btn btn-sm btn-dark btn-action"
                                   title="Riwayat">
                                    <i class="bi bi-clock-history"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    {{-- Modal Detail Presensi --}}
                    <div class="modal fade" id="modalDetail{{ $p->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 shadow" style="border-radius: var(--radius);">
                                <div class="modal-header border-0" style="background: var(--primary-color); color: white; border-top-left-radius: var(--radius); border-top-right-radius: var(--radius);">
                                    <h5 class="modal-title fw-bold">Detail Presensi</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="small fw-semibold text-muted mb-2">NAMA SISWA</label>
                                            <p class="fw-semibold mb-0">{{ $p->siswa->user->nama_lengkap }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-semibold text-muted mb-2">SEKOLAH</label>
                                            <p class="mb-0">{{ $p->siswa->sekolah->nama_sekolah ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small fw-semibold text-muted mb-2">TANGGAL</label>
                                            <p class="mb-0">{{ \Carbon\Carbon::parse($p->tanggal)->format('d F Y') }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small fw-semibold text-muted mb-2">JAM MASUK</label>
                                            <p class="mb-0">{{ $p->jam_masuk ? \Carbon\Carbon::parse($p->jam_masuk)->format('H:i:s') : '-' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small fw-semibold text-muted mb-2">JAM PULANG</label>
                                            <p class="mb-0">{{ $p->jam_pulang ? \Carbon\Carbon::parse($p->jam_pulang)->format('H:i:s') : 'Belum pulang' }}</p>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="small fw-semibold text-muted mb-2">JURNAL KEGIATAN</label>
                                            <p class="mb-0 p-3 border rounded-3" style="background-color: #f8f9fa;">{{ $p->jurnal_kegiatan ?? 'Belum mengisi jurnal' }}</p>
                                        </div>
                                        @if($p->keterangan_izin)
                                        <div class="col-md-12">
                                            <label class="small fw-semibold text-muted mb-2">KETERANGAN IZIN</label>
                                            <p class="mb-0 p-3 border rounded-3 bg-warning bg-opacity-10">{{ $p->keterangan_izin }}</p>
                                        </div>
                                        @endif
                                        
                                        {{-- FOTO MASUK - WITH PREVIEW --}}
                                        @if($p->foto_masuk)
                                        <div class="col-md-6">
                                            <label class="small fw-semibold text-muted mb-2">FOTO MASUK</label>
                                            <div class="position-relative image-preview-wrapper">
                                                <img src="{{ asset('storage/' . $p->foto_masuk) }}" 
                                                    class="img-fluid rounded-3 border clickable-image" 
                                                    alt="Foto Masuk"
                                                    onclick="showImagePreview('{{ asset('storage/' . $p->foto_masuk) }}', 'Foto Masuk')"
                                                    style="cursor: pointer; transition: all 0.3s ease;">
                                                <div class="image-overlay">
                                                    <button type="button" 
                                                            class="btn btn-light btn-sm rounded-circle me-1"
                                                            onclick="showImagePreview('{{ asset('storage/' . $p->foto_masuk) }}', 'Foto Masuk')"
                                                            title="Perbesar">
                                                        <i class="bi bi-zoom-in"></i>
                                                    </button>
                                                    <a href="{{ asset('storage/' . $p->foto_masuk) }}" 
                                                    target="_blank" 
                                                    class="btn btn-light btn-sm rounded-circle"
                                                    title="Buka di tab baru">
                                                        <i class="bi bi-box-arrow-up-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        {{-- BUKTI IZIN - WITH PREVIEW --}}
                                        @if($p->bukti_izin)
                                        <div class="col-md-6">
                                            <label class="small fw-semibold text-muted mb-2">BUKTI IZIN</label>
                                            <div class="position-relative image-preview-wrapper">
                                                <img src="{{ asset('storage/' . $p->bukti_izin) }}" 
                                                    class="img-fluid rounded-3 border clickable-image" 
                                                    alt="Bukti Izin"
                                                    onclick="showImagePreview('{{ asset('storage/' . $p->bukti_izin) }}', 'Bukti Izin')"
                                                    style="cursor: pointer; transition: all 0.3s ease;">
                                                <div class="image-overlay">
                                                    <button type="button" 
                                                            class="btn btn-light btn-sm rounded-circle me-1"
                                                            onclick="showImagePreview('{{ asset('storage/' . $p->bukti_izin) }}', 'Bukti Izin')"
                                                            title="Perbesar">
                                                        <i class="bi bi-zoom-in"></i>
                                                    </button>
                                                    <a href="{{ asset('storage/' . $p->bukti_izin) }}" 
                                                    target="_blank" 
                                                    class="btn btn-light btn-sm rounded-circle"
                                                    title="Buka di tab baru">
                                                        <i class="bi bi-box-arrow-up-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- MODAL PREVIEW GAMBAR (LETAKKAN DI LUAR LOOP, SEKALI SAJA) --}}
                    {{-- Tambahkan ini setelah semua modal detail, sebelum @endsection --}}
                    <div class="modal fade" id="modalImagePreview" tabindex="-1" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                            <div class="modal-content bg-transparent border-0">
                                <div class="modal-header border-0 p-2">
                                    <h6 class="modal-title text-white fw-bold" id="imagePreviewTitle"></h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-2 text-center">
                                    <img src="" id="imagePreviewSrc" class="img-fluid rounded-3" style="max-height: 85vh; box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
                                </div>
                                <div class="modal-footer border-0 p-2 justify-content-center">
                                    <a href="" id="imagePreviewLink" target="_blank" class="btn btn-light btn-sm rounded-pill px-4">
                                        <i class="bi bi-box-arrow-up-right me-2"></i>Buka di Tab Baru
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-3"></i>
                            <p class="text-muted fw-semibold mb-1">Tidak ada data presensi</p>
                            <small class="text-muted">Coba ubah filter pencarian Anda</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($presensis->hasPages())
        <div class="card-footer bg-white border-0 border-top py-3">
            {{ $presensis->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal Export Rekap PDF --}}
<div class="modal fade" id="modalExportRekap" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: var(--radius);">
            <div class="modal-header border-0 bg-success text-white" style="border-top-left-radius: var(--radius); border-top-right-radius: var(--radius);">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-pdf me-2"></i>Export Rekap PDF</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.presensi.export.rekap') }}" method="GET" id="formExportPDF">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted mb-2">PILIH SEKOLAH</label>
                        <select name="sekolah_id" id="sekolah_id" class="form-select form-select-custom" required>
                            <option value="">-- Pilih Sekolah --</option>
                            @foreach($sekolahs as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_sekolah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted mb-2">BULAN</label>
                        <input type="month" name="bulan" id="bulan_export" value="{{ \Carbon\Carbon::now()->format('Y-m') }}" class="form-control form-control-custom" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" onclick="confirmExport()" class="btn btn-success w-100 btn-action">
                        <i class="bi bi-download me-2"></i>Download PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmExport() {
    const sekolah = document.getElementById('sekolah_id');
    const bulan = document.getElementById('bulan_export');
    
    if (sekolah.value === "" || bulan.value === "") {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Silakan pilih sekolah dan bulan terlebih dahulu!',
            confirmButtonColor: '#198754'
        });
        return;
    }

    const namaSekolah = sekolah.options[sekolah.selectedIndex].text;

    Swal.fire({
        title: 'Konfirmasi Export',
        text: `Apakah Anda yakin ingin mengunduh rekap PDF untuk ${namaSekolah} periode ${bulan.value}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Download!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formExportPDF').submit();
            
            var myModalEl = document.getElementById('modalExportRekap');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide();
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'File PDF sedang diproses...',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

function showImagePreview(imageUrl, title) {
    document.getElementById('imagePreviewSrc').src = imageUrl;
    document.getElementById('imagePreviewTitle').textContent = title;
    document.getElementById('imagePreviewLink').href = imageUrl;
    
    const modal = new bootstrap.Modal(document.getElementById('modalImagePreview'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    const previewImg = document.getElementById('imagePreviewSrc');
    if (previewImg) {
        previewImg.addEventListener('click', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalImagePreview'));
            if (modal) {
                modal.hide();
            }
        });
    }
});
</script>
@endsection