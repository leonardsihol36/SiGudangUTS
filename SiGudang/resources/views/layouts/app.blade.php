<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SiGudang – 036 | @yield('title', 'Sistem Manajemen Inventaris')</title>
    
    <!-- Custom Favicon & Brand Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}?v=2026">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}?v=2026">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2026">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}?v=2026">

    <!-- Custom Design System CSS -->
    <link rel="stylesheet" href="{{ asset('css/sigudang.css') }}">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Navigation Drawer -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-header">
                <img src="{{ asset('img/logo.svg') }}" alt="SiGudang – 036" class="brand-logo-img">
                <div>
                    <div class="brand-title">SiGudang – 036</div>
                    <span class="brand-badge">WMS &bull; UTS 2026</span>
                </div>
            </div>

            <!-- Active User Card -->
            <div class="sidebar-user-card">
                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="sidebar-user-avatar">
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</div>
                    <div class="sidebar-user-role">
                        <i data-lucide="shield-check" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle;"></i>
                        {{ Auth::user()->role ? Auth::user()->role->name : 'Pengguna' }}
                    </div>
                </div>
            </div>

            <!-- Sidebar Navigation Menu -->
            <div class="sidebar-content">
                <div class="nav-section-title">Menu Utama</div>

                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard" class="nav-icon"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('barang.index') }}" class="nav-item {{ request()->routeIs('barang.*') ? 'active' : '' }}">
                    <i data-lucide="package" class="nav-icon"></i>
                    <span>Data Barang</span>
                </a>

                <a href="{{ route('supplier.index') }}" class="nav-item {{ request()->routeIs('supplier.*') ? 'active' : '' }}">
                    <i data-lucide="truck" class="nav-icon"></i>
                    <span>Supplier</span>
                </a>

                <a href="{{ route('mutasi.index') }}" class="nav-item {{ request()->routeIs('mutasi.*') ? 'active' : '' }}">
                    <i data-lucide="arrow-left-right" class="nav-icon"></i>
                    <span>Mutasi Stok</span>
                </a>

                <a href="{{ route('laporan.index') }}" class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <i data-lucide="file-bar-chart" class="nav-icon"></i>
                    <span>Laporan Inventaris</span>
                </a>

                @if(Auth::user()->isManajer() || Auth::user()->isAuditor())
                    <div class="nav-section-title">Pemeriksaan & Audit</div>
                    <a href="{{ route('audit.index') }}" class="nav-item {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                        <i data-lucide="clipboard-list" class="nav-icon"></i>
                        <span>Audit Log Aktivitas</span>
                    </a>
                @endif

                @if(Auth::user()->isManajer())
                    <div class="nav-section-title">Pengaturan Sistem</div>
                    <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i data-lucide="users" class="nav-icon"></i>
                        <span>Kelola Pengguna</span>
                    </a>
                @endif

                <div class="nav-section-title">Akun & Pengaturan</div>
                <a href="{{ route('profile.show') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i data-lucide="user-cog" class="nav-icon"></i>
                    <span>Profil Saya</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" id="logoutForm" style="margin-top: 12px;">
                    @csrf
                    <button type="submit" class="nav-item" style="width: 100%; border: none; background: none; cursor: pointer; text-align: left; color: var(--danger);">
                        <i data-lucide="log-out" class="nav-icon" style="color: var(--danger);"></i>
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Overlay for mobile drawer -->
        <div class="sidebar-overlay" id="sidebarOverlay" style="display: none;" onclick="toggleMobileSidebar()"></div>

        <!-- Main Wrapper -->
        <div class="app-main">
            <!-- Top Navbar -->
            <header class="app-navbar">
                <div class="navbar-left">
                    <button class="sidebar-toggle-btn" type="button" onclick="toggleMobileSidebar()">
                        <i data-lucide="menu" style="width: 22px; height: 22px;"></i>
                    </button>
                    <div>
                        <h1 class="navbar-title">@yield('page_title', 'SiGudang – 036')</h1>
                        <p class="navbar-subtitle">@yield('page_subtitle', 'Sistem Manajemen Inventaris Barang Gudang')</p>
                    </div>
                </div>

                <div class="navbar-right">
                    <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end;">
                        <span style="font-size: 0.82rem; font-weight: 700; color: var(--text-dark);">
                            {{ Auth::user()->name }}
                        </span>
                        <span class="badge badge-orange" style="margin-top: 2px;">
                            {{ Auth::user()->role ? Auth::user()->role->name : 'User' }}
                        </span>
                    </div>

                    <a href="{{ route('profile.show') }}">
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-orange);">
                    </a>
                </div>
            </header>

            <!-- Page Body -->
            <main class="page-container">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px; flex-shrink: 0; color: var(--success);"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i data-lucide="alert-octagon" style="width: 20px; height: 20px; flex-shrink: 0; color: var(--danger);"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i data-lucide="alert-triangle" style="width: 20px; height: 20px; flex-shrink: 0; color: var(--danger);"></i>
                        <div>
                            <strong>Terjadi kesalahan input:</strong>
                            <ul style="margin-left: 20px; margin-top: 4px;">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Mandatory Footer with Author Identity & Photo -->
            <footer class="app-footer">
                <div class="footer-creator">
                    <!-- Photo of Creator Leonard Sihol Pardede -->
                    <img src="{{ asset('uploads/profil/leonard-25k10036.png') }}" alt="Leonard Sihol Pardede" class="footer-avatar">
                    <div>
                        <div class="footer-info-title">© 2026 Leonard Sihol Pardede – 25.K1.0036</div>
                        <div class="footer-info-subtitle">Ujian Tengah Semester (UTS) &bull; Sistem Manajemen Inventaris SiGudang – 036</div>
                    </div>
                </div>
                <div>
                    <span class="badge badge-gray">Prefix: t036_</span>
                    <span class="badge badge-orange" style="margin-left: 6px;">Database: uts_25K10036</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Generic Confirmation Modal for Deletion / Actions -->
    <div class="modal-backdrop" id="confirmModal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="card-title" id="modalTitle" style="color: var(--danger);">
                    <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
                    Konfirmasi Tindakan
                </h3>
                <button type="button" onclick="closeConfirmModal()" style="background: none; border: none; cursor: pointer; color: var(--text-muted);">
                    <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                </button>
            </div>
            <div class="modal-body">
                <p id="modalMessage" style="color: var(--text-dark); line-height: 1.5; font-size: 0.92rem;">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeConfirmModal()">Batal</button>
                <form id="modalActionForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="_method" id="modalFormMethod" value="POST">
                    <button type="submit" class="btn btn-danger" id="modalConfirmBtn">Ya, Lanjutkan</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Mobile Sidebar Toggle
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
                overlay.style.display = 'none';
            } else {
                sidebar.classList.add('open');
                overlay.style.display = 'block';
            }
        }

        // Generic Confirmation Modal
        function showConfirmModal(url, method, title, message, btnText = 'Ya, Hapus') {
            document.getElementById('modalActionForm').action = url;
            document.getElementById('modalFormMethod').value = method;
            document.getElementById('modalTitle').innerHTML = '<i data-lucide="alert-circle" style="width: 20px; height: 20px; display: inline-block; vertical-align: middle;"></i> ' + title;
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('modalConfirmBtn').innerText = btnText;
            document.getElementById('confirmModal').style.display = 'flex';
            lucide.createIcons();
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }
    </script>

    @stack('scripts')
</body>
</html>
