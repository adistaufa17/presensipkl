@extends('layouts.app')

@section('page_title', 'Manajemen Pembayaran')

@section('content')
<style>
    /* Penyesuaian lebar agar lurus dengan header layouts/app */
    .content-wrapper-fixed {
        padding: 0 12px;
    }

    .content-card-custom {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .content-card-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-color);
        background-color: #fafbfc;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-custom thead {
        background-color: #f8f9fa;
    }

    .table-custom th {
        border-top: none;
        font-size: 12px;
        font-weight: 700;
        color: #495057;
        padding: 15px 24px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-custom td {
        padding: 15px 24px;
        font-size: 14px;
        vertical-align: middle;
    }

    .progress-custom {
        height: 8px;
        border-radius: 10px;
        background-color: #edf2f7;
    }

    .btn-action-group .btn {
        padding: 0.4rem 0.8rem;
        font-weight: 600;
        font-size: 13px;
    }
</style>

<div class="content-wrapper-fixed">
    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ACTION HEADER --}}
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary px-4 shadow-sm fw-bold rounded-3" data-bs-toggle="modal" data-bs-target="#modalTambahTagihan">
            <i class="bi bi-plus-lg me-2"></i> Buat Tagihan Baru
        </button>
    </div>

    {{-- BAGIAN 1: KONFIRMASI PEMBAYARAN --}}
    <div class="content-card-custom shadow-sm">
        <div class="content-card-header">
            <h6 class="fw-bold mb-0 text-primary">
                <i class="bi bi-clock-history me-2"></i>Menunggu Konfirmasi Bukti Bayar
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Tagihan / Periode</th>
                            <th>Nominal</th>
                            <th>Bukti</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $pending = \App\Models\TagihanSiswa::where('status', 'menunggu_konfirmasi')
                                        ->with(['siswa.user', 'tagihan'])
                                        ->orderBy('updated_at', 'desc')
                                        ->get();
                        @endphp
                        
                        @forelse($pending as $p)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $p->siswa->user->nama_lengkap }}</div>
                                <small class="text-muted">Deadline: {{ \Carbon\Carbon::parse($p->jatuh_tempo)->format('d M Y') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $p->tagihan->nama_tagihan }}</div>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2">Bulan ke-{{ $p->bulan_ke }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-success">Rp {{ number_format($p->tagihan->nominal, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <a href="{{ asset('storage/' . $p->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-light border text-primary fw-bold px-3">
                                    <i class="bi bi-eye-fill me-1"></i> Preview
                                </a>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center btn-action-group">
                                    <form action="{{ route('admin.tagihan.konfirmasi', $p->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="dibayar">
                                        <button type="submit" class="btn btn-success rounded-3 px-3">Terima</button>
                                    </form>
                                    <button class="btn btn-outline-danger rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#modalTolak{{ $p->id }}">Tolak</button>
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL TOLAK --}}
                        <div class="modal fade" id="modalTolak{{ $p->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content shadow border-0" style="border-radius: 16px;">
                                    <form action="{{ route('admin.tagihan.konfirmasi', $p->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body p-4">
                                            <h6 class="fw-bold text-dark mb-3">Alasan Penolakan</h6>
                                            <input type="hidden" name="status" value="ditolak">
                                            <textarea name="catatan_admin" class="form-control bg-light border-0" rows="4" placeholder="Sebutkan alasan penolakan agar siswa mengerti..." required style="border-radius: 12px; font-size: 14px;"></textarea>
                                        </div>
                                        <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                            <button type="button" class="btn btn-light btn-sm flex-grow-1 rounded-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger btn-sm flex-grow-1 rounded-3">Kirim Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-patch-check d-block mb-2 fs-2"></i>
                                Tidak ada pembayaran yang perlu dikonfirmasi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: DATA MASTER --}}
    <div class="content-card-custom shadow-sm">
        <div class="content-card-header">
            <h6 class="fw-bold mb-0 text-dark">Data Master & Progres Tagihan</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Nama Tagihan</th>
                            <th>Total Nominal</th>
                            <th>Progres</th>
                            <th>Status Master</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihans as $t)
                        <tr>
                            <td><div class="fw-bold text-dark">{{ $t->siswa->user->nama_lengkap }}</div></td>
                            <td>{{ $t->nama_tagihan }}</td>
                            <td class="fw-bold text-dark">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $totalBulan = $t->tagihanSiswas->count();
                                    $lunas = $t->tagihanSiswas->where('status', 'dibayar')->count();
                                    $persen = ($totalBulan > 0) ? ($lunas / $totalBulan) * 100 : 0;
                                @endphp
                                <div class="d-flex align-items-center" style="min-width: 160px;">
                                    <div class="progress progress-custom flex-grow-1 me-3 shadow-sm">
                                        <div class="progress-bar bg-success" style="width: {{ $persen }}%"></div>
                                    </div>
                                    <small class="fw-bold text-dark">{{ $lunas }}/{{ $totalBulan }}</small>
                                </div>
                            </td>
                            <td>
                                @if($t->status == 'lunas')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">LUNAS</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2">BERJALAN</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary fw-bold px-3 rounded-3" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $t->id }}">
                                    <i class="bi bi-search me-1"></i> Detail
                                </button>
                            </td>
                        </tr>

                        {{-- MODAL DETAIL --}}
                        <div class="modal fade" id="modalDetail{{ $t->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
                                    <div class="modal-header border-0 px-4 pt-4">
                                        <h5 class="modal-title fw-bold">Rincian: {{ $t->nama_tagihan }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="table-responsive rounded-4 border">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="text-center">Bulan</th>
                                                        <th>Jatuh Tempo</th>
                                                        <th class="text-center">Status</th>
                                                        <th>Tgl Bayar</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($t->tagihanSiswas->sortBy('bulan_ke') as $r)
                                                    <tr>
                                                        <td class="text-center fw-bold">{{ $r->bulan_ke }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($r->jatuh_tempo)->format('d/m/Y') }}</td>
                                                        <td class="text-center">
                                                            @php
                                                                $badgeStyles = [
                                                                    'dibayar' => 'bg-success text-white',
                                                                    'menunggu_konfirmasi' => 'bg-warning text-dark',
                                                                    'ditolak' => 'bg-danger text-white',
                                                                    'belum_bayar' => 'bg-light text-muted border'
                                                                ];
                                                                $currentStyle = $badgeStyles[$r->status] ?? 'bg-secondary';
                                                            @endphp
                                                            <span class="badge rounded-pill {{ $currentStyle }} px-3">
                                                                {{ str_replace('_', ' ', strtoupper($r->status)) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-muted small">
                                                            {{ $r->tanggal_bayar ? \Carbon\Carbon::parse($r->tanggal_bayar)->format('d/m/Y H:i') : '-' }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data master tagihan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambahTagihan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 16px;">
            <div class="modal-header px-4 pt-4 border-0">
                <h5 class="modal-title fw-bold">Buat Tagihan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.tagihan.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info" style="border-radius: 8px; font-size: 13px;">
                        <i class="bi bi-info-circle me-1"></i> Pilih satu atau beberapa siswa untuk mengirimkan tagihan ini secara massal.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Pilih Siswa</label>
                        <select name="siswa_id[]" class="form-select py-2" multiple="multiple" required style="width: 100%;">
                            @php
                                // Ambil ID siswa yang sudah punya tagihan aktif/berjalan
                                $siswaSudahAda = \App\Models\Tagihan::where('status', 'berjalan')->pluck('siswa_id')->toArray();
                            @endphp

                            @foreach(\App\Models\Siswa::with('user')->get() as $s)
                                @php $isExists = in_array($s->id, $siswaSudahAda); @endphp
                                
                                <option value="{{ $s->id }}" {{ $isExists ? 'data-exists="true"' : '' }}>
                                    {{ $s->user->nama_lengkap }} 
                                    ({{ $s->nis ?? 'No NIS' }}) 
                                    {{ $isExists ? ' ⚠️ [Sudah Ada Tagihan]' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 11px;">*Gunakan Ctrl + Klik untuk memilih manual atau ketik nama di kolom pencarian.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Tagihan</label>
                        <input type="text" name="nama_tagihan" class="form-control py-2" placeholder="Contoh: SPP Januari" required style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nominal per Bulan (Rp)</label>
                        <input type="number" name="nominal" class="form-control py-2" placeholder="0" required style="border-radius: 8px;">
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold small">Durasi (Bulan)</label>
                            <input type="number" name="jumlah_bulan" class="form-control py-2" value="1" min="1" required style="border-radius: 8px;">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold small">Tempo Awal</label>
                            <input type="date" name="jatuh_tempo_awal" class="form-control py-2" required style="border-radius: 8px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold">
                        <i class="bi bi-send me-1"></i> Kirim Tagihan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$(function () {

    // 🔒 Anti double submit
    $('form').on('submit', function () {
        $(this).find('button[type=submit]').prop('disabled', true);
    });

    // 🔽 Select2 safe init
    function initSelect2() {
        const $el = $('select[name="siswa_id[]"]');
        if ($el.hasClass('select2-hidden-accessible')) return;

        $el.select2({
            placeholder: "-- Pilih Satu atau Beberapa Siswa --",
            allowClear: true,
            dropdownParent: $('#modalTambahTagihan'),
            templateResult: formatSiswa
        });
    }

    function formatSiswa(state) {
        if (!state.id) return state.text;

        const isExists = $(state.element).data('exists');
        if (isExists) {
            return $('<span style="color:#999;text-decoration:line-through;">' + state.text + '</span>');
        }
        return state.text;
    }

    $('#modalTambahTagihan').on('shown.bs.modal', function () {
        initSelect2();
    });

});
</script>
@endpush
