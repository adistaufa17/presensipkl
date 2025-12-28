@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    :root {
        --primary-color: #213448;
        --text-muted: #717171;
        --border-color: #aeb4c1ff;
        --radius: 16px;
    }

    body {
        background-color: #fcfcfc;
        font-family: 'Poppins', sans-serif;
        color: var(--primary-color);
    }

    /* HEADER JAM (Sesuai Wireframe) */
    .hero-section {
        text-align: center;
        padding: 40px 0;
    }

    #currentTime {
        font-size: clamp(4rem, 10vw, 8rem);
        font-weight: 800;
        margin: 0;
        line-height: 1;
        letter-spacing: -2px;
    }

    .date-display {
        font-size: 1.1rem;
        color: var(--text-muted);
        margin-top: 10px;
    }

    .btn-jam-kerja {
        background: white;
        border: 1px solid var(--border-color);
        padding: 6px 18px;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-top: 15px;
        color: var(--text-muted);
    }

    /* LAYOUT GRID RESPONSIVE */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 20px;
        padding: 20px;
    }

    /* Desktop Default */
    .col-left { grid-column: span 4; }
    .col-center { grid-column: span 4; }
    .col-right { grid-column: span 4; }

    @media (min-width: 1400px) {
        .tagihan-header {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }

    @media (max-width: 1024px) {
        .col-left, .col-center, .col-right { grid-column: span 12; }
    }

    /* Tablet (iPad/Laptop Kecil) */
    @media (max-width: 1100px) {
        .col-left { grid-column: span 5; }
        .col-center { grid-column: span 7; }
        .col-right { grid-column: span 12; }
    }

    /* Mobile (Phone) */
    @media (max-width: 768px) {
        .col-left, .col-center, .col-right { grid-column: span 12; }
        #currentTime { font-size: 3rem !important; }
    }

    /* CARD STYLING */
    .card-modern {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 24px;
        display: flex;
        flex-direction: column;
        height: 100%; 
        position: relative;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 20px;
    }

    /* BUTTONS */
    .btn-outline-custom {
        border: 1px solid var(--border-color);
        background: white;
        padding: 12px;
        border-radius: 12px;
        font-weight: 600;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        color: var(--primary-color);
    }

    .btn-outline-custom:hover:not(:disabled) {
        background: #f9fafb;
        border-color: var(--primary-color);
    }

    .btn-outline-custom:disabled {
        background-color: #f3f4f6 !important; 
        border-color: #e5e7eb !important;    
        color: #9ca3af !important;                    
        opacity: 0.7;                       
    }

    .btn-primary-dark {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
    }

    /* STATISTIK CHART (Wireframe Style) */
    .chart-container {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        height: 180px;
        margin: 20px 0;
    }

    .bar-group {
        flex: 1;
        display: flex;
        flex-direction: column-reverse;
        border-radius: 6px;
        overflow: hidden;
        height: 100%;
        background: #f3f4f6;
    }

    .segment-present { background: var(--primary-color); }
    .segment-late { background: #9ca3af; }
    .segment-absent { background: #e5e7eb; }

    /* TABLE LOG BULANAN */
    .table-log {
        width: 100%;
        border-collapse: collapse;
    }

    .table-log th {
        text-align: left;
        color: var(--text-muted);
        font-weight: 500;
        font-size: 0.85rem;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border-color);
    }

    .table-log td {
        padding: 14px 0;
        font-size: 0.9rem;
        border-bottom: 1px solid #f9fafb;
    }

    .see-more {
        margin-top: auto; 
        padding-top: 20px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        color: var(--text-muted);
        text-decoration: none;
        font-size: 0.85rem;
        transition: 0.2s;
    }

    .see-more:hover {
        color: var(--primary-color);
        transition: ease 0.8s;
    }

    .empty-state-container {
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    /* TAGIHAN CARD */
    .card-modern-tagihan {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 30px;
        height: 100%;
    }

    .hr-black {
        border: none;
        border-top: 2px solid var(--border-color);
        margin: 15px 0 25px 0;
        opacity: 1;
    }

    /* Item Tagihan */
    .tagihan-item {
        border: 1.5px solid var(--border-color);
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 20px;
        background: #fff;
        display: flex;
        flex-direction: column;
        min-height: 220px;
    }

    .tagihan-header {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 15px;
    }

    .tagihan-title {
        font-size: 1.4rem;
        font-weight: 800;
        line-height: 1.2;
        margin: 0;
    }

    .badge-tenggat-outline {
        border: 1px solid #000;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
        background: #f8fafc;
        white-space: nowrap;
    }

    .tagihan-description {
        color: #6b7280;
        font-size: 0.95rem;
        line-height: 1.5;
        margin-bottom: 25px;
        max-width: 90%;
    }

    /* Footer: Harga & Tombol */
    .tagihan-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        gap: 10px;
        flex-wrap: wrap;
    }

    .price-bold {
        font-size: 1.4rem;
        font-weight: 600;
        color: #000;
        white-space: nowrap;
    }

    .btn-bayar-black {
        background: #1a202c;
        color: white !important;
        padding: 10px 25px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        flex-grow: 1;
        max-width: 120px;
    }

    .btn-bayar-black:hover {
        background: #585861ff;
        transform: translateY(-1px);
        transition: ease 0.6s;
    }

    /* Photo Card */
    .log-presensi-box {
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 15px;
        background-color: #fff;
    }

    .photo-placeholder {
        width: 80px;
        height: 80px;
        background-color: #e5e5e5;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .photo-placeholder img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .badge-status {
        padding: 8px 20px;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-status.success {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }

    .badge-status.danger {
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }
    
    .badge-status.warning {
        background-color: #fff3e0;
        color: #ef6c00;
        border: 1px solid #ffe0b2;
    }

    /* MODAL STYLE */
    .modal-content-custom {
        border-radius: 24px !important;
        border: none !important;
        padding: 10px;
    }

    .modal-body-webcam {
        padding: 20px !important;
    }

    .webcam-container {
        width: 100%;
        aspect-ratio: 16 / 10;
        background: #f3f4f6;
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        border: 1px solid var(--border-color);
    }

    #video, #photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .modal-select {
        width: 100%;
        padding: 10px 15px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        margin-top: 15px;
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .modal-footer-custom {
        border: none !important;
        padding: 0 20px 20px 20px !important;
        display: flex;
        gap: 12px;
    }

    .btn-modal-cancel {
        flex: 1;
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 12px;
        font-weight: 600;
        color: var(--primary-color);
    }

    .btn-modal-action {
        flex: 1;
        background: #1a202c;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px;
        font-weight: 600;
    }

    .btn-modal-action:disabled {
        background: #e5e7eb;
        color: #9ca3af;
    }

    .border-radius-20 {
        border-radius: 20px !important;
    }

    .swal2-styled.swal2-confirm {
        background-color: var(--primary-color) !important;
        border-radius: 12px !important;
    }

    @media (max-width: 1366px) {
        .tagihan-footer {
            flex-direction: column;
            align-items: stretch;
        }
        .btn-bayar-black {
            max-width: 100%;
        }
        .price-bold {
            margin-bottom: 5px;
        }
    } 
    
    @media (max-width: 480px) {
        .tagihan-header {
            flex-direction: column;
        }
        .tagihan-footer {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        .btn-bayar-black {
            width: 100%;
            text-align: center;
        }
    }

</style>

<div class="container-fluid">
    <div class="hero-section">
        <h1 id="currentTime">00:00:00</h1>
        <div class="date-display">{{ now()->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</div>
        <button class="btn-jam-kerja">
            <i class="bi bi-clock"></i> Jam Kerja
        </button>
    </div>

    <div class="dashboard-grid">
        <div class="col-left">
            <div class="d-flex flex-column gap-4">
                <div class="card-modern">
                    <h5 class="card-title">Presensi Hari Ini</h5>
                    
                    @php
                        $presensiHariIni = \App\Models\Presensi::where('siswa_id', auth()->user()->siswa->id)
                            ->whereDate('tanggal', now()->toDateString())->first();
                    @endphp

                    @if($presensiHariIni && $presensiHariIni->jam_masuk)
                    <div class="log-presensi-box mb-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="photo-placeholder">
                                @if(in_array($presensiHariIni->status_kehadiran, ['izin', 'sakit']))
                                    {{-- Tampilkan bukti izin jika status izin/sakit --}}
                                    @if($presensiHariIni->bukti_izin)
                                        <img src="{{ asset('storage/' . $presensiHariIni->bukti_izin) }}" alt="Bukti Izin">
                                    @else
                                        <div class="text-muted small text-center">
                                            <i class="bi bi-file-earmark-text fs-4"></i>
                                            <div style="font-size: 0.7rem;">Tanpa Bukti</div>
                                        </div>
                                    @endif
                                @else
                                    {{-- Tampilkan foto masuk jika hadir/telat --}}
                                    @if($presensiHariIni->foto_masuk)
                                        <img src="{{ asset('storage/' . $presensiHariIni->foto_masuk) }}" alt="Selfie">
                                    @else
                                        <div class="text-muted small">No Photo</div>
                                    @endif
                                @endif
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold fs-5">{{ \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i') }}</span>
                                    <span class="text-muted small">{{ \Carbon\Carbon::parse($presensiHariIni->tanggal)->format('m/d/Y') }}</span>
                                </div>
                                
                                {{-- Tampilkan keterangan izin jika ada --}}
                                @if(in_array($presensiHariIni->status_kehadiran, ['izin', 'sakit']) && $presensiHariIni->keterangan_izin)
                                <div class="mt-2 mb-2">
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">Keterangan:</small>
                                    <small class="text-dark">{{ Str::limit($presensiHariIni->keterangan_izin, 60) }}</small>
                                </div>
                                @endif
                                
                                <div class="d-flex justify-content-end mt-3">
                                    @if($presensiHariIni->status_kehadiran == 'hadir')
                                        <span class="badge-status success">Tepat Waktu</span>
                                    @elseif($presensiHariIni->status_kehadiran == 'telat')
                                        <span class="badge-status danger">Terlambat</span>
                                    @elseif($presensiHariIni->status_kehadiran == 'izin')
                                        <span class="badge-status warning">
                                            <i class="bi bi-calendar-x me-1"></i>Izin
                                        </span>
                                    @elseif($presensiHariIni->status_kehadiran == 'sakit')
                                        <span class="badge-status warning">
                                            <i class="bi bi-heart-pulse me-1"></i>Sakit
                                        </span>
                                    @else
                                        <span class="badge-status warning">{{ ucfirst($presensiHariIni->status_kehadiran) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Tombol Utama --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <button class="btn-outline-custom" data-bs-toggle="modal" data-bs-target="#modalAbsenMasuk"
                                {{ ($presensiHariIni && $presensiHariIni->jam_masuk) ? 'disabled' : '' }}>
                                <i class="bi bi-box-arrow-in-right fs-5"></i> Masuk
                            </button>
                        </div>
                        <div class="col-6">
                            {{-- Tombol hanya mati jika: Belum absen masuk ATAU Sudah absen pulang --}}
                            <button class="btn-outline-custom" onclick="cekJamKeluar()"
                                {{ (!$presensiHariIni || $presensiHariIni->jam_pulang) ? 'disabled' : '' }}>
                                <i class="bi bi-box-arrow-right fs-5"></i> Keluar
                            </button>
                        </div>
                    </div>
                    <div class="border-top pt-3">
                        {{-- Tambahkan kondisi disabled jika sudah absen masuk --}}
                        <button class="btn-outline-custom text-decoration-none w-100" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalAjukanIzin"
                            {{ ($presensiHariIni && $presensiHariIni->jam_masuk) ? 'disabled' : '' }}>
                            
                            @if($presensiHariIni && $presensiHariIni->jam_masuk)
                                <i class="bi bi-check-circle"></i> Sudah Melakukan Presensi
                            @else
                                <i class="bi bi-file-earmark-text"></i> Pengajuan Izin / Sakit
                            @endif
                        </button>
                    </div>
                </div>

                <div class="card-modern">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">Ringkasan Kehadiran</h5>
                        <small class="text-muted">{{ $labelBulan }}</small>
                    </div>

                    <div style="height: 250px;">
                        <canvas id="kehadiranChart"></canvas>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <small><i class="bi bi-circle-fill me-1" style="color:#213448"></i> Hadir</small>
                        <small><i class="bi bi-circle-fill me-1" style="color:#adb5bd"></i> Telat</small>
                        <small><i class="bi bi-circle-fill me-1" style="color:#dc3545"></i> Alpa/Izin</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-center">
            <div class="card-modern">
                <h5 class="card-title">{{ now()->locale('id')->isoFormat('MMMM YYYY') }}</h5>
                <table class="table-log">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Pulang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->tanggal)->format('d.m.Y') }}</td>
                            <td>{{ $log->jam_masuk ? \Carbon\Carbon::parse($log->jam_masuk)->format('H:i') : '-' }}</td>
                            <td>{{ $log->jam_pulang ? \Carbon\Carbon::parse($log->jam_pulang)->format('H:i') : '-' }}</td>
                            <td class="{{ $log->status_kehadiran == 'hadir' ? 'text-success' : 'text-danger' }}">
                                {{ ucfirst($log->status_kehadiran) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada data presensi bulan ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                    <a href="{{ route('siswa.riwayat-presensi') }}" class="see-more">See more →</a>            </div>
        </div>

        {{-- KOLOM KANAN (TAGIHAN) --}}
        <div class="col-right">
            <div class="card-modern-tagihan">
                <h2 class="fw-bold mb-0" style="font-size: 1.8rem;">Tagihan Keuangan</h2>
                <hr style="border-top: 2px solid #000; opacity: 1; margin: 15px 0 25px 0;">

                @forelse ($tagihanBelumBayar as $tagihanSiswa)
                <div class="tagihan-item">
                    <div class="tagihan-header">
                        <h3 class="tagihan-title">{{ $tagihanSiswa->tagihan->nama_tagihan }}</h3>
                        <div class="badge-tenggat-outline">
                            Tenggat: {{ \Carbon\Carbon::parse($tagihanSiswa->jatuh_tempo)->format('d / m / y') }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <span class="badge border text-dark" style="font-size: 0.65rem; background: #f3f4f6;">
                            BULAN KE-{{ $tagihanSiswa->bulan_ke }}
                        </span>
                    </div>

                    <p class="tagihan-description">
                        Pembayaran untuk {{ $tagihanSiswa->tagihan->nama_tagihan }} bulan ke-{{ $tagihanSiswa->bulan_ke }}. Mohon segera dilunasi sebelum tanggal jatuh tempo.
                    </p>

                    <div class="tagihan-footer">
                        <div class="price-bold">
                            Rp {{ number_format($tagihanSiswa->tagihan->nominal, 0, ',', '.') }}
                        </div>
                        <a href="{{ route('siswa.tagihan.index') }}" class="btn-bayar-black">
                            Bayar
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <p class="text-muted">Tidak ada tagihan tertunggak.</p>
                </div>
                @endforelse

                <div>
                    <a href="{{ route('siswa.tagihan.index') }}" class="see-more">See more →</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAbsenMasuk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-custom">
            <div class="modal-body modal-body-webcam">
                <div class="webcam-container">
                    <video id="video" autoplay></video>
                    <canvas id="canvas" style="display:none;"></canvas>
                    <img id="photo" style="display:none;">
                </div>

                <select class="modal-select" id="videoSource">
                    <option value="">Mencari Kamera...</option>
                </select>
            </div>
            
            <div class="modal-footer-custom">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-modal-action" id="btnCaptureAction" onclick="handleCaptureAction()">Take</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAbsenKeluar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-content-custom"> 
            <form id="formAbsenKeluar" method="POST" action="{{ route('siswa.absenPulang') }}">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Jurnal Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Tuliskan ringkasan pekerjaan Anda hari ini untuk melengkapi absensi keluar.</p>
                    <textarea name="jurnal_kegiatan" id="jurnal_kegiatan" class="form-control" rows="6" 
                        placeholder="Apa yang Anda kerjakan hari ini? (Min. 5 karakter)" 
                        style="border-radius: 12px; border: 1px solid var(--border-color);"
                        required minlength="5"></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal" style="width: auto; padding: 10px 25px;">Batal</button>
                    <button type="submit" class="btn-modal-action" style="width: auto; padding: 10px 25px;">Simpan & Keluar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Tambahkan di atas form modal --}}
@if(session('error'))
<script>
    Swal.fire('Error', '{{ session('error') }}', 'error');
</script>
@endif

@if(session('success'))
<script>
    Swal.fire('Success', '{{ session('success') }}', 'success');
</script>
@endif

@if($errors->any())
<script>
    Swal.fire('Error', '{{ $errors->first() }}', 'error');
</script>
@endif
<div class="modal fade" id="modalAjukanIzin" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-custom">
            <form method="POST" action="{{ route('siswa.ajukanIzin') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Pengajuan Izin/Sakit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis <span class="text-danger">*</span></label>
                        <select name="status_kehadiran" class="form-select" required>
                            <option value="">Pilih Jenis</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                        <textarea name="keterangan_izin" class="form-control" rows="4" 
                            placeholder="Jelaskan alasan izin/sakit Anda... (minimal 10 karakter)" 
                            required minlength="10"></textarea>
                        <small class="text-muted">Minimal 10 karakter</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bukti Surat (Opsional)</label>
                        <input type="file" name="bukti_izin" class="form-control" 
                               accept="image/jpeg,image/jpg,image/png">
                        <small class="text-muted">Upload foto surat izin/sakit jika ada (Max: 2MB)</small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-modal-action">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateTime() {
        const now = new Date();
        document.getElementById('currentTime').textContent = 
            now.getHours().toString().padStart(2, '0') + ":" + 
            now.getMinutes().toString().padStart(2, '0') + ":" + 
            now.getSeconds().toString().padStart(2, '0');
    }
        setInterval(updateTime, 1000);
        updateTime();

        let video = document.getElementById('video');
        let canvas = document.getElementById('canvas');
        let photo = document.getElementById('photo');
        let capturedImage = null;
        let isPhotoTaken = false;

    async function getCameraDevices() {
        try {
            await navigator.mediaDevices.getUserMedia({ video: true });
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoSelect = document.querySelector('#videoSource');
            videoSelect.innerHTML = '';

            const videoDevices = devices.filter(device => device.kind === 'videoinput');
            
            videoDevices.forEach((device, index) => {
                const option = document.createElement('option');
                option.value = device.deviceId;
                option.text = device.label || `Kamera ${index + 1}`;
                videoSelect.appendChild(option);
            });

            if (videoDevices.length === 0) {
                videoSelect.innerHTML = '<option>Kamera tidak ditemukan</option>';
            }
        } catch (err) {
            console.error("Error mendeteksi kamera: ", err);
            document.querySelector('#videoSource').innerHTML = '<option>Akses Kamera Ditolak</option>';
        }
    }

    async function startStream(deviceId) {
        if (window.stream) {
            window.stream.getTracks().forEach(track => track.stop());
        }

        const constraints = {
            video: {
                deviceId: deviceId ? { exact: deviceId } : undefined,
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };

        try {
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            window.stream = stream;
            video.srcObject = stream;
        } catch (err) {
            Swal.fire('Error', 'Gagal mengakses kamera pilihan', 'error');
        }
    }

    document.getElementById('modalAbsenMasuk').addEventListener('show.bs.modal', async () => {
        await getCameraDevices();
        const videoSelect = document.querySelector('#videoSource');
        if (videoSelect.options.length > 0) {
            startStream(videoSelect.value);
        }
    });

    document.querySelector('#videoSource').onchange = (e) => {
        startStream(e.target.value);
    };

    document.getElementById('modalAbsenMasuk').addEventListener('hidden.bs.modal', () => {
        isPhotoTaken = false;
        const btn = document.getElementById('btnCaptureAction');
        btn.textContent = 'Take';
        photo.style.display = 'none';
        video.style.display = 'block';
        
        if (window.stream) {
            window.stream.getTracks().forEach(track => track.stop());
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('kehadiranChart');
        if (!ctx) return;

        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Hadir', 'Telat', 'Alpa/Izin'], // Label kategori
                datasets: [{
                    label: 'Jumlah Hari',
                    data: [{{ $countHadir }}, {{ $countTelat }}, {{ $countAlpa }}],
                    backgroundColor: [
                        '#213448', 
                        '#adb5bd',
                        '#dc3545'  
                    ],
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            stepSize: 1,
                            precision: 0
                        },
                        grid: { color: '#f0f0f0' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });

    function handleCaptureAction() {
        const btn = document.getElementById('btnCaptureAction');
        
        if (!isPhotoTaken) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            capturedImage = canvas.toDataURL('image/png');
            
            photo.src = capturedImage;
            photo.style.display = 'block';
            video.style.display = 'none';
            
            btn.textContent = 'Send';
            btn.style.background = '#1a202c';
            isPhotoTaken = true;
        } else {
            submitAbsenMasuk();
        }
    }

    function submitAbsenMasuk() {
        Swal.fire({
            title: 'Mengirim Absensi...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch("{{ route('siswa.absenMasuk') }}", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json' 
            },
            body: JSON.stringify({ foto_masuk: capturedImage })
        })
        .then(res => {
            const contentType = res.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                return res.json();
            } else {
                return res.text().then(text => {
                    console.error('Response bukan JSON:', text);
                    throw new Error('Server mengembalikan HTML, bukan JSON. Cek console untuk detail.');
                });
            }
        })
        .then(data => {
            if(data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalAbsenMasuk'));
                if (modal) modal.hide();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Absensi Berhasil!',
                    text: 'Selamat bekerja, jangan lupa berdoa.',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'border-radius-20'
                    }
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: err.message || 'Tidak dapat terhubung ke server',
                footer: 'Silakan coba lagi atau hubungi admin'
            });
        });
    }

    function cekJamKeluar() {
        const sekarang = new Date();
        const jam = sekarang.getHours();

        if (jam < 16) {
            Swal.fire({
                title: 'Pulang Lebih Awal?',
                text: "Saat ini belum jam 16:00. Jika Anda pulang sekarang, jam pulang akan tercatat sesuai waktu saat ini. Lanjutkan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#213448',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjut',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const modalKeluar = new bootstrap.Modal(document.getElementById('modalAbsenKeluar'));
                    modalKeluar.show();
                }
            });
        } else {
            const modalKeluar = new bootstrap.Modal(document.getElementById('modalAbsenKeluar'));
            modalKeluar.show();
        }
    }
    
    document.getElementById('formAbsenKeluar').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const jurnalText = document.getElementById('jurnal_kegiatan').value;

        if (jurnalText.length < 5) {
            Swal.fire('Perhatian', 'Jurnal kegiatan minimal 5 karakter.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Menyimpan Jurnal...',
            text: 'Sedang memproses absensi keluar Anda',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Pulang!',
                    text: 'Jurnal tersimpan. Hati-hati di jalan!',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Gagal', data.message || 'Terjadi kesalahan saat simpan.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Gagal menyambung ke server.', 'error');
        });
    });
</script>
@endsection