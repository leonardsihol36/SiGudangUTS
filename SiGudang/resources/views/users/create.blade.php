@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')
@section('page_title', 'Pendaftaran Pengguna Baru')
@section('page_subtitle', 'Membuat kredensial akun dan menetapkan level otorisasi sistem')

@section('content')
<div style="max-width: 750px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i data-lucide="user-plus" style="color: var(--primary-orange);"></i>
                Formulir Akun Pengguna Baru
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                Kembali ke Daftar
            </a>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Username -->
                    <div class="form-group">
                        <label for="username" class="form-label">Username (NIM / ID Login) <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}" 
                            placeholder="Contoh: 25K10099 atau staf_andi"
                            value="{{ old('username') }}" 
                            required
                        >
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-hint">Digunakan saat proses login ke aplikasi.</div>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" 
                            placeholder="Contoh: Andi Pratama"
                            value="{{ old('name') }}" 
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">Alamat Email <span class="required">*</span></label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                            placeholder="Contoh: andi@sigudang.test"
                            value="{{ old('email') }}" 
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">Kata Sandi (Password) <span class="required">*</span></label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" 
                            placeholder="Minimal 6 karakter"
                            required
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Role -->
                    <div class="form-group">
                        <label for="role_id" class="form-label">Tingkat Hak Akses / Role <span class="required">*</span></label>
                        <select name="role_id" id="role_id" class="form-control {{ $errors->has('role_id') ? 'is-invalid' : '' }}" required>
                            <option value="">-- Pilih Level Role --</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->name }} — {{ $r->description }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status Keaktifan -->
                    <div class="form-group">
                        <label for="is_active" class="form-label">Status Keaktifan Akun <span class="required">*</span></label>
                        <select name="is_active" id="is_active" class="form-control {{ $errors->has('is_active') ? 'is-invalid' : '' }}" required>
                            <option value="1" {{ old('is_active', '1') === '1' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                            <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Nonaktif (Diblokir)</option>
                        </select>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border);">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
