<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\MutasiStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiMutasiController extends Controller
{
    public function index(Request $request)
    {
        $query = MutasiStok::with(['barang', 'user']);

        if ($request->filled('tipe_mutasi')) {
            $query->where('tipe_mutasi', $request->tipe_mutasi);
        }

        if ($request->filled('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_mutasi', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_mutasi', '<=', $request->tanggal_selesai);
        }

        $mutasiList = $query->latest('tanggal_mutasi')->get();

        return response()->json([
            'success' => true,
            'message' => 'Histori pergerakan mutasi stok berhasil diambil.',
            'data' => $mutasiList,
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user->isManajer() && !$user->isStafGudang()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki izin untuk mencatat mutasi stok.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'barang_id' => 'required|exists:t036_barang,id',
            'tipe_mutasi' => 'required|in:masuk,keluar',
            'jumlah' => 'required|integer|min:1',
            'tanggal_mutasi' => 'required|date',
            'keterangan' => 'required|string|max:255',
        ], [
            'barang_id.required' => 'ID barang wajib diisi.',
            'barang_id.exists' => 'Barang tidak ditemukan.',
            'tipe_mutasi.required' => 'Tipe mutasi wajib dipilih (masuk atau keluar).',
            'tipe_mutasi.in' => 'Tipe mutasi hanya boleh bernilai masuk atau keluar.',
            'jumlah.required' => 'Jumlah barang wajib diisi.',
            'jumlah.min' => 'Jumlah barang minimal 1.',
            'tanggal_mutasi.required' => 'Tanggal mutasi wajib diisi.',
            'keterangan.required' => 'Keterangan mutasi wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi input gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        return DB::transaction(function () use ($request, $user) {
            $barang = Barang::where('id', $request->barang_id)->lockForUpdate()->firstOrFail();
            $jumlah = (int) $request->jumlah;
            $stokSebelum = $barang->stok;

            if ($request->tipe_mutasi === 'keluar') {
                if ($stokSebelum < $jumlah) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok tidak mencukupi. Stok saat ini {$stokSebelum} {$barang->satuan}, sedangkan permintaan pengeluaran {$jumlah} {$barang->satuan}.",
                    ], 400);
                }
                $stokSesudah = $stokSebelum - $jumlah;
            } else {
                $stokSesudah = $stokSebelum + $jumlah;
            }

            $barang->stok = $stokSesudah;
            $barang->save();

            $mutasi = MutasiStok::create([
                'barang_id' => $barang->id,
                'user_id' => $user->id,
                'tipe_mutasi' => $request->tipe_mutasi,
                'jumlah' => $jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'keterangan' => $request->keterangan,
                'tanggal_mutasi' => $request->tanggal_mutasi,
            ]);

            AuditLog::record(
                $user->id,
                'MUTASI_API',
                'MUTASI_STOK',
                "Pencatatan mutasi API " . strtoupper($request->tipe_mutasi) . " oleh {$user->name}: {$barang->nama_barang} sebanyak {$jumlah} {$barang->satuan}."
            );

            return response()->json([
                'success' => true,
                'message' => "Mutasi stok {$request->tipe_mutasi} berhasil dicatat. Stok terkini: {$stokSesudah} {$barang->satuan}.",
                'data' => $mutasi->load(['barang', 'user']),
            ], 201);
        });
    }
}
