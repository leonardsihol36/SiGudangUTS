<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiGudang – 036 | Masuk Sistem</title>

    <!-- Custom Favicon & Brand Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}?v=2026">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}?v=2026">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2026">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}?v=2026">

    <link rel="stylesheet" href="{{ asset('css/sigudang.css') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <!-- Header Identity -->
            <div class="auth-header">
                <img src="{{ asset('img/logo.svg') }}" alt="SiGudang – 036" style="width: 72px; height: 72px; margin: 0 auto 14px; display: block; filter: drop-shadow(0 6px 14px rgba(230, 81, 0, 0.35));">
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-dark); letter-spacing: -0.02em;">
                    SiGudang – 036
                </h1>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">
                    Sistem Manajemen Inventaris Barang Gudang
                </p>
                <div style="margin-top: 8px;">
                    <span class="badge badge-orange">UTS 2026 &bull; WMS Enterprise</span>
                </div>
            </div>

            <!-- Login Form -->
            <div class="auth-body">
                @if(session('error'))
                    <div class="alert alert-danger" style="margin-bottom: 20px;">
                        <i data-lucide="alert-octagon" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success" style="margin-bottom: 20px;">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="form-group">
                        <label for="username" class="form-label">Username <span class="required">*</span></label>
                        <div style="position: relative;">
                            <input 
                                type="text" 
                                name="username" 
                                id="username" 
                                class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}" 
                                placeholder="Masukkan NIM / username"
                                value="{{ old('username') }}" 
                                required 
                                autofocus
                            >
                        </div>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password <span class="required">*</span></label>
                        <div style="position: relative;">
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" 
                                placeholder="Masukkan kata sandi" 
                                required
                                style="padding-right: 42px;"
                            >
                            <button 
                                type="button" 
                                onclick="togglePasswordVisibility()" 
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted); display: flex; align-items: center;"
                                title="Lihat/Sembunyikan password"
                            >
                                <i data-lucide="eye" id="eyeIcon" style="width: 18px; height: 18px;"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-muted); cursor: pointer;">
                            <input type="checkbox" name="remember" style="accent-color: var(--primary-orange);">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 0.95rem;">
                        <i data-lucide="log-in" style="width: 18px; height: 18px;"></i>
                        Masuk ke SiGudang
                    </button>
                </form>

                <!-- Quick Role Switcher for Demo / UTS Evaluation -->
                <div style="margin-top: 28px; padding-top: 20px; border-top: 1px dashed var(--border);">
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 10px; text-align: center;">
                        Akses Cepat Pengujian UTS (Klik untuk Isi Akun)
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="fillCredentials('25K10036', 'password123')" title="Manajer Utama">
                            <span style="color: var(--primary-orange); font-weight: 700;">Manajer</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="fillCredentials('staf036', 'password123')" title="Staf Gudang">
                            <span>Staf</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="fillCredentials('auditor036', 'password123')" title="Auditor">
                            <span>Auditor</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Author Footer on Login Card -->
            <div style="background-color: #FAFAFA; border-top: 1px solid var(--border); padding: 16px 24px; display: flex; align-items: center; justify-content: center; gap: 12px;">
                <img src="{{ asset('uploads/profil/leonard-25k10036.png') }}" alt="Leonard Sihol Pardede" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-orange);">
                <div style="text-align: left;">
                    <div style="font-size: 0.78rem; font-weight: 700; color: var(--text-dark);">
                        © 2026 Leonard Sihol Pardede – 25.K1.0036
                    </div>
                    <div style="font-size: 0.7rem; color: var(--primary-orange); font-weight: 600;">
                        Pembuat Aplikasi SiGudang – 036
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        function fillCredentials(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
