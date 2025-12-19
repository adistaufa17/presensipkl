@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="fw-bold mb-4">📄 Detail Pembayaran</h2>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- INFO SISWA & TAGIHAN --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nama Siswa:</strong><br>{{ $payment->user->name }}</p>
                            <p><strong>Email:</strong><br>{{ $payment->user->email }}</p>
                            <p><strong>Nama Tagihan:</strong><br>{{ $payment->nama_tagihan }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Kategori:</strong><br>
                                @if($payment->kategori == 'kos')
                                    <span class="badge bg-info">🏠 Kos</span>
                                @elseif($payment->kategori == 'alat_praktik')
                                    <span class="badge bg-warning text-dark">🔧 Alat Praktik</span>
                                @else
                                    <span class="badge bg-secondary">📦 Lainnya</span>
                                @endif
                            </p>
                            <p><strong>Nominal:</strong><br>
                                <span class="fs-5 text-primary fw-bold">Rp {{ number_format($payment->nominal, 0, ',', '.') }}</span>
                            </p>
                            <p><strong>Bulan:</strong> {{ $payment->bulan }}</p>
                            <p><strong>Tenggat:</strong> {{ $payment->tenggat->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INFO PEMBAYARAN --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Detail Transaksi</h5>
                </div>
                <div class="card-body">
                    <p><strong>Status:</strong><br>
                        @php
                            $badgeMap = [
                                'belum_bayar' => 'bg-secondary',
                                'pending' => 'bg-warning text-dark',
                                'diterima' => 'bg-success',
                                'ditolak' => 'bg-danger',
                            ];
                            $badge = $badgeMap[$payment->status] ?? 'bg-secondary';
                        @endphp
                        <span class="badge {{ $badge }} fs-6">
                            {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                        </span>
                    </p>

                    @if($payment->metode)
                        <p><strong>Metode Pembayaran:</strong> {{ ucfirst($payment->metode) }}</p>
                    @endif

                    @if($payment->tanggal_bayar)
                        <p><strong>Tanggal Pembayaran:</strong> {{ $payment->tanggal_bayar->format('d M Y H:i') }}</p>
                    @endif

                    {{-- BAGIAN BUKTI PEMBAYARAN --}}
                    @if($payment->bukti)
                        <p><strong>Bukti Pembayaran:</strong></p>
                        <a href="{{ asset('storage/'.$payment->bukti) }}" target="_blank">
                            <img src="{{ asset('storage/'.$payment->bukti) }}" 
                                class="img-fluid rounded shadow" 
                                style="max-width: 400px;">
                        </a>
                    @else
                        <p class="text-muted">Belum ada bukti pembayaran</p>
                    @endif

                    @if($payment->keterangan_pembimbing)
                        <div class="alert alert-info mt-3">
                            <strong>Catatan Pembimbing:</strong><br>
                            {{ $payment->keterangan_pembimbing }}
                        </div>
                    @endif
                </div>
            </div>


            {{-- FORM UPDATE STATUS --}}
            @if($payment->status == 'pending')
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">⚙️ Konfirmasi Pembayaran</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pembayaran.status', $payment->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status Pembayaran</label>
                            <select name="status" class="form-select" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="pending">⏳ Pending</option>
                                <option value="diterima">✅ Diterima</option>
                                <option value="ditolak">❌ Ditolak</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan (Opsional)</label>
                            <textarea name="keterangan_pembimbing" class="form-control" rows="3" 
                                      placeholder="Berikan catatan jika diperlukan..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">
                            💾 Simpan Status
                        </button>
                        <a href="{{ route('pembayaran.semua') }}" class="btn btn-secondary">
                            ← Kembali
                        </a>
                    </form>
                </div>
            </div>
            @else
            <div class="text-center mb-3">
                <a href="{{ route('pembayaran.semua') }}" class="btn btn-secondary">
                    ← Kembali ke Daftar
                </a>
            </div>
            @endif

            {{-- TOMBOL AKSI LAIN --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">⚙️ Aksi Lainnya</h6>
                </div>
                <div class="card-body">
                    @if($payment->status != 'belum_bayar')
                    <form action="{{ route('pembayaran.reset', $payment->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Yakin ingin reset pembayaran ini ke status belum bayar?')">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm">
                            🔄 Reset ke Belum Bayar
                        </button>
                    </form>
                    @endif

                </div>
            </div>

            {{-- HISTORY PEMBAYARAN SISWA --}}
            @if($historyPembayaran->count() > 0)
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">📜 History Pembayaran Siswa Ini</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Tagihan</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($historyPembayaran as $h)
                                <tr>
                                    <td>{{ $h->nama_tagihan }}</td>
                                    <td>Rp {{ number_format($h->nominal, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $badgeMap = [
                                                'belum_bayar' => 'bg-secondary',
                                                'pending' => 'bg-warning text-dark',
                                                'diterima' => 'bg-success',
                                                'ditolak' => 'bg-danger',
                                            ];
                                            $badge = $badgeMap[$h->status] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $badge }}">
                                            {{ ucfirst(str_replace('_', ' ', $h->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $h->tanggal_bayar ? $h->tanggal_bayar->format('d M Y') : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection