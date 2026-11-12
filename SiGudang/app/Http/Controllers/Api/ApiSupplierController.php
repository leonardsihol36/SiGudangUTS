<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ApiSupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('barang');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('kode_supplier', 'like', "%{$q}%")
                    ->orWhere('nama_supplier', 'like', "%{$q}%")
                    ->orWhere('nama_kontak', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $suppliers = $query->orderBy('nama_supplier')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar data supplier berhasil diambil.',
            'data' => $suppliers,
        ], 200);
    }
}
