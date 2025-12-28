@extends('layouts.app')

@section('page_title', 'Manajemen Pembayaran')

@section('content')

<style>
    :root {
        --primary-color: #213448;
        --border-color: #b3b9c4ff;
        --radius: 16px;
    }

    .content-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .content-card-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-color);
        background-color: #fafbfc;
    }

    .content-card-body {
        padding: 20px;
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
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nav-pills .nav-link {
        background: #ffffff;
        color: #495057;
        border: 1px solid var(--border-color);
        font-size: 13px;
        font-weight: 500;
        padding: 10px 20px;
    }

    .nav-pills .nav-link.active {
        background-color: #e7f1ff;
        color: #0d6efd;
        border-color: #0d6efd;
    }

    .nav-pills .nav-link:hover {
        background-color: #f8f9fa;
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
    }

    .table-custom td {
        padding: 16px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
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
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .form-control-custom, .form-select-custom {
        padding: 12px 16px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        background-color: #f8f9fa;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        background-color: #f0f0f0;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(33, 52, 72, 0.1);
    }

    /* Membuat modal preview lebih lebar */
    #modalPreviewImage .modal-dialog {
        max-width:1000px; /* Ukuran maksimal lebar */
    }

    /* Mengatur gambar agar responsif tapi tetap tajam */
    #previewImg {
        width: 100%;
        height: auto;
        object-fit: contain;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    /* Efek zoom saat kursor di atas gambar */
    .zoom-container {
        overflow: auto;
        max-height: 80vh; /* Agar tidak melebihi tinggi layar */
    }
</style>

<div class="container-fluid px-4 py-3">
    
    {{-- STATISTIK CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon me-3" style="background: #fff3cd;">
                    <i class="bi bi-hourglass-split text-warning"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small fw-semibold" style="font-size: 12px; text-transform: uppercase;">Menunggu Konfirmasi</p>
                    {{-- HAPUS ->count() DI SINI --}}
                    <h3 class="stats-number mb-0" style="color: var(--primary-color);">{{ $pembayaranMenunggu ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon me-3" style="background: #d4edda;">
                    <i class="bi bi-check-circle text-success"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small fw-semibold" style="font-size: 12px; text-transform: uppercase;">Total Lunas</p>
                    <h3 class="stats-number mb-0" style="color: var(--primary-color);">{{ $totalLunas ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon me-3" style="background: #f8d7da;">
                    <i class="bi bi-person-x text-danger"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small fw-semibold" style="font-size: 12px; text-transform: uppercase;">Belum Membayar</p>
                    <h3 class="stats-number mb-0" style="color: var(--primary-color);">{{ $totalBelumBayar ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

    {{-- TAB NAVIGATION --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <ul class="nav nav-pills gap-2" id="pills-tab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active rounded-pill" id="pills-transaksi-tab" data-bs-toggle="pill" data-bs-target="#pills-transaksi" type="button">
                    <i class="bi bi-list-ul me-2"></i>Transaksi
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill" id="pills-master-tab" data-bs-toggle="pill" data-bs-target="#pills-master" type="button">
                    <i class="bi bi-database me-2"></i>Master Tagihan
                </button>
            </li>
        </ul>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-action rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahTagihan">
                <i class="bi bi-plus-circle me-2"></i>Buat Tagihan
            </button>
            
            {{-- Tombol Export yang sudah diperbaiki --}}
            <button type="button" onclick="confirmExport()" class="btn btn-dark btn-action rounded-pill px-3">
                <i class="bi bi-file-pdf me-1"></i> Export PDF
            </button>
        </div>
    </div>

    {{-- TAB CONTENT --}}
    <div class="tab-content" id="pills-tabContent">
        {{-- TAB TRANSAKSI --}}
        <div class="tab-pane fade show active" id="pills-transaksi">
            {{-- FILTER --}}
            <div class="content-card mb-4">
                <div class="content-card-body">
                    <form action="" method="GET" class="row g-3">
                        <div class="col-md-7">
                            <div class="input-group">
                                <span class="input-group-text border-0" style="background-color: #f8f9fa;"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-custom border-start-0" placeholder="Cari nama siswa atau nama tagihan...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select form-select-custom">
                                <option value="">Semua Status</option>
                                <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                <option value="dibayar" {{ request('status') == 'dibayar' ? 'selected' : '' }}>Lunas</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark btn-action w-100 rounded-pill">
                                <i class="bi bi-search me-1"></i>Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- TABEL TRANSAKSI --}}
            <div class="content-card">
                <div class="table-responsive">
                    <table class="table table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="px-4">Siswa</th>
                                <th>Tagihan</th>
                                <th>Nominal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembayaran as $p)
                            <tr>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($p->siswa->user->nama_lengkap) }}&background=e0e0e0&color=333" 
                                             class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <div class="fw-semibold text-dark mb-0">{{ $p->siswa->user->nama_lengkap }}</div>
                                            <small class="text-muted">{{ $p->siswa->sekolah->nama_sekolah ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark mb-0">{{ $p->tagihan->nama_tagihan }}</div>
                                    <small class="text-muted">Bulan ke-{{ $p->bulan_ke }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-2">Rp {{ number_format($p->nominal_bayar ?? $p->tagihan->nominal, 0, ',', '.') }}</div>
                                    
                                    {{-- Tombol Aksi Inline --}}
                                    <div class="d-flex gap-1 flex-wrap">
                                        @if($p->bukti_pembayaran)
                                            <button onclick="viewImage('{{ asset('storage/' . $p->bukti_pembayaran) }}')" 
                                                    class="btn btn-sm btn-outline-primary btn-action">
                                                <i class="bi bi-image me-1"></i>Bukti
                                            </button>
                                        @endif

                                        @if($p->status == 'menunggu_konfirmasi')
                                            <form action="{{ route('admin.pembayaran.konfirmasi', $p->id) }}" method="POST" class="d-inline">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="dibayar">
                                                <button type="submit" class="btn btn-sm btn-success btn-action">
                                                    <i class="bi bi-check-lg me-1"></i>Terima
                                                </button>
                                            </form>
                                            <button class="btn btn-sm btn-danger btn-action" data-bs-toggle="modal" data-bs-target="#modalTolak{{ $p->id }}">
                                                <i class="bi bi-x-lg me-1"></i>Tolak
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($p->status == 'menunggu_konfirmasi')
                                        <span class="status-badge" style="background: #fff3cd; color: #ffc107;">MENUNGGU</span>
                                    @elseif($p->status == 'dibayar')
                                        <span class="status-badge" style="background: #d4edda; color: #28a745;">LUNAS</span>
                                    @else
                                        <span class="status-badge" style="background: #f0f0f0; color: #6c757d;">BELUM BAYAR</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-3"></i>
                                    <p class="text-muted fw-semibold mb-1">Tidak ada data pembayaran</p>
                                    <small class="text-muted">Data akan muncul setelah ada transaksi</small>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($pembayaran->hasPages())
                <div class="content-card-body border-top">
                    {{ $pembayaran->links() }}
                </div>
                @endif
            </div>
        </div>

       {{-- TAB MASTER TAGIHAN --}}
        <div class="tab-pane fade" id="pills-master">
            <div class="content-card">
                <div class="content-card-header">
                    <h6 class="section-title">
                        <i class="bi bi-database-fill"></i>
                        Daftar Master Tagihan
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="px-4">Nama Tagihan (Global)</th>
                                <th class="text-center">Nominal</th>
                                <th class="text-center">Target</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tagihans as $t)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-semibold text-dark">{{ $t->nama_tagihan }}</div>
                                    <small class="text-muted">Dibuat: {{ $t->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td class="text-center fw-bold text-primary">
                                    Rp {{ number_format($t->nominal, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-soft-info text-info">Semua Siswa</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        {{-- Tombol Cek Detail --}}
                                        <button type="button" 
                                                onclick="showDetailTagihan('{{ $t->id }}')" 
                                                class="btn btn-sm btn-dark btn-action rounded-pill px-3" title="Lihat Siswa">
                                            <i class="bi bi-people"></i>
                                        </button>

                                        {{-- Tombol Edit Massal --}}
                                        <button type="button" 
                                                onclick="editTagihan(
                                                    '{{ $t->id }}', 
                                                    '{{ $t->nama_tagihan }}', 
                                                    '{{ $t->nominal }}', 
                                                    '{{ $t->tagihanSiswas->max('bulan_ke') ?? 1 }}'
                                                )" 
                                                class="btn btn-sm btn-warning btn-action rounded-pill px-3 text-white" 
                                                title="Edit Tagihan">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- Tombol Hapus Massal --}}
                                        <form action="{{ route('admin.tagihan.destroy', $t->id) }}" method="POST" 
                                            onsubmit="return confirm('Hapus tagihan ini untuk SELURUH siswa? Data transaksi yang terkait juga akan hilang.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger btn-action rounded-pill px-3" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Belum ada master tagihan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MODAL TAMBAH TAGIHAN --}}
        <div class="modal fade" id="modalTambahTagihan" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.tagihan.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Buat Tagihan Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Tagihan</label>
                                <input type="text" name="nama_tagihan" class="form-control" placeholder="Contoh: Uang Kas PKL" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nominal (Per Bulan)</label>
                                <input type="number" name="nominal" class="form-control" placeholder="50000" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Durasi (Bulan)</label>
                                <input type="number" name="jumlah_bulan" class="form-control" value="1" min="1" required>
                                <small class="text-muted">Sistem akan otomatis membuat baris tagihan sebanyak bulan ini.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Jatuh Tempo Pertama</label>
                                <input type="date" name="jatuh_tempo_awal" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Tagihan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL TOLAK --}}
        @foreach($pembayaran as $p)
        <div class="modal fade" id="modalTolak{{ $p->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow" style="border-radius: var(--radius);">
                    <form action="{{ route('admin.pembayaran.konfirmasi', $p->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-body p-4 text-center">
                            <div class="text-danger mb-3"><i class="bi bi-exclamation-octagon-fill fs-1"></i></div>
                            <h6 class="fw-bold mb-3">Alasan Penolakan</h6>
                            <input type="hidden" name="status" value="ditolak">
                            <textarea name="catatan_admin" class="form-control form-control-custom mb-3" rows="3" placeholder="Tulis alasan..." required></textarea>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-light btn-action rounded-pill w-100" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger btn-action rounded-pill w-100">Kirim</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        {{-- MODAL PREVIEW BUKTI --}}
        <div class="modal fade" id="modalPreviewImage" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 bg-transparent">
                    <div class="modal-body p-0 text-center position-relative">
                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                        <img src="" id="previewImg" class="img-fluid rounded-4 shadow" style="max-height: 85vh; border: 4px solid white;">
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL DETAIL PEMBAYAR --}}
        <div class="modal fade" id="modalDetailTagihan" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow" style="border-radius: var(--radius);">
                    <div class="modal-header border-0" style="background: var(--primary-color); color: white; border-top-left-radius: var(--radius); border-top-right-radius: var(--radius);">
                        <div>
                            <h5 class="fw-bold mb-0" id="detailTitle">Detail Tagihan</h5>
                            <small class="opacity-75" id="detailSubtitle"></small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="px-4">Siswa</th>
                                        <th>Bulan</th>
                                        <th>Status</th>
                                        <th>Tgl Bayar</th>
                                    </tr>
                                </thead>
                                <tbody id="detailContent">
                                    <tr><td colspan="4" class="text-center py-4">Memuat data...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL EDIT TAGIHAN --}}
        <div class="modal fade" id="modalEditTagihan" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="formEditTagihan" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Master Tagihan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Tagihan</label>
                                <input type="text" name="nama_tagihan" id="edit_nama" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nominal (Per Bulan)</label>
                                <input type="number" name="nominal" id="edit_nominal" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jumlah Bulan (Total)</label>
                                <input type="number" name="jumlah_bulan" id="edit_jumlah_bulan" class="form-control" min="1" required>
                                <small class="text-danger">*Menambah angka akan menambah tagihan baru, mengurangi angka hanya akan menghapus tagihan yang belum dibayar.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Update Massal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

<script>
    function viewImage(url) {
        document.getElementById('previewImg').src = url;
        var modal = new bootstrap.Modal(document.getElementById('modalPreviewImage'));
        modal.show();
    }

    function confirmExport() {
        Swal.fire({
            title: 'Ekspor Laporan?',
            text: "Sistem akan mendownload laporan pembayaran dalam format PDF.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#213448',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Download!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                const urlParams = new URLSearchParams(window.location.search);
                const exportUrl = "{{ route('admin.pembayaran.export') }}?" + urlParams.toString();

                window.location.href = exportUrl;

                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Laporan sedang didownload.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }, 3000);
            }
        })
    }

    function showDetailTagihan(id) {
        document.getElementById('detailContent').innerHTML = '<tr><td colspan="4" class="text-center py-4">Memuat data...</td></tr>';
        
        var modal = new bootstrap.Modal(document.getElementById('modalDetailTagihan'));
        modal.show();

        fetch(`/admin/tagihan/${id}/detail`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('detailTitle').innerText = data.nama_tagihan;
                document.getElementById('detailSubtitle').innerText = 'Nominal: Rp ' + data.nominal;
                
                let html = '';
                if(data.data.length > 0) {
                    data.data.forEach(p => {
                        let statusBadge = '';
                        if(p.status === 'dibayar') {
                            statusBadge = '<span class="status-badge" style="background: #d4edda; color: #28a745;">Lunas</span>';
                        } else if(p.status === 'menunggu') {
                            statusBadge = '<span class="status-badge" style="background: #fff3cd; color: #ffc107;">Menunggu</span>';
                        } else {
                            statusBadge = '<span class="status-badge" style="background: #f0f0f0; color: #6c757d;">Belum Bayar</span>';
                        }

                        html += `
                            <tr>
                                <td class="px-4">
                                    <div class="fw-semibold small text-dark">${p.siswa.user.nama_lengkap}</div>
                                </td>
                                <td class="small">Bulan ke-${p.bulan_ke}</td>
                                <td class="small">${statusBadge}</td>
                                <td class="small text-muted">${p.tanggal_bayar ? p.tanggal_bayar : '-'}</td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data transaksi.</td></tr>';
                }
                document.getElementById('detailContent').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('detailContent').innerHTML = '<tr><td colspan="4" class="text-center py-4 text-danger">Gagal memuat data.</td></tr>';
            });
    }

    // Tambahkan parameter 'jumlahBulan'
    function editTagihan(id, nama, nominal, jumlahBulan) {
        document.getElementById('formEditTagihan').action = '/admin/tagihan/' + id;
        
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_nominal').value = nominal;
        
        // Set nilai jumlah bulan ke input modal
        document.getElementById('edit_jumlah_bulan').value = jumlahBulan;
        
        var modal = new bootstrap.Modal(document.getElementById('modalEditTagihan'));
        modal.show();
    }


    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{!! session('success') !!}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{!! session('error') !!}',
        });
    @endif
</script>

@endsection