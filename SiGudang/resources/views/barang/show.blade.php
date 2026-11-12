@extends('layouts.app')

@section('title', 'Detail Barang: ' . $barang->nama_barang)
@section('page_title', 'Detail Inventaris Barang')
@section('page_subtitle', 'Spesifikasi teknis, profil pemasok, dan riwayat mutasi ' . $barang->kode_barang)

@section('content')
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
    <!-- Main Info Card -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <div class="card-title">
                <i data-lucide="package" style="color: var(--primary-orange);"></i>
                {{ $barang->nama_barang }}
            </div>
            <div style="display: flex; gap: 8px;">
                @if(!Auth::user()->isAuditor())
                    <a href="{{ route('barang.edit', $barang) }}" class="btn btn-secondary btn-sm">
                        <i data-lucide="pencil" style="width: 15px; height: 15px; color: var(--primary-orange);"></i>
                        Edit Barang
                    </a>
                @endif
                <a href="{{ route('barang.index') }}" class="btn btn-secondary btn-sm">
                    <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i>
                    Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Kode Barang</div>
                    <div style="font-size: 1.1rem; font-weight: 800; color: var(--primary-orange); margin-top: 4px; font-family: monospace;">
                        {{ $barang->kode_barang }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Kategori & Satuan</div>
                    <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-top: 4px;">
                        {{ $barang->kategori }} &bull; Satuan: {{ $barang->satuan }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Lokasi Penyimpanan Gudang</div>
                    <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-dark); margin-top: 4px;">
                        <i data-lucide="map-pin" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; color: var(--primary-orange);"></i>
                        {{ $barang->lokasi_penyimpanan }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Batas Stok Minimum</div>
                    <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-dark); margin-top: 4px;">
                        {{ $barang->stok_minimum }} {{ $barang->satuan }}
                    </div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-light); padding-top: 16px;">
                <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; margin-bottom: 6px;">Deskripsi & Spesifikasi</div>
                <div style="font-size: 0.9rem; color: var(--text-dark); line-height: 1.6; background: #FAFAFA; padding: 14px; border-radius: var(--radius-md); border: 1px solid var(--border-light);">
                    {{ $barang->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side: Stock Badge & Supplier Mini Card -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Stock Level Card -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div class="card-title">Kondisi Stok Fisik</div>
                @if($barang->stok <= 0)
                    <span class="badge badge-red">Habis</span>
                @elseif($barang->stok <= $barang->stok_minimum)
                    <span class="badge badge-yellow">Menipis</span>
                @else
                    <span class="badge badge-green">Aman</span>
                @endif
            </div>
            <div class="card-body" style="text-align: center; padding: 24px;">
                <div style="font-size: 2.8rem; font-weight: 800; color: {{ $barang->stok <= $barang->stok_minimum ? 'var(--danger)' : 'var(--primary-orange)' }}; line-height: 1;">
                    {{ $barang->stok }}
                </div>
                <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-top: 4px;">
                    {{ $barang->satuan }} Tersedia
                </div>

                @if(!Auth::user()->isAuditor())
                    <div style="margin-top: 18px; display: flex; gap: 8px; justify-content: center;">
                        <a href="{{ route('mutasi.create', ['barang_id' => $barang->id]) }}" class="btn btn-primary btn-sm" style="width: 100%;">
                            <i data-lucide="arrow-left-right" style="width: 14px; height: 14px;"></i>
                            Catat Mutasi Stok
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Supplier Mini Card -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div class="card-title">
                    <i data-lucide="truck" style="color: var(--primary-orange);"></i>
                    Mitra Supplier
                </div>
            </div>
            <div class="card-body">
                @if($barang->supplier)
                    <div style="font-weight: 750; font-size: 0.95rem; color: var(--text-dark);">
                        {{ $barang->supplier->nama_supplier }}
                    </div>
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                        Kontak: {{ $barang->supplier->nama_kontak }} &bull; {{ $barang->supplier->no_telepon }}
                    </div>
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                        Email: {{ $barang->supplier->email }}
                    </div>
                    <div style="margin-top: 12px;">
                        <a href="{{ route('supplier.show', $barang->supplier) }}" class="btn btn-secondary btn-sm" style="width: 100%;">
                            Lihat Profil Supplier
                        </a>
                    </div>
                @else
                    <div style="color: var(--text-muted); font-size: 0.85rem;">Tidak ada data supplier.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Stock Movement History Table for This Item -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i data-lucide="history" style="color: var(--primary-orange);"></i>
            Riwayat Mutasi Stok Barang Ini
        </div>
        <span class="badge badge-orange">{{ $mutasiList->total() }} Transaksi</span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu Mutasi</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Stok Sebelum</th>
                    <th>Stok Sesudah</th>
                    <th>Petugas</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mutasiList as $m)
                    <tr>
                        <td style="font-size: 0.82rem; color: var(--text-muted);">
                            {{ $m->tanggal_mutasi->format('d M Y, H:i') }}
                        </td>
                        <td>
                            @if($m->tipe_mutasi === 'masuk')
                                <span class="badge badge-green">+ Masuk</span>
                            @else
                                <span class="badge badge-red">- Keluar</span>
                            @endif
                        </td>
                        <td style="font-weight: 700;">
                            {{ $m->jumlah }} {{ $barang->satuan }}
                        </td>
                        <td style="color: var(--text-muted);">{{ $m->stok_sebelum }}</td>
                        <td style="font-weight: 800; color: {{ $m->tipe_mutasi === 'masuk' ? 'var(--success)' : 'var(--danger)' }};">
                            {{ $m->stok_sesudah }}
                        </td>
                        <td style="font-size: 0.85rem;">
                            {{ $m->user ? $m->user->name : 'Sistem' }}
                        </td>
                        <td style="font-size: 0.82rem; color: var(--text-muted);">
                            {{ $m->keterangan }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 28px; color: var(--text-muted);">
                            Belum ada riwayat mutasi stok untuk barang ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($mutasiList->hasPages())
        <div class="pagination-wrapper">
            {{ $mutasiList->links() }}
        </div>
    @endif
</div>
@endsection
