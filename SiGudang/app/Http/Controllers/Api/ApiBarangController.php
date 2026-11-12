<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with('supplier');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('kode_barang', 'like', "%{$q}%")
                    ->orWhere('nama_barang', 'like', "%{$q}%")
                    ->orWhere('kategori', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $barangs = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar data barang berhasil diambil.',
            'data' => $barangs,
        ], 200);
    }

    public function show($id)
    {
        $barang = Barang::with(['supplier', 'mutasi'])->find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Data barang tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail data barang berhasil diambil.',
            'data' => $barang,
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user->isManajer() && !$user->isStafGudang()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki izin untuk menambah data barang.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
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
            'stok.min' => 'Stok tidak boleh bernilai negatif.',
            'stok_minimum.required' => 'Stok minimum wajib diisi.',
            'lokasi_penyimpanan.required' => 'Lokasi penyimpanan wajib diisi.',
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'supplier_id.exists' => 'Supplier tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi input gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $barang = Barang::create($validator->validated());

        AuditLog::record(
            $user->id,
            'TAMBAH_API',
            'BARANG',
            "Pengguna {$user->name} menambah barang via API: {$barang->nama_barang} ({$barang->kode_barang})."
        );

        return response()->json([
            'success' => true,
            'message' => 'Data barang berhasil ditambahkan.',
            'data' => $barang->load('supplier'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->isManajer() && !$user->isStafGudang()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki izin untuk mengubah data barang.',
            ], 403);
        }

        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Data barang tidak ditemukan.',
            ], 404);
        }

        $rules = [
            'kode_barang' => ($request->isMethod('patch') ? 'sometimes|' : 'required|') . 'string|max:50|unique:t036_barang,kode_barang,' . $barang->id,
            'nama_barang' => ($request->isMethod('patch') ? 'sometimes|' : 'required|') . 'string|max:150',
            'kategori' => ($request->isMethod('patch') ? 'sometimes|' : 'required|') . 'string|max:100',
            'satuan' => ($request->isMethod('patch') ? 'sometimes|' : 'required|') . 'string|max:50',
            'stok_minimum' => ($request->isMethod('patch') ? 'sometimes|' : 'required|') . 'integer|min:0',
            'lokasi_penyimpanan' => ($request->isMethod('patch') ? 'sometimes|' : 'required|') . 'string|max:100',
            'supplier_id' => ($request->isMethod('patch') ? 'sometimes|' : 'required|') . 'exists:t036_supplier,id',
            'deskripsi' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi input gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Direct editing of 'stok' is disallowed per requirements
        $validatedData = $validator->validated();
        unset($validatedData['stok']);

        $barang->update($validatedData);

        AuditLog::record(
            $user->id,
            'UBAH_API',
            'BARANG',
            "Pengguna {$user->name} memperbarui barang via API: {$barang->nama_barang} ({$barang->kode_barang})."
        );

        return response()->json([
            'success' => true,
            'message' => 'Data barang berhasil diperbarui.',
            'data' => $barang->load('supplier'),
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->isManajer()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya Manajer yang dapat menghapus data barang.',
            ], 403);
        }

        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Data barang tidak ditemukan.',
            ], 404);
        }

        if ($barang->mutasi()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak dapat dihapus karena sudah memiliki riwayat mutasi stok.',
            ], 400);
        }

        $nama = $barang->nama_barang;
        $kode = $barang->kode_barang;
        $barang->delete();

        AuditLog::record(
            $user->id,
            'HAPUS_API',
            'BARANG',
            "Pengguna {$user->name} menghapus data barang via API: {$nama} ({$kode})."
        );

        return response()->json([
            'success' => true,
            'message' => "Data barang '{$nama}' ({$kode}) berhasil dihapus.",
        ], 200);
    }
}
