@extends('layouts.app')

@section('title', 'Tambah Barang Baru')
@section('page_title', 'Tambah Barang Baru')
@section('page_subtitle', 'Mendaftarkan item baru ke dalam database inventaris SiGudang – 036')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i data-lucide="package-plus" style="color: var(--primary-orange);"></i>
                Formulir Pendaftaran Barang Inventaris
            </div>
            <a href="{{ route('barang.index') }}" class="btn btn-secondary btn-sm">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                Kembali ke Daftar
            </a>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('barang.store') }}">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Kode Barang -->
                    <div class="form-group">
                        <label for="kode_barang" class="form-label">Kode Barang <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="kode_barang" 
                            id="kode_barang" 
                            class="form-control {{ $errors->has('kode_barang') ? 'is-invalid' : '' }}" 
                            placeholder="Contoh: BRG-006"
                            value="{{ old('kode_barang') }}" 
                            required
                        >
                        @error('kode_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-hint">Kode unik identifikasi barang di gudang.</div>
                    </div>

                    <!-- Nama Barang -->
                    <div class="form-group">
                        <label for="nama_barang" class="form-label">Nama Barang <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="nama_barang" 
                            id="nama_barang" 
                            class="form-control {{ $errors->has('nama_barang') ? 'is-invalid' : '' }}" 
                            placeholder="Contoh: Rak Besi Siku Lubang 2 Meter"
                            value="{{ old('nama_barang') }}" 
                            required
                        >
                        @error('nama_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Kategori -->
                    <div class="form-group">
                        <label for="kategori" class="form-label">Kategori <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="kategori" 
                            id="kategori" 
                            list="kategoriList" 
                            class="form-control {{ $errors->has('kategori') ? 'is-invalid' : '' }}" 
                            placeholder="Pilih atau ketik kategori baru"
                            value="{{ old('kategori') }}" 
                            required
                        >
                        <datalist id="kategoriList">
                            <option value="Elektronik & Perangkat">
                            <option value="Packaging">
                            <option value="Alat Berat & Handling">
                            <option value="Safety & APD">
                            <option value="Spare Part & Utilitas">
                        </datalist>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Satuan -->
                    <div class="form-group">
                        <label for="satuan" class="form-label">Satuan Unit <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="satuan" 
                            id="satuan" 
                            list="satuanList" 
                            class="form-control {{ $errors->has('satuan') ? 'is-invalid' : '' }}" 
                            placeholder="Contoh: Unit, Box, Pcs, Roll, Kg"
                            value="{{ old('satuan', 'Unit') }}" 
                            required
                        >
                        <datalist id="satuanList">
                            <option value="Unit">
                            <option value="Box">
                            <option value="Pcs">
                            <option value="Roll">
                            <option value="Pallet">
                            <option value="Set">
                            <option value="Kg">
                        </datalist>
                        @error('satuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Stok Awal -->
                    <div class="form-group">
                        <label for="stok" class="form-label">Stok Awal Fisik <span class="required">*</span></label>
                        <input 
                            type="number" 
                            name="stok" 
                            id="stok" 
                            min="0" 
                            class="form-control {{ $errors->has('stok') ? 'is-invalid' : '' }}" 
                            placeholder="0"
                            value="{{ old('stok', 0) }}" 
                            required
                        >
                        @error('stok')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-hint">Kuantitas fisik awal saat pendaftaran barang.</div>
                    </div>

                    <!-- Stok Minimum -->
                    <div class="form-group">
                        <label for="stok_minimum" class="form-label">Batas Stok Minimum <span class="required">*</span></label>
                        <input 
                            type="number" 
                            name="stok_minimum" 
                            id="stok_minimum" 
                            min="0" 
                            class="form-control {{ $errors->has('stok_minimum') ? 'is-invalid' : '' }}" 
                            placeholder="10"
                            value="{{ old('stok_minimum', 10) }}" 
                            required
                        >
                        @error('stok_minimum')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-hint">Sistem akan memberi peringatan jika stok mencapai nilai ini.</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Lokasi Penyimpanan -->
                    <div class="form-group">
                        <label for="lokasi_penyimpanan" class="form-label">Lokasi Rak / Zona Penyimpanan <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="lokasi_penyimpanan" 
                            id="lokasi_penyimpanan" 
                            class="form-control {{ $errors->has('lokasi_penyimpanan') ? 'is-invalid' : '' }}" 
                            placeholder="Contoh: Rak A-02, Zona B-Pallet 03"
                            value="{{ old('lokasi_penyimpanan') }}" 
                            required
                        >
                        @error('lokasi_penyimpanan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Supplier -->
                    <div class="form-group">
                        <label for="supplier_id" class="form-label">Pemasok / Supplier <span class="required">*</span></label>
                        <select name="supplier_id" id="supplier_id" class="form-control {{ $errors->has('supplier_id') ? 'is-invalid' : '' }}" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                    {{ $sup->nama_supplier }} ({{ $sup->kode_supplier }})
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="form-group">
                    <label for="deskripsi" class="form-label">Deskripsi & Spesifikasi Barang</label>
                    <textarea 
                        name="deskripsi" 
                        id="deskripsi" 
                        rows="3" 
                        class="form-control {{ $errors->has('deskripsi') ? 'is-invalid' : '' }}" 
                        placeholder="Keterangan spesifikasi teknis, dimensi, atau instruksi penanganan barang..."
                    >{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border);">
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                        Simpan Barang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
