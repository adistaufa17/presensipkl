@extends('layouts.app')

@section('page_title', 'Pembayaran')

{{ dd($payments) }}

@section('content')
<style>
    :root {
        --card-radius: 15px;
        --soft-gray: #f8f9fa;
        --border-color: #e0e0e0;
        --primary-dark: #212529;
    }

    .stat-card {
        border-radius: var(--card-radius);
        border: 1px solid var(--border-color);
        transition: transform 0.2s;
    }

    .payment-card {
        border-radius: var(--card-radius);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }

    .payment-card .card-header {
        background: white;
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem;
    }

    .payment-card .card-body {
        padding: 1.25rem;
    }

    .info-box {
        background-color: var(--soft-gray);
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 15px;
    }

    /* Form Bayar yang disembunyikan (Dropdown Effect) */
    .form-pembayaran {
        display: none;
        border-top: 1px solid var(--border-color);
        background-color: #fff;
        padding-top: 15px;
    }

    .btn-pay-toggle {
        border-radius: 20px;
        padding: 5px 25px;
        font-weight: 600;
    }
</style>

<div class="container py-4">
    <h2 class="fw-bold mb-4">Tagihan Keuangan</h2>

    {{-- STATISTIK RINGKAS (Gaya Wireframe) --}}
    <div class="row mb-5">
        @php
            $stats = [
                ['label' => 'Belum Bayar', 'count' => $payments->where('status', 'belum_bayar')->count(), 'color' => '#6c757d'],
                ['label' => 'Pending', 'count' => $payments->where('status', 'pending')->count(), 'color' => '#ffc107'],
                ['label' => 'Diterima', 'count' => $payments->where('status', 'diterima')->count(), 'color' => '#198754'],
                ['label' => 'Ditolak', 'count' => $payments->where('status', 'ditolak')->count(), 'color' => '#dc3545'],
            ];
        @endphp
        @foreach($stats as $stat)
        <div class="col-md-3 mb-3">
            <div class="stat-card p-3 text-center" style="background-color: {{ $stat['color'] }}; color: {{ $stat['label'] == 'Pending' ? 'black' : 'white' }}">
                <div class="small fw-bold opacity-75">{{ $stat['label'] }}</div>
                <h2 class="mb-0 fw-bold">{{ $stat['count'] }}</h2>
            </div>
        </div>
        @endforeach
    </div>

    {{-- DAFTAR TAGIHAN --}}
    <div class="row">
        @forelse ($payments as $p)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="payment-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-muted">Detail Tagihan</h6>
                    <i class="bi bi-wallet2 text-muted"></i>
                </div>
                
                <div class="card-body">
    <div class="info-box">
        <small class="text-muted d-block">Tujuan Pembayaran:</small>
        {{-- Panggil 'nama' dari relasi tagihan --}}
        <span class="fw-bold">{{ $p->tagihan->nama ?? 'Tagihan Tanpa Nama' }}</span>
        
        <small class="text-muted d-block mt-2">Kategori:</small>
        <span class="badge bg-light text-dark border">
            {{ strtoupper($p->tagihan->kategori ?? 'UMUM') }}
        </span>
    </div>

    <div class="mb-3">
        <small class="text-muted">Total Tagihan:</small>
        {{-- Panggil 'nominal' dari relasi tagihan --}}
        <h3 class="fw-bold mb-0">Rp {{ number_format($p->tagihan->nominal ?? 0, 0, ',', '.') }}</h3>
        
        {{-- Tampilkan tenggat dari relasi tagihan --}}
        <small class="text-danger fw-bold">
            Tenggat: {{ \Carbon\Carbon::parse($p->tagihan->tenggat)->format('d/m/Y') }}
        </small>
    </div>

    {{-- Status tetap ambil dari $p karena status ada di tabel pembayarans --}}
    @if($p->status == 'belum_bayar' || $p->status == 'ditolak')
        <button class="btn btn-dark w-100 btn-pay-toggle mt-2" onclick="toggleFormBayar({{ $p->id }})">
            BAYAR
        </button>
    @elseif($p->status == 'pending')
        <div class="alert alert-warning text-center py-2 mb-0">⏳ Menunggu Konfirmasi</div>
    @else
        <div class="alert alert-success text-center py-2 mb-0">✅ Lunas</div>
    @endif
</div>

                    {{-- FORM DROPDOWN (Mirip Wireframe Samping) --}}
                    <div id="formBayar{{ $p->id }}" class="form-pembayaran mt-3">
                        <form action="{{ route('pembayaran.bayar') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $p->id }}">

                            <div class="mb-3">
                                <label class="small fw-bold text-muted">Payment Method</label>
                                <select class="form-select form-select-sm" name="metode" required onchange="handleMethodChange({{ $p->id }}, this.value)">
                                    <option value="">Pilih Metode</option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>

                            <div class="mb-3" id="uploadGroup{{ $p->id }}">
                                <label class="small fw-bold text-muted">Kirim Bukti Pembayaran</label>
                                <input type="file" class="form-control form-control-sm" name="bukti" id="inputBukti{{ $p->id }}">
                                <small class="text-muted" style="font-size: 0.7rem;">Untuk konfirmasi admin segera.</small>
                            </div>

                            <button type="submit" class="btn btn-dark btn-sm w-100 mt-2">KIRIM</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted">Tidak ada tagihan tertunggak.</p>
        </div>
        @endforelse
    </div>
</div>

<script>
    function toggleFormBayar(id) {
        const form = document.getElementById('formBayar' + id);
        if (form.style.display === 'block') {
            form.style.display = 'none';
        } else {
            // Sembunyikan form lain yang mungkin terbuka (optional)
            document.querySelectorAll('.form-pembayaran').forEach(f => f.style.display = 'none');
            form.style.display = 'block';
        }
    }

    function handleMethodChange(id, method) {
        const inputBukti = document.getElementById('inputBukti' + id);
        if (method === 'transfer') {
            inputBukti.setAttribute('required', 'required');
        } else {
            inputBukti.removeAttribute('required');
        }
    }
</script>
@endsection