@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

    {{-- Alert Success/Error --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- JAM BESAR --}}
    <div class="text-center mb-5">
        <h1 class="display-1 fw-bold mb-2" id="currentTime" style="font-size: 5rem; letter-spacing: -2px;">
            00:00:00
        </h1>
        <div class="d-inline-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-calendar"></i> Jam Kerja
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
                    
                    @php
                        $presensiHariIni = \App\Models\Presensi::where('user_id', auth()->id())
                            ->where('tanggal', now()->toDateString())
                            ->first();
                    @endphp

                    @if($presensiHariIni && $presensiHariIni->jam_masuk)

                        <div class="alert alert-primary mb-3">
                            <div class="d-flex align-items-center gap-3">
                                {{-- Foto Selfie Thumbnail --}}
                                @if($presensiHariIni->foto_masuk)
                                <img src="{{ asset('storage/' . $presensiHariIni->foto_masuk) }}" 
                                     class="rounded border cursor-pointer"
                                     width="60" 
                                     height="60"
                                     style="object-fit: cover;"
                                     onclick="showImageModal('{{ asset('storage/' . $presensiHariIni->foto_masuk) }}')"
                                     alt="Foto Presensi">
                                @endif
                                
                                {{-- Info Status --}}
                                <div class="flex-grow-1">
                                    @if($presensiHariIni && $presensiHariIni->jam_masuk)
                                        <strong>Status: {{ strtoupper($presensiHariIni->status) }}</strong>
                                    @endif
                                    <small>
                                        Masuk: {{ \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i') }}
                                        @if($presensiHariIni->jam_keluar)
                                            | Keluar: {{ \Carbon\Carbon::parse($presensiHariIni->jam_keluar)->format('H:i') }}
                                        @endif
                                    </small>
                                    
                                    {{-- Tombol Lihat Jurnal --}}
                                    @if($presensiHariIni->jurnal_kegiatan)
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="showJurnalModal('{{ addslashes($presensiHariIni->jurnal_kegiatan) }}')">
                                            <i class="bi bi-journal-text"></i> Lihat Jurnal
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            {{-- BUTTON MASUK (Modal Trigger) --}}
                            <button type="button" 
                                    class="btn btn-outline-success w-100 py-3 d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalAbsenMasuk"
                                    {{ ($presensiHariIni && $presensiHariIni->jam_masuk) ? 'disabled' : '' }}
                                <i class="bi bi-box-arrow-in-right" style="font-size: 1.5rem;"></i>
                                <span class="fw-semibold">Masuk</span>
                            </button>
                        </div>
                        <div class="col-6">
                            {{-- BUTTON KELUAR (Modal Trigger) --}}
                            <button type="button" 
                                    onclick="cekJamKeluar()"
                                    class="btn btn-outline-danger w-100 py-3 d-flex align-items-center justify-content-center gap-2"
                                    {{ (!$presensiHariIni || $presensiHariIni->jam_keluar) ? 'disabled' : '' }}>
                                <i class="bi bi-box-arrow-right" style="font-size: 1.5rem;"></i>
                                <span class="fw-semibold">Keluar</span>
                            </button>
                        </div>
                    </div>

                    <a href="{{ route('presensi.izin') }}" class="btn btn-light w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Pengajuan Izin / Sakit</span>
                    </a>
                </div>
            </div>

            {{-- RINGKASAN KEHADIRAN --}}
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Ringkasan Kehadiran</h5>
                        <span class="badge bg-primary">{{ now()->format('F Y') }}</span>
                    </div>

                    @php
                        $summary = [
                            'hadir' => \App\Models\Presensi::where('user_id', auth()->id())
                                ->where('status', 'hadir')
                                ->whereYear('tanggal', now()->year)
                                ->count(),
                            'izin' => \App\Models\Presensi::where('user_id', auth()->id())
                                ->whereIn('status', ['izin', 'sakit'])
                                ->whereYear('tanggal', now()->year)
                                ->count(),
                            'alpa' => \App\Models\Presensi::where('user_id', auth()->id())
                                ->where('status', 'alpa')
                                ->whereYear('tanggal', now()->year)
                                ->count(),
                        ];
                    @endphp

                    <div class="row g-3 mb-3">
                        <div class="col-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div style="width: 12px; height: 12px; background: #28a745; border-radius: 2px;" class="me-2"></div>
                                    <small class="text-muted">Hadir</small>
                                </div>
                                <h3 class="fw-bold mb-0">{{ $summary['hadir'] }}</h3>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div style="width: 12px; height: 12px; background: #6c757d; border-radius: 2px;" class="me-2"></div>
                                    <small class="text-muted">Izin</small>
                                </div>
                                <h3 class="fw-bold mb-0">{{ $summary['izin'] }}</h3>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div style="width: 12px; height: 12px; background: #dc3545; border-radius: 2px;" class="me-2"></div>
                                    <small class="text-muted">Alpa</small>
                                </div>
                                <h3 class="fw-bold mb-0">{{ $summary['alpa'] }}</h3>
                            </div>
                        </div>
                    </div>

                    {{-- GRAFIK KEHADIRAN MINGGU INI --}}
                    <div class="d-flex align-items-end justify-content-between gap-2 mt-4" style="height: 150px;">
                        @php
                            $startOfWeek = now()->startOfWeek();
                            $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                        @endphp
                        
                        @foreach($days as $index => $day)
                            @php
                                $date = $startOfWeek->copy()->addDays($index);
                                $presensi = \App\Models\Presensi::where('user_id', auth()->id())
                                    ->where('tanggal', $date->toDateString())
                                    ->first();
                                
                                $height = $presensi ? (rand(60, 100)) : 20;
                                $color = $presensi ? 'bg-success' : 'bg-secondary';
                            @endphp
                            
                            <div class="d-flex flex-column align-items-center flex-fill">
                                <div class="w-100 {{ $color }} rounded-top" style="height: {{ $height }}%;"></div>
                                <small class="text-muted mt-2">{{ $day }}</small>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex gap-3 justify-content-center mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 12px; height: 12px; background: #28a745; border-radius: 2px;"></div>
                            <small class="text-muted">Kehadiran: {{ $summary['hadir'] > 0 ? number_format(($summary['hadir'] / ($summary['hadir'] + $summary['izin'] + $summary['alpa'])) * 100, 0) : 0 }}%</small>
                        </div>
                    </div>

                    <a href="{{ route('presensi.riwayat') }}" class="btn btn-link text-decoration-none d-block text-center mt-3">
                        See more →
                    </a>
                </div>
            </div>
        </div>

        {{-- KOLOM TENGAH: KALENDER FUNGSIONAL --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button class="btn btn-sm btn-outline-secondary" onclick="prevMonth()">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <h5 class="fw-bold mb-0" id="calendarMonth"></h5>
                        <button class="btn btn-sm btn-outline-secondary" onclick="nextMonth()">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    
                    <table class="table table-borderless table-sm text-center" id="calendarTable">
                        <thead>
                            <tr class="text-muted">
                                <th>Min</th><th>Sen</th><th>Sel</th><th>Rab</th><th>Kam</th><th>Jum</th><th>Sab</th>
                            </tr>
                        </thead>
                        <tbody id="calendarBody">
                            <!-- Diisi dengan JavaScript -->
                        </tbody>
                    </table>
                    <p class="text-center text-muted small mb-2">Kalender kehadiran bulan ini</p>
                    
                    {{-- Legenda --}}
                    <div class="d-flex justify-content-center gap-3 mt-3 flex-wrap">
                        <small><span class="badge bg-success">Hadir</span></small>
                        <small><span class="badge bg-warning">Telat</span></small>
                        <small><span class="badge bg-info">Izin</span></small>
                        <small><span class="badge bg-danger">Alpa</span></small>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: TAGIHAN KEUANGAN --}}
        <div class="col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Tagihan Keuangan</h5>

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
                    <div class="mb-3 p-3 border rounded text-center">
                        <small class="text-muted">Tidak ada tagihan tertunggak.</small>
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

{{-- MODAL UNTUK FOTO BESAR --}}
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📸 Foto Presensi Hari Ini</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid rounded border" alt="Foto Presensi">
            </div>
        </div>
    </div>
</div>

{{-- MODAL UNTUK JURNAL HARI INI --}}
<div class="modal fade" id="jurnalModalToday" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📝 Jurnal Kegiatan Hari Ini</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="bg-light p-4 rounded">
                    <p id="modalJurnalToday" style="white-space: pre-line;"></p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL ABSEN MASUK (dengan Selfie Camera) --}}
<div class="modal fade" id="modalAbsenMasuk" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📸 Absen Masuk - Ambil Foto Selfie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <video id="video" width="100%" height="300" autoplay class="border rounded mb-3"></video>
                <canvas id="canvas" style="display:none;"></canvas>
                <img id="photo" style="display:none;" class="border rounded mb-3" width="100%">
                
                <button type="button" class="btn btn-primary mb-2" id="snap" onclick="takeSnapshot()">
                    <i class="bi bi-camera-fill"></i> Ambil Foto
                </button>
                <button type="button" class="btn btn-secondary mb-2" id="retake" onclick="retakePhoto()" style="display:none;">
                    <i class="bi bi-arrow-clockwise"></i> Ambil Ulang
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="submitAbsen" onclick="submitAbsenMasuk()" disabled>
                    <i class="bi bi-check-circle-fill"></i> Konfirmasi Absen
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL ABSEN KELUAR (dengan Form Jurnal) --}}
<div class="modal fade" id="modalAbsenKeluar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('presensi.keluar') }}" id="formKeluar">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">📝 Absen Keluar - Isi Jurnal Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Wajib diisi!</strong> Tuliskan kegiatan apa saja yang Anda lakukan hari ini (minimal 50 karakter).
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jurnal Kegiatan Hari Ini</label>
                        <textarea name="jurnal_kegiatan" 
                                  class="form-control" 
                                  rows="8" 
                                  placeholder="Contoh: Hari ini saya melakukan...&#10;1. Instalasi jaringan LAN di ruang server&#10;2. Konfigurasi router dan switch&#10;3. Testing koneksi internet&#10;4. Dokumentasi hasil pekerjaan"
                                  required
                                  minlength="50"
                                  maxlength="1000"></textarea>
                        <small class="text-muted">
                            <span id="charCount">0</span>/1000 karakter (minimal 50)
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right"></i> Konfirmasi Keluar
                    </button>
                </div>
            </form>
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
#calendarTable td {
    padding: 8px 4px;
    cursor: pointer;
    position: relative;
}
#calendarTable td:hover {
    background-color: #f8f9fa;
}
.calendar-day {
    width: 100%;
    height: 45px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border-radius: 4px;
}
.calendar-day.today {
    border: 2px solid #0d6efd;
}
.cursor-pointer {
    cursor: pointer;
}
.cursor-pointer:hover {
    opacity: 0.8;
}
</style>

<script>
// ==================== UPDATE REAL-TIME CLOCK ====================
function updateTime() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
}
setInterval(updateTime, 1000);
updateTime();

// ==================== KALENDER FUNGSIONAL ====================
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
const today = new Date();

// Data presensi dari server
// Data presensi dari server
const presensiData = @json($presensiData ?? []);

function renderCalendar() {
    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                        "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    
    document.getElementById('calendarMonth').textContent = `${monthNames[currentMonth]} ${currentYear}`;
    
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    
    let html = '<tr>';
    let dayCount = 1;
    
    // Fill empty cells before first day
    for (let i = 0; i < firstDay; i++) {
        html += '<td></td>';
    }
    
    // Fill days
    for (let day = 1; day <= daysInMonth; day++) {
        const date = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const presensi = presensiData[date];
        
        let badgeColor = 'secondary';
        let statusText = '';
        let timeInfo = '';
        
        if (presensi) {
            if (presensi.status === 'hadir') {
                badgeColor = 'success';
                statusText = '✓';
            } else if (presensi.status === 'terlambat') {
                badgeColor = 'warning';
                statusText = '⚠';
            } else if (presensi.status === 'izin' || presensi.status === 'sakit') {
                badgeColor = 'info';
                statusText = 'I';
            } else if (presensi.status === 'alpa') {
                badgeColor = 'danger';
                statusText = 'A';
            }
            
            if (presensi.jam_masuk) {
                timeInfo = `<small style="font-size: 9px;">${presensi.jam_masuk.substring(0, 5)}</small>`;
            }
        }
        
        const isToday = (today.getDate() === day && today.getMonth() === currentMonth && today.getFullYear() === currentYear);
        const todayClass = isToday ? 'today' : '';
        
        html += `<td>
            <div class="calendar-day ${todayClass}">
                <div class="fw-bold">${day}</div>
                ${statusText ? `<span class="badge bg-${badgeColor} badge-sm">${statusText}</span>` : ''}
                ${timeInfo}
            </div>
        </td>`;
        
        if ((firstDay + day) % 7 === 0) {
            html += '</tr><tr>';
        }
    }
    
    html += '</tr>';
    document.getElementById('calendarBody').innerHTML = html;
}

function prevMonth() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    renderCalendar();
}

function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    renderCalendar();
}

// Initialize calendar
renderCalendar();

// ==================== WEBCAM SELFIE ====================
let video = document.getElementById('video');
let canvas = document.getElementById('canvas');
let photo = document.getElementById('photo');
let capturedImage = null;

// Start camera when modal opens
document.getElementById('modalAbsenMasuk').addEventListener('shown.bs.modal', function () {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            video.srcObject = stream;
            video.style.display = 'block';
            photo.style.display = 'none';
            document.getElementById('snap').style.display = 'inline-block';
            document.getElementById('retake').style.display = 'none';
            document.getElementById('submitAbsen').disabled = true;
        })
        .catch(err => {
            Swal.fire('Error', 'Tidak dapat mengakses kamera: ' + err, 'error');
        });
});

// Stop camera when modal closes
document.getElementById('modalAbsenMasuk').addEventListener('hidden.bs.modal', function () {
    if (video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
    }
    capturedImage = null;
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
    capturedImage = null;
}

function submitAbsenMasuk() {
    if (!capturedImage) {
        Swal.fire('Error', 'Silakan ambil foto terlebih dahulu!', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Konfirmasi Absen Masuk',
        text: 'Apakah Anda yakin ingin melakukan absen masuk?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Absen!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit via AJAX
            fetch("{{ route('presensi.masuk') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    foto_masuk: capturedImage
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Terjadi kesalahan: ' + err, 'error');
            });
        }
    });
}

// ==================== ABSEN KELUAR ====================
function cekJamKeluar() {
    const now = new Date();
    const currentHour = now.getHours();

    if (currentHour < 16) {
        Swal.fire({
            icon: 'error',
            title: 'Belum Waktunya!',
            text: 'Tombol keluar hanya bisa diklik setelah jam 16:00',
            confirmButtonColor: '#dc3545'
        });
        return false;
    }

    // Buka modal jurnal
    const modal = new bootstrap.Modal(document.getElementById('modalAbsenKeluar'));
    modal.show();
}

// Character counter untuk jurnal
document.querySelector('textarea[name="jurnal_kegiatan"]')?.addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});

// ==================== MODAL FUNCTIONS ====================
// Function untuk show image modal
function showImageModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

// Function untuk show jurnal modal
function showJurnalModal(jurnal) {
    document.getElementById('modalJurnalToday').textContent = jurnal;
    const modal = new bootstrap.Modal(document.getElementById('jurnalModalToday'));
    modal.show();
}
</script>
@endsection