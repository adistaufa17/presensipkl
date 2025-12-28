@extends('layouts.app')

@section('page_title', 'Manajemen Sekolah')

@section('content')
<style>
    /* Sinkronisasi lebar agar sejajar lurus dengan header layouts/app */
    .content-wrapper-fixed {
        padding: 0 12px; /* Memberikan ruang agar tidak menempel ke sidebar tapi tetap lurus dengan header */
    }

    /* Penyesuaian Card agar sama dengan style Dashboard */
    .content-card-custom {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius); /* Mengikuti 16px dari layout */
        overflow: hidden;
    }

    .content-card-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-color);
        background-color: #fafbfc;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-custom thead {
        background-color: #f8f9fa;
    }

    .table-custom th {
        border-top: none;
        font-size: 13px;
        font-weight: 700;
        color: #495057;
        padding: 15px 24px;
    }

    .table-custom td {
        padding: 15px 24px;
        font-size: 14px;
        vertical-align: middle;
    }

    /* Penyesuaian Modal agar konsisten */
    .modal-content {
        border-radius: var(--radius);
        border: none;
    }
</style>

<div class="content-wrapper-fixed">
    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="content-card-custom shadow-sm">
        <div class="content-card-header">
            <h6 class="fw-bold mb-0 text-dark">Daftar Sekolah Mitra</h6>
            <button type="button" class="btn btn-primary btn-sm px-3 rounded-3" data-bs-toggle="modal" data-bs-target="#modalTambahSekolah">
                <i class="bi bi-plus-lg me-1"></i> Tambah Sekolah
            </button>
        </div>
        
        <div class="card-body p-0"> {{-- p-0 agar table-responsive bisa penuh ke pinggir card --}}
            <div class="table-responsive">
                <table class="table table-hover table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="80px">NO</th>
                            <th>NAMA SEKOLAH</th>
                            <th width="150px" class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sekolahs as $key => $s)
                        <tr>
                            <td class="text-muted">{{ $key + 1 }}</td>
                            <td><span class="fw-bold text-dark">{{ $s->nama_sekolah }}</span></td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <button class="btn btn-sm btn-white border" data-bs-toggle="modal" data-bs-target="#modalEditSekolah{{ $s->id }}" title="Edit">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </button>
                                    <form action="{{ route('admin.sekolah.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus sekolah ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-white border" title="Hapus">
                                            <i class="bi bi-trash text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Edit (tetap di dalam loop) --}}
                        <div class="modal fade" id="modalEditSekolah{{ $s->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Nama Sekolah</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.sekolah.update', $s->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body py-4">
                                            <div class="mb-0">
                                                <label class="form-label small fw-bold">Nama Sekolah</label>
                                                <input type="text" name="nama_sekolah" class="form-control px-3 py-2" value="{{ $s->nama_sekolah }}" required style="border-radius: 8px;">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary btn-sm px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-5">
                                <i class="bi bi-building-x d-block mb-2" style="font-size: 2rem;"></i>
                                Belum ada data sekolah.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambahSekolah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Sekolah Mitra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.sekolah.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Nama Sekolah</label>
                        <input type="text" name="nama_sekolah" class="form-control px-3 py-2" placeholder="Contoh: SMK Negeri 1 Jakarta" required autofocus style="border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Simpan Sekolah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection