@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">Detail Pembayaran</h2>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Nama Siswa:</strong> {{ $payment->user->name }}</p>
            <p><strong>Jenis Pembayaran:</strong> {{ ucfirst($payment->jenis) }}</p>
            <p><strong>Jumlah:</strong> Rp {{ number_format($payment->jumlah) }}</p>
            <p><strong>Status:</strong> {{ ucfirst($payment->status) }}</p>
            <p><strong>Tanggal:</strong> {{ $payment->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Bukti:</strong></p>
            <img src="{{ asset('storage/'.$payment->bukti) }}" class="img-fluid" style="max-width: 300px;">
        </div>
    </div>

    <form action="{{ route('pembayaran.status', $payment->id) }}" method="POST">
        @csrf

        <label class="form-label">Ubah Status:</label>
        <select name="status" class="form-control mb-3">
            <option value="pending">Pending</option>
            <option value="diterima">Diterima</option>
            <option value="ditolak">Ditolak</option>
        </select>

        <button class="btn btn-primary">Simpan Perubahan</button>
    </form>

</div>
@endsection
