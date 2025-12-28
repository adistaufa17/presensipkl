<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran Siswa PKL</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; line-height: 1.4; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 2px 0; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { background-color: #f2f2f2; font-weight: bold; border: 1px solid #ccc; padding: 8px; text-align: center; text-transform: uppercase; font-size: 9px; }
        table.data-table td { border: 1px solid #ccc; padding: 8px; }
        
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .status-lunas { color: #28a745; font-weight: bold; }
        .status-menunggu { color: #f39c12; font-weight: bold; }
        .status-ditolak { color: #d33; font-weight: bold; }
        
        .footer { margin-top: 30px; }
        .signature { float: right; width: 200px; text-align: center; margin-top: 20px; }
        .signature-space { height: 60px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Pembayaran Siswa PKL</h2>
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Periode Laporan</td>
            <td>: {{ date('F Y') }}</td>
        </tr>
        <tr>
            <td>Status Data</td>
            <td>: {{ request('status') ? strtoupper(request('status')) : 'SEMUA STATUS' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Siswa</th>
                <th>Tagihan</th>
                <th>Bulan Ke</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Tgl Bayar</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($data as $index => $p)
            @php $total += ($p->status == 'dibayar' ? ($p->nominal_bayar ?? $p->tagihan->nominal) : 0); @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <span class="fw-bold">{{ $p->siswa->user->nama_lengkap }}</span><br>
                    <small>NISN: {{ $p->siswa->nisn }}</small>
                </td>
                <td>{{ $p->tagihan->nama_tagihan }}</td>
                <td class="text-center">{{ $p->bulan_ke }}</td>
                <td class="text-right">Rp {{ number_format($p->nominal_bayar ?? $p->tagihan->nominal, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($p->status == 'dibayar')
                        <span class="status-lunas">LUNAS</span>
                    @elseif($p->status == 'menunggu')
                        <span class="status-menunggu">MENUNGGU</span>
                    @else
                        <span class="status-ditolak">DITOLAK</span>
                    @endif
                </td>
                <td class="text-center">{{ $p->updated_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-center fw-bold">TOTAL PENDAPATAN (LUNAS)</td>
                <td colspan="3" class="fw-bold">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="signature">
            <p>{{ date('d F Y') }}</p>
            <p>Bendahara,</p>
            <div class="signature-space"></div>
            <p><strong>( ________________ )</strong></p>
        </div>
    </div>
</body>
</html>