<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\MutasiStok;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'barang');

        $barangs = Barang::with('supplier')->orderBy('nama_barang')->get();
        $stokMinimumBarangs = Barang::whereColumn('stok', '<=', 'stok_minimum')->with('supplier')->get();

        $queryMutasi = MutasiStok::with(['barang', 'user']);

        if ($request->filled('tanggal_mulai')) {
            $queryMutasi->whereDate('tanggal_mutasi', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $queryMutasi->whereDate('tanggal_mutasi', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('tipe_mutasi')) {
            $queryMutasi->where('tipe_mutasi', $request->tipe_mutasi);
        }

        $mutasis = $queryMutasi->latest('tanggal_mutasi')->get();

        return view('laporan.index', compact('tab', 'barangs', 'stokMinimumBarangs', 'mutasis'));
    }

    public function exportCsv(Request $request)
    {
        $tab = $request->get('tab', 'barang');
        $filename = "laporan_{$tab}_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($tab, $request) {
            $output = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            if ($tab === 'barang') {
                fputcsv($output, ['No', 'Kode Barang', 'Nama Barang', 'Kategori', 'Satuan', 'Stok', 'Stok Min', 'Lokasi', 'Supplier']);
                $barangs = Barang::with('supplier')->orderBy('nama_barang')->get();
                foreach ($barangs as $i => $b) {
                    fputcsv($output, [
                        $i + 1,
                        $b->kode_barang,
                        $b->nama_barang,
                        $b->kategori,
                        $b->satuan,
                        $b->stok,
                        $b->stok_minimum,
                        $b->lokasi_penyimpanan,
                        $b->supplier ? $b->supplier->nama_supplier : '-',
                    ]);
                }
            } elseif ($tab === 'stok_minimum') {
                fputcsv($output, ['No', 'Kode Barang', 'Nama Barang', 'Kategori', 'Stok Saat Ini', 'Batas Minimum', 'Status', 'Supplier']);
                $barangs = Barang::whereColumn('stok', '<=', 'stok_minimum')->with('supplier')->get();
                foreach ($barangs as $i => $b) {
                    fputcsv($output, [
                        $i + 1,
                        $b->kode_barang,
                        $b->nama_barang,
                        $b->kategori,
                        $b->stok,
                        $b->stok_minimum,
                        $b->stok <= 0 ? 'HABIS' : 'KRITIS',
                        $b->supplier ? $b->supplier->nama_supplier : '-',
                    ]);
                }
            } else {
                fputcsv($output, ['No', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Tipe Mutasi', 'Jumlah', 'Stok Sebelum', 'Stok Sesudah', 'Petugas', 'Keterangan']);
                $query = MutasiStok::with(['barang', 'user']);
                if ($request->filled('tanggal_mulai')) {
                    $query->whereDate('tanggal_mutasi', '>=', $request->tanggal_mulai);
                }
                if ($request->filled('tanggal_selesai')) {
                    $query->whereDate('tanggal_mutasi', '<=', $request->tanggal_selesai);
                }
                if ($request->filled('tipe_mutasi')) {
                    $query->where('tipe_mutasi', $request->tipe_mutasi);
                }
                $mutasis = $query->latest('tanggal_mutasi')->get();
                foreach ($mutasis as $i => $m) {
                    fputcsv($output, [
                        $i + 1,
                        $m->tanggal_mutasi->format('Y-m-d H:i'),
                        $m->barang ? $m->barang->kode_barang : '-',
                        $m->barang ? $m->barang->nama_barang : '-',
                        strtoupper($m->tipe_mutasi),
                        $m->jumlah,
                        $m->stok_sebelum,
                        $m->stok_sesudah,
                        $m->user ? $m->user->name : '-',
                        $m->keterangan,
                    ]);
                }
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
