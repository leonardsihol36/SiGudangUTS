<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ApiIdentitasController extends Controller
{
    public function index(Request $request)
    {
        // Find author user by NIM/username 25K10036 or name
        $user = User::where('username', '25K10036')->first();

        $fotoUrl = '';
        if ($user && $user->profile_photo) {
            $fotoUrl = url($user->profile_photo);
        } else {
            $fotoUrl = url('uploads/profil/leonard-25k10036.png');
        }

        return response()->json([
            'nim' => '25K10036',
            'nama' => 'Leonard Sihol Pardede',
            'foto' => $fotoUrl,
            'aplikasi' => 'SiGudang – 036',
        ], 200);
    }
}
