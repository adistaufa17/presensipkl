@extends('layouts.app')

@section('page_title', 'Manajemen Absensi')

@section('content')
<div class="container-fluid">

    {{-- STATISTIK RINGKAS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid #e0e0e0 !important; border-radius: 12px;">
                <div class="card-body text-center py-4">
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-check-circle-fill text-success fs-3"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-success mb-1">{{ $stats['hadir'] ?? 0 }}</h2>
                    <p class="text-muted mb-0 small">Hadir</p>
                    @if($stats['total'] > 0)
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ ($stats['hadir']/$stats['total'])*100 }}%"></div>
                    </div>
                    @endif
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
                    <h2 class="fw-bold text-warning mb-1">{{ $stats['telat'] ?? 0 }}</h2>
                    <p class="text-muted mb-0 small">Terlambat</p>
                    @if($stats['total'] > 0)
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: {{ ($stats['telat']/$stats['total'])*100 }}%"></div>
                    </div>
                    @endif
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
                    <h2 class="fw-bold text-info mb-1">{{ $stats['izin'] ?? 0 }}</h2>
                    <p class="text-muted mb-0 small">Izin/Sakit</p>
                    @if($stats['total'] > 0)
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: {{ ($stats['izin']/$stats['total'])*100 }}%"></div>
                    </div>
                    @endif
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
                    <h2 class="fw-bold text-danger mb-1">{{ $stats['alpha'] ?? 0 }}</h2>
                    <p class="text-muted mb-0 small">Alpha</p>
                    @if($stats['total'] > 0)
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-danger" style="width: {{ ($stats['alpha']/$stats['total'])*100 }}%"></div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER & TABEL --}}
    <div class="card border-0 shadow-sm" style="border: 1px solid #e0e0e0 !important; border-radius: 16px;">
        <div class="card-header bg-white border-0 py-4">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-funnel-fill me-2 text-primary"></i>
                <h5 class="fw-bold mb-0">Filter Data Presensi</h5>
            </div>
            
            <form action="{{ route('admin.presensi') }}" method="GET">
                <div class="row g-3">
                    {{-- Filter Sekolah --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-2">SEKOLAH</label>
                        <select name="sekolah" class="form-select border-0" style="background-color: #f8f9fa; border-radius: 8px;">
                            <option value="all">Semua Sekolah</option>
                            @foreach($sekolahs as $s)
                            <option value="{{ $s->id }}" {{ $sekolah == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_sekolah }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- Filter Tanggal Tunggal --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-2">TANGGAL</label>
                        <input type="date" 
                               name="tanggal" 
                               value="{{ $tanggal }}" 
                               class="form-control border-0"
                               style="background-color: #f8f9fa; border-radius: 8px;">
                    </div>
                    
                    {{-- Filter Rentang Tanggal --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-2">DARI</label>
                        <input type="date" 
                               name="tanggal_mulai" 
                               value="{{ $tanggalMulai }}" 
                               class="form-control border-0"
                               style="background-color: #f8f9fa; border-radius: 8px;">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-2">SAMPAI</label>
                        <input type="date" 
                               name="tanggal_akhir" 
                               value="{{ $tanggalAkhir }}" 
                               class="form-control border-0"
                               style="background-color: #f8f9fa; border-radius: 8px;">
                    </div>
                    
                    {{-- Filter Status --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-2">STATUS</label>
                        <select name="status" class="form-select border-0" style="background-color: #f8f9fa; border-radius: 8px;">
                            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="hadir" {{ $status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="telat" {{ $status == 'telat' ? 'selected' : '' }}>Terlambat</option>
                            <option value="izin" {{ $status == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ $status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="alpha" {{ $status == 'alpha' ? 'selected' : '' }}>Alpha</option>
                        </select>
                    </div>
                    
                    {{-- Tombol Aksi --}}
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-dark w-100" style="border-radius: 8px;">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                
                {{-- Search Bar --}}
                <div class="row g-3 mt-2">
                    <div class="col-md-8">
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               class="form-control border-0" 
                               style="background-color: #f8f9fa; border-radius: 8px;"
                               placeholder="🔍 Cari nama siswa atau email...">
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.presensi') }}" class="btn btn-light w-100 border" style="border-radius: 8px;">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </a>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-success w-100" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalExportRekap">
                            <i class="bi bi-file-pdf me-1"></i>Export PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted small">NO</th>
                            <th class="border-0 py-3 fw-semibold text-muted small">SISWA</th>
                            <th class="border-0 py-3 fw-semibold text-muted small">SEKOLAH</th>
                            <th class="border-0 py-3 fw-semibold text-muted small">TANGGAL</th>
                            <th class="border-0 py-3 fw-semibold text-muted small">JAM MASUK</th>
                            <th class="border-0 py-3 fw-semibold text-muted small">JAM PULANG</th>
                            <th class="border-0 py-3 fw-semibold text-muted small">STATUS</th>
                            <th class="border-0 py-3 fw-semibold text-muted small text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presensis as $index => $p)
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td class="px-4 py-3">{{ $presensis->firstItem() + $index }}</td>
                            <td class="py-3">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ $p->siswa->user->nama_lengkap }}&background=e0e0e0&color=333" 
                                         class="rounded-2 me-3" width="40" height="40">
                                    <div>
                                        <div class="fw-semibold small">{{ $p->siswa->user->nama_lengkap }}</div>
                                        <small class="text-muted">{{ $p->siswa->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-light text-dark border small" style="font-weight: 500;">
                                    {{ $p->siswa->sekolah->nama_sekolah ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-3">
                                <small class="text-dark">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</small>
                            </td>
                            <td class="py-3">
                                @if($p->jam_masuk)
                                <span class="badge bg-light text-dark border small">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') }}
                                </span>
                                @else
                                <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($p->jam_pulang)
                                <span class="badge bg-light text-dark border small">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($p->jam_pulang)->format('H:i') }}
                                </span>
                                @else
                                <small class="text-muted">Belum pulang</small>
                                @endif
                            </td>
                            <td class="py-3">
                                @php
                                    $statusColor = match($p->status_kehadiran) {
                                        'hadir' => 'success',
                                        'telat' => 'warning',
                                        'izin', 'sakit' => 'info',
                                        'alpha' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }} small">
                                    {{ strtoupper($p->status_kehadiran) }}
                                </span>
                            </td>
                            <td class="py-3 text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-outline-primary px-3" 
                                            style="border-radius: 8px;"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalDetail{{ $p->id }}">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </button>
                                    <a href="{{ route('admin.presensi.show', $p->siswa_id) }}" 
                                       class="btn btn-sm btn-dark px-3"
                                       style="border-radius: 8px;">
                                        <i class="bi bi-clock-history me-1"></i>Riwayat
                                    </a>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Detail --}}
                        <div class="modal fade" id="modalDetail{{ $p->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                                    <div class="modal-header border-0 bg-dark text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
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
                                            @if($p->foto_masuk)
                                            <div class="col-md-6">
                                                <label class="small fw-semibold text-muted mb-2">FOTO MASUK</label>
                                                <img src="{{ asset('storage/' . $p->foto_masuk) }}" class="img-fluid rounded-3 border" alt="Foto Masuk">
                                            </div>
                                            @endif
                                            @if($p->bukti_izin)
                                            <div class="col-md-6">
                                                <label class="small fw-semibold text-muted mb-2">BUKTI IZIN</label>
                                                <img src="{{ asset('storage/' . $p->bukti_izin) }}" class="img-fluid rounded-3 border" alt="Bukti Izin">
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
                                <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-3"></i>
                                <p class="text-muted fw-semibold mb-1">Tidak ada data presensi</p>
                                <small class="text-muted">Coba ubah filter pencarian Anda</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($presensis->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $presensis->links() }}
        </div>
        @endif
    </div>

</div>

{{-- Modal Export Rekap PDF --}}
<div class="modal fade" id="modalExportRekap" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 bg-success text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-pdf me-2"></i>Export Rekap PDF</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            {{-- Tambahkan ID pada form --}}
            <form action="{{ route('admin.presensi.export.rekap') }}" method="GET" id="formExportPDF">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted mb-2">PILIH SEKOLAH</label>
                        <select name="sekolah_id" id="sekolah_id" class="form-select border-0" style="background-color: #f8f9fa; border-radius: 8px;" required>
                            <option value="">-- Pilih Sekolah --</option>
                            @foreach($sekolahs as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_sekolah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted mb-2">BULAN</label>
                        <input type="month" name="bulan" id="bulan_export" value="{{ \Carbon\Carbon::now()->format('Y-m') }}" class="form-control border-0" style="background-color: #f8f9fa; border-radius: 8px;" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    {{-- Ganti type="submit" menjadi type="button" untuk memicu JS --}}
                    <button type="button" onclick="confirmExport()" class="btn btn-success w-100" style="border-radius: 8px;">
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
    
    // Validasi Sederhana
    if (sekolah.value === "" || bulan.value === "") {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Silakan pilih sekolah dan bulan terlebih dahulu!',
            confirmButtonColor: '#198754'
        });
        return;
    }

    // Ambil nama sekolah yang dipilih untuk teks konfirmasi
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
            // Jika dikonfirmasi, submit form secara manual
            document.getElementById('formExportPDF').submit();
            
            // Tutup modal secara manual
            var myModalEl = document.getElementById('modalExportRekap');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide();
        }
    });
}
</script>

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