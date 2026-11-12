@extends('layouts.app')

@section('title', 'Catat Mutasi Stok')
@section('page_title', 'Pencatatan Mutasi Stok')
@section('page_subtitle', 'Formulir resmi pergerakan fisik barang masuk dan keluar gudang')

@section('content')
<div style="max-width: 750px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i data-lucide="plus-circle" style="color: var(--primary-orange);"></i>
                Formulir Transaksi Mutasi Stok
            </div>
            <a href="{{ route('mutasi.index') }}" class="btn btn-secondary btn-sm">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                Kembali ke Riwayat
            </a>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('mutasi.store') }}" id="mutasiForm">
                @csrf

                <!-- Pilih Barang -->
                <div class="form-group">
                    <label for="barang_id" class="form-label">Pilih Barang Inventaris <span class="required">*</span></label>
                    <select name="barang_id" id="barang_id" class="form-control {{ $errors->has('barang_id') ? 'is-invalid' : '' }}" required onchange="updateStockInfo()">
                        <option value="" data-stok="0" data-satuan="">-- Pilih Barang --</option>
                        @foreach($barangs as $brg)
                            <option 
                                value="{{ $brg->id }}" 
                                data-stok="{{ $brg->stok }}" 
                                data-satuan="{{ $brg->satuan }}"
                                {{ (old('barang_id', request('barang_id')) == $brg->id) ? 'selected' : '' }}
                            >
                                {{ $brg->kode_barang }} - {{ $brg->nama_barang }} (Stok Saat Ini: {{ $brg->stok }} {{ $brg->satuan }})
                            </option>
                        @endforeach
                    </select>
                    @error('barang_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <!-- Dynamic Stock Info Badge -->
                    <div id="stockNoticeBox" style="margin-top: 10px; padding: 12px 16px; background: var(--orange-soft); border-radius: var(--radius-md); border: 1px solid rgba(251, 140, 0, 0.3); display: none;">
                        <span style="font-size: 0.82rem; color: var(--text-dark);">
                            Stok Fisik Tersedia: <strong id="currentStockDisplay" style="color: var(--primary-orange); font-size: 1rem;">0</strong>
                        </span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Tipe Mutasi -->
                    <div class="form-group">
                        <label class="form-label">Tipe Pergerakan Stok <span class="required">*</span></label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 4px;">
                            <label style="display: flex; align-items: center; gap: 8px; padding: 12px; border: 1px solid var(--border); border-radius: var(--radius-md); cursor: pointer; background: #FAFAFA;">
                                <input type="radio" name="tipe_mutasi" value="masuk" {{ old('tipe_mutasi', 'masuk') === 'masuk' ? 'checked' : '' }} style="accent-color: var(--success);" onchange="validateStockLive()">
                                <div>
                                    <div style="font-weight: 700; color: var(--success);">Barang Masuk</div>
                                    <div style="font-size: 0.72rem; color: var(--text-muted);">Penerimaan / Tambah</div>
                                </div>
                            </label>

                            <label style="display: flex; align-items: center; gap: 8px; padding: 12px; border: 1px solid var(--border); border-radius: var(--radius-md); cursor: pointer; background: #FAFAFA;">
                                <input type="radio" name="tipe_mutasi" value="keluar" {{ old('tipe_mutasi') === 'keluar' ? 'checked' : '' }} style="accent-color: var(--danger);" onchange="validateStockLive()">
                                <div>
                                    <div style="font-weight: 700; color: var(--danger);">Barang Keluar</div>
                                    <div style="font-size: 0.72rem; color: var(--text-muted);">Pengeluaran / Kirim</div>
                                </div>
                            </label>
                        </div>
                        @error('tipe_mutasi')
                            <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Jumlah -->
                    <div class="form-group">
                        <label for="jumlah" class="form-label">Kuantitas / Jumlah <span class="required">*</span></label>
                        <input 
                            type="number" 
                            name="jumlah" 
                            id="jumlah" 
                            min="1" 
                            class="form-control {{ $errors->has('jumlah') ? 'is-invalid' : '' }}" 
                            placeholder="Contoh: 10"
                            value="{{ old('jumlah') }}" 
                            required
                            oninput="validateStockLive()"
                        >
                        @error('jumlah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="stockValidationWarning" style="color: var(--danger); font-size: 0.75rem; margin-top: 4px; display: none; font-weight: 600;">
                            <i data-lucide="alert-octagon" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                            Peringatan: Jumlah keluar melebihi stok tersedia! Sistem akan menolak mutasi negatif.
                        </div>
                    </div>
                </div>

                <!-- Tanggal Mutasi -->
                <div class="form-group">
                    <label for="tanggal_mutasi" class="form-label">Tanggal & Waktu Transaksi <span class="required">*</span></label>
                    <input 
                        type="datetime-local" 
                        name="tanggal_mutasi" 
                        id="tanggal_mutasi" 
                        class="form-control {{ $errors->has('tanggal_mutasi') ? 'is-invalid' : '' }}" 
                        value="{{ old('tanggal_mutasi', now()->format('Y-m-d\TH:i')) }}" 
                        required
                    >
                    @error('tanggal_mutasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div class="form-group">
                    <label for="keterangan" class="form-label">Keterangan / Nomor Referensi Dokumen <span class="required">*</span></label>
                    <textarea 
                        name="keterangan" 
                        id="keterangan" 
                        rows="3" 
                        class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}" 
                        placeholder="Contoh: Penerimaan surat jalan PO-2026-004 atau pengiriman pesanan cabang..."
                        required
                    >{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border);">
                    <a href="{{ route('mutasi.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" id="submitMutasiBtn">
                        <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                        Simpan & Perbarui Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateStockInfo() {
        const select = document.getElementById('barang_id');
        const selected = select.options[select.selectedIndex];
        const stockBox = document.getElementById('stockNoticeBox');
        const display = document.getElementById('currentStockDisplay');

        if (selected && selected.value) {
            const stok = selected.getAttribute('data-stok');
            const satuan = selected.getAttribute('data-satuan');
            display.innerText = stok + ' ' + satuan;
            stockBox.style.display = 'block';
        } else {
            stockBox.style.display = 'none';
        }
        validateStockLive();
    }

    function validateStockLive() {
        const select = document.getElementById('barang_id');
        const selected = select.options[select.selectedIndex];
        const warning = document.getElementById('stockValidationWarning');
        const submitBtn = document.getElementById('submitMutasiBtn');

        if (!selected || !selected.value) return;

        const currentStok = parseInt(selected.getAttribute('data-stok')) || 0;
        const jumlahInput = parseInt(document.getElementById('jumlah').value) || 0;
        const tipe = document.querySelector('input[name="tipe_mutasi"]:checked').value;

        if (tipe === 'keluar' && jumlahInput > currentStok) {
            warning.style.display = 'block';
            submitBtn.style.opacity = '0.6';
        } else {
            warning.style.display = 'none';
            submitBtn.style.opacity = '1';
        }
    }

    // Run on load
    document.addEventListener('DOMContentLoaded', function() {
        updateStockInfo();
    });
</script>
@endpush
