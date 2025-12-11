@extends('layouts.app')

@section('content')
{{-- SweetAlert & JQuery untuk Pop Up --}}
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
                 alt="Avatar" class="rounded-circle" width="40" height="40">
        </div>
    </div>

    {{-- Alert Sukses Pengajuan Izin --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                    
                    @if(isset($presensiHariIni))
                        <div class="alert alert-primary mb-3 text-center">
                            Status: <strong>{{ strtoupper($presensiHariIni->status) }}</strong>
                            <br>
                            Masuk: {{ \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i') }}
                            @if($presensiHariIni->jam_keluar)
                            | Keluar: {{ \Carbon\Carbon::parse($presensiHariIni->jam_keluar)->format('H:i') }}
                            @endif
                        </div>
                    @endif

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            {{-- BUTTON MASUK --}}
                            <button onclick="prosesMasuk()" 
                                    class="btn btn-outline-success w-100 py-3 d-flex align-items-center justify-content-center gap-2"
                                    {{ isset($presensiHariIni) ? 'disabled' : '' }}>
                                <i class="bi bi-box-arrow-in-right" style="font-size: 1.5rem;"></i>
                                <span class="fw-semibold">Masuk</span>
                            </button>
                        </div>
                        <div class="col-6">
                            {{-- BUTTON KELUAR --}}
                            <button onclick="prosesKeluar()" 
                                    class="btn btn-outline-danger w-100 py-3 d-flex align-items-center justify-content-center gap-2"
                                    {{ (!isset($presensiHariIni) || $presensiHariIni->jam_keluar) ? 'disabled' : '' }}>
                                <i class="bi bi-box-arrow-right" style="font-size: 1.5rem;"></i>
                                <span class="fw-semibold">Keluar</span>
                            </button>
                        </div>
                    </div>

                    {{-- BUTTON PENGAJUAN --}}
                    <a href="{{ route('presensi.izin') }}" class="btn btn-light w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Pengajuan Izin / Sakit</span>
                    </a>
                </div>
            </div>

            {{-- RINGKASAN KEHADIRAN (DATA DINAMIS) --}}
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Ringkasan Kehadiran</h5>
                        <span class="badge bg-primary">Tahun Ini</span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div style="width: 12px; height: 12px; background: #28a745; border-radius: 2px;" class="me-2"></div>
                                    <small class="text-muted">Hadir</small>
                                </div>
                                {{-- PERBAIKAN 1: Tambah '?? 0' agar tidak error --}}
                                <h3 class="fw-bold mb-0">{{ $summary['hadir'] ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div style="width: 12px; height: 12px; background: #6c757d; border-radius: 2px;" class="me-2"></div>
                                    <small class="text-muted">Izin/Skt</small>
                                </div>
                                <h3 class="fw-bold mb-0">{{ $summary['izin_total'] ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div style="width: 12px; height: 12px; background: #dc3545; border-radius: 2px;" class="me-2"></div>
                                    <small class="text-muted">Alpa</small>
                                </div>
                                <h3 class="fw-bold mb-0">{{ $summary['alpa'] ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>

                    {{-- PERBAIKAN 2: GRAFIK BULANAN DINAMIS (JAN-DES) --}}
                    {{-- Menggantikan kode grafik statis yang lama --}}
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3 text-muted">Statistik Bulanan</h6>
                        
                        {{-- Container Grafik --}}
                        <div class="d-flex align-items-end justify-content-between gap-1" style="height: 150px;">
                            @if(isset($dataGrafik))
                                @foreach($dataGrafik as $data)
                                <div class="d-flex flex-column align-items-center flex-fill" 
                                     title="Bulan {{ $data['label'] }}: {{ $data['total'] }} Hadir">
                                    
                                    {{-- Angka Kecil di atas batang (Opsional) --}}
                                    @if($data['total'] > 0)
                                        <span style="font-size: 9px;" class="mb-1 text-dark fw-bold">{{ $data['total'] }}</span>
                                    @endif
    
                                    {{-- Batang Grafik --}}
                                    <div class="w-100 {{ $data['color'] }} rounded-top" 
                                         style="height: {{ $data['height'] }}; transition: height 0.5s ease; min-height: 4px;"></div>
                                    
                                    {{-- Label Bulan (Jan, Feb...) --}}
                                    <small class="text-muted mt-2" style="font-size: 10px;">{{ $data['label'] }}</small>
                                </div>
                                @endforeach
                            @else
                                <p class="text-center w-100 small text-muted">Data grafik belum tersedia (Update Controller)</p>
                            @endif
                        </div>
                    </div>

                    {{-- Legend Warna --}}
                    <div class="d-flex gap-3 justify-content-center mt-3 pt-2 border-top flex-wrap">
                        <small class="text-muted d-flex align-items-center gap-1">
                            <div class="bg-success rounded-circle" style="width:8px; height:8px;"></div> Rajin (>20)
                        </small>
                        <small class="text-muted d-flex align-items-center gap-1">
                            <div class="bg-primary rounded-circle" style="width:8px; height:8px;"></div> Cukup (10-20)
                        </small>
                        <small class="text-muted d-flex align-items-center gap-1">
                            <div class="bg-warning rounded-circle" style="width:8px; height:8px;"></div> Kurang
                        </small>
                    </div>

                    <div class="d-flex gap-3 justify-content-center mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 12px; height: 12px; background: #28a745; border-radius: 2px;"></div>
                            <small class="text-muted">Kehadiran Global: {{ number_format($persentaseHadir ?? 0, 0) }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM TENGAH: KALENDER --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">{{ now()->isoFormat('MMMM YYYY') }}</h5>
                    
                    <table class="table table-borderless table-sm text-center">
                        <thead>
                            <tr class="text-muted">
                                <th>Min</th><th>Sen</th><th>Sel</th><th>Rab</th><th>Kam</th><th>Jum</th><th>Sab</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Baris 1 Contoh --}}
                            <tr>
                                <td class="py-2"><div class="text-muted small">01</div></td>
                                <td class="py-2"><div class="text-muted small">02</div></td>
                                <td class="py-2"><div class="text-muted small">03</div></td>
                                <td class="py-2"><div class="text-muted small">04</div></td>
                                
                                {{-- =============================================== --}}
                                {{-- BAGIAN INI YANG DIMODIFIKASI (HARI INI / NOW) --}}
                                {{-- =============================================== --}}
                                <td class="py-2 table-active align-middle border rounded border-primary bg-light">
                                    @if(isset($presensiHariIni))
                                        {{-- Jika SUDAH Absen, Tampilkan Jam --}}
                                        <div class="d-flex flex-column justify-content-center" style="line-height: 1.2;">
                                            {{-- Jam Masuk (Hijau) --}}
                                            <span class="text-success fw-bold" style="font-size: 10px;" title="Masuk">
                                                {{ \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i') }}
                                            </span>

                                            {{-- Jam Keluar (Merah) - Jika ada --}}
                                            @if($presensiHariIni->jam_keluar)
                                                <span class="text-danger fw-bold" style="font-size: 10px;" title="Keluar">
                                                    {{ \Carbon\Carbon::parse($presensiHariIni->jam_keluar)->format('H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        {{-- Jika BELUM Absen, Tampilkan 'Now' --}}
                                        <div class="text-primary small fw-bold">Now</div>
                                    @endif
                                </td>
                                {{-- =============================================== --}}

                                <td class="py-2"><div class="text-muted small">06</div></td>
                                <td class="py-2"><div class="text-muted small">07</div></td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-center text-muted small">Kalender...</p>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: TAGIHAN --}}
        <div class="col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Tagihan Keuangan</h5>
                    @forelse ($tagihanBelumBayar ?? [] as $tagihan)
                    <div class="mb-3 p-3 border rounded">
                        <h6 class="fw-bold">{{ $tagihan->nama_tagihan }}</h6>
                        <span class="text-danger fw-bold">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <div class="mb-3 p-3 border rounded text-center">
                        <small class="text-muted">Tidak ada tagihan tertunggak.</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card { transition: transform 0.2s; }
.card:hover { transform: translateY(-2px); }
.btn-outline-success:hover, .btn-outline-danger:hover { transform: scale(1.02); }
</style>

<script>
// Update Jam Real-time
function updateTime() {
    const now = new Date();
    document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID', { hour12: false });
}
setInterval(updateTime, 1000);
updateTime();

// Fungsi AJAX Masuk
function prosesMasuk() {
    $.ajax({
        url: "{{ route('presensi.masuk') }}",
        type: "POST",
        data: { _token: "{{ csrf_token() }}" },
        success: function(response) {
            if(response.status === 'success') {
                Swal.fire({
                    icon: 'success', 
                    title: response.message,
                    color: '#28a745', 
                    confirmButtonColor: '#28a745'
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
        }
    });
}

// Fungsi AJAX Keluar
function prosesKeluar() {
    const now = new Date();
    const currentHour = now.getHours();

    // Cek jika jam < 16 (4 sore)
    if (currentHour < 16) {
        Swal.fire({
            icon: 'error',
            title: 'Belum Waktunya!',
            text: 'Tombol keluar tidak dapat diklik sebelum jam 16:00',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    // Jika sudah jam 16:00
    $.ajax({
        url: "{{ route('presensi.keluar') }}",
        type: "POST",
        data: { _token: "{{ csrf_token() }}" },
        success: function(response) {
            if(response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Pulang',
                    text: response.message,
                    confirmButtonColor: '#dc3545'
                }).then(() => location.reload());
            } else {
                Swal.fire('Gagal', response.message, 'error');
            }
        }
    });
}
</script>
@endsection