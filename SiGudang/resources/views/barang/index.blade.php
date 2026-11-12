@extends('layouts.app')

@section('title', 'Data Barang')
@section('page_title', 'Manajemen Data Barang')
@section('page_subtitle', 'Katalog seluruh inventaris barang gudang dan pemantauan stok fisik')

@section('content')
<div class="card">
    <!-- Header with Action Button & Filters -->
    <div class="card-header">
        <div class="card-title">
            <i data-lucide="package" style="color: var(--primary-orange);"></i>
            Daftar Inventaris Barang
        </div>

        @if(!Auth::user()->isAuditor())
            <a href="{{ route('barang.create') }}" class="btn btn-primary">
                <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                Tambah Barang Baru
            </a>
        @endif
    </div>

    <!-- Filter & Search Toolbar -->
    <div style="padding: 16px 24px; background: #FAFAFA; border-bottom: 1px solid var(--border);">
        <form method="GET" action="{{ route('barang.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 220px;">
                <input 
                    type="text" 
                    name="q" 
                    class="form-control" 
                    placeholder="Cari kode, nama, atau lokasi barang..." 
                    value="{{ request('q') }}"
                >
            </div>

            <div style="width: 180px;">
                <select name="kategori" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="width: 200px;">
                <select name="supplier_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                            {{ $sup->nama_supplier }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-secondary">
                <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                Filter
            </button>

            @if(request()->hasAny(['q', 'kategori', 'supplier_id']))
                <a href="{{ route('barang.index') }}" class="btn btn-secondary" title="Reset Filter">
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
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama & Kategori</th>
                    <th>Stok Tersedia</th>
                    <th>Stok Minimum</th>
                    <th>Lokasi Rak</th>
                    <th>Mitra Supplier</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangs as $index => $b)
                    <tr>
                        <td style="color: var(--text-muted); font-size: 0.8rem;">
                            {{ $barangs->firstItem() + $index }}
                        </td>
                        <td>
                            <span class="badge badge-orange" style="font-family: monospace; font-size: 0.8rem;">
                                {{ $b->kode_barang }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-dark);">{{ $b->nama_barang }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $b->kategori }} &bull; {{ $b->satuan }}</div>
                        </td>
                        <td>
                            @if($b->stok <= 0)
                                <span class="badge badge-red">
                                    <i data-lucide="x-circle" style="width: 12px; height: 12px;"></i>
                                    Habis (0)
                                </span>
                            @elseif($b->stok <= $b->stok_minimum)
                                <span class="badge badge-yellow">
                                    <i data-lucide="alert-triangle" style="width: 12px; height: 12px;"></i>
                                    Kritis ({{ $b->stok }} {{ $b->satuan }})
                                </span>
                            @else
                                <span class="badge badge-green">
                                    <i data-lucide="check" style="width: 12px; height: 12px;"></i>
                                    Aman ({{ $b->stok }} {{ $b->satuan }})
                                </span>
                            @endif
                        </td>
                        <td style="font-size: 0.85rem; color: var(--text-muted);">
                            {{ $b->stok_minimum }} {{ $b->satuan }}
                        </td>
                        <td style="font-size: 0.85rem; font-weight: 600;">
                            <i data-lucide="map-pin" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; color: var(--primary-orange);"></i>
                            {{ $b->lokasi_penyimpanan }}
                        </td>
                        <td style="font-size: 0.82rem; color: var(--text-dark);">
                            {{ $b->supplier ? $b->supplier->nama_supplier : '-' }}
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('barang.show', $b) }}" class="btn btn-secondary btn-icon" title="Detail Barang">
                                    <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                                </a>

                                @if(!Auth::user()->isAuditor())
                                    <a href="{{ route('barang.edit', $b) }}" class="btn btn-secondary btn-icon" title="Edit Barang">
                                        <i data-lucide="pencil" style="width: 15px; height: 15px; color: var(--primary-orange);"></i>
                                    </a>
                                @endif

                                @if(Auth::user()->isManajer())
                                    <button 
                                        type="button" 
                                        class="btn btn-secondary btn-icon" 
                                        title="Hapus Barang"
                                        onclick="showConfirmModal('{{ route('barang.destroy', $b) }}', 'DELETE', 'Hapus Barang', 'Apakah Anda yakin ingin menghapus barang \'{{ $b->nama_barang }}\' ({{ $b->kode_barang }}) dari sistem?')"
                                    >
                                        <i data-lucide="trash-2" style="width: 15px; height: 15px; color: var(--danger);"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i data-lucide="package-x" class="empty-icon"></i>
                                <div class="empty-title">Data Barang Tidak Ditemukan</div>
                                <div class="empty-desc">Tidak ada data inventaris yang sesuai dengan filter pencarian Anda.</div>
                                @if(!Auth::user()->isAuditor())
                                    <a href="{{ route('barang.create') }}" class="btn btn-primary">
                                        Tambah Barang Sekarang
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($barangs->hasPages())
        <div class="pagination-wrapper">
            <div style="font-size: 0.82rem; color: var(--text-muted);">
                Menampilkan {{ $barangs->firstItem() }} - {{ $barangs->lastItem() }} dari {{ $barangs->total() }} barang
            </div>
            <div>
                {{ $barangs->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
