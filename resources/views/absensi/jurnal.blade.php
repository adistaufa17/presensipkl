<!DOCTYPE html>
<html>
<head>
    <title>Isi Jurnal Kegiatan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
<div class="container py-4">

    <h3 class="mb-4">Jurnal Kegiatan Harian</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('presensi.jurnal.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Kegiatan yang dikerjakan hari ini</label>
            <textarea name="kegiatan" class="form-control" rows="5" required></textarea>
        </div>

        <button class="btn btn-primary w-100">Simpan Jurnal</button>
    </form>

</div>
</body>
</html>
