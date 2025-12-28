<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi_{{ $pembayaran->siswa->user->nama_lengkap }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .invoice-card {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
        }
        .header-logo { font-size: 24px; fw-bold; color: #0d6efd; letter-spacing: -1px; }
        .status-badge {
            background: #e7f1ff;
            color: #0d6efd;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: bold;
        }
        .line { border-top: 2px dashed #eee; margin: 25px 0; }
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .invoice-card { border: none; box-shadow: none; margin: 0; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="invoice-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <div class="header-logo fw-bold text-uppercase">KWITANSI PEMBAYARAN</div>
                <small class="text-muted">No. Invoice: #PAY-{{ $pembayaran->id }}{{ date('dmY') }}</small>
            </div>
            <div class="status-badge text-uppercase">LUNAS</div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px;">DIBAYAR KEPADA</small>
                <div class="fw-bold">{{ config('app.name', 'Nama Sekolah') }}</div>
                <small class="text-muted">Bagian Administrasi Keuangan</small>
            </div>
            <div class="col-6 text-end">
                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px;">DITERIMA DARI</small>
                <div class="fw-bold">{{ $pembayaran->siswa->user->nama_lengkap }}</div>
                <small class="text-muted">Siswa Aktif</small>
            </div>
        </div>

        <div class="line"></div>

        <table class="table table-borderless">
            <thead>
                <tr class="text-muted" style="font-size: 11px;">
                    <th>DESKRIPSI TAGIHAN</th>
                    <th class="text-end">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="fw-bold">{{ $pembayaran->tagihan->nama_tagihan }}</div>
                        <small class="text-muted">Pembayaran Bulan Ke-{{ $pembayaran->bulan_ke }}</small>
                    </td>
                    <td class="text-end fw-bold">Rp {{ number_format($pembayaran->nominal_bayar, 0, ',', '.') }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><div class="line"></div></td>
                </tr>
                <tr>
                    <td class="fw-bold fs-5">TOTAL BAYAR</td>
                    <td class="text-end fw-bold fs-5 text-primary">Rp {{ number_format($pembayaran->nominal_bayar, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-4 p-3 bg-light rounded-3">
            <small class="text-muted d-block" style="font-size: 11px;">
                * Kwitansi ini adalah bukti pembayaran sah yang dihasilkan secara digital pada <strong>{{ $pembayaran->updated_at->format('d M Y H:i') }}</strong>.
            </small>
        </div>

        <div class="mt-5 d-flex gap-2 no-print">
            <button onclick="window.print()" class="btn btn-dark w-100 rounded-pill">
                <i class="bi bi-printer me-2"></i> Cetak Kwitansi
            </button>
            <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-light w-100 rounded-pill">Kembali</a>
        </div>
    </div>
</div>

</body>
</html>