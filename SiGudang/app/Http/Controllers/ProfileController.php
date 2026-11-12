<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:t036_users,email,' . $user->id,
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (!empty($validated['new_password'])) {
            if (empty($validated['current_password'])) {
                return back()->withErrors(['current_password' => 'Masukkan password saat ini untuk mengganti password.']);
            }

            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah.']);
            }

            $user->password = Hash::make($validated['new_password']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        AuditLog::record(
            $user->id,
            'UBAH',
            'PROFIL',
            "Pengguna {$user->name} memperbarui informasi biodata profilnya."
        );

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'profile_photo.required' => 'Pilih file foto profil terlebih dahulu.',
            'profile_photo.image' => 'File yang diunggah harus berupa gambar.',
            'profile_photo.mimes' => 'Format file yang diperbolehkan hanya JPG, JPEG, dan PNG.',
            'profile_photo.max' => 'Ukuran file foto maksimal adalah 2 MB (2048 KB).',
        ]);

        $user = Auth::user();

        $file = $request->file('profile_photo');
        $extension = $file->getClientOriginalExtension();
        $filename = 'profil_' . $user->id . '_' . time() . '_' . Str::random(8) . '.' . $extension;

        $targetDir = public_path('uploads/profil');
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $file->move($targetDir, $filename);
        $relativePath = 'uploads/profil/' . $filename;

        // Delete old custom photo if exists and not default
        if ($user->profile_photo && file_exists(public_path($user->profile_photo)) && !str_contains($user->profile_photo, 'leonard-25k10036.png')) {
            @unlink(public_path($user->profile_photo));
        }

        $user->profile_photo = $relativePath;
        $user->save();

        AuditLog::record(
            $user->id,
            'UBAH',
            'PROFIL',
            "Pengguna {$user->name} berhasil mengunggah dan mengganti foto profil."
        );

        return back()->with('success', 'Foto profil berhasil diunggah dan diperbarui.');
    }
}
