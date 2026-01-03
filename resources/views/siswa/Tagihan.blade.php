@extends('layouts.app')

@section('page_title', 'Tagihan Saya')

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        @forelse($tagihans as $index => $t)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 position-relative" style="border-radius: 20px; border: 1px solid var(--border-color) !important;">
                
                {{-- Label Urutan --}}
                <div class="position-absolute top-0 start-0 bg-primary text-white px-3 py-1 small fw-bold" style="border-radius: 20px 0 20px 0;">
                    #{{ $t->bulan_ke }}
                </div>

                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4 mt-2">
                        {{-- Badge Status Modern --}}
                        @php
                            $statusClass = [
                                'dibayar' => 'bg-success-subtle text-success',
                                'menunggu_konfirmasi' => 'bg-warning-subtle text-warning-emphasis',
                                'ditolak' => 'bg-danger-subtle text-danger',
                                'belum_bayar' => 'bg-secondary-subtle text-secondary'
                            ][$t->status] ?? 'bg-light text-dark';
                        @endphp
                        <span class="badge border-0 px-3 py-2 rounded-pill {{ $statusClass }}">
                            {{ ucwords(str_replace('_', ' ', $t->status)) }}
                        </span>
                        
                        <div class="text-end">
                            <small class="text-muted d-block small">Jatuh Tempo</small>
                            <small class="fw-bold text-dark">{{ \Carbon\Carbon::parse($t->jatuh_tempo)->translatedFormat('d M Y') }}</small>
                        </div>
                    </div>
                    
                    {{-- Nama Tagihan + Keterangan Bulan --}}
                    <h5 class="fw-bold text-dark mb-1">
                        {{ $t->tagihan->nama_tagihan }} 
                        <span class="text-primary">(Bulan ke-{{ $t->bulan_ke }})</span>
                    </h5>
                    <h3 class="fw-bold text-primary mb-4">Rp {{ number_format($t->tagihan->nominal, 0, ',', '.') }}</h3>

                    <hr class="opacity-50 mb-4" style="border-top: 1px dashed var(--border-color);">

                    @if($t->status == 'belum_bayar' || $t->status == 'ditolak')
                        @if($t->status == 'ditolak')
                            <div class="alert alert-danger border-0 small mb-3 p-2 rounded-3">
                                <i class="bi bi-info-circle me-1"></i> <strong>Alasan:</strong> {{ $t->catatan_admin }}
                            </div>
                        @endif
                        <button class="btn btn-primary w-100 fw-bold py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBayar{{ $t->id }}">
                            <i class="bi bi-cloud-arrow-up me-2"></i> Upload Bukti Bayar
                        </button>
                    @elseif($t->status == 'menunggu_konfirmasi')
                        <div class="bg-light text-center py-2 rounded-3 border border-light-subtle">
                            <span class="text-muted small fw-bold"><i class="bi bi-hourglass-split me-1"></i> Sedang Diverifikasi</span>
                        </div>
                    @else
                        <div class="bg-success-subtle text-center py-2 rounded-3 border border-success-subtle">
                            <span class="text-success small fw-bold">
                                <i class="bi bi-patch-check-fill me-1"></i> Lunas: {{ \Carbon\Carbon::parse($t->tanggal_bayar)->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal Bayar --}}
        <div class="modal fade" id="modalBayar{{ $t->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold">Upload Bukti Bayar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    {{-- Pastikan form memiliki enctype="multipart/form-data" --}}
                    <form action="{{ route('siswa.tagihan.bayar', $t->id) }}" 
                        method="POST" 
                        enctype="multipart/form-data">
                        @csrf
                        
                        <div class="modal-body px-4">
                            <div class="p-3 bg-light rounded-4 mb-3 border border-dashed">
                                <p class="small text-muted mb-0">
                                    Tagihan: <strong>{{ $t->tagihan->nama_tagihan }} (Bulan ke-{{ $t->bulan_ke }})</strong>
                                </p>
                                <p class="h5 fw-bold text-primary mb-0">
                                    Rp {{ number_format($t->tagihan->nominal, 0, ',', '.') }}
                                </p>
                            </div>
                            
                            <label class="form-label small fw-bold text-muted">Pilih File Foto/Scan Struk</label>
                            
                            {{-- PENTING: name="bukti_pembayaran" harus sesuai dengan validation di controller --}}
                            <input type="file" 
                                name="bukti_pembayaran" 
                                class="form-control border-0 bg-light py-2" 
                                accept="image/*" 
                                required 
                                style="border-radius: 10px;">
                            
                            <div class="form-text small mt-2">Format: JPG, PNG (Maks. 5MB)</div>
                            
                            {{-- Preview gambar (opsional) --}}
                            <div id="preview-{{ $t->id }}" class="mt-3" style="display: none;">
                                <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                        </div>
                        
                        <div class="modal-footer border-0 px-4 pb-4 mt-2">
                            <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary fw-bold px-4 rounded-3">
                                <i class="bi bi-send me-1"></i> Kirim Bukti
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <img src="https://illustrations.popsy.co/gray/success.svg" alt="No Data" style="width: 200px;" class="mb-4 opacity-50">
            <h5 class="text-muted">Tidak ada data tagihan untuk saat ini.</h5>
        </div>
        @endforelse
    </div>
</div>

<style>
    /* Custom Subtle Colors */
    .bg-success-subtle { background-color: #e8f5e9 !important; }
    .bg-warning-subtle { background-color: #fff8e1 !important; }
    .bg-danger-subtle { background-color: #ffebee !important; }
    .bg-secondary-subtle { background-color: #f5f5f5 !important; }
    
    .border-dashed {
        border: 1px dashed var(--border-color) !important;
    }
</style>

{{-- JavaScript untuk preview gambar (opsional) --}}
                    <script>
                    document.querySelector('input[name="bukti_pembayaran"]').addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(event) {
                                const preview = document.querySelector('#preview-{{ $t->id }}');
                                const img = preview.querySelector('img');
                                img.src = event.target.result;
                                preview.style.display = 'block';
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                    </script>
@endsection