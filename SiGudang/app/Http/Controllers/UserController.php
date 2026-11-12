<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('username', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:t036_users,username|regex:/^[a-zA-Z0-9_.-]+$/',
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:t036_users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:t036_roles,id',
            'is_active' => 'required|boolean',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah terdaftar.',
            'username.regex' => 'Format username hanya boleh huruf, angka, titik, atau strip.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'role_id.required' => 'Pilih level/role pengguna.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $newUser = User::create($validated);

        $currentUser = Auth::user();
        AuditLog::record(
            $currentUser->id,
            'TAMBAH',
            'PENGGUNA',
            "Manajer {$currentUser->name} membuat pengguna baru: {$newUser->name} ({$newUser->username}) dengan role {$newUser->role->name}."
        );

        return redirect()->route('users.index')->with('success', "Akun pengguna '{$newUser->name}' berhasil dibuat.");
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:t036_users,email,' . $user->id,
            'role_id' => 'required|exists:t036_roles,id',
            'password' => 'nullable|string|min:6',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'role_id.required' => 'Pilih level/role pengguna.',
            'password.min' => 'Password baru minimal 6 karakter.',
        ]);

        // Guard: Prevent changing sole active Manajer role away from Manajer
        $manajerRole = Role::where('name', 'Manajer')->first();
        if ($user->role_id === $manajerRole->id && (int) $validated['role_id'] !== $manajerRole->id) {
            $activeManajers = User::where('role_id', $manajerRole->id)->where('is_active', true)->count();
            if ($activeManajers <= 1) {
                return back()->withInput()->with('error', 'Tidak dapat mengubah role pengguna ini. Sistem mewajibkan minimal satu akun Manajer aktif.');
            }
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role_id = $validated['role_id'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $currentUser = Auth::user();
        AuditLog::record(
            $currentUser->id,
            'UBAH',
            'PENGGUNA',
            "Manajer {$currentUser->name} memperbarui profil pengguna: {$user->name} ({$user->username})."
        );

        return redirect()->route('users.index')->with('success', "Data pengguna '{$user->name}' berhasil diperbarui.");
    }

    public function toggleStatus(User $user)
    {
        $currentUser = Auth::user();

        // Safety Guard: Cannot deactivate sole active Manajer
        $manajerRole = Role::where('name', 'Manajer')->first();
        if ($user->role_id === $manajerRole->id && $user->is_active) {
            $activeManajers = User::where('role_id', $manajerRole->id)->where('is_active', true)->count();
            if ($activeManajers <= 1) {
                return back()->with('error', 'Tidak dapat menonaktifkan akun Manajer ini. Harus selalu terdapat minimal satu Manajer aktif dalam sistem SiGudang – 036.');
            }
        }

        $newStatus = !$user->is_active;
        $user->is_active = $newStatus;
        $user->save();

        $statusLabel = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        AuditLog::record(
            $currentUser->id,
            'UBAH',
            'PENGGUNA',
            "Manajer {$currentUser->name} mengubah status pengguna {$user->name} ({$user->username}) menjadi {$statusLabel}."
        );

        return back()->with('success', "Status akun '{$user->name}' berhasil diubah menjadi: " . ($newStatus ? 'Aktif' : 'Nonaktif') . ".");
    }

    public function destroy(User $user)
    {
        // Delete implemented as deactivation (soft behavior)
        return $this->toggleStatus($user);
    }
}
