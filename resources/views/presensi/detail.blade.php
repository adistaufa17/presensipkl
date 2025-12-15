@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">📋 Detail Presensi</h2>
            <p class="text-muted mb-0">Informasi lengkap presensi siswa</p>
        </div>
        <a href="{{ route('presensi.rekap') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        {{-- KOLOM KIRI: INFO SISWA & STATUS --}}
        <div class="col-lg-4">
            {{-- INFO SISWA --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($presensi->user->name ?? 'User') }}&background=4a90e2&color=fff&size=120" 
                         class="rounded-circle mb-3 border border-3" 
                         width="120" height="120">
                    <h4 class="fw-bold mb-1">{{ $presensi->user->name ?? 'N/A' }}</h4>
                    <p class="text-muted mb-0">{{ $presensi->user->email ?? '-' }}</p>
                    <span class="badge bg-secondary mt-2">{{ strtoupper($presensi->user->role ?? 'SISWA') }}</span>
                </div>
            </div>

            {{-- STATUS PRESENSI --}}
            <div class="card shadow-sm">
                <div class="card-header bg-{{ $presensi->status_color }} text-white">
                    <h5 class="mb-0">Status: {{ strtoupper($presensi->status) }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Tanggal</label>
                        <h6 class="fw-bold">{{ \Carbon\Carbon::parse($presensi->tanggal)->format('d F Y') }}</h6>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="text-muted small">Jam Masuk</label>
                            <h6 class="fw-bold text-success">
                                @if($presensi->jam_masuk)
                                    {{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i:s') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </h6>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="text-muted small">Jam Keluar</label>
                            <h6 class="fw-bold text-danger">
                                @if($presensi->jam_keluar)
                                    {{ \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i:s') }}
                                @else
                                    <span class="text-muted">Belum Keluar</span>
                                @endif
                            </h6>
                        </div>
                    </div>

                    @if($presensi->jam_masuk && $presensi->jam_keluar)
                    <div class="alert alert-info">
                        <strong>Durasi Kerja:</strong> {{ $presensi->durasi_kerja }} jam
                    </div>
                    @endif

                    @if($presensi->keterangan)
                    <div class="mt-3">
                        <label class="text-muted small">Keterangan</label>
                        <p class="border p-2 rounded bg-light">{{ $presensi->keterangan }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: FOTO & JURNAL --}}
        <div class="col-lg-8">
            {{-- FOTO SELFIE --}}
            @if($presensi->foto_masuk)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="fw-bold mb-0">📸 Foto Selfie Saat Masuk</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ $presensi->foto_masuk_url }}" 
                         class="img-fluid rounded border" 
                         style="max-height: 400px; object-fit: cover;"
                         alt="Foto Presensi">
                    <p class="text-muted mt-2 mb-0">
                        <small>Diambil pada {{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i:s, d F Y') }}</small>
                    </p>
                </div>
            </div>
            @else
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center py-5">
                    <i class="bi bi-camera-video-off text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">Tidak ada foto selfie untuk presensi ini</p>
                </div>
            </div>
            @endif

            {{-- JURNAL KEGIATAN --}}
            @if($presensi->jurnal_kegiatan)
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="fw-bold mb-0">📝 Jurnal Kegiatan</h5>
                </div>
                <div class="card-body">
                    <div class="bg-light p-4 rounded border">
                        <p class="mb-0" style="white-space: pre-line;">{{ $presensi->jurnal_kegiatan }}</p>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <small>
                            <i class="bi bi-clock"></i> Ditulis saat absen keluar: 
                            {{ $presensi->jam_keluar ? \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i:s, d F Y') : '-' }}
                        </small>
                    </p>
                </div>
            </div>
            @else
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">
                        @if($presensi->jam_keluar)
                            Tidak ada jurnal kegiatan yang ditulis
                        @else
                            Siswa belum melakukan absen keluar dan menulis jurnal
                        @endif
                    </p>
                </div>
            </div>
            @endif
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
</style>
@endsection