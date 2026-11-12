# MATRIKS HAK AKSES & OTORISASI RBAC
## SiGudang – 036 | Sistem Manajemen Inventaris Barang

> **Penulis:** Leonard Sihol Pardede  
> **NIM:** 25K10036  
> **Tahun:** 2026  
> **Aplikasi:** SiGudang – 036  

---

## 1. Definisi Tiga Role Pengguna

Sistem **SiGudang – 036** mengimplementasikan kontrol akses berbasis peran (*Role-Based Access Control / RBAC*) dinamis yang tersimpan dalam tabel database `t036_roles` dan terhubung secara relasional ke tabel `t036_users`:

### A. Role 1 — Manajer (Level Tertinggi)
- **Akun Utama:** Username `25K10036`, Nama `Leonard Sihol Pardede`.
- **Tanggung Jawab:**
  - Pengawasan menyeluruh atas seluruh operasional gudang.
  - Manajemen akun pengguna: menambah user, mengubah data pengguna, mengganti level/role, serta mengaktifkan dan menonaktifkan akun.
  - Manajemen penuh data barang (katalog, spesifikasi, lokasi).
  - Manajemen penuh data mitra pemasok (supplier).
  - Pencatatan transaksi mutasi stok masuk dan keluar.
  - Peninjauan laporan inventaris dan ekspor dokumen.
  - Pemeriksaan menyeluruh seluruh catatan audit log aktivitas sistem.

### B. Role 2 — Staf Gudang (Operasional Harian)
- **Akun Uji:** Username `staf036`, Nama `Budi Santoso`.
- **Tanggung Jawab:**
  - Operasional inventaris sehari-hari di area gudang.
  - Melihat data katalog barang dan supplier.
  - Menambah item barang baru dan memperbarui informasi barang.
  - Mencatat transaksi mutasi barang masuk (penerimaan dari supplier) dan barang keluar (pengiriman/distribusi).
  - Memantau kondisi stok fisik dan batas stok minimum.
  - Melihat laporan inventaris.
  - *Batasan:* Staf Gudang **dilarang keras** mengelola pengguna, mengubah role, menghapus data barang/supplier, maupun mengakses audit log aktivitas sistem.

### C. Role 3 — Auditor (Pemeriksaan & Pengawasan)
- **Akun Uji:** Username `auditor036`, Nama `Siti Nurhaliza`.
- **Tanggung Jawab:**
  - Bertanggung jawab melakukan pemeriksaan kesesuaian fisik dan administratif tanpa kewenangan mengubah data operasional (*read-only*).
  - Melihat data katalog barang dan stok fisik.
  - Melihat profil data supplier.
  - Memeriksa histori pergerakan mutasi stok masuk dan keluar.
  - Meninjau laporan inventaris dan mengunduh rekapitulasi CSV.
  - Mengawasi log audit aktivitas pengguna untuk mendeteksi anomali operasional.
  - *Batasan:* Auditor **tidak memiliki izin modifikasi data** (tidak dapat menambah, mengubah, atau menghapus barang, supplier, maupun mencatat mutasi).

---

## 2. Tabel Matriks Hak Akses (Role × Modul × Aksi)

Keterangan:
- **C** = Create (Membuat data baru)
- **R** = Read (Melihat / membaca data)
- **U** = Update (Mengubah / memperbarui data)
- **D** = Delete (Menghapus data / deaktifasi akun)
- **—** = Tidak Memiliki Akses (Akses Ditolak HTTP 403)

| Modul Aplikasi | Manajer | Staf Gudang | Auditor | Keterangan & Aturan Khusus |
|---|:---:|:---:|:---:|---|
| **Dashboard** | R | R | R | Tampilan ringkasan statistik disesuaikan dengan kebutuhan peran masing-masing. |
| **Kelola Pengguna** | CRUD* | — | — | *Aksi delete diterapkan sebagai penonaktifan akun (*soft-deactivation*). Terdapat *guard* pelindung agar selalu tersedia minimal 1 akun Manajer aktif. |
| **Data Barang** | CRUD | CRU | R | Staf Gudang tidak diizinkan menghapus barang. Auditor hanya dapat melihat. Perubahan kuantitas stok wajib melalui mutasi, bukan edit barang. |
| **Data Supplier** | CRUD | R | R | Penghapusan supplier dicegah jika masih terdapat relasi barang aktif. |
| **Mutasi Stok** | CR | CR | R | Pencatatan mutasi otomatis memperbarui saldo fisik barang dengan *row locking*. Transaksi yang tersimpan bersifat permanen (tidak dapat dihapus). |
| **Laporan Inventaris** | R | R | R | Seluruh role dapat meninjau rekapitulasi stok, daftar barang minimum, dan mengekspor CSV. |
| **Audit Log Aktivitas** | R | — | R | Catatan aktivitas sistem bersifat read-only khusus untuk Manajer dan Auditor. |
| **Profil Sendiri** | RU | RU | RU | Setiap pengguna dapat melihat informasi akun, mengubah kata sandi, dan mengunggah foto profil (JPG/PNG max 2MB). |

---

## 3. Implementasi Teknis Otorisasi (Lapisan Keamanan)

Sistem memastikan otorisasi ditegakkan secara berlapis (*defense-in-depth*), tidak hanya menyembunyikan tombol antarmuka:

### A. Lapisan 1: Tampilan Sidebar & Navbar (Blade Directive)
Elemen navigasi dan tombol aksi disaring secara kondisional di sisi Blade:
```blade
@if(Auth::user()->isManajer())
    <a href="{{ route('users.index') }}">Kelola Pengguna</a>
@endif
```

### B. Lapisan 2: Routing & Middleware Otorisasi (`CheckRole`)
Seluruh rute dilindungi oleh middleware `App\Http\Middleware\CheckRole`. Bila pengguna mencoba mengetikkan URL terlarang secara manual di browser (misalnya Staf Gudang membuka `/pengguna`), middleware akan langsung menghentikan proses dan menghasilkan respon **HTTP 403**:
```php
if (!$user->role || !in_array($user->role->name, $roles)) {
    abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
}
```

### C. Lapisan 3: Penanganan Controller & Model Policy
Pada controller, setiap aksi sensitif (seperti `store`, `update`, `destroy`) kembali memvalidasi peran pengguna dan aturan bisnis.
Contoh pada `BarangController@destroy`:
```php
if (!Auth::user()->isManajer()) {
    abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
}

if ($barang->mutasi()->count() > 0) {
    return back()->with('error', 'Barang tidak dapat dihapus karena memiliki riwayat mutasi.');
}
```

### D. Lapisan 4: RESTful API Security (`CheckApiRole`)
Pada endpoint API, token Sanctum divalidasi dan middleware `CheckApiRole` memeriksa kesesuaian peran. Pelanggaran hak akses mengembalikan respons JSON terstruktur dengan kode status HTTP 403:
```json
{
  "success": false,
  "message": "Akses ditolak. Anda tidak memiliki izin untuk mengakses resource ini."
}
```

---

## 4. Aturan Keamanan Khusus (Security Guards)

1. **Proteksi Akun Manajer Terakhir:**
   Sistem secara otomatis menghitung jumlah Manajer aktif sebelum melakukan penonaktifan atau penurunan role. Jika akun yang dituju adalah satu-satunya Manajer aktif, aksi akan ditolak dengan pesan:
   > *"Tidak dapat menonaktifkan akun Manajer ini. Harus selalu terdapat minimal satu Manajer aktif dalam sistem SiGudang – 036."*
2. **Pencegahan Stok Negatif:**
   Pada mutasi barang keluar, transaksi dieksekusi di dalam `DB::transaction` dengan `lockForUpdate()`. Jika kuantitas keluar melebihi stok tersedia, mutasi otomatis digagalkan demi menjaga integritas inventaris fisik.
3. **Pemisahan Perubahan Stok dari Edit Barang:**
   Kolom kuantitas stok dinonaktifkan (*readonly*) pada formulir edit barang untuk mencegah manipulasi stok tanpa jejak mutasi.
4. **Proteksi Integritas Referensial Supplier:**
   Supplier yang masih tercatat sebagai pemasok untuk data barang yang ada di gudang tidak dapat dihapus sebelum relasi barang dialihkan terlebih dahulu.

---
**© 2026 Leonard Sihol Pardede – 25.K1.0036**
