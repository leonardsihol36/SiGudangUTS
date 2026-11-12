<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\MutasiStok;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Total records inserted: exactly 16 rows across the entire database.
     * - t036_roles: 3
     * - t036_users: 3
     * - t036_supplier: 3
     * - t036_barang: 5
     * - t036_mutasi_stok: 2
     */
    public function run(): void
    {
        // 1. Roles (3 records)
        $roles = [
            [
                'id' => 1,
                'name' => 'Manajer',
                'description' => 'Hak akses tertinggi: kelola pengguna, barang, supplier, mutasi, laporan, dan audit log.',
            ],
            [
                'id' => 2,
                'name' => 'Staf Gudang',
                'description' => 'Operasional gudang: kelola barang, catat mutasi stok masuk dan keluar, serta lihat supplier.',
            ],
            [
                'id' => 3,
                'name' => 'Auditor',
                'description' => 'Pemeriksaan sistem: hak akses peninjauan (read-only) untuk seluruh data inventaris dan log aktivitas.',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(['id' => $roleData['id']], $roleData);
        }

        // 2. Users (3 records)
        $defaultPassword = Hash::make('password123');

        $users = [
            [
                'id' => 1,
                'role_id' => 1,
                'username' => '25K10036',
                'name' => 'Leonard Sihol Pardede',
                'email' => 'leonard.pardede@sigudang.test',
                'password' => $defaultPassword,
                'profile_photo' => 'uploads/profil/leonard-25k10036.png',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'role_id' => 2,
                'username' => 'staf036',
                'name' => 'Budi Santoso',
                'email' => 'staf@sigudang.test',
                'password' => $defaultPassword,
                'profile_photo' => null,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'role_id' => 3,
                'username' => 'auditor036',
                'name' => 'Siti Nurhaliza',
                'email' => 'auditor@sigudang.test',
                'password' => $defaultPassword,
                'profile_photo' => null,
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['id' => $userData['id']], $userData);
        }

        // 3. Suppliers (3 records)
        $suppliers = [
            [
                'id' => 1,
                'kode_supplier' => 'SUP-001',
                'nama_supplier' => 'PT Global Logistik Nusantara',
                'nama_kontak' => 'Hendra Wijaya',
                'no_telepon' => '081234567890',
                'email' => 'info@globallogistik.co.id',
                'alamat' => 'Jl. Industri Raya No. 45, Kawasan Industri Jababeka, Cikarang',
                'status' => 'Aktif',
            ],
            [
                'id' => 2,
                'kode_supplier' => 'SUP-002',
                'nama_supplier' => 'CV Maju Jaya Abadi',
                'nama_kontak' => 'Dewi Lestari',
                'no_telepon' => '082198765432',
                'email' => 'sales@majujayaabadi.com',
                'alamat' => 'Jl. Rungkut Industri III No. 12, Surabaya',
                'status' => 'Aktif',
            ],
            [
                'id' => 3,
                'kode_supplier' => 'SUP-003',
                'nama_supplier' => 'PT Sumber Elektronik Mandiri',
                'nama_kontak' => 'Agus Pratama',
                'no_telepon' => '085712345678',
                'email' => 'kontak@sumberelektronik.id',
                'alamat' => 'Jl. Gajah Mada No. 88, Jakarta Barat',
                'status' => 'Aktif',
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::updateOrCreate(['id' => $supplierData['id']], $supplierData);
        }

        // 4. Barang (5 records)
        $barangs = [
            [
                'id' => 1,
                'kode_barang' => 'BRG-001',
                'nama_barang' => 'Barcode Scanner Wireless 2D',
                'kategori' => 'Elektronik & Perangkat',
                'satuan' => 'Unit',
                'stok' => 45,
                'stok_minimum' => 10,
                'lokasi_penyimpanan' => 'Rak A-01',
                'supplier_id' => 3,
                'deskripsi' => 'Scanner barcode nirkabel frekuensi 2.4GHz dengan jangkauan 50 meter.',
            ],
            [
                'id' => 2,
                'kode_barang' => 'BRG-002',
                'nama_barang' => 'Kardus Packing Double Wall 40x30x30',
                'kategori' => 'Packaging',
                'satuan' => 'Pcs',
                'stok' => 250,
                'stok_minimum' => 50,
                'lokasi_penyimpanan' => 'Zona B-Pallet 04',
                'supplier_id' => 1,
                'deskripsi' => 'Kardus corrugated karton tebal double wall untuk pengiriman kargo berat.',
            ],
            [
                'id' => 3,
                'kode_barang' => 'BRG-003',
                'nama_barang' => 'Hand Pallet Truck 3 Ton',
                'kategori' => 'Alat Berat & Handling',
                'satuan' => 'Unit',
                'stok' => 8,
                'stok_minimum' => 5,
                'lokasi_penyimpanan' => 'Area Docking C',
                'supplier_id' => 2,
                'deskripsi' => 'Hand pallet hidrolik kapasitas angkat 3000 kg roda polyurethane.',
            ],
            [
                'id' => 4,
                'kode_barang' => 'BRG-004',
                'nama_barang' => 'Thermal Label Sticker 100x150 mm',
                'kategori' => 'Packaging',
                'satuan' => 'Roll',
                'stok' => 8,
                'stok_minimum' => 15,
                'lokasi_penyimpanan' => 'Rak A-03',
                'supplier_id' => 1,
                'deskripsi' => 'Kertas label barcode direct thermal isi 500 pcs per roll.',
            ],
            [
                'id' => 5,
                'kode_barang' => 'BRG-005',
                'nama_barang' => 'Stretch Film 50cm x 300m 17 Micron',
                'kategori' => 'Packaging',
                'satuan' => 'Roll',
                'stok' => 60,
                'stok_minimum' => 20,
                'lokasi_penyimpanan' => 'Zona B-Pallet 01',
                'supplier_id' => 2,
                'deskripsi' => 'Plastik wrapping pembungkus pallet elastis kualitas industri.',
            ],
        ];

        foreach ($barangs as $barangData) {
            Barang::updateOrCreate(['id' => $barangData['id']], $barangData);
        }

        // 5. Mutasi Stok (2 records)
        $mutasis = [
            [
                'id' => 1,
                'barang_id' => 1,
                'user_id' => 1,
                'tipe_mutasi' => 'masuk',
                'jumlah' => 20,
                'stok_sebelum' => 25,
                'stok_sesudah' => 45,
                'keterangan' => 'Penerimaan stok baru dari PT Sumber Elektronik Mandiri PO-2026-001',
                'tanggal_mutasi' => '2026-03-10 09:30:00',
            ],
            [
                'id' => 2,
                'barang_id' => 4,
                'user_id' => 2,
                'tipe_mutasi' => 'keluar',
                'jumlah' => 12,
                'stok_sebelum' => 20,
                'stok_sesudah' => 8,
                'keterangan' => 'Pengeluaran label shipping untuk pemenuhan pesanan batch pagi',
                'tanggal_mutasi' => '2026-03-12 14:15:00',
            ],
        ];

        foreach ($mutasis as $mutasiData) {
            MutasiStok::updateOrCreate(['id' => $mutasiData['id']], $mutasiData);
        }
    }
}
