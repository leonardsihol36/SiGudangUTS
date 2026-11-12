@extends('layouts.app')

@section('title', 'Edit Supplier: ' . $supplier->nama_supplier)
@section('page_title', 'Ubah Data Supplier')
@section('page_subtitle', 'Memperbarui informasi kemitraan pemasok ' . $supplier->kode_supplier)

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i data-lucide="edit" style="color: var(--primary-orange);"></i>
                Edit Data: {{ $supplier->nama_supplier }} ({{ $supplier->kode_supplier }})
            </div>
            <a href="{{ route('supplier.index') }}" class="btn btn-secondary btn-sm">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                Kembali ke Daftar
            </a>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('supplier.update', $supplier) }}">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Kode Supplier -->
                    <div class="form-group">
                        <label for="kode_supplier" class="form-label">Kode Supplier <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="kode_supplier" 
                            id="kode_supplier" 
                            class="form-control {{ $errors->has('kode_supplier') ? 'is-invalid' : '' }}" 
                            value="{{ old('kode_supplier', $supplier->kode_supplier) }}" 
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
                            value="{{ old('nama_supplier', $supplier->nama_supplier) }}" 
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
                            value="{{ old('nama_kontak', $supplier->nama_kontak) }}" 
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
                            value="{{ old('no_telepon', $supplier->no_telepon) }}" 
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
                            value="{{ old('email', $supplier->email) }}" 
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
                            <option value="Aktif" {{ old('status', $supplier->status) === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Nonaktif" {{ old('status', $supplier->status) === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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
                        required
                    >{{ old('alamat', $supplier->alamat) }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border);">
                    <a href="{{ route('supplier.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                        Perbarui Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
