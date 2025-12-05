@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="fw-bold mb-4">📄 Pembayaran Saya</h2>

    {{-- Ringkasan --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center border-0">
                <h6>Total Pembayaran</h6>
                <h4 class="fw-bold text-primary">Rp {{ number_format($total) }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center border-0">
                <h6>Diterima</h6>
                <h4 class="fw-bold text-success">{{ $diterima }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center border-0">
                <h6>Pending</h6>
                <h4 class="fw-bold text-warning">{{ $pending }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center border-0">
                <h6>Ditolak</h6>
                <h4 class="fw-bold text-danger">{{ $ditolak }}</h4>
            </div>
        </div>
    </div>

    <div class="text-end mb-3">
        <a href="{{ route('pembayaran.create') }}" class="btn btn-primary">
            + Tambah Pembayaran
        </a>
    </div>

    {{-- Tabel Pembayaran --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="130">Tanggal</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th width="90">Bukti</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($payments as $p)
                    <tr>
                        <td>{{ $p->created_at->format('d/m/Y') }}</td>
                        <td>{{ ucfirst($p->jenis) }}</td>
                        <td class="fw-bold">Rp {{ number_format($p->jumlah) }}</td>

                        <td>
                            @if ($p->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif ($p->status == 'diterima')
                                <span class="badge bg-success">Diterima</span>
                            @else
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </td>

                        <td class="text-center">
                            @if ($p->bukti)
                                <a href="{{ asset('storage/' . $p->bukti) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   target="_blank">
                                   Lihat
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td>
                            @if ($p->keterangan)
                                <small class="text-danger">{{ $p->keterangan }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            Belum ada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection
