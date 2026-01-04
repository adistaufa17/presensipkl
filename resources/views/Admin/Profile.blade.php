@extends('layouts.app')

@section('page_title', 'Profil Saya')

@section('content')

<style>
    :root {
        --primary-color: #213448;
        --border-color: #b3b9c4ff;
        --radius: 16px;
    }

    .content-wrapper-fixed {
        padding: 0 12px;
    }

    .profile-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .bg-primary-subtle { 
        background-color: #e7f1ff !important; 
    }
    
    .input-group-text {
        border-right: none !important;
        background-color: #f8f9fa;
        border: 1px solid var(--border-color);
    }
    
    .form-control-custom {
        background-color: #f8f9fa;
        border: 1px solid var(--border-color);
        border-left: none;
        padding: 10px 16px;
    }
    
    .form-control-custom:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.05);
        border-color: #0d6efd !important;
    }

    .input-group:focus-within .input-group-text {
        border-color: #0d6efd !important;
        background-color: #e7f1ff;
    }

    .info-box {
        padding: 16px;
        background-color: #f8f9fa;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }

    .avatar-wrapper {
        position: relative;
        display: inline-block;
    }

    .online-indicator {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 20px;
        height: 20px;
        background-color: #28a745;
        border: 3px solid white;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="content-wrapper-fixed">
    <div class="row">
        {{-- Kartu Info Profil (Kiri) --}}
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="profile-card text-center p-4 h-100">
                <div class="card-body">
                    <div class="avatar-wrapper mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}&background=0D6EFD&color=fff&size=128" 
                             class="rounded-circle shadow-sm border border-4 border-white" 
                             alt="Avatar" 
                             style="width: 120px; height: 120px; object-fit: cover;">
                        <div class="online-indicator" title="Online"></div>
                    </div>
                    
                    <h4 class="fw-bold mb-1 text-dark">{{ $user->nama_lengkap }}</h4>
                    <p class="text-muted mb-4">{{ $user->email }}</p>
                    
                    <div class="d-flex flex-column gap-3">
                        {{-- Role --}}
                        <div class="info-box">
                            <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 10px; letter-spacing: 1px;">Role Pengguna</small>
                            <span class="badge bg-primary-subtle text-primary border-0 px-3 py-2 rounded-pill">
                                <i class="bi bi-shield-check me-1"></i> {{ strtoupper($user->role) }}
                            </span>
                        </div>
                        
                        {{-- Anggota Sejak - WITH NULL CHECK --}}
                        <div class="info-box">
                            <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 10px; letter-spacing: 1px;">Anggota Sejak</small>
                            <span class="text-dark fw-bold small">
                                <i class="bi bi-calendar3 me-1 text-primary"></i> 
                                @if($user->created_at)
                                    {{ $user->created_at->translatedFormat('d F Y') }}
                                @else
                                    <span class="text-muted">Tidak tersedia</span>
                                @endif
                            </span>
                        </div>

                        {{-- Additional Info: Last Login (Optional) --}}
                        @if(isset($user->last_login_at) && $user->last_login_at)
                        <div class="info-box">
                            <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 10px; letter-spacing: 1px;">Login Terakhir</small>
                            <span class="text-dark fw-bold small">
                                <i class="bi bi-clock-history me-1 text-primary"></i> 
                                {{ \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Edit Profil (Kanan) --}}
        <div class="col-xl-8 col-lg-7">
            <div class="profile-card">
                <div class="card-body p-4">
                    {{-- Alert Success --}}
                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <div>{{ session('success') }}</div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Alert Error Validation --}}
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-4 mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                <strong>Terdapat kesalahan:</strong>
                            </div>
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form Dinamis berdasarkan Role --}}
                    @php
                        $updateRoute = (auth()->user()->role === 'admin') 
                            ? route('admin.profile.update') 
                            : route('siswa.profile.update');
                    @endphp

                    <form action="{{ $updateRoute }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        {{-- Section: Info Personal --}}
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
                                    <span class="input-group-text"><i class="bi bi-person text-primary"></i></span>
                                    <input type="text" 
                                           name="nama_lengkap" 
                                           class="form-control form-control-custom @error('nama_lengkap') is-invalid @enderror" 
                                           value="{{ old('nama_lengkap', $user->nama_lengkap) }}" 
                                           required
                                           style="border-radius: 0 10px 10px 0;">
                                </div>
                                @error('nama_lengkap')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">ALAMAT EMAIL</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope text-primary"></i></span>
                                    <input type="email" 
                                           name="email" 
                                           class="form-control form-control-custom @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" 
                                           required
                                           style="border-radius: 0 10px 10px 0;">
                                </div>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4 opacity-50" style="border-top: 1px dashed var(--border-color);">

                        {{-- Section: Keamanan --}}
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="bi bi-shield-lock-fill text-danger fs-4"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Keamanan Akun</h5>
                        </div>

                        <div class="alert alert-info border-0 rounded-4 mb-4 d-flex align-items-start">
                            <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                            <small>Kosongkan field password jika tidak ingin mengubah password Anda.</small>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">PASSWORD BARU</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock text-danger"></i></span>
                                    <input type="password" 
                                           name="password" 
                                           class="form-control form-control-custom @error('password') is-invalid @enderror" 
                                           placeholder="Minimal 6 karakter"
                                           style="border-radius: 0 10px 10px 0;">
                                </div>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">KONFIRMASI PASSWORD</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill text-danger"></i></span>
                                    <input type="password" 
                                           name="password_confirmation" 
                                           class="form-control form-control-custom" 
                                           placeholder="Ulangi password baru"
                                           style="border-radius: 0 10px 10px 0;">
                                </div>
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
@endsection