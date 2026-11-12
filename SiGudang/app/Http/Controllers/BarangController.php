<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with('supplier');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('kode_barang', 'like', "%{$q}%")
                    ->orWhere('nama_barang', 'like', "%{$q}%")
                    ->orWhere('lokasi_penyimpanan', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $barangs = $query->latest()->paginate(10)->withQueryString();

        $categories = Barang::select('kategori')->distinct()->pluck('kategori');
        $suppliers = Supplier::where('status', 'Aktif')->orderBy('nama_supplier')->get();

        return view('barang.index', compact('barangs', 'categories', 'suppliers'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->isManajer() && !$user->isStafGudang()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $suppliers = Supplier::where('status', 'Aktif')->orderBy('nama_supplier')->get();
        return view('barang.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isManajer() && !$user->isStafGudang()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:t036_barang,kode_barang',
            'nama_barang' => 'required|string|max:150',
            'kategori' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'lokasi_penyimpanan' => 'required|string|max:100',
            'supplier_id' => 'required|exists:t036_supplier,id',
            'deskripsi' => 'nullable|string',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'kategori.required' => 'Kategori wajib diisi.',
            'satuan.required' => 'Satuan wajib diisi.',
            'stok.required' => 'Stok awal wajib diisi.',
            'stok.min' => 'Stok awal tidak boleh negatif.',
            'stok_minimum.required' => 'Stok minimum wajib diisi.',
            'lokasi_penyimpanan.required' => 'Lokasi penyimpanan wajib diisi.',
            'supplier_id.required' => 'Pilih supplier pemasok.',
            'supplier_id.exists' => 'Supplier yang dipilih tidak valid.',
        ]);

        $barang = Barang::create($validated);

        AuditLog::record(
            $user->id,
            'TAMBAH',
            'BARANG',
            "Pengguna {$user->name} menambahkan barang baru: {$barang->nama_barang} ({$barang->kode_barang}) dengan stok awal {$barang->stok} {$barang->satuan}."
        );

        return redirect()->route('barang.index')->with('success', "Barang '{$barang->nama_barang}' berhasil ditambahkan ke inventaris.");
    }

    public function show(Barang $barang)
    {
        $barang->load(['supplier', 'mutasi.user']);
        $mutasiList = $barang->mutasi()->with('user')->latest('tanggal_mutasi')->paginate(10);

        return view('barang.show', compact('barang', 'mutasiList'));
    }

    public function edit(Barang $barang)
    {
        $user = Auth::user();
        if (!$user->isManajer() && !$user->isStafGudang()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $suppliers = Supplier::where('status', 'Aktif')->orderBy('nama_supplier')->get();
        return view('barang.edit', compact('barang', 'suppliers'));
    }

    public function update(Request $request, Barang $barang)
    {
        $user = Auth::user();
        if (!$user->isManajer() && !$user->isStafGudang()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        // Note: 'stok' is intentionally excluded from direct editing per requirement!
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:t036_barang,kode_barang,' . $barang->id,
            'nama_barang' => 'required|string|max:150',
            'kategori' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'stok_minimum' => 'required|integer|min:0',
            'lokasi_penyimpanan' => 'required|string|max:100',
            'supplier_id' => 'required|exists:t036_supplier,id',
            'deskripsi' => 'nullable|string',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'kategori.required' => 'Kategori wajib diisi.',
            'satuan.required' => 'Satuan wajib diisi.',
            'stok_minimum.required' => 'Stok minimum wajib diisi.',
            'lokasi_penyimpanan.required' => 'Lokasi penyimpanan wajib diisi.',
            'supplier_id.required' => 'Pilih supplier pemasok.',
            'supplier_id.exists' => 'Supplier yang dipilih tidak valid.',
        ]);

        $barang->update($validated);

        AuditLog::record(
            $user->id,
            'UBAH',
            'BARANG',
            "Pengguna {$user->name} memperbarui data barang: {$barang->nama_barang} ({$barang->kode_barang})."
        );

        return redirect()->route('barang.index')->with('success', "Informasi barang '{$barang->nama_barang}' berhasil diperbarui.");
    }

    public function destroy(Barang $barang)
    {
        $user = Auth::user();
        if (!$user->isManajer()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $mutasiCount = $barang->mutasi()->count();
        if ($mutasiCount > 0) {
            return back()->with('error', "Barang '{$barang->nama_barang}' tidak dapat dihapus karena memiliki {$mutasiCount} riwayat mutasi stok.");
        }

        $nama = $barang->nama_barang;
        $kode = $barang->kode_barang;
        $barang->delete();

        AuditLog::record(
            $user->id,
            'HAPUS',
            'BARANG',
            "Pengguna {$user->name} menghapus data barang: {$nama} ({$kode})."
        );

        return redirect()->route('barang.index')->with('success', "Barang '{$nama}' ({$kode}) berhasil dihapus dari inventaris.");
    }
}
