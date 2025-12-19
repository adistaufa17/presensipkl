@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- HEADER --}}
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <div>
                    <h2 class="fw-bold mb-1">Pengajuan Izin / Sakit</h2>
                    <p class="text-muted mb-0">Isi formulir di bawah untuk mengajukan izin atau sakit</p>
                </div>
            </div>

            {{-- CARD FORM --}}
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('presensi.izin.store') }}">
                        @csrf

                        {{-- PILIH TANGGAL --}}
                        <div class="mb-4">
                            <label for="tanggal" class="form-label fw-bold">
                                <i class="bi bi-calendar-event text-primary"></i> Tanggal <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control form-control-lg @error('tanggal') is-invalid @enderror" 
                                   id="tanggal" 
                                   name="tanggal" 
                                   value="{{ old('tanggal', now()->toDateString()) }}"
                                   max="{{ now()->toDateString() }}"
                                   required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                📌 Pilih tanggal yang ingin diajukan izin/sakit (hari ini atau sebelumnya)
                            </small>
                        </div>

                        {{-- PILIH STATUS (IZIN / SAKIT) --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-clipboard-check text-warning"></i> Status <span class="text-danger">*</span>
                            </label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="form-check form-check-card">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="status" 
                                               id="statusIzin" 
                                               value="izin" 
                                               {{ old('status', 'izin') == 'izin' ? 'checked' : '' }}
                                               required>
                                        <label class="form-check-label w-100" for="statusIzin">
                                            <div class="card border-2 border-warning h-100">
                                                <div class="card-body text-center">
                                                    <i class="bi bi-file-earmark-text fs-1 text-warning mb-2"></i>
                                                    <h6 class="fw-bold">Izin</h6>
                                                    <small class="text-muted">Untuk keperluan pribadi</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check form-check-card">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="status" 
                                               id="statusSakit" 
                                               value="sakit" 
                                               {{ old('status') == 'sakit' ? 'checked' : '' }}
                                               required>
                                        <label class="form-check-label w-100" for="statusSakit">
                                            <div class="card border-2 border-danger h-100">
                                                <div class="card-body text-center">
                                                    <i class="bi bi-heart-pulse fs-1 text-danger mb-2"></i>
                                                    <h6 class="fw-bold">Sakit</h6>
                                                    <small class="text-muted">Kondisi tidak sehat</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('status')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- KETERANGAN --}}
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-bold">
                                <i class="bi bi-chat-left-text text-info"></i> Keterangan
                            </label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="5" 
                                      placeholder="Jelaskan alasan izin/sakit Anda... (Opsional)">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Berikan penjelasan singkat tentang alasan Anda</small>
                        </div>

                        {{-- INFO PENTING --}}
                        <div class="alert alert-info mb-4">
                            <h6 class="fw-bold mb-2">
                                <i class="bi bi-info-circle-fill"></i> Informasi Penting:
                            </h6>
                            <ul class="mb-0 small">
                                <li>Pengajuan izin/sakit bisa untuk <strong>hari ini atau hari sebelumnya</strong></li>
                                <li>Tidak bisa mengajukan izin untuk <strong>tanggal yang belum terjadi</strong></li>
                                <li>Jika sudah absen hadir, <strong>tidak bisa mengajukan izin</strong> di tanggal yang sama</li>
                                <li>Untuk sakit lebih dari 3 hari, disarankan melampirkan surat keterangan dokter ke pembimbing</li>
                            </ul>
                        </div>

                        {{-- TOMBOL SUBMIT --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-send-fill me-2"></i> Ajukan Sekarang
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- RIWAYAT PENGAJUAN --}}
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-clock-history"></i> Riwayat Pengajuan Terakhir
                        </h6>
                        <a href="{{ route('presensi.riwayat') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $riwayatIzin = \App\Models\Presensi::where('user_id', auth()->id())
                            ->whereIn('status', ['izin', 'sakit'])
                            ->orderBy('tanggal', 'desc')
                            ->take(5)
                            ->get();
                    @endphp

                    @forelse($riwayatIzin as $izin)
                    <div class="border-bottom py-3">
                        <div class="d-flex gap-3">
                            {{-- Foto Thumbnail jika ada --}}
                            @if($izin->foto_masuk)
                            <img src="{{ asset('storage/' . $izin->foto_masuk) }}" 
                                 class="rounded border cursor-pointer"
                                 width="60" 
                                 height="60"
                                 style="object-fit: cover;"
                                 onclick="showImageModal('{{ asset('storage/' . $izin->foto_masuk) }}', '{{ \Carbon\Carbon::parse($izin->tanggal)->format('d M Y') }}')"
                                 alt="Foto">
                            @else
                            <div class="bg-light rounded border d-flex align-items-center justify-content-center"
                                 style="width: 60px; height: 60px; min-width: 60px;">
                                <i class="bi bi-person-circle fs-3 text-muted"></i>
                            </div>
                            @endif
                            
                            {{-- Info --}}
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="badge {{ $izin->status == 'izin' ? 'bg-warning text-dark' : 'bg-danger' }}">
                                        {{ strtoupper($izin->status) }}
                                    </span>
                                    <span class="fw-bold">{{ \Carbon\Carbon::parse($izin->tanggal)->format('d M Y') }}</span>
                                    <span class="text-muted small">({{ \Carbon\Carbon::parse($izin->tanggal)->locale('id')->isoFormat('dddd') }})</span>
                                </div>
                                @if($izin->keterangan)
                                    <small class="text-muted d-block">{{ Str::limit($izin->keterangan, 80) }}</small>
                                @else
                                    <small class="text-muted fst-italic">Tidak ada keterangan</small>
                                @endif
                                
                                {{-- Tombol Lihat Jurnal jika ada --}}
                                @if($izin->jurnal_kegiatan)
                                <button class="btn btn-sm btn-outline-primary mt-2" 
                                        onclick="showJurnalModal('{{ addslashes($izin->jurnal_kegiatan) }}', '{{ \Carbon\Carbon::parse($izin->tanggal)->format('d M Y') }}')">
                                    <i class="bi bi-journal-text"></i> Lihat Jurnal
                                </button>
                                @endif
                            </div>
                            
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 mb-2 d-block"></i>
                        <small>Belum ada riwayat pengajuan izin/sakit</small>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- QUICK INFO CARD --}}
            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <div class="card border-warning h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-exclamation-triangle text-warning fs-1 mb-2"></i>
                            <h6 class="fw-bold">Penting!</h6>
                            <small class="text-muted">Ajukan sebelum/pada hari yang sama</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-clock-history text-info fs-1 mb-2"></i>
                            <h6 class="fw-bold">Fleksibel</h6>
                            <small class="text-muted">Bisa untuk hari ini atau kemarin</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle text-success fs-1 mb-2"></i>
                            <h6 class="fw-bold">Tercatat</h6>
                            <small class="text-muted">Langsung masuk sistem</small>
                        </div>
                    </div>
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
                <h5 class="modal-title" id="imageModalLabel">Foto Presensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid rounded" alt="Foto Presensi">
            </div>
        </div>
    </div>
</div>

{{-- MODAL UNTUK JURNAL LENGKAP --}}
<div class="modal fade" id="jurnalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jurnalModalLabel">Jurnal Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="bg-light p-4 rounded">
                    <p id="modalJurnal" style="white-space: pre-line;"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-check-card .form-check-input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.form-check-card .card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-check-card input:checked ~ label .card {
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.form-check-card input:not(:checked) ~ label .card {
    opacity: 0.7;
}

.form-check-card:hover .card {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.cursor-pointer {
    cursor: pointer;
}
.cursor-pointer:hover {
    opacity: 0.8;
}
</style>

<script>
// Auto-resize textarea
document.getElementById('keterangan')?.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});

// Set max date to today (prevent future dates)
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('tanggal');
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('max', today);
});

// Function untuk show image modal
function showImageModal(imageUrl, tanggal) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('imageModalLabel').textContent = 'Foto Presensi - ' + tanggal;
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

// Function untuk show jurnal modal
function showJurnalModal(jurnal, tanggal) {
    document.getElementById('modalJurnal').textContent = jurnal;
    document.getElementById('jurnalModalLabel').textContent = 'Jurnal Kegiatan - ' + tanggal;
    const modal = new bootstrap.Modal(document.getElementById('jurnalModal'));
    modal.show();
}
</script>
@endsection