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
    .col-center { grid-column: span 4; } /* Kurangi sedikit tengah */
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
        .col-right { grid-column: span 12; } /* Tagihan pindah ke bawah */
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
        justify-content: flex-end; /* Menjorok ke kanan */
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
        flex-grow: 1; /* Mengambil semua ruang kosong di antara judul dan link 'see more' */
        display: flex;
        align-items: center; /* Center Vertikal */
        justify-content: center; /* Center Horizontal */
        text-align: center;
    }

    /* TAGIHAN CARD */
   .card-modern-tagihan {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 30px; /* Space luar yang lebih luas */
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
        flex-direction: column; /* Default tumpuk ke bawah agar aman */
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
        margin-bottom: 25px; /* Memberi jarak ke harga */
        max-width: 90%;
    }

    /* Footer: Harga & Tombol */
    .tagihan-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto; /* Paksa ke paling bawah card */
        gap: 10px;
        flex-wrap: wrap
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
        flex-grow: 1; /* Biarkan tombol melebar jika turun ke bawah */
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
    /* MODAL STYLE REVISION (Wireframe Match) */
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

/* Custom Select/Dropdown in Modal (If needed) */
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
    background: #1a202c; /* Hitam sesuai tombol Send di wireframe */
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
    background-color: var(--primary-color) !important; /* Warna biru gelap sesuai tema */
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
            flex-direction: column; /* Tumpuk kembali di layar HP sangat kecil */
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
    {{-- JAM BESAR TENGAH --}}
    <div class="hero-section">
        <h1 id="currentTime">00:00:00</h1>
        <div class="date-display">{{ now()->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</div>
        <button class="btn-jam-kerja">
            <i class="bi bi-clock"></i> Jam Kerja
        </button>
    </div>

    <div class="dashboard-grid">
        {{-- KOLOM KIRI --}}
        <div class="col-left">
            <div class="d-flex flex-column gap-4">
                {{-- CARD PRESENSI HARI INI (DIREVISI) --}}
                <div class="card-modern">
                    <h5 class="card-title">Presensi Hari Ini</h5>
                    
                    @php
                        $presensiHariIni = \App\Models\Presensi::where('user_id', auth()->id())
                            ->where('tanggal', now()->toDateString())->first();
                    @endphp

                    {{-- INFO LOG SETELAH ABSEN (Muncul hanya jika sudah ada data masuk) --}}
                    @if($presensiHariIni && $presensiHariIni->jam_masuk)
                    <div class="log-presensi-box mb-3">
                        <div class="d-flex align-items-start gap-3">
                            {{-- Foto Selfie --}}
                            <div class="photo-placeholder">
                                @if($presensiHariIni->foto_masuk)
                                    <img src="{{ asset('storage/' . $presensiHariIni->foto_masuk) }}" alt="Selfie">
                                @else
                                    <div class="text-muted small">No Photo</div>
                                @endif
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold fs-5">{{ \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i') }}</span>
                                    <span class="text-muted small">{{ \Carbon\Carbon::parse($presensiHariIni->tanggal)->format('m/d/Y') }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-end mt-3">
                                    @if($presensiHariIni->status == 'hadir')
                                        <span class="badge-status success">Tepat Waktu</span>
                                    @elseif($presensiHariIni->status == 'terlambat')
                                        <span class="badge-status danger">Terlambat</span>
                                    @else
                                        <span class="badge-status warning">{{ ucfirst($presensiHariIni->status) }}</span>
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
                            <button class="btn-outline-custom" onclick="cekJamKeluar()"
                                {{ (!$presensiHariIni || $presensiHariIni->jam_keluar) ? 'disabled' : '' }}>
                                <i class="bi bi-box-arrow-right fs-5"></i> Keluar
                            </button>
                        </div>
                    </div>
                    <div class="border-top pt-3">
                        <a href="{{ route('presensi.izin') }}" class="btn-outline-custom text-decoration-none">
                            <i class="bi bi-file-earmark-text"></i> Pengajuan Izin / Sakit
                        </a>
                    </div>
                </div>

                {{-- RINGKASAN KEHADIRAN --}}
                <div class="card-modern">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">Ringkasan Kehadiran</h5>
                        <small class="text-muted">6 bulan terakhir</small>
                    </div>

                    <div class="chart-container">
                        @for($i=0; $i<6; $i++)
                        <div class="bar-group">
                            <div class="segment-present" style="height: {{ rand(40,70) }}%"></div>
                            <div class="segment-late" style="height: {{ rand(10,20) }}%"></div>
                            <div class="segment-absent" style="height: {{ rand(5,10) }}%"></div>
                        </div>
                        @endfor
                    </div>

                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <small><i class="bi bi-circle-fill me-1" style="color:var(--primary-color)"></i> Tepat Waktu</small>
                        <small><i class="bi bi-circle-fill me-1 text-secondary"></i> Terlambat</small>
                        <small><i class="bi bi-circle-fill me-1 text-light border rounded-circle"></i> Alpa</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM TENGAH (LOG BULANAN) --}}
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
                        @php
                            $logs = \App\Models\Presensi::where('user_id', auth()->id())
                                ->whereMonth('tanggal', now()->month)
                                ->orderBy('tanggal', 'desc')->take(8)->get();
                        @endphp
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->tanggal)->format('d.m.Y') }}</td>
                            <td>{{ $log->jam_masuk ? substr($log->jam_masuk, 0, 5) : '-' }}</td>
                            <td>{{ $log->jam_keluar ? substr($log->jam_keluar, 0, 5) : '-' }}</td>
                            <td class="{{ $log->status == 'hadir' ? 'text-success' : 'text-danger' }}">
                                {{ ucfirst($log->status) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{ route('presensi.riwayat') }}" class="see-more">See more →</a>
            </div>
        </div>

        {{-- KOLOM KANAN (TAGIHAN) --}}
        <div class="col-right">
    <div class="card-modern-tagihan">
    <h2 class="fw-bold mb-0" style="font-size: 1.8rem;">Tagihan Keuangan</h2>
    <hr style="border-top: 2px solid #000; opacity: 1; margin: 15px 0 25px 0;">

    @forelse ($tagihanBelumBayar ?? [] as $tagihan)
    <div class="tagihan-item">
        <div class="tagihan-header">
            <h3 class="tagihan-title">{{ $tagihan->nama }}</h3>
            <div class="badge-tenggat-outline">
                Tenggat : {{ \Carbon\Carbon::parse($tagihan->tenggat)->format('d / m / y') }}
            </div>
        </div>

        <div class="mb-2">
            <span class="badge border text-dark" style="font-size: 0.65rem; background: #f3f4f6;">
                {{ strtoupper($tagihan->kategori) }}
            </span>
        </div>

        <p class="tagihan-description">
            {{ $tagihan->keterangan ?? 'Mohon untuk segera membayar tagihan untuk ' . strtolower($tagihan->nama) . ' yang digunakan.' }}
        </p>

        <div class="tagihan-footer">
            <div class="price-bold">
                Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
            </div>
            <a href="{{ route('pembayaran.siswa') }}" class="btn-bayar-black">
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
        <a href="{{ route('pembayaran.siswa') }}" class="see-more">See more →</a>
</div>

{{-- SEMUA MODAL DARI KODE ASLI (DIINTEGRASIKAN KEMBALI) --}}
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
                <form id="formAbsenKeluar" method="POST" action="{{ route('presensi.keluar') }}">
                    @csrf
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Jurnal Kegiatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">Tuliskan ringkasan pekerjaan Anda hari ini untuk melengkapi absensi keluar.</p>
                        <textarea name="jurnal_kegiatan" id="jurnal_kegiatan" class="form-control" rows="6" 
                            placeholder="Apa yang Anda kerjakan hari ini? (Min. 50 karakter)" 
                            style="border-radius: 12px; border: 1px solid var(--border-color);"
                            required minlength="50"></textarea>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal" style="width: auto; padding: 10px 25px;">Batal</button>
                        <button type="submit" class="btn-modal-action" style="width: auto; padding: 10px 25px;">Simpan & Keluar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
    // 1. UPDATE JAM REAL-TIME
    function updateTime() {
        const now = new Date();
        document.getElementById('currentTime').textContent = 
            now.getHours().toString().padStart(2, '0') + ":" + 
            now.getMinutes().toString().padStart(2, '0') + ":" + 
            now.getSeconds().toString().padStart(2, '0');
    }
    setInterval(updateTime, 1000);
    updateTime();

    // 2. LOGIKA WEBCAM (Sesuai kode asli Anda)
    let video = document.getElementById('video');
    let canvas = document.getElementById('canvas');
    let photo = document.getElementById('photo');
    let capturedImage = null;

    document.getElementById('modalAbsenMasuk').addEventListener('shown.bs.modal', function () {
        navigator.mediaDevices.getUserMedia({ video: true }).then(stream => {
            video.srcObject = stream;
        }).catch(err => Swal.fire('Error', 'Kamera tidak ditemukan', 'error'));
    });

    function takeSnapshot() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        capturedImage = canvas.toDataURL('image/png');
        photo.src = capturedImage;
        photo.style.display = 'block';
        video.style.display = 'none';
        document.getElementById('snap').style.display = 'none';
        document.getElementById('retake').style.display = 'inline-block';
        document.getElementById('submitAbsen').disabled = false;
    }

    function retakePhoto() {
        video.style.display = 'block';
        photo.style.display = 'none';
        document.getElementById('snap').style.display = 'inline-block';
        document.getElementById('retake').style.display = 'none';
        document.getElementById('submitAbsen').disabled = true;
    }

    function submitAbsenMasuk() {
    // Tampilkan loading sebentar agar user tahu proses sedang berjalan
    Swal.fire({
        title: 'Mengirim Absensi...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch("{{ route('presensi.masuk') }}", {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json', 
            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
        },
        body: JSON.stringify({ foto_masuk: capturedImage })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            // Notifikasi Berhasil (Style Wireframe/Clean)
            Swal.fire({
                icon: 'success',
                title: 'Absensi Berhasil!',
                text: 'Selamat bekerja, jangan lupa berdoa.',
                showConfirmButton: false,
                timer: 2000, // Pop-up hilang otomatis dalam 2 detik
                timerProgressBar: true,
                customClass: {
                    popup: 'border-radius-20' // Opsional: sesuaikan radius
                }
            }).then(() => {
                location.reload(); // Reload halaman setelah pop-up hilang
            });
        } else {
            Swal.fire('Gagal', data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
    });
}

    // 3. VALIDASI JAM KELUAR (Sesuai kode asli Anda)   
    function cekJamKeluar() {
        if (new Date().getHours() < 16) {
            Swal.fire('Belum Waktunya', 'Absen keluar tersedia setelah jam 16:00', 'error');
            return;
        }
        new bootstrap.Modal(document.getElementById('modalAbsenKeluar')).show();
    }

    let isPhotoTaken = false;

function handleCaptureAction() {
    const btn = document.getElementById('btnCaptureAction');
    
    if (!isPhotoTaken) {
        // Logika Mengambil Foto
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        capturedImage = canvas.toDataURL('image/png');
        
        photo.src = capturedImage;
        photo.style.display = 'block';
        video.style.display = 'none';
        
        // Ubah State Tombol
        btn.textContent = 'Send';
        btn.style.background = '#1a202c'; // Warna gelap
        isPhotoTaken = true;
    } else {
        // Logika Mengirim (Submit)
        submitAbsenMasuk();
    }
}

// Reset modal saat ditutup
document.getElementById('modalAbsenMasuk').addEventListener('hidden.bs.modal', function () {
    isPhotoTaken = false;
    const btn = document.getElementById('btnCaptureAction');
    btn.textContent = 'Take';
    photo.style.display = 'none';
    video.style.display = 'block';
    
    // Matikan kamera agar hemat resource
    let stream = video.srcObject;
    if (stream) {
        let tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
    }
});

const videoElement = document.querySelector('#video');
const videoSelect = document.querySelector('#videoSource');

// 1. Fungsi untuk mendapatkan daftar kamera yang tersedia
async function getCameraDevices() {
    try {
        // Minta izin kamera dulu agar label/nama kamera muncul (tidak anonim)
        await navigator.mediaDevices.getUserMedia({ video: true });
        
        const devices = await navigator.mediaDevices.enumerateDevices();
        videoSelect.innerHTML = ''; // Kosongkan dropdown

        const videoDevices = devices.filter(device => device.kind === 'videoinput');
        
        videoDevices.forEach((device, index) => {
            const option = document.createElement('option');
            option.value = device.deviceId;
            // Jika label kosong, beri nama generic Kamera 1, Kamera 2
            option.text = device.label || `Kamera ${index + 1}`;
            videoSelect.appendChild(option);
        });

        if (videoDevices.length === 0) {
            videoSelect.innerHTML = '<option>Kamera tidak ditemukan</option>';
        }
    } catch (err) {
        console.error("Error mendeteksi kamera: ", err);
        videoSelect.innerHTML = '<option>Akses Kamera Ditolak</option>';
    }
}

// 2. Fungsi untuk menjalankan stream berdasarkan deviceId yang dipilih
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
        videoElement.srcObject = stream;
    } catch (err) {
        Swal.fire('Error', 'Gagal mengakses kamera pilihan', 'error');
    }
}

// 3. Event Listener: Jalankan deteksi saat modal akan ditampilkan
document.getElementById('modalAbsenMasuk').addEventListener('show.bs.modal', async () => {
    await getCameraDevices();
    // Jalankan kamera pertama secara otomatis jika ada
    if (videoSelect.options.length > 0) {
        startStream(videoSelect.value);
    }
});

// 4. Event Listener: Ganti kamera saat dropdown diubah
videoSelect.onchange = () => {
    startStream(videoSelect.value);
};

// Pastikan fungsi stop kamera saat modal ditutup (tambahan dari kode sebelumnya)
document.getElementById('modalAbsenMasuk').addEventListener('hidden.bs.modal', () => {
    if (window.stream) {
        window.stream.getTracks().forEach(track => track.stop());
    }
});

document.getElementById('formAbsenKeluar').addEventListener('submit', function(e) {
    e.preventDefault(); // Berhenti sejenak untuk memunculkan loading

    const form = this;
    const jurnalText = document.getElementById('jurnal_kegiatan').value;

    if (jurnalText.length < 50) {
        Swal.fire('Perhatian', 'Jurnal kegiatan minimal 50 karakter.', 'warning');
        return;
    }

    // 1. Tampilkan Loading
    Swal.fire({
        title: 'Menyimpan Jurnal...',
        text: 'Sedang memproses absensi keluar Anda',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // 2. Kirim data via AJAX agar bisa memunculkan notifikasi sukses
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
            // 3. Tampilkan Notifikasi Sukses
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