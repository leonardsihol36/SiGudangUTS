<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('barang');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('kode_supplier', 'like', "%{$q}%")
                    ->orWhere('nama_supplier', 'like', "%{$q}%")
                    ->orWhere('nama_kontak', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $suppliers = $query->latest()->paginate(10)->withQueryString();

        return view('supplier.index', compact('suppliers'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->isManajer()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return view('supplier.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isManajer()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $validated = $request->validate([
            'kode_supplier' => 'required|string|max:50|unique:t036_supplier,kode_supplier',
            'nama_supplier' => 'required|string|max:150',
            'nama_kontak' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:30',
            'email' => 'required|email|max:100',
            'alamat' => 'required|string',
            'status' => 'required|in:Aktif,Nonaktif',
        ], [
            'kode_supplier.required' => 'Kode supplier wajib diisi.',
            'kode_supplier.unique' => 'Kode supplier sudah digunakan.',
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'nama_kontak.required' => 'Nama kontak person wajib diisi.',
            'no_telepon.required' => 'Nomor telepon wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'alamat.required' => 'Alamat kantor / gudang wajib diisi.',
            'status.required' => 'Status supplier wajib dipilih.',
        ]);

        $supplier = Supplier::create($validated);

        AuditLog::record(
            $user->id,
            'TAMBAH',
            'SUPPLIER',
            "Pengguna {$user->name} menambahkan supplier baru: {$supplier->nama_supplier} ({$supplier->kode_supplier})."
        );

        return redirect()->route('supplier.index')->with('success', "Supplier '{$supplier->nama_supplier}' berhasil ditambahkan.");
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('barang');
        $barangs = $supplier->barang()->paginate(10);

        return view('supplier.show', compact('supplier', 'barangs'));
    }

    public function edit(Supplier $supplier)
    {
        $user = Auth::user();
        if (!$user->isManajer()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return view('supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $user = Auth::user();
        if (!$user->isManajer()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $validated = $request->validate([
            'kode_supplier' => 'required|string|max:50|unique:t036_supplier,kode_supplier,' . $supplier->id,
            'nama_supplier' => 'required|string|max:150',
            'nama_kontak' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:30',
            'email' => 'required|email|max:100',
            'alamat' => 'required|string',
            'status' => 'required|in:Aktif,Nonaktif',
        ], [
            'kode_supplier.required' => 'Kode supplier wajib diisi.',
            'kode_supplier.unique' => 'Kode supplier sudah digunakan.',
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'nama_kontak.required' => 'Nama kontak person wajib diisi.',
            'no_telepon.required' => 'Nomor telepon wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'alamat.required' => 'Alamat kantor / gudang wajib diisi.',
            'status.required' => 'Status supplier wajib dipilih.',
        ]);

        $supplier->update($validated);

        AuditLog::record(
            $user->id,
            'UBAH',
            'SUPPLIER',
            "Pengguna {$user->name} memperbarui informasi supplier: {$supplier->nama_supplier} ({$supplier->kode_supplier})."
        );

        return redirect()->route('supplier.index')->with('success', "Informasi supplier '{$supplier->nama_supplier}' berhasil diperbarui.");
    }

    public function destroy(Supplier $supplier)
    {
        $user = Auth::user();
        if (!$user->isManajer()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $barangCount = $supplier->barang()->count();
        if ($barangCount > 0) {
            return back()->with('error', "Supplier '{$supplier->nama_supplier}' tidak dapat dihapus karena masih menjadi pemasok untuk {$barangCount} data barang.");
        }

        $nama = $supplier->nama_supplier;
        $kode = $supplier->kode_supplier;
        $supplier->delete();

        AuditLog::record(
            $user->id,
            'HAPUS',
            'SUPPLIER',
            "Pengguna {$user->name} menghapus data supplier: {$nama} ({$kode})."
        );

        return redirect()->route('supplier.index')->with('success', "Supplier '{$nama}' ({$kode}) berhasil dihapus.");
    }
}
