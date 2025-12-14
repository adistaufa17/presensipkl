@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">💰 Tagihan Saya</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- STATISTIK RINGKAS --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-secondary text-white text-center shadow-sm">
                <div class="card-body py-3">
                    <h6 class="mb-1">Belum Bayar</h6>
                    <h3 class="mb-0">{{ $payments->where('status', 'belum_bayar')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark text-center shadow-sm">
                <div class="card-body py-3">
                    <h6 class="mb-1">Pending</h6>
                    <h3 class="mb-0">{{ $payments->where('status', 'pending')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white text-center shadow-sm">
                <div class="card-body py-3">
                    <h6 class="mb-1">Diterima</h6>
                    <h3 class="mb-0">{{ $payments->where('status', 'diterima')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white text-center shadow-sm">
                <div class="card-body py-3">
                    <h6 class="mb-1">Ditolak</h6>
                    <h3 class="mb-0">{{ $payments->where('status', 'ditolak')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- DAFTAR TAGIHAN DALAM BENTUK CARD --}}
    <div class="row">
        @forelse ($payments as $p)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm h-100 {{ $p->status == 'belum_bayar' && $p->tenggat < now() ? 'border-danger' : '' }}">
                <div class="card-header {{ $p->status == 'belum_bayar' ? 'bg-secondary text-white' : ($p->status == 'pending' ? 'bg-warning' : ($p->status == 'diterima' ? 'bg-success text-white' : 'bg-danger text-white')) }}">
                    <h5 class="mb-0">{{ $p->nama_tagihan }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        @if($p->kategori == 'kos')
                            <span class="badge bg-info mb-2">🏠 Kos</span>
                        @elseif($p->kategori == 'alat_praktik')
                            <span class="badge bg-warning text-dark mb-2">🔧 Alat Praktik</span>
                        @else
                            <span class="badge bg-secondary mb-2">📦 Lainnya</span>
                        @endif
                    </div>

                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Nominal:</td>
                            <td class="text-end"><strong class="text-primary">Rp {{ number_format($p->nominal, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bulan:</td>
                            <td class="text-end"><strong>Bulan {{ $p->bulan }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tenggat:</td>
                            <td class="text-end">
                                <strong>{{ \Carbon\Carbon::parse($p->tenggat)->format('d M Y') }}</strong>
                                @if($p->status == 'belum_bayar' && $p->tenggat < now())
                                    <br><small class="text-danger fw-bold">⚠️ TELAT!</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status:</td>
                            <td class="text-end">
                                @php
                                    $badgeMap = [
                                        'belum_bayar' => 'bg-secondary',
                                        'pending' => 'bg-warning text-dark',
                                        'diterima' => 'bg-success',
                                        'ditolak' => 'bg-danger',
                                    ];
                                    $badge = $badgeMap[$p->status] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                </span>
                            </td>
                        </tr>
                    </table>

                    @if($p->status == 'ditolak' && $p->keterangan_pembimbing)
                        <div class="alert alert-danger mt-2 mb-0" role="alert">
                            <small><strong>Alasan Ditolak:</strong><br>{{ $p->keterangan_pembimbing }}</small>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white">
                    @if($p->status == 'belum_bayar' || $p->status == 'ditolak')
                        <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalBayar{{ $p->id }}">
                            💳 Bayar Sekarang
                        </button>
                    @elseif($p->status == 'pending')
                        <button class="btn btn-warning w-100" disabled>
                            ⏳ Menunggu Konfirmasi
                        </button>
                    @elseif($p->status == 'diterima')
                        <button class="btn btn-success w-100" disabled>
                            ✅ Pembayaran Diterima
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- MODAL FORM BAYAR --}}
        <div class="modal fade" id="modalBayar{{ $p->id }}" tabindex="-1" aria-labelledby="modalBayarLabel{{ $p->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalBayarLabel{{ $p->id }}">💳 Bayar: {{ $p->nama_tagihan }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('pembayaran.bayar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- PENTING: Sesuai dengan controller, gunakan 'id' bukan 'pembayaran_id' --}}
                        <input type="hidden" name="id" value="{{ $p->id }}">
                        
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <strong>Nominal:</strong> Rp {{ number_format($p->nominal, 0, ',', '.') }}<br>
                                <strong>Tenggat:</strong> {{ \Carbon\Carbon::parse($p->tenggat)->format('d M Y') }}
                            </div>

                            <div class="mb-3">
                                <label for="metode{{ $p->id }}" class="form-label fw-bold">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select class="form-select" id="metode{{ $p->id }}" name="metode" required onchange="toggleBukti{{ $p->id }}(this.value)">
                                    <option value="">-- Pilih Metode --</option>
                                    <option value="cash">💵 Cash/Tunai</option>
                                    <option value="transfer">🏦 Transfer Bank</option>
                                </select>
                            </div>

                            <div class="mb-3" id="buktiBayar{{ $p->id }}" style="display: none;">
                                <label for="bukti{{ $p->id }}" class="form-label fw-bold">
                                    Upload Bukti Transfer <span class="text-danger" id="required{{ $p->id }}">*</span>
                                </label>
                                <input type="file" class="form-control" id="bukti{{ $p->id }}" name="bukti" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, JPEG (Max 2MB)</small>
                                <small class="text-danger d-block" id="info{{ $p->id }}" style="display: none;">
                                    <strong>WAJIB upload bukti untuk metode transfer!</strong>
                                </small>
                            </div>

                            <div class="alert alert-warning">
                                <small>
                                    <strong>⚠️ Catatan:</strong><br>
                                    • <strong>Cash/Tunai:</strong> Bukti pembayaran opsional<br>
                                    • <strong>Transfer:</strong> Bukti transfer WAJIB diupload
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Batal</button>
                            <button type="submit" class="btn btn-primary">💾 Kirim Pembayaran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function toggleBukti{{ $p->id }}(metode) {
                const buktiDiv = document.getElementById('buktiBayar{{ $p->id }}');
                const buktiInput = document.getElementById('bukti{{ $p->id }}');
                const requiredSign = document.getElementById('required{{ $p->id }}');
                const infoText = document.getElementById('info{{ $p->id }}');
                
                if (metode === 'transfer') {
                    buktiDiv.style.display = 'block';
                    buktiInput.setAttribute('required', 'required');
                    requiredSign.style.display = 'inline';
                    infoText.style.display = 'block';
                } else if (metode === 'cash') {
                    buktiDiv.style.display = 'block';
                    buktiInput.removeAttribute('required');
                    requiredSign.style.display = 'none';
                    infoText.style.display = 'none';
                } else {
                    buktiDiv.style.display = 'none';
                    buktiInput.removeAttribute('required');
                }
            }
        </script>
        @empty
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <h4 class="text-muted">📭 Belum ada tagihan</h4>
                    <p class="text-muted">Anda belum memiliki tagihan yang perlu dibayar</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection