@extends('layouts.app')

@section('title', 'Dashboard ' . (Auth::user()->role ? Auth::user()->role->name : ''))
@section('page_title', 'Dashboard ' . (Auth::user()->role ? Auth::user()->role->name : ''))
@section('page_subtitle', 'Ringkasan operasional dan status inventaris terkini SiGudang – 036')

@section('content')
<div style="margin-bottom: 24px;">
    <!-- Welcome Banner with Primary Orange Accent -->
    <div style="background: linear-gradient(135deg, #FB8C00, #E65100); border-radius: var(--radius-lg); padding: 24px 28px; color: white; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; box-shadow: var(--shadow-orange);">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                <span class="badge" style="background: rgba(255,255,255,0.25); color: white; border: none; font-size: 0.75rem;">
                    {{ Auth::user()->role ? Auth::user()->role->name : 'Pengguna' }}
                </span>
                <span style="font-size: 0.85rem; opacity: 0.9;">Tahun Operasional: 2026</span>
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 800; line-height: 1.2;">
                Selamat Datang, {{ Auth::user()->name }}!
            </h2>
            <p style="font-size: 0.88rem; opacity: 0.9; margin-top: 4px; max-width: 600px;">
                @if(Auth::user()->isManajer())
                    Anda memiliki kendali penuh atas manajemen pengguna, barang, pemasok, transaksi mutasi, laporan, serta audit sistem.
                @elseif(Auth::user()->isStafGudang())
                    Kelola pencatatan barang masuk & keluar, perbarui status fisik barang, dan pastikan ketersediaan stok inventaris harian.
                @else
                    Akses peninjauan inventaris dan pengawasan audit log aktivitas gudang secara independen dan transparan.
                @endif
            </p>
        </div>

        @if(!Auth::user()->isAuditor())
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('mutasi.create') }}" class="btn" style="background: white; color: var(--orange-dark); font-weight: 700;">
                    <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i>
                    Catat Mutasi Stok
                </a>
                @if(Auth::user()->isManajer())
                    <a href="{{ route('barang.create') }}" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4);">
                        <i data-lucide="package-plus" style="width: 18px; height: 18px;"></i>
                        Tambah Barang
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- ==========================================
     METRIC STAT CARDS
     ========================================== -->
<div class="stats-grid">
    <!-- Stat 1: Total Barang -->
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Jenis Barang</div>
            <div class="stat-value">{{ number_format($totalBarang) }}</div>
            <div class="stat-desc">Katalog terdaftar dalam sistem</div>
        </div>
        <div class="stat-icon-wrapper icon-orange">
            <i data-lucide="package" style="width: 26px; height: 26px;"></i>
        </div>
    </div>

    <!-- Stat 2: Total Stok Fisik -->
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Unit Fisik</div>
            <div class="stat-value">{{ number_format($totalStok) }}</div>
            <div class="stat-desc">Akumulasi kuantitas seluruh barang</div>
        </div>
        <div class="stat-icon-wrapper icon-blue">
            <i data-lucide="layers" style="width: 26px; height: 26px;"></i>
        </div>
    </div>

    <!-- Stat 3: Stok Menipis (Warning) -->
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Stok Minimum / Menipis</div>
            <div class="stat-value" style="color: {{ $totalStokMenipis > 0 ? 'var(--danger)' : 'var(--text-dark)' }};">
                {{ number_format($totalStokMenipis) }}
            </div>
            <div class="stat-desc">Kuantitas di bawah batas aman</div>
        </div>
        <div class="stat-icon-wrapper {{ $totalStokMenipis > 0 ? 'icon-red' : 'icon-yellow' }}">
            <i data-lucide="alert-triangle" style="width: 26px; height: 26px;"></i>
        </div>
    </div>

    @if(Auth::user()->isManajer())
        <!-- Stat 4 for Manajer: Total Supplier -->
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">Mitra Supplier</div>
                <div class="stat-value">{{ number_format($totalSupplier) }}</div>
                <div class="stat-desc">Pemasok resmi aktif</div>
            </div>
            <div class="stat-icon-wrapper icon-green">
                <i data-lucide="truck" style="width: 26px; height: 26px;"></i>
            </div>
        </div>

        <!-- Stat 5 for Manajer: Pengguna Aktif -->
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">Pengguna Aktif</div>
                <div class="stat-value">{{ number_format($totalUserAktif) }}</div>
                <div class="stat-desc">Akun dengan izin akses login</div>
            </div>
            <div class="stat-icon-wrapper icon-orange">
                <i data-lucide="users" style="width: 26px; height: 26px;"></i>
            </div>
        </div>
    @elseif(Auth::user()->isStafGudang())
        <!-- Stat 4 for Staf: Transaksi Masuk Hari Ini / Terakhir -->
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">Riwayat Masuk</div>
                <div class="stat-value">{{ number_format($recentMasuk->count()) }}</div>
                <div class="stat-desc">Transaksi penerimaan terbaru</div>
            </div>
            <div class="stat-icon-wrapper icon-green">
                <i data-lucide="arrow-down-left" style="width: 26px; height: 26px;"></i>
            </div>
        </div>

        <!-- Stat 5 for Staf: Transaksi Keluar Hari Ini / Terakhir -->
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">Riwayat Keluar</div>
                <div class="stat-value">{{ number_format($recentKeluar->count()) }}</div>
                <div class="stat-desc">Transaksi pengeluaran terbaru</div>
            </div>
            <div class="stat-icon-wrapper icon-red">
                <i data-lucide="arrow-up-right" style="width: 26px; height: 26px;"></i>
            </div>
        </div>
    @else
        <!-- Stat for Auditor: Audit Logs count -->
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">Aktivitas Diaudit</div>
                <div class="stat-value">{{ number_format($recentAuditLogs->count()) }}</div>
                <div class="stat-desc">Jejak log aktivitas terbaru</div>
            </div>
            <div class="stat-icon-wrapper icon-orange">
                <i data-lucide="shield" style="width: 26px; height: 26px;"></i>
            </div>
        </div>
    @endif
</div>

<!-- ==========================================
     CHARTS & LOW STOCK SECTION
     ========================================== -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
    <!-- Chart: Mutasi Masuk vs Keluar -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <div class="card-title">
                <i data-lucide="trending-up" style="color: var(--primary-orange);"></i>
                Tren Aktivitas Mutasi Stok Barang (Masuk vs Keluar)
            </div>
            <span class="badge badge-orange">Tahun 2026</span>
        </div>
        <div class="card-body">
            <div style="height: 280px; position: relative;">
                <canvas id="mutasiChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Alert List: Barang Stok Menipis -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <div class="card-title" style="color: var(--danger);">
                <i data-lucide="alert-circle" style="color: var(--danger);"></i>
                Peringatan Stok Rendah
            </div>
            <span class="badge badge-red">{{ $barangStokMenipis->count() }} Item</span>
        </div>
        <div class="card-body" style="padding: 12px 16px; max-height: 310px; overflow-y: auto;">
            @forelse($barangStokMenipis as $item)
                <div style="padding: 12px; border-bottom: 1px solid var(--border-light); display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-dark);">
                            {{ $item->nama_barang }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                            {{ $item->kode_barang }} &bull; {{ $item->lokasi_penyimpanan }}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge {{ $item->stok <= 0 ? 'badge-red' : 'badge-yellow' }}">
                            Stok: {{ $item->stok }} / Min: {{ $item->stok_minimum }}
                        </span>
                        <div style="font-size: 0.7rem; color: var(--text-light); margin-top: 2px;">{{ $item->satuan }}</div>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="padding: 24px 12px;">
                    <i data-lucide="check-check" style="width: 36px; height: 36px; color: var(--success); margin-bottom: 8px;"></i>
                    <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-dark);">Seluruh Stok Aman</div>
                    <div style="font-size: 0.78rem; color: var(--text-muted);">Tidak ada barang yang berada di bawah stok minimum.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- ==========================================
     TABLE SECTION BASED ON ROLE
     ========================================== -->
@if(Auth::user()->isManajer())
    <!-- Manajer: Mutasi Stok Terbaru -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i data-lucide="history" style="color: var(--primary-orange);"></i>
                Transaksi Mutasi Stok Terbaru (Live Audit)
            </div>
            <a href="{{ route('mutasi.index') }}" class="btn btn-outline-orange btn-sm">
                Lihat Seluruh Mutasi
            </a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Waktu Transaksi</th>
                        <th>Kode & Nama Barang</th>
                        <th>Tipe Mutasi</th>
                        <th>Jumlah</th>
                        <th>Perubahan Stok</th>
                        <th>Petugas</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentMutasi as $m)
                        <tr>
                            <td style="font-size: 0.82rem; color: var(--text-muted);">
                                {{ $m->tanggal_mutasi->format('d M Y, H:i') }}
                            </td>
                            <td>
                                <div style="font-weight: 700;">{{ $m->barang->nama_barang }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $m->barang->kode_barang }}</div>
                            </td>
                            <td>
                                @if($m->tipe_mutasi === 'masuk')
                                    <span class="badge badge-green">
                                        <i data-lucide="arrow-down-left" style="width: 12px; height: 12px;"></i>
                                        Barang Masuk
                                    </span>
                                @else
                                    <span class="badge badge-red">
                                        <i data-lucide="arrow-up-right" style="width: 12px; height: 12px;"></i>
                                        Barang Keluar
                                    </span>
                                @endif
                            </td>
                            <td style="font-weight: 700; font-size: 0.95rem;">
                                {{ $m->jumlah }} {{ $m->barang->satuan }}
                            </td>
                            <td>
                                <span style="color: var(--text-muted); font-size: 0.82rem;">{{ $m->stok_sebelum }}</span>
                                <i data-lucide="arrow-right" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle; margin: 0 4px; color: var(--primary-orange);"></i>
                                <span style="font-weight: 800; color: {{ $m->tipe_mutasi === 'masuk' ? 'var(--success)' : 'var(--danger)' }};">
                                    {{ $m->stok_sesudah }}
                                </span>
                            </td>
                            <td style="font-size: 0.85rem;">
                                {{ $m->user ? $m->user->name : 'Sistem' }}
                            </td>
                            <td style="font-size: 0.82rem; color: var(--text-muted); max-width: 250px;">
                                {{ $m->keterangan }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding: 32px; text-align: center; color: var(--text-muted);">
                                Belum ada riwayat mutasi stok tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif(Auth::user()->isStafGudang())
    <!-- Staf Gudang: Transaksi Masuk & Keluar Terbaru -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Masuk Terbaru -->
        <div class="card">
            <div class="card-header">
                <div class="card-title" style="color: var(--success);">
                    <i data-lucide="arrow-down-left" style="color: var(--success);"></i>
                    Barang Masuk Terbaru
                </div>
                <a href="{{ route('mutasi.create') }}" class="btn btn-sm btn-success">+ Masuk</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentMasuk as $m)
                            <tr>
                                <td>
                                    <div style="font-weight: 700;">{{ $m->barang->nama_barang }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $m->barang->kode_barang }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-green">+{{ $m->jumlah }} {{ $m->barang->satuan }}</span>
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-muted);">
                                    {{ $m->tanggal_mutasi->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                    Belum ada transaksi barang masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Keluar Terbaru -->
        <div class="card">
            <div class="card-header">
                <div class="card-title" style="color: var(--danger);">
                    <i data-lucide="arrow-up-right" style="color: var(--danger);"></i>
                    Barang Keluar Terbaru
                </div>
                <a href="{{ route('mutasi.create') }}" class="btn btn-sm btn-danger">+ Keluar</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentKeluar as $m)
                            <tr>
                                <td>
                                    <div style="font-weight: 700;">{{ $m->barang->nama_barang }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $m->barang->kode_barang }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-red">-{{ $m->jumlah }} {{ $m->barang->satuan }}</span>
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-muted);">
                                    {{ $m->tanggal_mutasi->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                    Belum ada transaksi barang keluar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@else
    <!-- Auditor: Audit Log Terbaru & Histori Mutasi -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i data-lucide="shield-alert" style="color: var(--primary-orange);"></i>
                Jejak Audit Aktivitas Sistem Terkini (Read-Only)
            </div>
            <a href="{{ route('audit.index') }}" class="btn btn-outline-orange btn-sm">
                Lihat Seluruh Log
            </a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Waktu Log</th>
                        <th>Pengguna</th>
                        <th>Aksi</th>
                        <th>Modul</th>
                        <th>Deskripsi Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAuditLogs as $log)
                        <tr>
                            <td style="font-size: 0.8rem; color: var(--text-muted);">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td style="font-weight: 600;">
                                {{ $log->user ? $log->user->name : 'Sistem' }}
                            </td>
                            <td>
                                <span class="badge badge-orange">{{ $log->action }}</span>
                            </td>
                            <td>
                                <span class="badge badge-gray">{{ $log->module }}</span>
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-dark);">
                                {{ $log->description }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 28px; color: var(--text-muted);">
                                Belum ada aktivitas yang dicatat dalam log audit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    // Initialize Chart.js for Stock Movement Trends
    const ctx = document.getElementById('mutasiChart').getContext('2d');
    const monthlyLabels = {!! json_encode($monthlyData['labels']) !!};
    const dataMasuk = {!! json_encode($monthlyData['masuk']) !!};
    const dataKeluar = {!! json_encode($monthlyData['keluar']) !!};

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [
                {
                    label: 'Barang Masuk',
                    data: dataMasuk,
                    backgroundColor: '#2E7D32',
                    borderRadius: 6,
                },
                {
                    label: 'Barang Keluar',
                    data: dataKeluar,
                    backgroundColor: '#D32F2F',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600' }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
</script>
@endpush
