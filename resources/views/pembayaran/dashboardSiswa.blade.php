
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
                    
                    {{-- Status Presensi Hari Ini --}}
                    @php
                        $presensiHariIni = \App\Models\Presensi::where('user_id', auth()->id())
                            ->where('tanggal', now()->toDateString())
                            ->first();
                    @endphp

                    @if($presensiHariIni)
                        <div class="alert alert-primary mb-3 text-center">
                            <strong>Status: {{ strtoupper($presensiHariIni->status) }}</strong>
                            <br>
                            <small>
                                Masuk: {{ \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i') }}
                                @if($presensiHariIni->jam_keluar)
                                    | Keluar: {{ \Carbon\Carbon::parse($presensiHariIni->jam_keluar)->format('H:i') }}
                                @endif
                            </small>
                        </div>
                    @endif
                    
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            {{-- FORM MASUK --}}
                            <form method="POST" action="{{ route('presensi.masuk') }}" id="formMasuk">
                                @csrf
                                <button type="submit" 
                                        class="btn btn-outline-success w-100 py-3 d-flex align-items-center justify-content-center gap-2"
                                        {{ $presensiHariIni ? 'disabled' : '' }}>
                                    <i class="bi bi-box-arrow-in-right" style="font-size: 1.5rem;"></i>
                                    <span class="fw-semibold">Masuk</span>
                                </button>
                            </form>
                        </div>
                        <div class="col-6">
                            {{-- FORM KELUAR --}}
                            <form method="POST" action="{{ route('presensi.keluar') }}" id="formKeluar">
                                @csrf
                                <button type="button" 
                                        onclick="cekJamKeluar()"
                                        class="btn btn-outline-danger w-100 py-3 d-flex align-items-center justify-content-center gap-2"
                                        {{ (!$presensiHariIni || $presensiHariIni->jam_keluar) ? 'disabled' : '' }}>
                                    <i class="bi bi-box-arrow-right" style="font-size: 1.5rem;"></i>
                                    <span class="fw-semibold">Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- BUTTON PENGAJUAN IZIN --}}
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

                    <a href="#" class="btn btn-link text-decoration-none d-block text-center mt-3">
                        See more →
                    </a>
                </div>
            </div>
        </div>

        {{-- KOLOM TENGAH: KALENDER --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">{{ now()->format('F Y') }}</h5>
                    
                    <table class="table table-borderless table-sm text-center">
                        <thead>
                            <tr class="text-muted">
                                <th>Min</th><th>Sen</th><th>Sel</th><th>Rab</th><th>Kam</th><th>Jum</th><th>Sab</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-2"><div class="text-muted small">1</div></td>
                                <td class="py-2"><div class="text-muted small">2</div></td>
                                <td class="py-2"><div class="text-muted small">3</div></td>
                                <td class="py-2"><div class="text-muted small">4</div></td>
                                <td class="py-2 table-active align-middle border rounded border-primary bg-light">
                                    @if($presensiHariIni)
                                        <div class="d-flex flex-column justify-content-center" style="line-height: 1.2;">
                                            <span class="text-success fw-bold" style="font-size: 10px;">
                                                {{ \Carbon\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i') }}
                                            </span>
                                            @if($presensiHariIni->jam_keluar)
                                                <span class="text-danger fw-bold" style="font-size: 10px;">
                                                    {{ \Carbon\Carbon::parse($presensiHariIni->jam_keluar)->format('H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-primary small fw-bold">Now</div>
                                    @endif
                                </td>
                                <td class="py-2"><div class="text-muted small">6</div></td>
                                <td class="py-2"><div class="text-muted small">7</div></td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-center text-muted small">Kalender kehadiran bulan ini</p>
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
</style>

<script>
// Update Real-time Clock
function updateTime() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
}
setInterval(updateTime, 1000);
updateTime();

// Cek Jam Keluar (harus setelah jam 16:00)
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

    // Jika sudah jam 16:00, submit form
    document.getElementById('formKeluar').submit();
}

// Konfirmasi sebelum absen masuk
document.getElementById('formMasuk')?.addEventListener('submit', function(e) {
    e.preventDefault();
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
            this.submit();
        }
    });
});
</script>
@endsection