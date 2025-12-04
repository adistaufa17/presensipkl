
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Pembayaran</h2>

    <form action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Jenis Pembayaran</label>
            <select name="jenis" class="form-control" required>
                <option value="kos">Kos</option>
                <option value="alat_praktik">Alat Praktik</option>
                <option value="administrasi">Administrasi PKL</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Jumlah (Rp)</label>
            <input type="number" name="jumlah" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Bukti Pembayaran</label>
            <input type="file" name="bukti" class="form-control" required>
        </div>

        <button class="btn btn-primary">Kirim</button>
    </form>
</div>
@endsection
