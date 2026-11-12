@extends('layouts.app')

@section('title', 'Data Supplier')
@section('page_title', 'Manajemen Supplier & Pemasok')
@section('page_subtitle', 'Kelola daftar mitra penyedia barang inventaris gudang SiGudang – 036')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i data-lucide="truck" style="color: var(--primary-orange);"></i>
            Daftar Mitra Pemasok
        </div>

        @if(Auth::user()->isManajer())
            <a href="{{ route('supplier.create') }}" class="btn btn-primary">
                <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                Tambah Supplier Baru
            </a>
        @endif
    </div>

    <!-- Filter & Search Toolbar -->
    <div style="padding: 16px 24px; background: #FAFAFA; border-bottom: 1px solid var(--border);">
        <form method="GET" action="{{ route('supplier.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px;">
                <input 
                    type="text" 
                    name="q" 
                    class="form-control" 
                    placeholder="Cari kode, nama perusahaan, kontak, atau email..." 
                    value="{{ request('q') }}"
                >
            </div>

            <div style="width: 160px;">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Nonaktif" {{ request('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <button type="submit" class="btn btn-secondary">
                <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                Filter
            </button>

            @if(request()->hasAny(['q', 'status']))
                <a href="{{ route('supplier.index') }}" class="btn btn-secondary" title="Reset Filter">
                    <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Supplier</th>
                    <th>Nama Perusahaan</th>
                    <th>Kontak Person</th>
                    <th>Telepon / Email</th>
                    <th>Total Barang</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $index => $s)
                    <tr>
                        <td style="color: var(--text-muted); font-size: 0.8rem;">
                            {{ $suppliers->firstItem() + $index }}
                        </td>
                        <td>
                            <span class="badge badge-orange" style="font-family: monospace; font-size: 0.8rem;">
                                {{ $s->kode_supplier }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-dark);">{{ $s->nama_supplier }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $s->alamat }}
                            </div>
                        </td>
                        <td style="font-size: 0.88rem; font-weight: 600;">
                            {{ $s->nama_kontak }}
                        </td>
                        <td>
                            <div style="font-size: 0.85rem;">{{ $s->no_telepon }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $s->email }}</div>
                        </td>
                        <td>
                            <span class="badge badge-gray">
                                {{ $s->barang_count }} Barang
                            </span>
                        </td>
                        <td>
                            @if($s->status === 'Aktif')
                                <span class="badge badge-green">Aktif</span>
                            @else
                                <span class="badge badge-gray">Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('supplier.show', $s) }}" class="btn btn-secondary btn-icon" title="Detail Supplier">
                                    <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                                </a>

                                @if(Auth::user()->isManajer())
                                    <a href="{{ route('supplier.edit', $s) }}" class="btn btn-secondary btn-icon" title="Edit Supplier">
                                        <i data-lucide="pencil" style="width: 15px; height: 15px; color: var(--primary-orange);"></i>
                                    </a>

                                    <button 
                                        type="button" 
                                        class="btn btn-secondary btn-icon" 
                                        title="Hapus Supplier"
                                        onclick="showConfirmModal('{{ route('supplier.destroy', $s) }}', 'DELETE', 'Hapus Supplier', 'Apakah Anda yakin ingin menghapus supplier \'{{ $s->nama_supplier }}\' ({{ $s->kode_supplier }})?')"
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
                                <i data-lucide="truck" class="empty-icon"></i>
                                <div class="empty-title">Supplier Tidak Ditemukan</div>
                                <div class="empty-desc">Tidak ada mitra pemasok yang cocok dengan kata kunci pencarian.</div>
                                @if(Auth::user()->isManajer())
                                    <a href="{{ route('supplier.create') }}" class="btn btn-primary">
                                        Tambah Supplier Sekarang
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($suppliers->hasPages())
        <div class="pagination-wrapper">
            <div style="font-size: 0.82rem; color: var(--text-muted);">
                Menampilkan {{ $suppliers->firstItem() }} - {{ $suppliers->lastItem() }} dari {{ $suppliers->total() }} supplier
            </div>
            <div>
                {{ $suppliers->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
