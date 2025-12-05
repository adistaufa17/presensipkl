
@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Verifikasi Izin / Sakit</h3>

    <div class="card mt-3">
        <div class="card-body">

            @if($permissions->count() == 0)
                <p class="text-muted">Tidak ada permohonan izin / sakit.</p>
            @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Alasan</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($permissions as $p)
                    <tr>
                        <td>{{ $p->user->name }}</td>
                        <td>{{ $p->date }}</td>
                        <td>{{ ucfirst($p->type) }}</td>
                        <td>{{ $p->reason ?? '-' }}</td>
                        <td>
                            @if($p->proof_path)
                                <img src="{{ asset('storage/'.$p->proof_path) }}" width="120" class="img-thumbnail">
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $p->status }}</span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('pembimbing.permission.approve', $p->id) }}">
                                @csrf
                                <button class="btn btn-success btn-sm">Setujui</button>
                            </form>

                            <form method="POST" action="{{ route('pembimbing.permission.reject', $p->id) }}" class="mt-1">
                                @csrf
                                <button class="btn btn-danger btn-sm">Tolak</button>
                            </form>
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
