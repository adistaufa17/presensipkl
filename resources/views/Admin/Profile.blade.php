@extends('layouts.app')

@section('page_title', 'Profil Saya')

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Kartu Info Profil (Kiri) --}}
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card border-0 shadow-sm text-center p-4 h-100" style="border-radius: 20px; border: 1px solid var(--border-color) !important;">
                <div class="card-body">
                    <div class="position-relative d-inline-block mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}&background=0D6EFD&color=fff&size=128" 
                             class="rounded-circle shadow-sm border border-4 border-white" 
                             alt="Avatar" style="width: 120px; height: 120px; object-fit: cover;">
                        <div class="position-absolute bottom-0 end-0 bg-success border border-white border-3 rounded-circle" style="width: 20px; height: 20px;" title="Online"></div>
                    </div>
                    
                    <h4 class="fw-bold mb-1 text-dark">{{ $user->nama_lengkap }}</h4>
                    <p class="text-muted mb-4">{{ $user->email }}</p>
                    
                    <div class="d-flex flex-column gap-2">
                        <div class="p-3 bg-light rounded-4 border-0">
                            <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Role Pengguna</small>
                            <span class="badge bg-primary-subtle text-primary border-0 px-3 py-2 rounded-pill">
                                <i class="bi bi-shield-check me-1"></i> {{ strtoupper($user->role) }}
                            </span>
                        </div>
                        
                        <div class="p-3 bg-light rounded-4 border-0">
                            <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Anggota Sejak</small>
                            <span class="text-dark fw-bold small">
                                <i class="bi bi-calendar3 me-1 text-primary"></i> {{ $user->created_at->translatedFormat('d F Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Edit Profil (Kanan) --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; border: 1px solid var(--border-color) !important;">
                <div class="card-body p-4">
                    {{-- Alert Success --}}
                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <div>{{ session('success') }}</div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Form Dinamis berdasarkan Role --}}
                    @php
                        $updateRoute = (auth()->user()->role === 'admin') ? route('admin.profile.update') : route('siswa.profile.update');
                    @endphp

                    <form action="{{ $updateRoute }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="bi bi-person-lines-fill text-primary fs-4"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Informasi Personal</h5>
                        </div>

                        <div class="row g-3 mb-5">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">NAMA LENGKAP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-person text-primary"></i></span>
                                    <input type="text" name="nama_lengkap" class="form-control bg-light border-0 py-2" 
                                           value="{{ old('nama_lengkap', $user->nama_lengkap) }}" style="border-radius: 0 10px 10px 0;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">ALAMAT EMAIL</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-primary"></i></span>
                                    <input type="email" name="email" class="form-control bg-light border-0 py-2" 
                                           value="{{ old('email', $user->email) }}" style="border-radius: 0 10px 10px 0;">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-50" style="border-top: 1px dashed var(--border-color);">

                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="bi bi-shield-lock-fill text-danger fs-4"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Keamanan Akun</h5>
                        </div>

                        {{-- Alert Error Validation --}}
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 rounded-4 mb-4">
                                <ul class="mb-0 small fw-bold">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">PASSWORD BARU</label>
                                <input type="password" name="password" class="form-control bg-light border-0 py-2" 
                                       placeholder="Kosongkan jika tidak ganti" style="border-radius: 10px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">KONFIRMASI PASSWORD</label>
                                <input type="password" name="password_confirmation" class="form-control bg-light border-0 py-2" 
                                       placeholder="Ulangi password baru" style="border-radius: 10px;">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm" style="border-radius: 12px;">
                                <i class="bi bi-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-subtle { background-color: #e7f1ff !important; }
    .input-group-text {
        border-right: none !important;
    }
    .form-control:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.05);
        border: 1px solid #0d6efd !important;
    }
</style>
@endsection