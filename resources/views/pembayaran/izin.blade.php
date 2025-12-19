@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">📝 Pengajuan Izin / Sakit</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('presensi.store-izin') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Pilihan Kategori --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Kategori Pengajuan</label>
                            <div class="d-flex gap-3">
                                <div class="form-check custom-option">
                                    <input class="form-check-input" type="radio" name="kategori" id="izin" value="izin" required>
                                    <label class="form-check-label" for="izin">
                                        🗓️ Izin (Keperluan Pribadi)
                                    </label>
                                </div>
                                <div class="form-check custom-option">
                                    <input class="form-check-input" type="radio" name="kategori" id="sakit" value="sakit">
                                    <label class="form-check-label" for="sakit">
                                        💊 Sakit (Kesehatan)
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Input Alasan --}}
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-semibold">Alasan Lengkap</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="4" placeholder="Jelaskan alasan izin/sakit..." required></textarea>
                        </div>

                        {{-- Upload Foto --}}
                        <div class="mb-4">
                            <label for="bukti" class="form-label fw-semibold">Bukti Foto / Surat Dokter</label>
                            <input type="file" class="form-control" id="bukti" name="bukti" accept="image/*" required>
                            <div class="form-text">Format: JPG, PNG. Maksimal 2MB.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">Kirim Pengajuan</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-light py-2">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection