@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">➕ Buat Tagihan Baru</h2>
        <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
            ← Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📋 Form Tagihan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tagihan.store') }}" method="POST">
                        @csrf

                        {{-- Nama Tagihan --}}
                        <div class="mb-3">
                            <label for="nama" class="form-label fw-bold">
                                Nama Tagihan <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama') is-invalid @enderror" 
                                   id="nama" 
                                   name="nama" 
                                   value="{{ old('nama') }}" 
                                   placeholder="Contoh: Biaya Kos Bulan 1"
                                   autofocus>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Berikan nama yang jelas dan deskriptif</small>
                        </div>

                        {{-- Kategori --}}
                        <div class="mb-3">
                            <label for="kategori" class="form-label fw-bold">
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('kategori') is-invalid @enderror" 
                                    id="kategori" 
                                    name="kategori">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="kos" {{ old('kategori') == 'kos' ? 'selected' : '' }}>
                                    🏠 Kos
                                </option>
                                <option value="alat_praktik" {{ old('kategori') == 'alat_praktik' ? 'selected' : '' }}>
                                    🔧 Alat Praktik
                                </option>
                                <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>
                                    📦 Lainnya
                                </option>
                            </select>
                            @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nominal --}}
                        <div class="mb-3">
                            <label for="nominal" class="form-label fw-bold">
                                Nominal (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" 
                                       class="form-control @error('nominal') is-invalid @enderror" 
                                       id="nominal" 
                                       name="nominal" 
                                       value="{{ old('nominal') }}" 
                                       placeholder="300000" 
                                       min="0"
                                       step="1000">
                                @error('nominal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Masukkan jumlah tanpa titik atau koma</small>
                        </div>

                        {{-- Bulan --}}
                        <div class="mb-3">
                            <label for="bulan" class="form-label fw-bold">
                                Bulan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('bulan') is-invalid @enderror" 
                                    id="bulan" 
                                    name="bulan">
                                <option value="">-- Pilih Bulan --</option>
                                @php
                                    $namaBulan = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 
                                        4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                @endphp
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('bulan') == $i ? 'selected' : '' }}>
                                        Bulan {{ $i }} ({{ $namaBulan[$i] }})
                                    </option>
                                @endfor
                            </select>
                            @error('bulan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Bulan ke berapa dalam program praktik</small>
                        </div>

                        {{-- Tanggal Tenggat --}}
                        <div class="mb-3">
                            <label for="tenggat" class="form-label fw-bold">
                                Tanggal Tenggat <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('tenggat') is-invalid @enderror" 
                                   id="tenggat" 
                                   name="tenggat" 
                                   value="{{ old('tenggat') }}">
                            @error('tenggat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Batas akhir pembayaran untuk tagihan ini</small>
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-3">
                            <label for="keterangan" class="form-label fw-bold">
                                Keterangan <span class="text-muted">(Opsional)</span>
                            </label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="4" 
                                      placeholder="Tambahkan keterangan atau instruksi pembayaran jika diperlukan...">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Contoh: "Transfer ke rekening BCA 1234567890 a.n. Yayasan"</small>
                        </div>

                        {{-- Alert Info --}}
                        <div class="alert alert-info mb-4">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <span style="font-size: 2rem;">ℹ️</span>
                                </div>
                                <div>
                                    <h6 class="alert-heading mb-2">Informasi Penting:</h6>
                                    <ul class="mb-0 ps-3">
                                        <li>Tagihan ini akan <strong>otomatis dibuat untuk semua siswa</strong> yang terdaftar</li>
                                        <li>Setiap siswa akan mendapat 1 pembayaran dengan status <span class="badge bg-secondary">Belum Bayar</span></li>
                                        <li>Siswa dapat melihat dan membayar tagihan ini di halaman mereka</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                💾 Simpan & Generate Tagihan
                            </button>
                            <a href="{{ route('tagihan.index') }}" class="btn btn-secondary px-4">
                                ❌ Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tips Card --}}
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">💡 Tips Pengisian</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Nama Tagihan:</strong> Gunakan format yang jelas seperti "Biaya Kos Bulan 1" atau "Alat Praktik Bulan 2"</li>
                        <li><strong>Kategori:</strong> Pilih sesuai jenis tagihan untuk memudahkan filter dan laporan</li>
                        <li><strong>Nominal:</strong> Pastikan nominal sudah benar, karena akan langsung digenerate ke semua siswa</li>
                        <li><strong>Tenggat:</strong> Berikan waktu yang cukup untuk siswa melakukan pembayaran</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto set tanggal tenggat 7 hari dari sekarang jika belum diisi
    document.addEventListener('DOMContentLoaded', function() {
        const tenggatInput = document.getElementById('tenggat');
        if (!tenggatInput.value) {
            const today = new Date();
            today.setDate(today.getDate() + 7); // 7 hari dari sekarang
            const formattedDate = today.toISOString().split('T')[0];
            tenggatInput.value = formattedDate;
        }
    });

    // Format nominal dengan separator ribuan (visual only)
    document.getElementById('nominal').addEventListener('input', function(e) {
        // Ini hanya untuk visual, value asli tetap angka
        console.log('Nominal: Rp ' + parseInt(this.value || 0).toLocaleString('id-ID'));
    });
</script>
@endpush
@endsection