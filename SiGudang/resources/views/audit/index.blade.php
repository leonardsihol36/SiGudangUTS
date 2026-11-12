@extends('layouts.app')

@section('title', 'Audit Log Aktivitas')
@section('page_title', 'Jejak Audit Aktivitas Gudang')
@section('page_subtitle', 'Catatan kronologis seluruh interaksi pengguna, modifikasi inventaris, dan peristiwa sistem')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i data-lucide="shield-check" style="color: var(--primary-orange);"></i>
            Log Aktivitas Sistem (Read-Only)
        </div>
        <span class="badge badge-orange">Keamanan & Kepatuhan UTS 2026</span>
    </div>

    <!-- Filter Toolbar -->
    <div style="padding: 16px 24px; background: #FAFAFA; border-bottom: 1px solid var(--border);">
        <form method="GET" action="{{ route('audit.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 220px;">
                <input 
                    type="text" 
                    name="q" 
                    class="form-control" 
                    placeholder="Cari deskripsi, pengguna, atau modul..." 
                    value="{{ request('q') }}"
                >
            </div>

            <div style="width: 160px;">
                <select name="module" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $m)
                        <option value="{{ $m }}" {{ request('module') === $m ? 'selected' : '' }}>
                            {{ $m }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="width: 160px;">
                <select name="action" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $a)
                        <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>
                            {{ $a }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="width: 145px;">
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}" title="Dari Tanggal">
            </div>

            <div style="width: 145px;">
                <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}" title="Sampai Tanggal">
            </div>

            <button type="submit" class="btn btn-secondary">
                <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                Filter
            </button>

            @if(request()->hasAny(['q', 'module', 'action', 'tanggal_mulai', 'tanggal_selesai']))
                <a href="{{ route('audit.index') }}" class="btn btn-secondary" title="Reset Filter">
                    <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu (WIB)</th>
                    <th>Pengguna</th>
                    <th>Tindakan / Aksi</th>
                    <th>Modul Sistem</th>
                    <th>Rincian Deskripsi Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap;">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-dark);">
                                {{ $log->user ? $log->user->name : 'Sistem Otomatis' }}
                            </div>
                            <div style="font-size: 0.72rem; color: var(--text-muted); font-family: monospace;">
                                {{ $log->user ? $log->user->username : 'system' }}
                            </div>
                        </td>
                        <td>
                            @php
                                $badgeClass = 'badge-gray';
                                if (str_contains($log->action, 'TAMBAH') || str_contains($log->action, 'CREATE')) $badgeClass = 'badge-green';
                                elseif (str_contains($log->action, 'HAPUS') || str_contains($log->action, 'DELETE')) $badgeClass = 'badge-red';
                                elseif (str_contains($log->action, 'UBAH') || str_contains($log->action, 'UPDATE')) $badgeClass = 'badge-yellow';
                                elseif (str_contains($log->action, 'LOGIN') || str_contains($log->action, 'MUTASI')) $badgeClass = 'badge-orange';
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-gray" style="font-size: 0.75rem;">
                                {{ $log->module }}
                            </span>
                        </td>
                        <td style="font-size: 0.86rem; color: var(--text-dark); line-height: 1.5;">
                            {{ $log->description }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i data-lucide="clipboard-check" class="empty-icon"></i>
                                <div class="empty-title">Log Aktivitas Bersih</div>
                                <div class="empty-desc">Belum ada peristiwa atau riwayat audit log yang tercatat pada filter ini.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="pagination-wrapper">
            <div style="font-size: 0.82rem; color: var(--text-muted);">
                Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} jejak audit
            </div>
            <div>
                {{ $logs->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
