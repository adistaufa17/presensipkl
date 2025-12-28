@extends('layouts.app')

@section('page_title', 'Manajemen Siswa')

@section('content')
<style>
    /* Menggunakan variabel yang sudah ada di layouts jika memungkinkan */
    :root {
        --primary-color: #213448;
        --border-color: #b3b9c4ff;
        --radius: 16px;
    }

    /* Card Styling - Ukuran disamakan dengan header layouts */
    .content-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius); /* Mengikuti radius 16px layouts */
        overflow: hidden;
    }

    .content-card-header {
        padding: 20px 32px; /* Disamakan dengan padding dashboard-header di layouts */
        border-bottom: 1px solid var(--border-color);
        background-color: #ffffff;
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

    /* Table Styling */
    .table-custom {
        width: 100%;
        margin-bottom: 0;
    }

    .table-custom thead {
        background: #f8f9fa;
    }

    .table-custom th {
        padding: 12px 32px; /* Padding kiri-kanan disamakan dengan header */
        font-size: 13px;
        font-weight: 600;
        color: #495057;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }

    .table-custom td {
        padding: 16px 32px; /* Padding kiri-kanan disamakan dengan header */
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
        vertical-align: middle;
    }

    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }

    /* Badge & Button Styling - Desain Tetap */
    .status-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }

    .btn-add {
        background: var(--primary-color);
        color: white;
        border-radius: 10px;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-add:hover {
        background: #2c455f;
        color: white;
        transform: translateY(-1px);
    }

    .btn-action-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
        border: 1px solid #eee;
        background: white;
        color: #666;
    }

    .btn-action-icon:hover {
        background: #f8f9fa;
        color: var(--primary-color);
        border-color: var(--border-color);
    }

    /* Modal Styling */
    .modal-custom .modal-content {
        border: none;
        border-radius: var(--radius);
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .form-control-custom {
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 14px;
    }
</style>

{{-- Menggunakan container-fluid p-0 agar sejajar dengan dashboard-header di layouts --}}
<div class="container-fluid p-0">
    <div class="content-card">
        <div class="content-card-header d-flex justify-content-between align-items-center">
            <h6 class="section-title">
                <i class="bi bi-people"></i>
                Daftar Siswa PKL Aktif
            </h6>
            <button type="button" class="btn btn-add border-0" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                <i class="bi bi-plus-lg me-1"></i> Tambah Siswa
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Identitas Siswa</th>
                        <th>Asal Sekolah</th>
                        <th>Durasi PKL</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswas as $s)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($s->user->nama_lengkap) }}&background=f0f2f5&color=213448&bold=true" 
                                     class="rounded-circle" width="38" height="38">
                                <div>
                                    <div class="fw-bold text-dark">{{ $s->user->nama_lengkap }}</div>
                                    <small class="text-muted">{{ $s->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-dark">{{ $s->sekolah->nama_sekolah }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-calendar3 text-muted"></i>
                                <span>{{ $s->durasi_pkl_bulan }} Bulan</span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge" style="background: #e7f1ff; color: #0d6efd;">
                                {{ strtoupper($s->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                {{-- Tombol Edit --}}
                                <button class="btn-action-icon" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditSiswa{{ $s->id }}" 
                                        title="Edit Data">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                {{-- Form Hapus --}}
                                <form action="{{ route('admin.siswa.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Yakin hapus siswa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon text-danger" title="Hapus Data">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal tetap menggunakan desain yang kamu suka --}}
<div class="modal fade modal-custom" id="modalTambahSiswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header px-4 pt-4 border-0">
                <h5 class="fw-bold mb-0">Tambah Siswa Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.siswa.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <p class="text-muted mb-4 small">Lengkapi formulir di bawah ini untuk mendaftarkan siswa PKL baru ke sistem.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control form-control-custom" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Email Instansi/Pribadi</label>
                            <input type="email" name="email" class="form-control form-control-custom" placeholder="nama@email.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Asal Sekolah</label>
                            <select name="sekolah_id" class="form-select form-control-custom" required>
                                <option value="" selected disabled>Pilih Sekolah</option>
                                @foreach($sekolahs as $sekolah)
                                    <option value="{{ $sekolah->id }}">{{ $sekolah->nama_sekolah }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Durasi PKL (Bulan)</label>
                            <input type="number" name="durasi_pkl_bulan" class="form-control form-control-custom" placeholder="Contoh: 3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai_pkl" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai_pkl" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Password Akun</label>
                            <input type="password" name="password" class="form-control form-control-custom" placeholder="Buat password login siswa" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 pb-4 border-0">
                    <button type="button" class="btn btn-light px-4 border" style="border-radius: 10px;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add px-4 border-0">Simpan Data Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- Modal Edit Siswa (Dinamis per Siswa) --}}
@foreach($siswas as $s)
<div class="modal fade modal-custom" id="modalEditSiswa{{ $s->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header px-4 pt-4 border-0">
                <h5 class="fw-bold mb-0">Edit Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.siswa.update', $s->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body px-4">
                    <p class="text-muted mb-4 small">Perbarui informasi siswa <strong>{{ $s->user->nama_lengkap }}</strong>.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control form-control-custom" value="{{ $s->user->nama_lengkap }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Email</label>
                            <input type="email" name="email" class="form-control form-control-custom" value="{{ $s->user->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Asal Sekolah</label>
                            <select name="sekolah_id" class="form-select form-control-custom" required>
                                @foreach($sekolahs as $sekolah)
                                    <option value="{{ $sekolah->id }}" {{ $s->sekolah_id == $sekolah->id ? 'selected' : '' }}>
                                        {{ $sekolah->nama_sekolah }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Durasi PKL (Bulan)</label>
                            <input type="number" name="durasi_pkl_bulan" class="form-control form-control-custom" value="{{ $s->durasi_pkl_bulan }}" required>
                        </div>
                       <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai_pkl" 
                                class="form-control form-control-custom" 
                                {{-- Kita paksa formatnya ke Y-m-d --}}
                                value="{{ \Carbon\Carbon::parse($s->tanggal_mulai_pkl)->format('Y-m-d') }}" 
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai_pkl" 
                                class="form-control form-control-custom" 
                                {{-- Kita paksa formatnya ke Y-m-d --}}
                                value="{{ \Carbon\Carbon::parse($s->tanggal_selesai_pkl)->format('Y-m-d') }}" 
                                required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Password Baru (Kosongkan jika tidak ingin ganti)</label>
                            <input type="password" name="password" class="form-control form-control-custom" placeholder="Masukkan password baru jika perlu">
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 pb-4 border-0">
                    <button type="button" class="btn btn-light px-4 border" style="border-radius: 10px;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add px-4 border-0">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach