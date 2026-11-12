<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\MutasiStok;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Common metrics
        $totalBarang = Barang::count();
        $totalStok = (int) Barang::sum('stok');
        $barangStokMenipis = Barang::whereColumn('stok', '<=', 'stok_minimum')->orderBy('stok', 'asc')->get();
        $totalStokMenipis = $barangStokMenipis->count();

        // Chart data: Monthly transactions of current year (2026 or current)
        $chartMasuk = MutasiStok::where('tipe_mutasi', 'masuk')
            ->selectRaw('MONTH(tanggal_mutasi) as bulan, SUM(jumlah) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->all();

        $chartKeluar = MutasiStok::where('tipe_mutasi', 'keluar')
            ->selectRaw('MONTH(tanggal_mutasi) as bulan, SUM(jumlah) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->all();

        $monthlyData = [];
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData['labels'][] = $monthNames[$m - 1];
            $monthlyData['masuk'][] = (int) ($chartMasuk[$m] ?? 0);
            $monthlyData['keluar'][] = (int) ($chartKeluar[$m] ?? 0);
        }

        // Role specific data
        $totalSupplier = 0;
        $totalUserAktif = 0;
        $recentMutasi = collect();
        $recentMasuk = collect();
        $recentKeluar = collect();
        $recentAuditLogs = collect();

        if ($user->isManajer()) {
            $totalSupplier = Supplier::count();
            $totalUserAktif = User::where('is_active', true)->count();
            $recentMutasi = MutasiStok::with(['barang', 'user'])
                ->latest('tanggal_mutasi')
                ->take(6)
                ->get();
        } elseif ($user->isStafGudang()) {
            $recentMasuk = MutasiStok::with(['barang', 'user'])
                ->where('tipe_mutasi', 'masuk')
                ->latest('tanggal_mutasi')
                ->take(5)
                ->get();
            $recentKeluar = MutasiStok::with(['barang', 'user'])
                ->where('tipe_mutasi', 'keluar')
                ->latest('tanggal_mutasi')
                ->take(5)
                ->get();
        } elseif ($user->isAuditor()) {
            $recentMutasi = MutasiStok::with(['barang', 'user'])
                ->latest('tanggal_mutasi')
                ->take(6)
                ->get();
            $recentAuditLogs = AuditLog::with('user')
                ->latest('created_at')
                ->take(6)
                ->get();
        }

        return view('dashboard.index', compact(
            'user',
            'totalBarang',
            'totalStok',
            'barangStokMenipis',
            'totalStokMenipis',
            'totalSupplier',
            'totalUserAktif',
            'recentMutasi',
            'recentMasuk',
            'recentKeluar',
            'recentAuditLogs',
            'monthlyData'
        ));
    }
}
