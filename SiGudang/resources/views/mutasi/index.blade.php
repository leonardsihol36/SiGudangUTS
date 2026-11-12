@extends('layouts.app')

@section('title', 'Mutasi Stok')
@section('page_title', 'Riwayat & Mutasi Stok Gudang')
@section('page_subtitle', 'Pencatatan pergerakan barang masuk dan keluar dengan audit stok akurat')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i data-lucide="arrow-left-right" style="color: var(--primary-orange);"></i>
            Riwayat Pergerakan Stok Barang
        </div>

        @if(!Auth::user()->isAuditor())
            <a href="{{ route('mutasi.create') }}" class="btn btn-primary">
                <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i>
                Catat Mutasi Baru
            </a>
        @endif
    </div>

    <!-- Filter Toolbar -->
    <div style="padding: 16px 24px; background: #FAFAFA; border-bottom: 1px solid var(--border);">
        <form method="GET" action="{{ route('mutasi.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <input 
                    type="text" 
                    name="q" 
                    class="form-control" 
                    placeholder="Cari kode/nama barang atau keterangan..." 
                    value="{{ request('q') }}"
                >
            </div>

            <div style="width: 160px;">
                <select name="tipe_mutasi" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Tipe</option>
                    <option value="masuk" {{ request('tipe_mutasi') === 'masuk' ? 'selected' : '' }}>Barang Masuk</option>
                    <option value="keluar" {{ request('tipe_mutasi') === 'keluar' ? 'selected' : '' }}>Barang Keluar</option>
                </select>
            </div>

            <div style="width: 200px;">
                <select name="barang_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Barang</option>
                    @foreach($barangs as $brg)
                        <option value="{{ $brg->id }}" {{ request('barang_id') == $brg->id ? 'selected' : '' }}>
                            {{ $brg->nama_barang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="width: 145px;">
                <input 
                    type="date" 
                    name="tanggal_mulai" 
                    class="form-control" 
                    value="{{ request('tanggal_mulai') }}" 
                    title="Dari Tanggal"
                >
            </div>

            <div style="width: 145px;">
                <input 
                    type="date" 
                    name="tanggal_selesai" 
                    class="form-control" 
                    value="{{ request('tanggal_selesai') }}" 
                    title="Sampai Tanggal"
                >
            </div>

            <button type="submit" class="btn btn-secondary">
                <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                Filter
            </button>

            @if(request()->hasAny(['q', 'tipe_mutasi', 'barang_id', 'tanggal_mulai', 'tanggal_selesai']))
                <a href="{{ route('mutasi.index') }}" class="btn btn-secondary" title="Reset Filter">
                    <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Data Table -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu Mutasi</th>
                    <th>Kode & Nama Barang</th>
                    <th>Tipe Transaksi</th>
                    <th>Jumlah Mutasi</th>
                    <th>Perubahan Stok</th>
                    <th>Petugas Operator</th>
                    <th>Keterangan Transaksi</th>
                    <th style="text-align: right;">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mutasis as $m)
                    <tr>
                        <td style="font-size: 0.82rem; color: var(--text-muted); white-space: nowrap;">
                            {{ $m->tanggal_mutasi->format('d M Y, H:i') }}
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-dark);">
                                {{ $m->barang ? $m->barang->nama_barang : 'Barang Dihapus' }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">
                                {{ $m->barang ? $m->barang->kode_barang : '-' }}
                            </div>
                        </td>
                        <td>
                            @if($m->tipe_mutasi === 'masuk')
                                <span class="badge badge-green">
                                    <i data-lucide="arrow-down-left" style="width: 12px; height: 12px;"></i>
                                    Barang Masuk
                                </span>
                            @else
                                <span class="badge badge-red">
                                    <i data-lucide="arrow-up-right" style="width: 12px; height: 12px;"></i>
                                    Barang Keluar
                                </span>
                            @endif
                        </td>
                        <td style="font-weight: 750; font-size: 0.95rem;">
                            {{ $m->tipe_mutasi === 'masuk' ? '+' : '-' }}{{ $m->jumlah }} {{ $m->barang ? $m->barang->satuan : '' }}
                        </td>
                        <td>
                            <span style="color: var(--text-muted); font-size: 0.85rem;">{{ $m->stok_sebelum }}</span>
                            <i data-lucide="arrow-right" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle; margin: 0 4px; color: var(--primary-orange);"></i>
                            <span style="font-weight: 800; color: {{ $m->tipe_mutasi === 'masuk' ? 'var(--success)' : 'var(--danger)' }}; font-size: 0.95rem;">
                                {{ $m->stok_sesudah }}
                            </span>
                        </td>
                        <td style="font-size: 0.85rem; font-weight: 600;">
                            {{ $m->user ? $m->user->name : 'Sistem' }}
                        </td>
                        <td style="font-size: 0.82rem; color: var(--text-muted); max-width: 250px;">
                            {{ $m->keterangan }}
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('mutasi.show', $m) }}" class="btn btn-secondary btn-icon" title="Lihat Rincian Bukti Transaksi">
                                <i data-lucide="file-text" style="width: 15px; height: 15px;"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i data-lucide="arrow-left-right" class="empty-icon"></i>
                                <div class="empty-title">Belum Ada Riwayat Mutasi</div>
                                <div class="empty-desc">Tidak ditemukan transaksi mutasi stok sesuai parameter filter pencarian.</div>
                                @if(!Auth::user()->isAuditor())
                                    <a href="{{ route('mutasi.create') }}" class="btn btn-primary">
                                        Catat Transaksi Pertama
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($mutasis->hasPages())
        <div class="pagination-wrapper">
            <div style="font-size: 0.82rem; color: var(--text-muted);">
                Menampilkan {{ $mutasis->firstItem() }} - {{ $mutasis->lastItem() }} dari {{ $mutasis->total() }} mutasi
            </div>
            <div>
                {{ $mutasis->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
