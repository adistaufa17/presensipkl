
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Dashboard Pembimbing</h2>

    <div class="card">
        <div class="card-header">
            <strong>Daftar Siswa PKL</strong>
        </div>

        <div class="card-body">
            @if($students->count() == 0)
                <p class="text-muted">Belum ada siswa PKL yang terdaftar.</p>
            @else
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($students as $s)
                        <tr>
                            <td>{{ $s->name }}</td>
                            <td>{{ $s->email }}</td>
                            <td>
                                <a href="{{ route('pembimbing.detail', $s->id) }}" class="btn btn-primary btn-sm">
                                    Lihat Presensi & Jurnal
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
