@extends('layouts.app')

@section('title', 'Detail Supplier: ' . $supplier->nama_supplier)
@section('page_title', 'Profil Mitra Supplier')
@section('page_subtitle', 'Rincian kontak, alamat, dan katalog barang pasokan ' . $supplier->kode_supplier)

@section('content')
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
    <!-- Company Card -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <div class="card-title">
                <i data-lucide="building" style="color: var(--primary-orange);"></i>
                {{ $supplier->nama_supplier }}
            </div>
            <div style="display: flex; gap: 8px;">
                @if(Auth::user()->isManajer())
                    <a href="{{ route('supplier.edit', $supplier) }}" class="btn btn-secondary btn-sm">
                        <i data-lucide="pencil" style="width: 15px; height: 15px; color: var(--primary-orange);"></i>
                        Edit
                    </a>
                @endif
                <a href="{{ route('supplier.index') }}" class="btn btn-secondary btn-sm">
                    <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i>
                    Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Kode Supplier</div>
                    <div style="font-size: 1.1rem; font-weight: 800; color: var(--primary-orange); margin-top: 4px; font-family: monospace;">
                        {{ $supplier->kode_supplier }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Status Kerjasama</div>
                    <div style="margin-top: 4px;">
                        @if($supplier->status === 'Aktif')
                            <span class="badge badge-green">Aktif Beroperasi</span>
                        @else
                            <span class="badge badge-gray">Nonaktif</span>
                        @endif
                    </div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Kontak Person (PIC)</div>
                    <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-top: 4px;">
                        {{ $supplier->nama_kontak }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">No. Telepon / WhatsApp</div>
                    <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-dark); margin-top: 4px;">
                        {{ $supplier->no_telepon }}
                    </div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-light); padding-top: 16px;">
                <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; margin-bottom: 6px;">Email Resmi</div>
                <div style="font-size: 0.9rem; color: var(--text-dark);">{{ $supplier->email }}</div>
            </div>

            <div style="border-top: 1px solid var(--border-light); padding-top: 16px; margin-top: 16px;">
                <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; margin-bottom: 6px;">Alamat Kantor / Pergudangan</div>
                <div style="font-size: 0.9rem; color: var(--text-dark); line-height: 1.6; background: #FAFAFA; padding: 14px; border-radius: var(--radius-md); border: 1px solid var(--border-light);">
                    <i data-lucide="map-pin" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; color: var(--primary-orange);"></i>
                    {{ $supplier->alamat }}
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side: Total Goods Supplied Stat -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <div class="card-title">Katalog Pasokan</div>
        </div>
        <div class="card-body" style="text-align: center; padding: 32px 20px;">
            <div class="stat-icon-wrapper icon-orange" style="margin: 0 auto 16px; width: 64px; height: 64px;">
                <i data-lucide="boxes" style="width: 32px; height: 32px;"></i>
            </div>
            <div style="font-size: 2.5rem; font-weight: 800; color: var(--text-dark); line-height: 1;">
                {{ $barangs->total() }}
            </div>
            <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-top: 6px;">
                Item Barang Dipasok
            </div>
            @if(Auth::user()->isManajer() || Auth::user()->isStafGudang())
                <div style="margin-top: 24px;">
                    <a href="{{ route('barang.create') }}" class="btn btn-outline-orange btn-sm" style="width: 100%;">
                        + Tambah Barang dari Pemasok Ini
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- List of goods supplied by this supplier -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i data-lucide="package" style="color: var(--primary-orange);"></i>
            Katalog Barang dari {{ $supplier->nama_supplier }}
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Stok Saat Ini</th>
                    <th>Satuan</th>
                    <th>Lokasi Rak</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangs as $b)
                    <tr>
                        <td>
                            <span class="badge badge-orange" style="font-family: monospace;">
                                {{ $b->kode_barang }}
                            </span>
                        </td>
                        <td style="font-weight: 700;">{{ $b->nama_barang }}</td>
                        <td>{{ $b->kategori }}</td>
                        <td>
                            <span class="badge {{ $b->stok <= $b->stok_minimum ? 'badge-yellow' : 'badge-green' }}">
                                {{ $b->stok }}
                            </span>
                        </td>
                        <td>{{ $b->satuan }}</td>
                        <td>{{ $b->lokasi_penyimpanan }}</td>
                        <td style="text-align: right;">
                            <a href="{{ route('barang.show', $b) }}" class="btn btn-secondary btn-sm">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 28px; color: var(--text-muted);">
                            Belum ada barang inventaris yang didaftarkan dari supplier ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($barangs->hasPages())
        <div class="pagination-wrapper">
            {{ $barangs->links() }}
        </div>
    @endif
</div>
@endsection
