@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Pembayaran Saya</h2>

    <a href="{{ route('pembayaran.create') }}" class="btn btn-primary mb-3">
        Tambah Pembayaran
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Bukti</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $p)
            <tr>
                <td>{{ $p->created_at->format('d/m/Y') }}</td>
                <td>{{ ucfirst($p->jenis) }}</td>
                <td>Rp {{ number_format($p->jumlah) }}</td>
                <td>
                    @if($p->status == 'pending')
                        <span class="badge bg-warning text-dark">Menunggu</span>
                    @elseif($p->status == 'diterima')
                        <span class="badge bg-success">Diterima</span>
                    @else
                        <span class="badge bg-danger">Ditolak</span>
                    @endif
                </td>
                <td>
                    <a href="{{ asset('storage/'.$p->bukti) }}" target="_blank">Lihat</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
