@extends('layouts.app')
@section('page_title', 'Riwayat Presensi')
@section('content')
<div class="container-fluid">
    {{-- Card Filter --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; border: 1px solid var(--border-color) !important;">
        <div class="card-body p-4">
            <form action="{{ route('siswa.riwayat-presensi') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted">Pilih Bulan</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-month text-primary"></i></span>
                        <select name="bulan" class="form-select border-0 bg-light">
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Tahun</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-event text-primary"></i></span>
                        <select name="tahun" class="form-select border-0 bg-light">
                            @for($y=date('Y'); $y>=date('Y')-1; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 fw-bold" style="border-radius: 10px; padding: 10px;">
                        <i class="bi bi-search me-2"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px; border: 1px solid var(--border-color) !important;">
        <div class="card-body p-0"> {{-- P-0 agar tabel menempel rapi ke pinggir card --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0 text-muted small fw-bold" style="width: 150px;">TANGGAL</th>
                            <th class="py-3 border-0 text-muted small fw-bold">STATUS</th>
                            <th class="py-3 border-0 text-muted small fw-bold">MASUK</th>
                            <th class="py-3 border-0 text-muted small fw-bold">PULANG</th>
                            <th class="py-3 border-0 text-muted small fw-bold pe-4">JURNAL / KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                        <tr>
                            <td class="ps-4 py-3 fw-bold text-dark">
                                {{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d M Y') }}
                            </td>
                            <td>
                                @if($r->status_kehadiran == 'hadir')
                                    <span class="badge bg-success-subtle text-success border-0 px-3 py-2 rounded-pill">Hadir</span>
                                @elseif($r->status_kehadiran == 'terlambat')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border-0 px-3 py-2 rounded-pill">Terlambat</span>
                                @elseif(in_array($r->status_kehadiran, ['izin', 'sakit']))
                                    <span class="badge bg-info-subtle text-info-emphasis border-0 px-3 py-2 rounded-pill">{{ ucfirst($r->status_kehadiran) }}</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border-0 px-3 py-2 rounded-pill">Alpha</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $r->jam_masuk ?? '--:--' }}</td>
                            <td class="text-muted">{{ $r->jam_pulang ?? '--:--' }}</td>
                            <td class="pe-4">
                                @if(in_array($r->status_kehadiran, ['izin', 'sakit']))
                                    <div class="small">
                                        <span class="text-primary fw-bold">Alasan:</span> 
                                        <span class="text-muted text-wrap">{{ $r->keterangan_izin }}</span>
                                    </div>
                                @else
                                    <div class="small text-muted text-wrap" style="max-width: 300px; line-height: 1.4;">
                                        {{ Str::limit($r->jurnal_kegiatan ?? 'Tidak ada catatan', 100) }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x d-block mb-2" style="font-size: 3rem; opacity: 0.3;"></i>
                                Tidak ada data presensi pada periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($riwayat->hasPages())
        <div class="card-footer bg-white border-0 py-3" style="border-radius: 0 0 16px 16px;">
            {{ $riwayat->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .bg-success-subtle { background-color: #d1e7dd !important; }
    .bg-warning-subtle { background-color: #fff3cd !important; }
    .bg-info-subtle { background-color: #cff4fc !important; }
    .bg-danger-subtle { background-color: #f8d7da !important; }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .table td, .table th {
        border-bottom: 1px solid #f0f0f0;
    }
</style>
@endsection