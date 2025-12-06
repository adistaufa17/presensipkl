@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">📊 Dashboard Pembimbing</h2>

    {{-- RINGKASAN STATISTIK --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Total Tagihan</h6>
                    <h2 class="mb-0">{{ $totalTagihan }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-secondary text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Belum Bayar</h6>
                    <h2 class="mb-0">{{ $belumBayar }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Pending</h6>
                    <h2 class="mb-0">{{ $pending }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Diterima</h6>
                    <h2 class="mb-0">{{ $diterima }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- TOTAL NOMINAL DITERIMA --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Nominal Pembayaran Diterima</h6>
                    <h1 class="text-success fw-bold mb-0">Rp {{ number_format($totalNominalDiterima, 0, ',', '.') }}</h1>
                </div>
            </div>
        </div>
    </div>

    {{-- MENU NAVIGASI --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <a href="{{ route('tagihan.create') }}" class="text-decoration-none">
                <div class="card shadow-sm border-primary h-100">
                    <div class="card-body text-center">
                        <h1 class="mb-3">➕</h1>
                        <h5>Buat Tagihan Baru</h5>
                        <p class="text-muted mb-0">Tambah tagihan untuk semua siswa</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a href="{{ route('tagihan.index') }}" class="text-decoration-none">
                <div class="card shadow-sm border-info h-100">
                    <div class="card-body text-center">
                        <h1 class="mb-3">📋</h1>
                        <h5>Kelola Tagihan</h5>
                        <p class="text-muted mb-0">Lihat dan kelola semua tagihan</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a href="{{ route('pembayaran.semua') }}" class="text-decoration-none">
                <div class="card shadow-sm border-success h-100">
                    <div class="card-body text-center">
                        <h1 class="mb-3">💰</h1>
                        <h5>Lihat Pembayaran</h5>
                        <p class="text-muted mb-0">Monitor semua pembayaran siswa</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- PEMBAYARAN PENDING TERBARU --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning">
            <h5 class="mb-0">⏳ Pembayaran Menunggu Konfirmasi</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Siswa</th>
                            <th>Tagihan</th>
                            <th>Nominal</th>
                            <th>Metode</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingPayments as $p)
                        <tr>
                            <td><strong>{{ $p->user->name }}</strong></td>
                            <td>{{ $p->nama_tagihan }}</td>
                            <td>Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($p->metode) }}</span>
                            </td>
                            <td>{{ $p->tanggal_bayar->format('d M Y H:i') }}</td>
                            <td>
                                <a href="{{ route('pembayaran.detail', $p->id) }}" class="btn btn-sm btn-primary">
                                    👁️ Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                ✅ Tidak ada pembayaran pending
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SISWA YANG TELAT BAYAR --}}
    @if($siswaTelat->count() > 0)
    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">⚠️ Siswa dengan Tagihan Telat</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Jumlah Tagihan Telat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($siswaTelat as $index => $data)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $data['user']->name }}</strong></td>
                            <td>
                                <span class="badge bg-danger">{{ $data['jumlah'] }} Tagihan</span>
                            </td>
                            <td>
                                <a href="{{ route('pembayaran.by_siswa', $data['user']->id) }}" class="btn btn-sm btn-info">
                                    📋 Lihat Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection