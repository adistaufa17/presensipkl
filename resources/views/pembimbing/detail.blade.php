
@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Detail Siswa: {{ $student->name }}</h3>

    {{-- PRESENSI --}}
    <div class="card mt-4">
        <div class="card-header">
            <strong>Data Presensi</strong>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Status</th>
                        <th>Terlambat?</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($presences as $p)
                    <tr>
                        <td>{{ $p->date }}</td>
                        <td>{{ $p->time_in ?? '-' }}</td>
                        <td>{{ $p->time_out ?? '-' }}</td>
                        <td>{{ ucfirst($p->status) }}</td>
                        <td>
                            @if($p->is_late)
                                <span class="badge bg-danger">Ya</span>
                            @else
                                <span class="badge bg-success">Tidak</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- JURNAL --}}
    <div class="card mt-4">
        <div class="card-header">
            <strong>Jurnal Kegiatan</strong>
        </div>

        <div class="card-body">
            @if($journals->count() == 0)
                <p class="text-muted">Belum ada jurnal.</p>
            @else
                @foreach($journals as $j)
                <div class="border p-3 mb-3">
                    <h5>{{ $j->title }} ({{ $j->date }})</h5>
                    <p>{{ $j->description }}</p>

                    @if($j->photo_path)
                        <img src="{{ asset('storage/'.$j->photo_path) }}" alt="foto jurnal" width="200" class="img-thumbnail">
                    @endif
                </div>
                @endforeach
            @endif
        </div>
    </div>

</div>
@endsection
