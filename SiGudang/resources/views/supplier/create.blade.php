@extends('layouts.app')

@section('title', 'Tambah Supplier')
@section('page_title', 'Tambah Mitra Supplier Baru')
@section('page_subtitle', 'Mendaftarkan perusahaan pemasok resmi barang gudang')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i data-lucide="truck" style="color: var(--primary-orange);"></i>
                Formulir Pendaftaran Supplier
            </div>
            <a href="{{ route('supplier.index') }}" class="btn btn-secondary btn-sm">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                Kembali ke Daftar
            </a>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('supplier.store') }}">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Kode Supplier -->
                    <div class="form-group">
                        <label for="kode_supplier" class="form-label">Kode Supplier <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="kode_supplier" 
                            id="kode_supplier" 
                            class="form-control {{ $errors->has('kode_supplier') ? 'is-invalid' : '' }}" 
                            placeholder="Contoh: SUP-004"
                            value="{{ old('kode_supplier') }}" 
                            required
                        >
                        @error('kode_supplier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nama Supplier -->
                    <div class="form-group">
                        <label for="nama_supplier" class="form-label">Nama Perusahaan / Supplier <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="nama_supplier" 
                            id="nama_supplier" 
                            class="form-control {{ $errors->has('nama_supplier') ? 'is-invalid' : '' }}" 
                            placeholder="Contoh: PT Prima Logistik Solusindo"
                            value="{{ old('nama_supplier') }}" 
                            required
                        >
                        @error('nama_supplier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Kontak Person -->
                    <div class="form-group">
                        <label for="nama_kontak" class="form-label">Nama Kontak Person (PIC) <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="nama_kontak" 
                            id="nama_kontak" 
                            class="form-control {{ $errors->has('nama_kontak') ? 'is-invalid' : '' }}" 
                            placeholder="Contoh: Michael Chandra"
                            value="{{ old('nama_kontak') }}" 
                            required
                        >
                        @error('nama_kontak')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- No Telepon -->
                    <div class="form-group">
                        <label for="no_telepon" class="form-label">Nomor Telepon / WhatsApp <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="no_telepon" 
                            id="no_telepon" 
                            class="form-control {{ $errors->has('no_telepon') ? 'is-invalid' : '' }}" 
                            placeholder="Contoh: 08123456789"
                            value="{{ old('no_telepon') }}" 
                            required
                        >
                        @error('no_telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">Alamat Email Perusahaan <span class="required">*</span></label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                            placeholder="Contoh: contact@primalogistik.co.id"
                            value="{{ old('email') }}" 
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status" class="form-label">Status Kemitraan <span class="required">*</span></label>
                        <select name="status" id="status" class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" required>
                            <option value="Aktif" {{ old('status', 'Aktif') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Nonaktif" {{ old('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Alamat -->
                <div class="form-group">
                    <label for="alamat" class="form-label">Alamat Kantor / Pergudangan Pemasok <span class="required">*</span></label>
                    <textarea 
                        name="alamat" 
                        id="alamat" 
                        rows="3" 
                        class="form-control {{ $errors->has('alamat') ? 'is-invalid' : '' }}" 
                        placeholder="Alamat lengkap, nomor kawasan industri, kota..."
                        required
                    >{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border);">
                    <a href="{{ route('supplier.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                        Simpan Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
