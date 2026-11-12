@extends('layouts.app')

@section('title', 'Edit Pengguna: ' . $user->name)
@section('page_title', 'Ubah Data Akun Pengguna')
@section('page_subtitle', 'Memperbarui informasi profil atau level otorisasi pengguna ' . $user->username)

@section('content')
<div style="max-width: 750px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i data-lucide="user-cog" style="color: var(--primary-orange);"></i>
                Edit Pengguna: {{ $user->name }}
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                Kembali ke Daftar
            </a>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Username (Terkunci) -->
                    <div class="form-group">
                        <label class="form-label">Username (ID Permanen)</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            value="{{ $user->username }}" 
                            disabled 
                            style="background-color: #F1F5F9; font-weight: 700; color: var(--text-dark);"
                        >
                        <div class="form-hint">Username unik tidak dapat diubah setelah pembuatan.</div>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" 
                            value="{{ old('name', $user->name) }}" 
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
                            value="{{ old('email', $user->email) }}" 
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Level / Role -->
                    <div class="form-group">
                        <label for="role_id" class="form-label">Tingkat Hak Akses / Role <span class="required">*</span></label>
                        <select name="role_id" id="role_id" class="form-control {{ $errors->has('role_id') ? 'is-invalid' : '' }}" required>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}" {{ old('role_id', $user->role_id) == $r->id ? 'selected' : '' }}>
                                    {{ $r->name }} — {{ $r->description }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Reset Password (Opsional) -->
                <div class="form-group" style="border-top: 1px solid var(--border-light); padding-top: 20px; margin-top: 10px;">
                    <label for="password" class="form-label">Reset Password Baru (Opsional)</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" 
                        placeholder="Kosongkan jika tidak ingin mengubah password akun ini"
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-hint">Isi hanya bila pengguna meminta pengaturan ulang kata sandi.</div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border);">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
