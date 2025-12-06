@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">💰 Semua Pembayaran Siswa</h2>

    {{-- QUICK STATS --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <small class="text-muted">Belum Bayar</small>
                    <h3 class="text-secondary">{{ $groupedPayments['belum_bayar']->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <small class="text-muted">Pending</small>
                    <h3 class="text-warning">{{ $groupedPayments['pending']->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <small class="text-muted">Diterima</small>
                    <h3 class="text-success">{{ $groupedPayments['diterima']->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <small class="text-muted">Ditolak</small>
                    <h3 class="text-danger">{{ $groupedPayments['ditolak']->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Siswa</th>
                            <th>Tagihan</th>
                            <th>Kategori</th>
                            <th>Nominal</th>
                            <th>Bulan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $index => $p)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $p->user->name }}</strong>
                                <br>
                                <a href="{{ route('pembayaran.by_siswa', $p->user_id) }}" class="btn btn-xs btn-outline-info btn-sm mt-1">
                                    📋 Lihat Semua
                                </a>
                            </td>
                            <td>{{ $p->nama_tagihan }}</td>
                            <td>
                                @if($p->kategori == 'kos')
                                    <span class="badge bg-info">🏠 Kos</span>
                                @elseif($p->kategori == 'alat_praktik')
                                    <span class="badge bg-warning text-dark">🔧 Alat Praktik</span>
                                @else
                                    <span class="badge bg-secondary">📦 Lainnya</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                            <td>{{ $p->bulan }}</td>
                            <td>
                                @php
                                    $badgeMap = [
                                        'belum_bayar' => 'bg-secondary',
                                        'pending' => 'bg-warning text-dark',
                                        'diterima' => 'bg-success',
                                        'ditolak' => 'bg-danger',
                                    ];
                                    $badge = $badgeMap[$p->status] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                </span>
                            </td>
                            <td>
                                @if($p->status != 'belum_bayar')
                                    <a href="{{ route('pembayaran.detail', $p->id) }}" class="btn btn-sm btn-primary">
                                        👁️ Detail
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <h5>📭 Belum ada pembayaran</h5>
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