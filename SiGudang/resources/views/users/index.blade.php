@extends('layouts.app')

@section('title', 'Kelola Pengguna')
@section('page_title', 'Manajemen Akun Pengguna')
@section('page_subtitle', 'Pengaturan hak akses, penugasan level/role, dan status keaktifan akun sistem')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i data-lucide="users" style="color: var(--primary-orange);"></i>
            Daftar Pengguna Sistem
        </div>

        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i>
            Tambah Pengguna Baru
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div style="padding: 16px 24px; background: #FAFAFA; border-bottom: 1px solid var(--border);">
        <form method="GET" action="{{ route('users.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px;">
                <input 
                    type="text" 
                    name="q" 
                    class="form-control" 
                    placeholder="Cari username, nama lengkap, atau email..." 
                    value="{{ request('q') }}"
                >
            </div>

            <div style="width: 180px;">
                <select name="role_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Role</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="width: 160px;">
                <select name="is_active" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <button type="submit" class="btn btn-secondary">
                <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                Filter
            </button>

            @if(request()->hasAny(['q', 'role_id', 'is_active']))
                <a href="{{ route('users.index') }}" class="btn btn-secondary" title="Reset Filter">
                    <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Level / Role</th>
                    <th>Status Akun</th>
                    <th>Terdaftar Sejak</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="{{ $u->profile_photo_url }}" alt="{{ $u->name }}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border);">
                                <div>
                                    <div style="font-weight: 700; color: var(--text-dark);">
                                        {{ $u->name }}
                                        @if($u->id === Auth::id())
                                            <span class="badge badge-orange" style="font-size: 0.65rem; margin-left: 4px;">Anda</span>
                                        @endif
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">ID: #{{ $u->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-gray" style="font-family: monospace; font-size: 0.82rem;">
                                {{ $u->username }}
                            </span>
                        </td>
                        <td style="font-size: 0.85rem; color: var(--text-dark);">
                            {{ $u->email }}
                        </td>
                        <td>
                            @if($u->role && $u->role->name === 'Manajer')
                                <span class="badge badge-orange">
                                    <i data-lucide="shield" style="width: 12px; height: 12px;"></i>
                                    Manajer
                                </span>
                            @elseif($u->role && $u->role->name === 'Staf Gudang')
                                <span class="badge badge-yellow">
                                    <i data-lucide="user" style="width: 12px; height: 12px;"></i>
                                    Staf Gudang
                                </span>
                            @else
                                <span class="badge badge-gray">
                                    <i data-lucide="check-square" style="width: 12px; height: 12px;"></i>
                                    Auditor
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($u->is_active)
                                <span class="badge badge-green">Aktif</span>
                            @else
                                <span class="badge badge-red">Nonaktif</span>
                            @endif
                        </td>
                        <td style="font-size: 0.8rem; color: var(--text-muted);">
                            {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('users.edit', $u) }}" class="btn btn-secondary btn-icon" title="Edit Pengguna">
                                    <i data-lucide="pencil" style="width: 15px; height: 15px; color: var(--primary-orange);"></i>
                                </a>

                                <!-- Toggle Active Status Form -->
                                <form method="POST" action="{{ route('users.toggle', $u) }}" style="display: inline;">
                                    @csrf
                                    <button 
                                        type="submit" 
                                        class="btn btn-secondary btn-icon" 
                                        title="{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                        onclick="return confirm('Apakah Anda yakin ingin {{ $u->is_active ? 'menonaktifkan' : 'mengaktifkan' }} akun {{ $u->name }}?')"
                                    >
                                        @if($u->is_active)
                                            <i data-lucide="user-x" style="width: 15px; height: 15px; color: var(--danger);"></i>
                                        @else
                                            <i data-lucide="user-check" style="width: 15px; height: 15px; color: var(--success);"></i>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i data-lucide="users" class="empty-icon"></i>
                                <div class="empty-title">Pengguna Tidak Ditemukan</div>
                                <div class="empty-desc">Tidak ada akun pengguna yang sesuai dengan parameter filter pencarian.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="pagination-wrapper">
            <div style="font-size: 0.82rem; color: var(--text-muted);">
                Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} pengguna
            </div>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
