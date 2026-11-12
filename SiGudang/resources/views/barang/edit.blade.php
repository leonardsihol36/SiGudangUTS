@extends('layouts.app')

@section('title', 'Edit Barang: ' . $barang->nama_barang)
@section('page_title', 'Ubah Informasi Barang')
@section('page_subtitle', 'Memperbarui rincian spesifikasi barang ' . $barang->kode_barang)

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i data-lucide="edit-3" style="color: var(--primary-orange);"></i>
                Edit Data: {{ $barang->nama_barang }} ({{ $barang->kode_barang }})
            </div>
            <a href="{{ route('barang.index') }}" class="btn btn-secondary btn-sm">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                Kembali ke Daftar
            </a>
        </div>

        <div class="card-body">
            <!-- Notice on stock locked rule -->
            <div class="alert alert-info" style="margin-bottom: 24px;">
                <i data-lucide="info" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
                <div>
                    <strong>Ketentuan Mutasi Stok:</strong> Jumlah kuantitas stok fisik tidak dapat diubah secara langsung pada form edit barang. Untuk menambah atau mengurangi stok, silakan gunakan menu 
                    <a href="{{ route('mutasi.create') }}" style="color: var(--primary-orange); font-weight: 700; text-decoration: underline;">Mutasi Stok</a> 
                    agar seluruh jejak histori tercatat secara akuntabel.
                </div>
            </div>

            <form method="POST" action="{{ route('barang.update', $barang) }}">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Kode Barang -->
                    <div class="form-group">
                        <label for="kode_barang" class="form-label">Kode Barang <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="kode_barang" 
                            id="kode_barang" 
                            class="form-control {{ $errors->has('kode_barang') ? 'is-invalid' : '' }}" 
                            value="{{ old('kode_barang', $barang->kode_barang) }}" 
                            required
                        >
                        @error('kode_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nama Barang -->
                    <div class="form-group">
                        <label for="nama_barang" class="form-label">Nama Barang <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="nama_barang" 
                            id="nama_barang" 
                            class="form-control {{ $errors->has('nama_barang') ? 'is-invalid' : '' }}" 
                            value="{{ old('nama_barang', $barang->nama_barang) }}" 
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
                            value="{{ old('kategori', $barang->kategori) }}" 
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
                            value="{{ old('satuan', $barang->satuan) }}" 
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
                    <!-- Current Stock (Readonly/Disabled) -->
                    <div class="form-group">
                        <label class="form-label">Stok Fisik Saat Ini (Terkunci)</label>
                        <div style="display: flex; gap: 8px;">
                            <input 
                                type="text" 
                                class="form-control" 
                                value="{{ $barang->stok }} {{ $barang->satuan }}" 
                                disabled 
                                style="background-color: #F1F5F9; font-weight: 700; color: var(--text-dark);"
                            >
                            <a href="{{ route('mutasi.create') }}" class="btn btn-outline-orange" style="white-space: nowrap;">
                                Ubah via Mutasi
                            </a>
                        </div>
                        <div class="form-hint">Sesuai aturan WMS, perubahan stok wajib melalui mutasi.</div>
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
                            value="{{ old('stok_minimum', $barang->stok_minimum) }}" 
                            required
                        >
                        @error('stok_minimum')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                            value="{{ old('lokasi_penyimpanan', $barang->lokasi_penyimpanan) }}" 
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
                                <option value="{{ $sup->id }}" {{ old('supplier_id', $barang->supplier_id) == $sup->id ? 'selected' : '' }}>
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
                    >{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border);">
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                        Perbarui Informasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
