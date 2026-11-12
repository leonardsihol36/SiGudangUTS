<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\MutasiStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MutasiStokController extends Controller
{
    public function index(Request $request)
    {
        $query = MutasiStok::with(['barang', 'user']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('barang', function ($b) use ($q) {
                $b->where('kode_barang', 'like', "%{$q}%")
                  ->orWhere('nama_barang', 'like', "%{$q}%");
            })->orWhere('keterangan', 'like', "%{$q}%");
        }

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

        $mutasis = $query->latest('tanggal_mutasi')->paginate(10)->withQueryString();
        $barangs = Barang::orderBy('nama_barang')->get();

        return view('mutasi.index', compact('mutasis', 'barangs'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->isManajer() && !$user->isStafGudang()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $barangs = Barang::orderBy('nama_barang')->get();
        return view('mutasi.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isManajer() && !$user->isStafGudang()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $request->validate([
            'barang_id' => 'required|exists:t036_barang,id',
            'tipe_mutasi' => 'required|in:masuk,keluar',
            'jumlah' => 'required|integer|min:1',
            'tanggal_mutasi' => 'required|date',
            'keterangan' => 'required|string|max:255',
        ], [
            'barang_id.required' => 'Pilih barang yang mengalami mutasi.',
            'barang_id.exists' => 'Barang yang dipilih tidak valid.',
            'tipe_mutasi.required' => 'Pilih tipe mutasi (masuk atau keluar).',
            'tipe_mutasi.in' => 'Tipe mutasi harus berupa barang masuk atau keluar.',
            'jumlah.required' => 'Jumlah barang wajib diisi.',
            'jumlah.min' => 'Jumlah barang minimal adalah 1.',
            'tanggal_mutasi.required' => 'Tanggal mutasi wajib ditentukan.',
            'keterangan.required' => 'Keterangan mutasi stok wajib diisi.',
        ]);

        return DB::transaction(function () use ($request, $user) {
            // Lock row for update to handle concurrent requests safely
            $barang = Barang::where('id', $request->barang_id)->lockForUpdate()->firstOrFail();
            $jumlah = (int) $request->jumlah;
            $stokSebelum = $barang->stok;

            if ($request->tipe_mutasi === 'keluar') {
                if ($stokSebelum < $jumlah) {
                    return back()->withInput()->with('error', "Stok tidak mencukupi! Stok saat ini untuk '{$barang->nama_barang}' adalah {$stokSebelum} {$barang->satuan}, sedangkan permintaan pengeluaran adalah {$jumlah} {$barang->satuan}. Permintaan ditolak demi mencegah stok negatif.");
                }

                $stokSesudah = $stokSebelum - $jumlah;
            } else {
                $stokSesudah = $stokSebelum + $jumlah;
            }

            // Update item stock
            $barang->stok = $stokSesudah;
            $barang->save();

            // Record mutasi history
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

            // Record audit log
            $aksiTipe = strtoupper($request->tipe_mutasi);
            AuditLog::record(
                $user->id,
                'MUTASI',
                'MUTASI_STOK',
                "Pencatatan mutasi {$aksiTipe} oleh {$user->name}: {$barang->nama_barang} ({$barang->kode_barang}) sebanyak {$jumlah} {$barang->satuan}. Stok berubah dari {$stokSebelum} menjadi {$stokSesudah}."
            );

            $labelTipe = $request->tipe_mutasi === 'masuk' ? 'Barang Masuk' : 'Barang Keluar';
            return redirect()->route('mutasi.index')->with('success', "Mutasi {$labelTipe} untuk '{$barang->nama_barang}' berhasil dicatat. Stok terkini: {$stokSesudah} {$barang->satuan}.");
        });
    }

    public function show(MutasiStok $mutasi)
    {
        $mutasi->load(['barang.supplier', 'user.role']);
        return view('mutasi.show', compact('mutasi'));
    }
}
