<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiGudang – 036 | Akses Ditolak (403)</title>

    <!-- Custom Favicon & Brand Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}?v=2026">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}?v=2026">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2026">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}?v=2026">

    <link rel="stylesheet" href="{{ asset('css/sigudang.css') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body style="background: linear-gradient(135deg, #FFF3E0 0%, #F6F7F9 50%, #ECEFF1 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div style="max-width: 500px; width: 100%; background: #ffffff; border: 1px solid #E2E8F0; border-radius: 14px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); padding: 40px 32px; text-align: center;">
        <div style="width: 72px; height: 72px; background: #FFEBEE; color: #D32F2F; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
            <i data-lucide="shield-alert" style="width: 38px; height: 38px;"></i>
        </div>
        <h1 style="font-size: 2.2rem; font-weight: 800; color: #D32F2F; margin-bottom: 8px;">403</h1>
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #263238; margin-bottom: 12px;">Akses Ditolak</h2>
        <p style="color: #64748B; font-size: 0.95rem; margin-bottom: 28px; line-height: 1.6;">
            {{ $exception->getMessage() ?: 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.' }}
        </p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <a href="{{ route('dashboard') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i>
                Kembali ke Dashboard
            </a>
        </div>

        <div style="margin-top: 32px; padding-top: 20px; border-top: 1px solid #E2E8F0; font-size: 0.75rem; color: #94A3B8;">
            SiGudang – 036 &bull; Leonard Sihol Pardede (25K10036)
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
