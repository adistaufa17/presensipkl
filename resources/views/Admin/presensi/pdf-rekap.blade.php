<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Presensi - {{ $sekolah->nama_sekolah }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            padding: 15px 0;
            border-bottom: 3px solid #000;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 4px;
        }
        .info-table td:first-child {
            width: 120px;
            font-weight: bold;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table thead {
            background-color: #343a40;
            color: white;
        }
        table.data-table th,
        table.data-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }
        table.data-table th {
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        table.data-table td {
            font-size: 9px;
        }
        table.data-table td.nama {
            text-align: left;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .summary-box {
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .summary-box h3 {
            font-size: 12px;
            margin-bottom: 8px;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 8px;
            text-align: center;
        }
        .summary-item .number {
            font-size: 20px;
            font-weight: bold;
        }
        .summary-item .label {
            font-size: 9px;
            color: #666;
        }
        
        .color-hadir { color: #28a745; }
        .color-terlambat { color: #ffc107; }
        .color-izin { color: #17a2b8; }
        .color-alpha { color: #dc3545; }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
        .signature-section {
            margin-top: 30px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
            margin-right: 50px;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #000;
            width: 180px;
        }
    </style>
</head>
<body>
    {{-- HEADER --}}
    <div class="header">
        <h1>REKAP PRESENSI SISWA PKL</h1>
        <h2 style="font-size: 14px; margin: 5px 0;">{{ $sekolah->nama_sekolah }}</h2>
        <p>Periode: {{ $bulanNama }}</p>
    </div>

    {{-- INFO --}}
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>Sekolah</td>
                <td>: {{ $sekolah->nama_sekolah }}</td>
            </tr>
            <tr>
                <td>Periode</td>
                <td>: {{ $bulanNama }}</td>
            </tr>
            <tr>
                <td>Total Siswa</td>
                <td>: {{ count($rekapData) }} Siswa</td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    {{-- RINGKASAN KESELURUHAN --}}
    <div class="summary-box">
        <h3>Ringkasan Kehadiran Keseluruhan</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="number color-hadir">{{ collect($rekapData)->sum('hadir') }}</div>
                <div class="label">Total Hadir</div>
            </div>
            <div class="summary-item">
                <div class="number color-terlambat">{{ collect($rekapData)->sum('terlambat') }}</div>
                <div class="label">Total Terlambat</div>
            </div>
            <div class="summary-item">
                <div class="number color-izin">{{ collect($rekapData)->sum('izin') }}</div>
                <div class="label">Total Izin/Sakit</div>
            </div>
            <div class="summary-item">
                <div class="number color-alpha">{{ collect($rekapData)->sum('alpha') }}</div>
                <div class="label">Total Alpha</div>
            </div>
        </div>
    </div>

    {{-- TABEL REKAP PER SISWA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Nama Siswa</th>
                <th width="20%">Email</th>
                <th width="10%">Hadir</th>
                <th width="10%">Terlambat</th>
                <th width="10%">Izin/Sakit</th>
                <th width="10%">Alpha</th>
                <th width="5%">%</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalHariKerja = 22; // Approximate working days
            @endphp
            @forelse($rekapData as $index => $data)
            @php
                $persentase = $totalHariKerja > 0 
                    ? number_format(($data['hadir'] / $totalHariKerja) * 100, 1) 
                    : 0;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="nama">{{ $data['siswa']->user->nama_lengkap }}</td>
                <td class="nama" style="font-size: 8px;">{{ $data['siswa']->user->email }}</td>
                <td><strong class="color-hadir">{{ $data['hadir'] }}</strong></td>
                <td><strong class="color-terlambat">{{ $data['terlambat'] }}</strong></td>
                <td><strong class="color-izin">{{ $data['izin'] }}</strong></td>
                <td><strong class="color-alpha">{{ $data['alpha'] }}</strong></td>
                <td><strong>{{ $persentase }}%</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 15px;">
                    Tidak ada data siswa untuk sekolah ini
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot style="background-color: #e9ecef; font-weight: bold;">
            <tr>
                <td colspan="3" style="text-align: right;">TOTAL</td>
                <td>{{ collect($rekapData)->sum('hadir') }}</td>
                <td>{{ collect($rekapData)->sum('terlambat') }}</td>
                <td>{{ collect($rekapData)->sum('izin') }}</td>
                <td>{{ collect($rekapData)->sum('alpha') }}</td>
                <td>-</td>
            </tr>
        </tfoot>
    </table>

    {{-- CATATAN --}}
    <div style="margin-top: 15px; padding: 10px; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
        <strong>Catatan:</strong>
        <ul style="margin-left: 15px; margin-top: 5px;">
            <li>Laporan ini menampilkan rekap kehadiran seluruh siswa dari {{ $sekolah->nama_sekolah }}</li>
            <li>Persentase dihitung berdasarkan estimasi {{ $totalHariKerja }} hari kerja</li>
            <li>Data yang ditampilkan adalah akumulasi untuk periode {{ $bulanNama }}</li>
        </ul>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="signature-section">
        <div class="signature-box">
            <p>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Mengetahui,<br>Pembimbing IDUKA</p>
            <div class="signature-line"></div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari Sistem Presensi PKL</p>
        <p>© {{ date('Y') }} - Sistem Informasi Presensi & Pembayaran PKL</p>
    </div>
</body>
</html>