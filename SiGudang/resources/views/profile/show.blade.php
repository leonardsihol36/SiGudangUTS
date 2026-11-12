@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Pengguna')
@section('page_subtitle', 'Kelola informasi identitas akun, ganti kata sandi, dan unggah foto profil')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px; max-width: 1000px; margin: 0 auto;">
    <!-- Left Column: Photo Upload & Summary Card -->
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i data-lucide="camera" style="color: var(--primary-orange);"></i>
                    Foto Profil
                </div>
            </div>
            <div class="card-body" style="text-align: center;">
                <div style="position: relative; display: inline-block; margin-bottom: 16px;">
                    <img 
                        src="{{ $user->profile_photo_url }}" 
                        alt="{{ $user->name }}" 
                        id="profilePreview"
                        style="width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-orange); box-shadow: var(--shadow-md);"
                    >
                </div>

                <div style="font-weight: 800; font-size: 1.1rem; color: var(--text-dark);">
                    {{ $user->name }}
                </div>
                <div style="margin-top: 4px;">
                    <span class="badge badge-orange">{{ $user->role ? $user->role->name : 'Pengguna' }}</span>
                </div>
                <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 6px; font-family: monospace;">
                    Username: {{ $user->username }}
                </div>

                <!-- Form Upload Foto -->
                <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data" style="margin-top: 24px; text-align: left; border-top: 1px solid var(--border-light); padding-top: 20px;">
                    @csrf
                    <div class="form-group">
                        <label for="profile_photo" class="form-label">Unggah Foto Baru</label>
                        <input 
                            type="file" 
                            name="profile_photo" 
                            id="profile_photo" 
                            class="form-control {{ $errors->has('profile_photo') ? 'is-invalid' : '' }}" 
                            accept="image/png, image/jpeg, image/jpg"
                            required
                            onchange="previewUploadedImage(this)"
                        >
                        @error('profile_photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-hint" style="font-size: 0.72rem; line-height: 1.4;">
                            Maksimal 2 MB. Format yang didukung: JPG, JPEG, PNG.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                        <i data-lucide="upload" style="width: 14px; height: 14px;"></i>
                        Simpan Foto Profil
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Profile Info & Password Management -->
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i data-lucide="user-check" style="color: var(--primary-orange);"></i>
                    Informasi Akun & Kata Sandi
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <!-- Username (Readonly) -->
                        <div class="form-group">
                            <label class="form-label">Username (Permanen)</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                value="{{ $user->username }}" 
                                disabled 
                                style="background-color: #F1F5F9; font-weight: 700;"
                            >
                        </div>

                        <!-- Role (Readonly) -->
                        <div class="form-group">
                            <label class="form-label">Level Otorisasi</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                value="{{ $user->role ? $user->role->name : '-' }}" 
                                disabled 
                                style="background-color: #F1F5F9; font-weight: 700; color: var(--primary-orange);"
                            >
                        </div>
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

                    <!-- Section Ganti Password -->
                    <div style="border-top: 1px solid var(--border-light); margin-top: 24px; padding-top: 20px;">
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                            Ubah Kata Sandi (Opsional)
                        </h4>
                        <p style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 16px;">
                            Kosongkan ketiga kolom berikut jika Anda tidak bermaksud mengganti kata sandi.
                        </p>

                        <div class="form-group">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input 
                                type="password" 
                                name="current_password" 
                                id="current_password" 
                                class="form-control {{ $errors->has('current_password') ? 'is-invalid' : '' }}" 
                                placeholder="Masukkan kata sandi lama Anda"
                            >
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label for="new_password" class="form-label">Password Baru</label>
                                <input 
                                    type="password" 
                                    name="new_password" 
                                    id="new_password" 
                                    class="form-control {{ $errors->has('new_password') ? 'is-invalid' : '' }}" 
                                    placeholder="Minimal 6 karakter"
                                >
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                <input 
                                    type="password" 
                                    name="new_password_confirmation" 
                                    id="new_password_confirmation" 
                                    class="form-control" 
                                    placeholder="Ulangi password baru"
                                >
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border);">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                            Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewUploadedImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
