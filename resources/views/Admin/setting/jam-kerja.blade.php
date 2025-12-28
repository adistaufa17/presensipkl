@extends('layouts.app')

@section('page_title', 'Pengaturan Jam Kerja')

@section('content')

<style>
    :root {
        --primary-color: #213448;
        --border-color: #b3b9c4ff;
        --radius: 16px;
    }

    .content-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .content-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background-color: #fafbfc;
    }

    .content-card-body {
        padding: 24px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #212529;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-control-custom {
        padding: 12px 16px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        background-color: #f8f9fa;
    }

    .form-control-custom:focus {
        background-color: #f0f0f0;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(33, 52, 72, 0.1);
    }

    .input-group-text-custom {
        background-color: #f8f9fa;
        border: 1px solid var(--border-color);
        border-right: 0;
        border-radius: 8px 0 0 8px;
        padding: 12px 16px;
    }

    .form-control-custom.grouped {
        border-left: 0;
        border-radius: 0 8px 8px 0;
    }

    .btn-action {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .switch-container {
        background: #f8f9fa;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px;
    }

    .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }

    .alert-custom {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
    }

    .alert-success-custom {
        background: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .alert-danger-custom {
        background: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
</style>

<div class="container-fluid px-4 py-3">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            {{-- ALERT MESSAGES --}}
            @if(session('error'))
            <div class="alert-custom alert-danger-custom">
                <div class="d-flex align-items-start">
                    <i class="bi bi-exclamation-octagon-fill me-3 fs-5"></i>
                    <div>
                        <strong class="d-block mb-1">Gagal Menyimpan</strong>
                        <p class="mb-0 small">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if(session('success'))
            <div class="alert-custom alert-success-custom">
                <div class="d-flex align-items-start">
                    <i class="bi bi-check-circle-fill me-3 fs-5"></i>
                    <div>
                        <strong class="d-block mb-1">Berhasil Disimpan</strong>
                        <p class="mb-0 small">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- MAIN CARD --}}
            <div class="content-card">
                <div class="content-card-header">
                    <h6 class="section-title">
                        <i class="bi bi-clock-history"></i>
                        Konfigurasi Waktu Presensi
                    </h6>
                    <small class="text-muted d-block mt-1" style="font-size: 12px;">
                        Atur jadwal masuk dan pulang untuk seluruh siswa PKL
                    </small>
                </div>

                <form action="{{ route('admin.setting.jam-kerja.update') }}" method="POST" id="formJamKerja">
                    @csrf
                    @method('PUT')

                    <div class="content-card-body">
                        
                        {{-- STATUS AKTIF --}}
                        <div class="switch-container mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="fw-semibold mb-1 d-block" style="font-size: 14px;">Status Aturan Jam Kerja</label>
                                    <small class="text-muted" style="font-size: 12px;">
                                        Jika non-aktif, sistem tidak akan memvalidasi keterlambatan
                                    </small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                        {{ ($setting->is_active ?? true) ? 'checked' : '' }} 
                                        style="width: 50px; height: 26px; cursor: pointer;">
                                </div>
                            </div>
                        </div>

                        {{-- JAM KERJA INPUTS --}}
                        <div class="row g-4">
                            {{-- Jam Masuk --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted mb-2">JAM MASUK</label>
                                <div class="input-group">
                                    <span class="input-group-text-custom">
                                        <i class="bi bi-door-open text-success"></i>
                                    </span>
                                    <input type="time" name="jam_masuk" class="form-control form-control-custom grouped" 
                                        value="{{ $setting->jam_masuk ?? '07:00' }}" required>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                    Waktu standar siswa mulai bekerja
                                </small>
                            </div>

                            {{-- Batas Telat --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted mb-2">BATAS AKHIR MASUK</label>
                                <div class="input-group">
                                    <span class="input-group-text-custom">
                                        <i class="bi bi-exclamation-triangle text-warning"></i>
                                    </span>
                                    <input type="time" name="batas_telat" class="form-control form-control-custom grouped" 
                                        value="{{ $setting->batas_telat ?? '08:00' }}" required>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                    Lewat jam ini, siswa dianggap terlambat
                                </small>
                            </div>

                            {{-- Jam Pulang --}}
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-muted mb-2">JAM PULANG</label>
                                <div class="input-group">
                                    <span class="input-group-text-custom">
                                        <i class="bi bi-door-closed text-danger"></i>
                                    </span>
                                    <input type="time" name="jam_pulang" class="form-control form-control-custom grouped" 
                                        value="{{ $setting->jam_pulang ?? '16:00' }}" required>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                    Tombol absen pulang hanya aktif setelah jam ini
                                </small>
                            </div>
                        </div>

                        {{-- INFO BOX --}}
                        <div class="mt-4 p-3 border rounded-3" style="background: #e7f1ff; border-color: #0d6efd !important;">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-info-circle-fill text-primary me-2 mt-1"></i>
                                <small class="text-dark" style="font-size: 12px;">
                                    <strong>Catatan:</strong> Perubahan pengaturan ini akan langsung mempengaruhi 
                                    validasi status absensi siswa mulai hari ini.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="content-card-body border-top">
                        <div class="d-flex gap-2">
                            <button type="button" onclick="confirmUpdateJam()" class="btn btn-dark btn-action flex-grow-1">
                                <i class="bi bi-check2-circle me-2"></i>Simpan Perubahan
                            </button>
                            <button type="reset" class="btn btn-light btn-action" style="border: 1px solid var(--border-color);">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- PREVIEW CARD --}}
            <div class="content-card mt-4">
                <div class="content-card-header">
                    <h6 class="section-title">
                        <i class="bi bi-eye"></i>
                        Preview Pengaturan
                    </h6>
                </div>
                <div class="content-card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded-3" style="background: #d4edda;">
                                <i class="bi bi-check-circle-fill text-success fs-4 d-block mb-2"></i>
                                <small class="text-muted d-block mb-1" style="font-size: 11px;">HADIR</small>
                                <strong class="text-success" style="font-size: 14px;">
                                    {{ $setting->jam_masuk ?? '07:00' }} - {{ $setting->batas_telat ?? '08:00' }}
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded-3" style="background: #fff3cd;">
                                <i class="bi bi-exclamation-circle-fill text-warning fs-4 d-block mb-2"></i>
                                <small class="text-muted d-block mb-1" style="font-size: 11px;">TERLAMBAT</small>
                                <strong class="text-warning" style="font-size: 14px;">
                                    Setelah {{ $setting->batas_telat ?? '08:00' }}
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded-3" style="background: #f8d7da;">
                                <i class="bi bi-door-closed-fill text-danger fs-4 d-block mb-2"></i>
                                <small class="text-muted d-block mb-1" style="font-size: 11px;">PULANG</small>
                                <strong class="text-danger" style="font-size: 14px;">
                                    {{ $setting->jam_pulang ?? '16:00' }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmUpdateJam() {
        Swal.fire({
            title: 'Simpan Perubahan?',
            text: "Pengaturan jam kerja ini akan langsung diterapkan untuk semua siswa.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#213448',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-dark me-2',
                cancelButton: 'btn btn-light'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formJamKerja').submit();
            }
        });
    }

    // Auto dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-custom');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>

@endsection