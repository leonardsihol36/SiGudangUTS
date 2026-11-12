@extends('layouts.app')

@section('title', 'Laporan Inventaris')
@section('page_title', 'Laporan & Rekapitulasi Inventaris')
@section('page_subtitle', 'Laporan menyeluruh status stok, barang minimum, dan mutasi pergerakan')

@section('content')
<div class="card">
    <div class="card-header" style="border-bottom: none; padding-bottom: 0;">
        <div class="card-title">
            <i data-lucide="file-bar-chart" style="color: var(--primary-orange);"></i>
            Pusat Laporan Gudang
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('laporan.export', request()->all()) }}" class="btn btn-secondary btn-sm" title="Download Format CSV untuk Excel">
                <i data-lucide="download" style="width: 16px; height: 16px;"></i>
                Ekspor CSV
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i data-lucide="printer" style="width: 16px; height: 16px;"></i>
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div style="padding: 16px 24px 0; display: flex; gap: 8px; border-bottom: 1px solid var(--border); overflow-x: auto;">
        <a href="{{ route('laporan.index', ['tab' => 'barang']) }}" 
           class="btn btn-sm {{ $tab === 'barang' ? 'btn-primary' : 'btn-secondary' }}" 
           style="border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
            <i data-lucide="package" style="width: 15px; height: 15px;"></i>
            Katalog & Stok Fisik ({{ $barangs->count() }})
        </a>

        <a href="{{ route('laporan.index', ['tab' => 'stok_minimum']) }}" 
           class="btn btn-sm {{ $tab === 'stok_minimum' ? 'btn-primary' : 'btn-secondary' }}"
           style="border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
            <i data-lucide="alert-triangle" style="width: 15px; height: 15px;"></i>
            Daftar Stok Kritis ({{ $stokMinimumBarangs->count() }})
        </a>

        <a href="{{ route('laporan.index', ['tab' => 'mutasi']) }}" 
           class="btn btn-sm {{ $tab === 'mutasi' ? 'btn-primary' : 'btn-secondary' }}"
           style="border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
            <i data-lucide="arrow-left-right" style="width: 15px; height: 15px;"></i>
            Riwayat Masuk & Keluar ({{ $mutasis->count() }})
        </a>
    </div>

    @if($tab === 'mutasi')
        <!-- Date & Type Filter for Mutasi -->
        <div style="padding: 16px 24px; background: #FAFAFA; border-bottom: 1px solid var(--border);">
            <form method="GET" action="{{ route('laporan.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
                <input type="hidden" name="tab" value="mutasi">

                <div style="width: 160px;">
                    <select name="tipe_mutasi" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua Mutasi</option>
                        <option value="masuk" {{ request('tipe_mutasi') === 'masuk' ? 'selected' : '' }}>Barang Masuk</option>
                        <option value="keluar" {{ request('tipe_mutasi') === 'keluar' ? 'selected' : '' }}>Barang Keluar</option>
                    </select>
                </div>

                <div style="width: 150px;">
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}" title="Dari Tanggal">
                </div>

                <div style="width: 150px;">
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}" title="Sampai Tanggal">
                </div>

                <button type="submit" class="btn btn-secondary">
                    <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                    Saring Periode
                </button>

                @if(request()->hasAny(['tipe_mutasi', 'tanggal_mulai', 'tanggal_selesai']))
                    <a href="{{ route('laporan.index', ['tab' => 'mutasi']) }}" class="btn btn-secondary">
                        <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                    </a>
                @endif
            </form>
        </div>
    @endif

    <!-- Tab 1: Rekap Katalog Barang -->
    @if($tab === 'barang')
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok Tersedia</th>
                        <th>Batas Min</th>
                        <th>Lokasi Gudang</th>
                        <th>Supplier</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangs as $idx => $b)
                        <tr>
                            <td style="color: var(--text-muted); font-size: 0.8rem;">{{ $idx + 1 }}</td>
                            <td><span class="badge badge-orange" style="font-family: monospace;">{{ $b->kode_barang }}</span></td>
                            <td style="font-weight: 700;">{{ $b->nama_barang }}</td>
                            <td>{{ $b->kategori }}</td>
                            <td>
                                <span class="badge {{ $b->stok <= $b->stok_minimum ? 'badge-yellow' : 'badge-green' }}">
                                    {{ $b->stok }} {{ $b->satuan }}
                                </span>
                            </td>
                            <td style="color: var(--text-muted);">{{ $b->stok_minimum }} {{ $b->satuan }}</td>
                            <td>{{ $b->lokasi_penyimpanan }}</td>
                            <td>{{ $b->supplier ? $b->supplier->nama_supplier : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 28px; color: var(--text-muted);">
                                Tidak ada data inventaris.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    <!-- Tab 2: Stok Minimum -->
    @elseif($tab === 'stok_minimum')
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok Saat Ini</th>
                        <th>Batas Minimum</th>
                        <th>Status Defisit</th>
                        <th>Supplier Pemasok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stokMinimumBarangs as $idx => $b)
                        <tr>
                            <td style="color: var(--text-muted); font-size: 0.8rem;">{{ $idx + 1 }}</td>
                            <td><span class="badge badge-orange" style="font-family: monospace;">{{ $b->kode_barang }}</span></td>
                            <td style="font-weight: 700; color: var(--danger);">{{ $b->nama_barang }}</td>
                            <td>{{ $b->kategori }}</td>
                            <td style="font-weight: 800; font-size: 1rem; color: var(--danger);">
                                {{ $b->stok }} {{ $b->satuan }}
                            </td>
                            <td>{{ $b->stok_minimum }} {{ $b->satuan }}</td>
                            <td>
                                @if($b->stok <= 0)
                                    <span class="badge badge-red">STOK KOSONG</span>
                                @else
                                    <span class="badge badge-yellow">KURANG {{ $b->stok_minimum - $b->stok }} {{ $b->satuan }}</span>
                                @endif
                            </td>
                            <td>{{ $b->supplier ? $b->supplier->nama_supplier : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 36px; color: var(--success);">
                                <i data-lucide="shield-check" style="width: 32px; height: 32px; margin-bottom: 8px;"></i>
                                <div style="font-weight: 700;">Seluruh Kuantitas Stok Berada di Atas Batas Minimum!</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    <!-- Tab 3: Mutasi Stok -->
    @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Waktu Mutasi</th>
                        <th>Kode & Nama Barang</th>
                        <th>Tipe</th>
                        <th>Kuantitas</th>
                        <th>Stok Sebelum</th>
                        <th>Stok Sesudah</th>
                        <th>Petugas</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mutasis as $idx => $m)
                        <tr>
                            <td style="color: var(--text-muted); font-size: 0.8rem;">{{ $idx + 1 }}</td>
                            <td style="font-size: 0.82rem; color: var(--text-muted);">{{ $m->tanggal_mutasi->format('d/m/Y H:i') }}</td>
                            <td>
                                <div style="font-weight: 700;">{{ $m->barang ? $m->barang->nama_barang : '-' }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $m->barang ? $m->barang->kode_barang : '-' }}</div>
                            </td>
                            <td>
                                @if($m->tipe_mutasi === 'masuk')
                                    <span class="badge badge-green">Masuk</span>
                                @else
                                    <span class="badge badge-red">Keluar</span>
                                @endif
                            </td>
                            <td style="font-weight: 800;">
                                {{ $m->tipe_mutasi === 'masuk' ? '+' : '-' }}{{ $m->jumlah }} {{ $m->barang ? $m->barang->satuan : '' }}
                            </td>
                            <td style="color: var(--text-muted);">{{ $m->stok_sebelum }}</td>
                            <td style="font-weight: 700; color: {{ $m->tipe_mutasi === 'masuk' ? 'var(--success)' : 'var(--danger)' }};">
                                {{ $m->stok_sesudah }}
                            </td>
                            <td>{{ $m->user ? $m->user->name : 'Sistem' }}</td>
                            <td style="font-size: 0.82rem; color: var(--text-muted); max-width: 220px;">{{ $m->keterangan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 28px; color: var(--text-muted);">
                                Tidak ada transaksi mutasi yang tercatat untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
