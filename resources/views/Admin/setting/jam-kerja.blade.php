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
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .content-card-header {
        padding: 20px 32px;
        border-bottom: 1px solid var(--border-color);
        background: linear-gradient(to bottom, #fafbfc, #ffffff);
    }

    .content-card-body {
        padding: 28px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        font-size: 20px;
        color: var(--primary-color);
    }

    .form-control-custom {
        padding: 14px 18px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 15px;
        background-color: #ffffff;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .form-control-custom:focus {
        background-color: #f8f9fa;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(33, 52, 72, 0.08);
        outline: none;
    }

    .input-group-text-custom {
        background-color: #f8f9fa;
        border: 1px solid var(--border-color);
        border-right: 0;
        border-radius: 10px 0 0 10px;
        padding: 14px 18px;
        min-width: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .input-group-text-custom i {
        font-size: 18px;
    }

    .form-control-custom.grouped {
        border-left: 0;
        border-radius: 0 10px 10px 0;
    }

    .btn-action {
        padding: 14px 28px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-action:active {
        transform: translateY(0);
    }

    .btn-dark {
        background: var(--primary-color);
        color: white;
    }

    .btn-light {
        background: #f8f9fa;
        color: #495057;
        border: 1px solid var(--border-color);
    }

    .switch-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 20px 24px;
    }

    .form-check-input {
        width: 56px;
        height: 30px;
        cursor: pointer;
        border: 2px solid var(--border-color);
        background-color: #e0e0e0;
        transition: all 0.3s ease;
    }

    .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }

    .form-check-input:focus {
        box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.15);
    }

    .alert-custom {
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: start;
        gap: 14px;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-success-custom {
        background: linear-gradient(135deg, #d4edda 0%, #e8f5e9 100%);
        border-color: #c3e6cb;
        color: #155724;
    }

    .alert-danger-custom {
        background: linear-gradient(135deg, #f8d7da 0%, #ffebee 100%);
        border-color: #f5c6cb;
        color: #721c24;
    }

    .alert-custom i {
        font-size: 22px;
        flex-shrink: 0;
    }

    .form-label {
        font-size: 13px;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }

    .info-box {
        background: linear-gradient(135deg, #e7f1ff 0%, #f0f7ff 100%);
        border: 1px solid #b3d7ff;
        border-radius: 12px;
        padding: 18px 20px;
        margin-top: 24px;
    }

    .info-box i {
        font-size: 20px;
        color: #0d6efd;
    }

    .preview-card {
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }

    .preview-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }

    .preview-card.success {
        background: linear-gradient(135deg, #d4edda 0%, #e8f5e9 100%);
        border-color: #28a745;
    }

    .preview-card.warning {
        background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%);
        border-color: #ffc107;
    }

    .preview-card.danger {
        background: linear-gradient(135deg, #f8d7da 0%, #ffebee 100%);
        border-color: #dc3545;
    }

    .preview-card i {
        font-size: 32px;
        margin-bottom: 12px;
        display: block;
    }

    .preview-card .label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 8px;
    }

    .preview-card .time {
        font-size: 16px;
        font-weight: 700;
    }

    .input-helper {
        font-size: 12px;
        color: #6c757d;
        margin-top: 8px;
        display: block;
        line-height: 1.4;
    }

    @media (max-width: 768px) {
        .content-card-body {
            padding: 20px;
        }

        .section-title {
            font-size: 16px;
        }

        .btn-action {
            padding: 12px 20px;
            font-size: 14px;
        }
    }
</style>

<div class="container-fluid px-3 px-md-4 py-3">
    <div class="row">
        <div class="col-12">
            
            {{-- ALERT MESSAGES --}}
            @if(session('error'))
            <div class="alert-custom alert-danger-custom">
                <i class="bi bi-exclamation-octagon-fill"></i>
                <div class="flex-grow-1">
                    <strong class="d-block mb-1" style="font-size: 15px;">Gagal Menyimpan</strong>
                    <p class="mb-0" style="font-size: 13px;">{{ session('error') }}</p>
                </div>
            </div>
            @endif

            @if(session('success'))
            <div class="alert-custom alert-success-custom">
                <i class="bi bi-check-circle-fill"></i>
                <div class="flex-grow-1">
                    <strong class="d-block mb-1" style="font-size: 15px;">Berhasil Disimpan</strong>
                    <p class="mb-0" style="font-size: 13px;">{{ session('success') }}</p>
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
                    <small class="text-muted d-block mt-2" style="font-size: 13px; font-weight: 500;">
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
                                <div class="flex-grow-1">
                                    <label class="fw-bold mb-2 d-block" style="font-size: 15px; color: #212529;">
                                        Status Aturan Jam Kerja
                                    </label>
                                    <small class="text-muted d-block" style="font-size: 13px; line-height: 1.5;">
                                        Jika non-aktif, sistem tidak akan memvalidasi keterlambatan siswa
                                    </small>
                                </div>
                                <div class="form-check form-switch ms-3">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                        {{ ($setting->is_active ?? true) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        {{-- JAM KERJA INPUTS --}}
                        <div class="row g-4">
                            {{-- Jam Masuk --}}
                            <div class="col-md-6">
                                <label class="form-label">Jam Masuk</label>
                                <div class="input-group">
                                    <span class="input-group-text-custom">
                                        <i class="bi bi-door-open text-success"></i>
                                    </span>
                                    <input type="time" name="jam_masuk" class="form-control form-control-custom grouped" 
                                        value="{{ $setting->jam_masuk ?? '07:00' }}" required>
                                </div>
                                <small class="input-helper">
                                    Waktu standar siswa mulai bekerja
                                </small>
                            </div>

                            {{-- Batas Telat --}}
                            <div class="col-md-6">
                                <label class="form-label">Batas Akhir Masuk</label>
                                <div class="input-group">
                                    <span class="input-group-text-custom">
                                        <i class="bi bi-exclamation-triangle text-warning"></i>
                                    </span>
                                    <input type="time" name="batas_telat" class="form-control form-control-custom grouped" 
                                        value="{{ $setting->batas_telat ?? '08:00' }}" required>
                                </div>
                                <small class="input-helper">
                                    Lewat jam ini, siswa dianggap terlambat
                                </small>
                            </div>

                            {{-- Jam Pulang --}}
                            <div class="col-md-12">
                                <label class="form-label">Jam Pulang</label>
                                <div class="input-group">
                                    <span class="input-group-text-custom">
                                        <i class="bi bi-door-closed text-danger"></i>
                                    </span>
                                    <input type="time" name="jam_pulang" class="form-control form-control-custom grouped" 
                                        value="{{ $setting->jam_pulang ?? '16:00' }}" required>
                                </div>
                                <small class="input-helper">
                                    Tombol absen pulang hanya aktif setelah jam ini
                                </small>
                            </div>
                        </div>

                        {{-- INFO BOX --}}
                        <div class="info-box">
                            <div class="d-flex align-items-start gap-3">
                                <i class="bi bi-info-circle-fill"></i>
                                <small class="text-dark" style="font-size: 13px; line-height: 1.6;">
                                    <strong>Catatan:</strong> Perubahan pengaturan ini akan langsung mempengaruhi 
                                    validasi status absensi siswa mulai hari ini.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="content-card-body border-top" style="background: #fafbfc;">
                        <div class="d-flex gap-3">
                            <button type="button" onclick="confirmUpdateJam()" class="btn btn-dark btn-action flex-grow-1">
                                <i class="bi bi-check2-circle"></i>
                                <span>Simpan Perubahan</span>
                            </button>
                            <button type="reset" class="btn btn-light btn-action">
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
                            <div class="preview-card success">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <div class="label">HADIR</div>
                                <div class="time text-success">
                                    {{ $setting->jam_masuk ?? '07:00' }} - {{ $setting->batas_telat ?? '08:00' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="preview-card warning">
                                <i class="bi bi-exclamation-circle-fill text-warning"></i>
                                <div class="label">TERLAMBAT</div>
                                <div class="time text-warning">
                                    Setelah {{ $setting->batas_telat ?? '08:00' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="preview-card danger">
                                <i class="bi bi-door-closed-fill text-danger"></i>
                                <div class="label">PULANG</div>
                                <div class="time text-danger">
                                    {{ $setting->jam_pulang ?? '16:00' }}
                                </div>
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
                popup: 'border-0 shadow-lg',
                confirmButton: 'btn btn-dark btn-action me-2',
                cancelButton: 'btn btn-light btn-action'
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