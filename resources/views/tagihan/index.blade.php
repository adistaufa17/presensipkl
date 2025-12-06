@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">📋 Kelola Tagihan</h2>
        <a href="{{ route('tagihan.create') }}" class="btn btn-primary">
            ➕ Buat Tagihan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Tagihan</th>
                            <th>Kategori</th>
                            <th>Nominal</th>
                            <th>Bulan</th>
                            <th>Tenggat</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tagihans as $index => $t)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $t->nama }}</strong></td>
                            <td>
                                @if($t->kategori == 'kos')
                                    <span class="badge bg-info">🏠 Kos</span>
                                @elseif($t->kategori == 'alat_praktik')
                                    <span class="badge bg-warning text-dark">🔧 Alat Praktik</span>
                                @else
                                    <span class="badge bg-secondary">📦 Lainnya</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                            <td>Bulan {{ $t->bulan }}</td>
                            <td>{{ \Carbon\Carbon::parse($t->tenggat)->format('d M Y') }}</td>
                            <td>{{ $t->keterangan ?? '-' }}</td>
                            <td>
                                <form action="{{ route('tagihan.destroy', $t->id) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('⚠️ Yakin hapus tagihan ini? Semua pembayaran terkait akan ikut terhapus!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <h5>📭 Belum ada tagihan</h5>
                                <p>Klik tombol "Buat Tagihan Baru" untuk menambah tagihan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection