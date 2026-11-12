<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withInput()->withErrors([
                'username' => 'Username atau password yang Anda masukkan salah.',
            ]);
        }

        if (!$user->is_active) {
            return back()->withInput()->with('error', 'Akun Anda telah dinonaktifkan oleh Manajer. Silakan hubungi administrator.');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Audit Log
        AuditLog::record(
            $user->id,
            'LOGIN',
            'AUTENTIKASI',
            "Pengguna {$user->name} ({$user->username}) berhasil login ke sistem sebagai {$user->role->name}."
        );

        return redirect()->intended(route('dashboard'))->with('success', "Selamat datang kembali, {$user->name}!");
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::record(
                $user->id,
                'LOGOUT',
                'AUTENTIKASI',
                "Pengguna {$user->name} ({$user->username}) telah logout dari sistem."
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem SiGudang – 036.');
    }
}
