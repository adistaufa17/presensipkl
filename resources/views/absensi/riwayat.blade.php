<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Presensi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
<div class="container py-4">

    <h3 class="mb-3">Riwayat Presensi</h3>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Status</th>
                <th>Jurnal</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($presensi as $p)
            <tr>
                <td>{{ $p->tanggal }}</td>
                <td>{{ $p->jam_masuk }}</td>
                <td>{{ $p->jam_pulang ?? '-' }}</td>
                <td>
                    @if($p->telat)
                        <span class="badge bg-danger">Terlambat</span>
                    @else
                        <span class="badge bg-success">Tepat Waktu</span>
                    @endif
                </td>
                <td>
                    @if($p->jurnal)
                        <a href="{{ route('presensi.jurnal.show', $p->id) }}" class="btn btn-sm btn-primary">Lihat</a>
                    @else
                        <span class="text-muted">Belum diisi</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
</body>
</html>
