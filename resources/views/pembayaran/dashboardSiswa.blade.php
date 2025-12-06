{{-- File: resources/views/pembayaran/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Dashboard</h2>
            <p class="text-muted mb-0">{{ now()->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted">{{ auth()->user()->email }}</span>
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4a90e2&color=fff" 
                 alt="Avatar" 
                 class="rounded-circle" 
                 width="40" 
                 height="40">
        </div>
    </div>

    {{-- JAM BESAR --}}
    <div class="text-center mb-5">
        <h1 class="display-1 fw-bold mb-2" id="currentTime" style="font-size: 5rem; letter-spacing: -2px;">
            00:00:00
        </h1>
        <div class="d-inline-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-calendar"></i> Jan, Kerja
            </button>
        </div>
    </div>

    <div class="row g-4">
        {{-- KOLOM KIRI: PRESENSI & RINGKASAN KEHADIRAN --}}
        <div class="col-lg-5">
            {{-- CARD PRESENSI HARI INI --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Presensi Hari Ini</h5>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <button class="btn btn-outline-success w-100 py-3 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#modalMasuk">
                                <i class="bi bi-box-arrow-in-right" style="font-size: 1.5rem;"></i>
                                <span class="fw-semibold">Masuk</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-danger w-100 py-3 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#modalKeluar">
                                <i class="bi bi-box-arrow-right" style="font-size: 1.5rem;"></i>
                                <span class="fw-semibold">Keluar</span>
                            </button>
                        </div>
                    </div>

                    <button class="btn btn-light w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Pengajuan Izin / Sakit</span>
                    </button>
                </div>
            </div>

            {{-- RINGKASAN KEHADIRAN --}}
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Ringkasan Kehadiran</h5>
                        <span class="badge bg-primary">Desember 2025</span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div style="width: 12px; height: 12px; background: #28a745; border-radius: 2px;" class="me-2"></div>
                                    <small class="text-muted">Hadir</small>
                                </div>
                                <h3 class="fw-bold mb-0">18</h3>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div style="width: 12px; height: 12px; background: #6c757d; border-radius: 2px;" class="me-2"></div>
                                    <small class="text-muted">Izin</small>
                                </div>
                                <h3 class="fw-bold mb-0">2</h3>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div style="width: 12px; height: 12px; background: #dc3545; border-radius: 2px;" class="me-2"></div>
                                    <small class="text-muted">Alpa</small>
                                </div>
                                <h3 class="fw-bold mb-0">1</h3>
                            </div>
                        </div>
                    </div>

                    {{-- GRAFIK KEHADIRAN --}}
                    <div class="d-flex align-items-end justify-content-between gap-2 mt-4" style="height: 150px;">
                        <div class="d-flex flex-column align-items-center flex-fill">
                            <div class="w-100 bg-success rounded-top" style="height: 85%;"></div>
                            <small class="text-muted mt-2">Sen</small>
                        </div>
                        <div class="d-flex flex-column align-items-center flex-fill">
                            <div class="w-100 bg-success rounded-top" style="height: 70%;"></div>
                            <small class="text-muted mt-2">Sel</small>
                        </div>
                        <div class="d-flex flex-column align-items-center flex-fill">
                            <div class="w-100 bg-success rounded-top" style="height: 95%;"></div>
                            <small class="text-muted mt-2">Rab</small>
                        </div>
                        <div class="d-flex flex-column align-items-center flex-fill">
                            <div class="w-100 bg-success rounded-top" style="height: 60%;"></div>
                            <small class="text-muted mt-2">Kam</small>
                        </div>
                        <div class="d-flex flex-column align-items-center flex-fill">
                            <div class="w-100 bg-success rounded-top" style="height: 100%;"></div>
                            <small class="text-muted mt-2">Jum</small>
                        </div>
                        <div class="d-flex flex-column align-items-center flex-fill">
                            <div class="w-100 bg-secondary rounded-top" style="height: 45%;"></div>
                            <small class="text-muted mt-2">Sab</small>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-center mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 12px; height: 12px; background: #28a745; border-radius: 2px;"></div>
                            <small class="text-muted">Kehadiran tepat: 95%</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 12px; height: 12px; background: #6c757d; border-radius: 2px;"></div>
                            <small class="text-muted">Keterlambatan: 5%</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 12px; height: 12px; background: #dc3545; border-radius: 2px;"></div>
                            <small class="text-muted">Sesi: 11%</small>
                        </div>
                    </div>

                    <a href="#" class="btn btn-link text-decoration-none d-block text-center mt-3">
                        See more →
                    </a>
                </div>
            </div>
        </div>

        {{-- KOLOM TENGAH: KALENDER & RIWAYAT --}}
        <div class="col-lg-4">
            {{-- KALENDER BULAN INI --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Desember 2025</h5>
                    
                    <table class="table table-borderless table-sm text-center">
                        <thead>
                            <tr class="text-muted">
                                <th>Minggu</th>
                                <th>Senin</th>
                                <th>Selasa</th>
                                <th>Rabu</th>
                                <th>Kamis</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-2">
                                    <div class="text-muted small">01-12-2025</div>
                                    <div class="fw-semibold">07:00</div>
                                    <div class="small text-success">16:30</div>
                                    <div class="badge bg-success mt-1 small">Kerja Normal</div>
                                </td>
                                <td class="py-2">
                                    <div class="text-muted small">02-12-2025</div>
                                    <div class="fw-semibold">08:15</div>
                                    <div class="small text-success">16:00</div>
                                    <div class="badge bg-warning text-dark mt-1 small">Terlambat</div>
                                </td>
                                <td class="py-2 bg-light">
                                    <div class="text-muted small">03-12-2025</div>
                                    <div class="text-muted">-</div>
                                    <div class="badge bg-secondary mt-1 small">Libur</div>
                                </td>
                                <td class="py-2">
                                    <div class="text-muted small">04-12-2025</div>
                                    <div class="fw-semibold">07:05</div>
                                    <div class="small text-success">16:10</div>
                                    <div class="badge bg-success mt-1 small">Kerja Normal</div>
                                </td>
                                <td class="py-2 table-active">
                                    <div class="text-primary small fw-bold">Hari Ini</div>
                                    <div class="fw-semibold">-</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <a href="#" class="btn btn-link text-decoration-none d-block text-center mt-2">
                        See more →
                    </a>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: TAGIHAN KEUANGAN --}}
        <div class="col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Tagihan Keuangan</h5>

                    {{-- ITEM TAGIHAN --}}
                    @forelse ($tagihanBelumBayar ?? [] as $tagihan)
                    <div class="mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $tagihan->nama_tagihan }}</h6>
                                <small class="text-muted">{{ $tagihan->kategori == 'kos' ? '🏠 Kos' : '🔧 Alat Praktik' }}</small>
                            </div>
                            <span class="badge bg-danger">Belum Bayar</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Tenggat: {{ \Carbon\Carbon::parse($tagihan->tenggat)->format('d M Y') }}</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="text-danger mb-0 fw-bold">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</h5>
                            <a href="{{ route('pembayaran.siswa') }}" class="btn btn-sm btn-primary">
                                Bayar
                            </a>
                        </div>
                    </div>
                    @empty
                    {{-- TEMPLATE DUMMY DATA --}}
                    <div class="mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1">Peralatan</h6>
                                <small class="text-muted">Finger-tip untuk Alat Motor ...</small>
                            </div>
                            <span class="badge bg-danger">Belum Bayar</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Tenggat: 15 Des 2025</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="text-danger mb-0 fw-bold">Rp 100.000</h5>
                            <a href="{{ route('pembayaran.siswa') }}" class="btn btn-sm btn-primary">
                                Bayar
                            </a>
                        </div>
                    </div>

                    <div class="mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1">Makanan</h6>
                                <small class="text-muted">Makan siang semester ganjil...</small>
                            </div>
                            <span class="badge bg-warning text-dark">Pending</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Tenggat: 20 Des 2025</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="text-warning mb-0 fw-bold">Rp 350.000</h5>
                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                Pending
                            </button>
                        </div>
                    </div>
                    @endforelse

                    <a href="{{ route('pembayaran.siswa') }}" class="btn btn-link text-decoration-none d-block text-center mt-3">
                        See more →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PRESENSI MASUK --}}
<div class="modal fade" id="modalMasuk" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">✅ Presensi Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Fitur presensi akan segera tersedia
                </div>
                <p class="text-muted">Backend untuk sistem absensi sedang dalam pengembangan</p>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PRESENSI KELUAR --}}
<div class="modal fade" id="modalKeluar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">🚪 Presensi Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Fitur presensi akan segera tersedia
                </div>
                <p class="text-muted">Backend untuk sistem absensi sedang dalam pengembangan</p>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.btn-outline-success:hover,
.btn-outline-danger:hover {
    transform: scale(1.02);
}

body.dark-mode .card {
    background: #2a2a2a;
    color: #e0e0e0;
}

body.dark-mode .bg-light {
    background: #3a3a3a !important;
    color: #e0e0e0 !important;
}

body.dark-mode .table {
    color: #e0e0e0;
}

body.dark-mode .border {
    border-color: #444 !important;
}

body.dark-mode .text-muted {
    color: #aaa !important;
}
</style>

<script>
// Update Real-time Clock
function updateTime() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('currentTime').textContent = `${hours}.${minutes}.${seconds}`;
}

// Update every second
setInterval(updateTime, 1000);
updateTime(); // Initial call
</script>
@endsection