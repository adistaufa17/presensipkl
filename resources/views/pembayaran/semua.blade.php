@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Semua Pembayaran Siswa</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Siswa</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($payments as $p)
            <tr>
                <td>{{ $p->user->name }}</td>
                <td>{{ ucfirst($p->jenis) }}</td>
                <td>Rp {{ number_format($p->jumlah) }}</td>
                <td>{{ ucfirst($p->status) }}</td>
                <td>{{ $p->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('pembayaran.detail', $p->id) }}" class="btn btn-info btn-sm">
                        Lihat Detail
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>
</div>
@endsection
