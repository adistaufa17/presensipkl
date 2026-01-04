@extends('layouts.app')

@section('page_title', 'Detail Profil')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px; border: 1px solid var(--border-color) !important;">
                {{-- Header Warna Solid (Aksen) --}}
                <div style="height: 100px; background: linear-gradient(to right, #213448, #3b5977);"></div>
                
                <div class="card-body p-4 p-md-5">
                    {{-- Foto Profil & Nama --}}
                    <div class="text-center" style="margin-top: -80px;">
                        <div class="position-relative d-inline-block">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}&size=128&background=0D6EFD&color=fff" 
                                 class="rounded-circle border border-4 border-white shadow-sm mb-3" 
                                 alt="Avatar" style="width: 120px; height: 120px; object-fit: cover;">
                            <div class="position-absolute bottom-0 end-0 bg-success border border-white border-3 rounded-circle" style="width: 22px; height: 22px;"></div>
                        </div>
                        <h3 class="fw-bold mb-1 text-dark">{{ $user->nama_lengkap }}</h3>
                            @php
                            $now = \Carbon\Carbon::now();
                            $mulai = \Carbon\Carbon::parse($siswa->tanggal_mulai_pkl);
                            $selesai = \Carbon\Carbon::parse($siswa->tanggal_selesai_pkl);

                            if ($now->between($mulai, $selesai)) {
                                $label = 'Siswa PKL Aktif';
                                $icon = 'bi-person-badge';
                                $class = 'bg-primary-subtle text-primary';
                            } elseif ($now->lt($mulai)) {
                                $label = 'PKL Belum Dimulai';
                                $icon = 'bi-hourglass-split';
                                $class = 'bg-warning-subtle text-warning';
                            } else {
                                $label = 'PKL Selesai';
                                $icon = 'bi-check-circle-fill';
                                $class = 'bg-secondary-subtle text-secondary';
                            }
                        @endphp

                        <span class="badge {{ $class }} border-0 px-3 py-2 rounded-pill fw-bold">
                            <i class="bi {{ $icon }} me-1"></i> {{ $label }}
                        </span>

                    </div>

                    <div class="mt-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="bi bi-card-list text-primary fs-4"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Informasi Data Diri</h5>
                        </div>

                        <div class="row g-4">
                            {{-- Nama Lengkap --}}
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4 border-0 h-100">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Nama Lengkap</label>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person me-2 text-primary"></i>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $user->nama_lengkap }}</h6>
                                    </div>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4 border-0 h-100">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Alamat Email</label>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-envelope me-2 text-primary"></i>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $user->email }}</h6>
                                    </div>
                                </div>
                            </div>

                            {{-- Sekolah --}}
                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded-4 border-0">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Asal Sekolah / Instansi</label>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white p-2 rounded-3 me-3 shadow-sm">
                                            <i class="bi bi-building text-primary fs-5"></i>
                                        </div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $siswa->sekolah->nama_sekolah ?? 'Data Sekolah Tidak Ditemukan' }}</h6>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Tanggal Bergabung --}}
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4 border-0 h-100">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Tanggal Mulai PKL</label>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar-check me-2 text-primary"></i>
                                        <h6 class="mb-0 fw-bold text-dark">
                                            {{ \Carbon\Carbon::parse($siswa->tanggal_mulai_pkl)->translatedFormat('d F Y') }}
                                        </h6>
                                    </div>
                                </div>
                            </div>

                            {{-- Status Keanggotaan --}}
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4 border-0 h-100">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Status Akun</label>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill me-2 text-success"></i>
                                        <h6 class="mb-0 fw-bold text-dark">Terverifikasi</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Info --}}
                    <div class="alert bg-primary-subtle border-0 mt-5 p-3" style="border-radius: 15px;">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-info-circle-fill text-primary me-3 fs-5"></i>
                            <div>
                                <p class="small text-primary-emphasis mb-0 fw-medium">
                                    Data profil ini bersifat <strong>terkunci</strong>. Jika terdapat perubahan data instansi atau identitas, silakan hubungi admin sistem untuk memperbarui data Anda.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-subtle { background-color: #e7f1ff !important; }
    .text-primary-emphasis { color: #052c65 !important; }
</style>
@endsection