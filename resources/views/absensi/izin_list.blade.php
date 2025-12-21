<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pengajuan Izin / Sakit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
<div class="container py-4">

    <h3 class="mb-4">Pengajuan Izin / Sakit</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Siswa</th>
                <th>Jenis</th>
                <th>Alasan</th>
                <th>Bukti</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
        @foreach($izin as $i)
            <tr>
                <td>{{ $i->user->name }}</td>
                <td>{{ ucfirst($i->jenis) }}</td>
                <td>{{ $i->alasan }}</td>
                <td>
                    <a href="{{ asset('storage/' . $i->bukti) }}" class="btn btn-sm btn-primary" target="_blank">
                        Lihat Foto
                    </a>
                </td>
                <td>
                    @if($i->status === 'pending')
                        <span class="badge bg-warning">Menunggu</span>
                    @elseif($i->status === 'ditolak')
                        <span class="badge bg-danger">Ditolak</span>
                    @else
                        <span class="badge bg-success">Disetujui</span>
                    @endif
                </td>
                <td>
                    @if($i->status === 'pending')
                        <form action="{{ route('izin.setujui', $i->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success">Setujui</button>
                        </form>

                        <form action="{{ route('izin.tolak', $i->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-danger">Tolak</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
</body>
</html>
