@extends('layouts.app')

@section('content')
<div class="container">
  <h3>Presensi Harian</h3>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
  @if(session('info')) <div class="alert alert-info">{{ session('info') }}</div> @endif

  <div class="mb-3">
    @if(!$presenceToday)
      <form method="POST" action="{{ route('presence.checkin') }}">
        @csrf
        <button class="btn btn-primary">Absen Masuk</button>
      </form>
    @elseif(!$presenceToday->time_out)
      <div>Masuk: {{ $presenceToday->time_in }} — Status: {{ $presenceToday->status }}</div>
      <form method="POST" action="{{ route('presence.checkout') }}" class="mt-2">
        @csrf
        <button class="btn btn-warning">Absen Pulang</button>
      </form>
    @else
      <div>Masuk: {{ $presenceToday->time_in }} | Pulang: {{ $presenceToday->time_out }} | Status: {{ $presenceToday->status }}</div>
    @endif
  </div>

  <a href="{{ route('presence.permission.form') }}" class="btn btn-secondary mb-3">Ajukan Izin / Sakit</a>

  <h5>Riwayat Presensi</h5>
  <table class="table">
    <thead><tr><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Status</th></tr></thead>
    <tbody>
      @foreach($recent as $r)
        <tr>
          <td>{{ $r->date }}</td>
          <td>{{ $r->time_in ?? '-' }}</td>
          <td>{{ $r->time_out ?? '-' }}</td>
          <td>{{ $r->status }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
