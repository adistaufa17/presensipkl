@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Tambah Siswa</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('pembimbing.siswa.store') }}">
        @csrf

        <div class="mb-3">
            <label>Nama Siswa</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
