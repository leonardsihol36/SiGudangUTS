@extends('layouts.app')

@section('title', 'Detail Mutasi #' . $mutasi->id)
@section('page_title', 'Rincian Transaksi Mutasi Stok')
@section('page_subtitle', 'Bukti riwayat pergerakan stok #' . $mutasi->id . ' tercatat permanen dalam sistem')

@section('content')
<div style="max-width: 720px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i data-lucide="receipt" style="color: var(--primary-orange);"></i>
                Bukti Mutasi Stok: #MUT-{{ str_pad($mutasi->id, 5, '0', STR_PAD_LEFT) }}
            </div>
            <a href="{{ route('mutasi.index') }}" class="btn btn-secondary btn-sm">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                Kembali
            </a>
        </div>

        <div class="card-body">
            <!-- Transaction Type Banner -->
            <div style="padding: 20px; border-radius: var(--radius-md); background: {{ $mutasi->tipe_mutasi === 'masuk' ? 'var(--success-bg)' : 'var(--danger-bg)' }}; border: 1px solid {{ $mutasi->tipe_mutasi === 'masuk' ? 'rgba(46,125,50,0.3)' : 'rgba(211,47,47,0.3)' }}; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span class="badge {{ $mutasi->tipe_mutasi === 'masuk' ? 'badge-green' : 'badge-red' }}" style="font-size: 0.85rem;">
                        {{ $mutasi->tipe_mutasi === 'masuk' ? 'TRANSAKSI BARANG MASUK' : 'TRANSAKSI BARANG KELUAR' }}
                    </span>
                    <div style="font-size: 1.6rem; font-weight: 800; color: {{ $mutasi->tipe_mutasi === 'masuk' ? 'var(--success-text)' : 'var(--danger-text)' }}; margin-top: 6px;">
                        {{ $mutasi->tipe_mutasi === 'masuk' ? '+' : '-' }}{{ $mutasi->jumlah }} {{ $mutasi->barang ? $mutasi->barang->satuan : '' }}
                    </div>
                </div>
                <div style="text-align: right; font-size: 0.85rem; color: var(--text-dark);">
                    <div style="font-weight: 700;">Waktu Eksekusi:</div>
                    <div style="color: var(--text-muted);">{{ $mutasi->tanggal_mutasi->format('d F Y, H:i:s') }}</div>
                </div>
            </div>

            <!-- Stock Shift Visual Box -->
            <div style="background: #FAFAFA; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 20px; margin-bottom: 24px; text-align: center;">
                <div style="font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; margin-bottom: 12px;">
                    Perubahan Saldo Fisik Stok
                </div>
                <div style="display: flex; align-items: center; justify-content: center; gap: 32px;">
                    <div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Stok Sebelum</div>
                        <div style="font-size: 1.8rem; font-weight: 800; color: var(--text-dark);">{{ $mutasi->stok_sebelum }}</div>
                    </div>

                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <span class="badge {{ $mutasi->tipe_mutasi === 'masuk' ? 'badge-green' : 'badge-red' }}">
                            {{ $mutasi->tipe_mutasi === 'masuk' ? '+' : '-' }}{{ $mutasi->jumlah }}
                        </span>
                        <i data-lucide="arrow-right" style="width: 24px; height: 24px; color: var(--primary-orange); margin-top: 4px;"></i>
                    </div>

                    <div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Stok Sesudah</div>
                        <div style="font-size: 1.8rem; font-weight: 800; color: {{ $mutasi->tipe_mutasi === 'masuk' ? 'var(--success)' : 'var(--danger)' }};">
                            {{ $mutasi->stok_sesudah }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 0.9rem;">
                <div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Barang Terkait</div>
                    <div style="font-weight: 700; margin-top: 4px; color: var(--text-dark);">
                        {{ $mutasi->barang ? $mutasi->barang->nama_barang : 'Barang telah dihapus' }}
                    </div>
                    <div style="font-size: 0.78rem; color: var(--text-muted);">
                        Kode: {{ $mutasi->barang ? $mutasi->barang->kode_barang : '-' }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Petugas Operator</div>
                    <div style="font-weight: 700; margin-top: 4px; color: var(--text-dark);">
                        {{ $mutasi->user ? $mutasi->user->name : 'Sistem' }}
                    </div>
                    <div style="font-size: 0.78rem; color: var(--text-muted);">
                        Role: {{ $mutasi->user && $mutasi->user->role ? $mutasi->user->role->name : '-' }}
                    </div>
                </div>
            </div>

            <!-- Keterangan Transaksi -->
            <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border-light);">
                <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; margin-bottom: 6px;">Keterangan & Catatan Transaksi</div>
                <div style="padding: 12px; background: #FAFAFA; border-radius: var(--radius-sm); border: 1px solid var(--border-light); color: var(--text-dark); line-height: 1.5;">
                    {{ $mutasi->keterangan }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
