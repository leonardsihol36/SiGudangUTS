# LAPORAN TUGAS PROYEK UJIAN TENGAH SEMESTER (UTS)
## PENGEMBANGAN APLIKASI WEB SISTEM MANAJEMEN INVENTARIS GUDANG
### "SiGudang – 036"

---

## LEMBAR IDENTITAS MAHASISWA

| Data | Keterangan |
|---|---|
| **Nama Lengkap** | **Leonard Sihol Pardede** |
| **Nomor Induk Mahasiswa (NIM)** | **25K10036** |
| **Mata Kuliah** | Pengembangan Aplikasi Web / Pemrograman Web |
| **Tahun Akademik** | 2026 |
| **Nama Aplikasi** | **SiGudang – 036** |
| **Nama Database** | `uts_25K10036` |
| **Prefix Seluruh Tabel** | `t036_` |
| **Role Pengguna Tertinggi** | **Manajer** |

---

## 1. PENDAHULUAN

### 1.1 Latar Belakang
Manajemen inventaris fisik pada gudang modern menuntut ketepatan pencatatan, akuntabilitas pergerakan stok, kecepatan akses data, dan pengawasan operasional yang transparan. Kesalahan pembukuan manual atau perbedaan antara saldo sistem dengan saldo fisik sering memicu kerugian finansial, keterlambatan pengiriman, hingga kekosongan stok (*stockout*).

Proyek **SiGudang – 036** dikembangkan untuk menghadirkan solusi *Warehouse Management System (WMS)* berbasis web yang terintegrasi secara *end-to-end*. Aplikasi ini tidak hanya menyediakan antarmuka pengguna (*User Interface*) modern dan ergonomis bagi staf lapangan, tetapi juga menerapkan kontrol akses peran bertingkat (*Role-Based Access Control / RBAC*), integritas transaksi atomik dengan penguncian baris database (*row locking*), pencatatan jejak audit (*audit trail*), serta antarmuka pemrograman aplikasi berbasis REST (*RESTful API*) yang aman menggunakan token bearer Laravel Sanctum.

### 1.2 Tujuan Pengembangan
1. Membangun sistem inventaris berbasis web dengan arsitektur Model-View-Controller (MVC) Laravel yang kokoh dan mudah dirawat.
2. Mengimplementasikan pembagian hak akses tiga level pengguna: **Manajer**, **Staf Gudang**, dan **Auditor**.
3. Menjamin keakuratan saldo stok melalui transaksi atomik serta pencegahan tegas terhadap nilai stok negatif.
4. Menyediakan sistem pelaporan inventaris dan ekspor dokumen yang siap digunakan untuk audit internal.
5. Menyediakan layanan data melalui RESTful JSON API yang terstandarisasi untuk integrasi sistem eksternal.

---

## 2. STUDI KASUS SISTEM INVENTARIS GUDANG

Skenario operasional yang diangkat adalah pusat distribusi pergudangan (Distribution Center) yang menampung beragam komoditas, mencakup perangkat elektronik nirkabel, material kemasan (*packaging*), alat berat handling, dan perlengkapan logistik.

Alur operasional terbagi menjadi:
1. **Penerimaan Barang Masuk (*Inbound*):** Staf Gudang mencatat penerimaan kiriman dari mitra supplier resmi. Sistem secara otomatis menambahkan saldo stok barang dan mencatat mutasi dengan nomor surat jalan / purchase order terkait.
2. **Pengeluaran Barang (*Outbound*):** Staf Gudang memproses permintaan pengeluaran barang untuk pemenuhan pesanan. Sistem memverifikasi kecukupan saldo fisik. Jika saldo mencukupi, sistem mengurangi stok dan mencatat saldo sebelum serta sesudah mutasi. Jika saldo tidak cukup, permintaan ditolak seketika demi mencegah stok negatif.
3. **Pemeriksaan & Pengawasan (*Auditing*):** Auditor secara berkala memantau laporan ketersediaan stok, barang yang berada di bawah stok minimum, serta meninjau seluruh jejak audit aktivitas yang dicatat oleh sistem.
4. **Manajemen Pengguna & Konfigurasi (*Administration*):** Manajer mengelola otorisasi akun pengguna, kemitraan supplier, katalog master barang, dan memiliki hak akses tertinggi di seluruh modul.

---

## 3. ANALISIS KEBUTUHAN SISTEM

### 3.1 Kebutuhan Fungsional (Functional Requirements)
1. **Autentikasi & Sesi:** Login dengan kombinasi username (NIM/ID) dan password bcrypt; pemeriksaan akun aktif; penutupan sesi aman saat logout; proteksi token CSRF.
2. **Otorisasi RBAC:** Pembatasan akses berbasis peran di level sidebar, routing middleware, controller, dan tombol aksi.
3. **Manajemen Katalog Barang (CRUD):** Tambah, lihat, ubah spesifikasi, dan hapus barang (khusus Manajer). Perubahan kuantitas stok dilarang dilakukan secara bebas melalui edit barang, melainkan wajib melalui transaksi mutasi.
4. **Manajemen Supplier (CRUD):** Pendataan mitra pemasok resmi beserta validasi penghapusan jika masih menjadi pemasok barang aktif.
5. **Manajemen Mutasi Stok:** Pencatatan mutasi barang masuk dan keluar dengan perhitungan saldo otomatis dan verifikasi kecukupan kuantitas.
6. **Manajemen Pengguna:** Pengelolaan akun pengguna oleh Manajer dengan aturan pengaman bahwa minimal harus selalu ada satu akun Manajer aktif.
7. **Profil Pengguna & Upload Foto:** Setiap pengguna dapat memperbarui biodata, mengubah kata sandi, dan mengunggah foto profil fisik (JPG/PNG <= 2MB).
8. **Pusat Laporan & Ekspor:** Rekapitulasi katalog barang, daftar stok kritis/minimum, riwayat mutasi per periode, dan ekspor CSV.
9. **Audit Trail (Log Aktivitas):** Pencatatan otomatis peristiwa penting sistem (login, logout, modifikasi barang, mutasi, ubah role) secara read-only.
10. **RESTful API:** Endpoint JSON lengkap dengan proteksi Bearer Token Sanctum, termasuk endpoint identitas dinamis `GET /api/identitas`.

### 3.2 Kebutuhan Non-Fungsional (Non-Functional Requirements)
1. **Keamanan:** Penyimpanan password menggunakan hash Bcrypt; pencegahan SQL Injection dengan Eloquent Parameter Binding; sanitasi input; pencegahan Cross-Site Scripting (XSS).
2. **Performa & Integritas:** Menggunakan *database transaction* dan *pessimistic row locking (`lockForUpdate()`)* pada operasi mutasi untuk menghindari *race condition*.
3. **Ergonomi UI/UX:** Tampilan responsif dengan warna utama Primary Orange `#FB8C00`, ikon Lucide, tata letak kartu bersih, dan umpan balik visual jelas.
4. **Kepatuhan Database UTS:** Database bernama `uts_25K10036` dan seluruh tabel wajib menggunakan prefix `t036_`.
5. **Batasan Data Awal:** Total record awal seeder dibatasi maksimal **16 baris secara keseluruhan**.

---

## 4. SISTEM USER LEVELING & PERAN

Sistem membagi pengguna ke dalam tiga tingkatan hak akses yang tersimpan dalam tabel `t036_roles`:

1. **Role 1 — Manajer (Level Tertinggi)**
   - Mengawasi keseluruhan inventaris gudang.
   - Mengelola akun pengguna (tambah, edit, ubah role, aktif/nonaktifkan akun).
   - Mengelola master data barang dan data supplier.
   - Melakukan pencatatan mutasi stok.
   - Mengakses laporan lengkap dan seluruh log audit sistem.
2. **Role 2 — Staf Gudang (Operasional Harian)**
   - Bertanggung jawab atas pergerakan fisik barang di gudang.
   - Melihat dan menambah/mengubah informasi katalog barang.
   - Melihat data supplier.
   - Mencatat transaksi barang masuk dan keluar.
   - Memantau peringatan stok minimum.
   - Melihat laporan inventaris.
   - Dilarang mengelola akun pengguna, menghapus barang, atau mengakses audit log.
3. **Role 3 — Auditor (Pemeriksaan Independen)**
   - Melakukan audit fisik dan kepatuhan sistem (*read-only*).
   - Melihat daftar barang, supplier, histori mutasi, dan laporan stok.
   - Memeriksa catatan audit log aktivitas.
   - Tidak diizinkan melakukan modifikasi data operasional apa pun.

---

## 5. MATRIKS HAK AKSES SISTEM (RBAC)

Keterangan: **C** = Create, **R** = Read, **U** = Update, **D** = Delete, **—** = Ditolak (HTTP 403)

| Modul | Manajer | Staf Gudang | Auditor | Aturan Penegakan |
|---|:---:|:---:|:---:|---|
| **Dashboard** | R | R | R | Tampilan widget statistik disesuaikan per role |
| **Kelola Pengguna** | CRUD* | — | — | Delete diimplementasikan sebagai deaktifasi akun; guard Manajer tunggal |
| **Data Barang** | CRUD | CRU | R | Hapus barang hanya Manajer; kuantitas stok diubah via mutasi |
| **Data Supplier** | CRUD | R | R | Hapus supplier dicegah bila ada barang terkait |
| **Mutasi Stok** | CR | CR | R | Mutasi tersimpan bersifat permanen (tanpa delete sembarangan) |
| **Laporan Inventaris**| R | R | R | Mendukung filter periode dan ekspor CSV |
| **Audit Log Aktivitas**| R | — | R | Bersifat read-only untuk pengawasan transparansi |
| **Profil Sendiri** | RU | RU | RU | Mengubah nama, kontak, password, dan upload foto |

> Apabila pengguna mencoba mengakses halaman URL di luar kewenangannya, sistem mengembalikan kode HTTP 403 dengan pesan:  
> **"Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini."**

---

## 6. PERANCANGAN DATABASE & ERD

Database MySQL yang digunakan adalah `uts_25K10036` dengan engine InnoDB. Seluruh tabel menggunakan prefix wajib `t036_`.

### 6.1 Daftar Tabel Database
1. `t036_roles`: Menyimpan tingkatan role pengguna (Manajer, Staf Gudang, Auditor).
2. `t036_users`: Menyimpan kredensial pengguna, profil foto, status aktif, dan role_id.
3. `t036_supplier`: Menyimpan data profil mitra pemasok barang inventaris.
4. `t036_barang`: Menyimpan katalog barang, batas stok minimum, satuan, lokasi, dan supplier_id.
5. `t036_mutasi_stok`: Mencatat riwayat transaksi pergerakan stok masuk/keluar, saldo sebelum/sesudah, dan petugas.
6. `t036_audit_logs`: Menyimpan jejak kronologis aktivitas penting pengguna di sistem.
7. `t036_personal_access_tokens`: Tabel token bearer API Laravel Sanctum dengan prefix proyek.
8. `t036_password_reset_tokens`: Tabel token pemulihan password Laravel.
9. `t036_migrations`: Tabel pencatat riwayat migrasi skema database.

### 6.2 Data Awal Seeder (Maksimal 16 Record Keseluruhan)
Sesuai batasan ketat UTS, seeder mengisi tepat **16 baris record**:
- `t036_roles`: 3 baris
- `t036_users`: 3 baris (Akun utama Leonard Sihol Pardede, Staf Budi Santoso, Auditor Siti Nurhaliza)
- `t036_supplier`: 3 baris
- `t036_barang`: 5 baris
- `t036_mutasi_stok`: 2 baris
- `t036_audit_logs`: 0 baris
- **Total: 16 baris.**

### 6.3 Diagram Hubungan Entitas (ERD)
*(Lihat rincian diagram Mermaid lengkap pada berkas `ERD.md`)*

---

## 7. IMPLEMENTASI APLIKASI WEB

Aplikasi diimplementasikan mengikuti standar arsitektur industri:
1. **Model & Relasi Eloquent:** Model `Role`, `User`, `Supplier`, `Barang`, `MutasiStok`, dan `AuditLog` mendefinisikan relasi `hasMany` dan `belongsTo`.
2. **Middleware Otorisasi:**
   - `CheckRole`: Memeriksa status keaktifan akun dan mencocokkan peran pengguna dengan parameter rute web.
   - `CheckApiRole`: Memeriksa otorisasi pada endpoint JSON REST API.
3. **Pencegahan Stok Negatif & Konkurensi:**
   ```php
   DB::transaction(function () use ($request, $user) {
       $barang = Barang::where('id', $request->barang_id)->lockForUpdate()->firstOrFail();
       if ($request->tipe_mutasi === 'keluar' && $barang->stok < $request->jumlah) {
           return back()->with('error', 'Stok tidak mencukupi!');
       }
       // Update stok & catat mutasi...
   });
   ```
4. **Upload Foto Profil Fisik:**
   File foto diunggah ke folder `public/uploads/profil/` dengan penamaan unik berbasis timestamp dan string acak, serta path relatifnya disimpan ke kolom `profile_photo` pada tabel `t036_users`. Validasi ketat diterapkan di backend (hanya format JPG, JPEG, PNG berukuran maksimal 2 MB).
5. **Footer Identitas Pembuat:**
   Setiap halaman web menampilkan teks wajib:
   **© 2026 Leonard Sihol Pardede – 25.K1.0036** beserta foto pembuat aplikasi.

---

## 8. PENJELASAN PENGGUNAAN CSS & DESAIN SISTEM

*(Dokumentasi teknis lengkap tersedia pada berkas `DOKUMENTASI_CSS.md`)*

Desain antarmuka berpedoman pada palet warna resmi:
- **Primary Orange (#FB8C00):** Diterapkan pada tombol utama, active navigation menu, badge kode barang, dan header penting.
- **Orange Dark (#E65100):** Digunakan untuk efek hover tombol dan aksen gradien.
- **Orange Soft (#FFF3E0):** Digunakan untuk background highlight kartu pengguna dan kotak informasi.
- **Background Utama (#F6F7F9) & Surface (#FFFFFF):** Memberikan tampilan bersih berstandar WMS.
- **Semantic Status Colors:** Hijau (`#2E7D32`) untuk barang masuk/stok aman, merah (`#D32F2F`) untuk barang keluar/stok habis, dan kuning (`#F9A825`) untuk peringatan stok kritis.
- **Responsivitas:** Mendukung tampilan Desktop (sidebar penuh), Tablet (drawer geser dengan overlay), dan Mobile (tata letak kartu adaptif).

---

## 9. IMPLEMENTASI RESTFUL API

REST API dibangun pada prefix `/api/` menggunakan Laravel Sanctum Bearer Token.

### 9.1 Endpoint Identitas Pembuat (WAJIB)
- **Endpoint:** `GET /api/identitas`
- **Header:** `Authorization: Bearer <TOKEN>`
- **Hasil JSON Dinamis:**
```json
{
  "nim": "25K10036",
  "nama": "Leonard Sihol Pardede",
  "foto": "http://127.0.0.1:8000/uploads/profil/leonard-25k10036.png",
  "aplikasi": "SiGudang – 036"
}
```

### 9.2 Koleksi Postman
Seluruh endpoint telah didokumentasikan dan dikemas dalam file **`POSTMAN_COLLECTION.json`** yang dilengkapi environment variable otomatis (`base_url`, `bearer_token`, `barang_id`).

---

## 10. HASIL PENGUJIAN SISTEM (TEST REPORT)

Pengujian dilakukan menggunakan rangkaian **Laravel Feature Tests (`tests/Feature/SiGudangTest.php`)** secara menyeluruh:

| No | Skenario Uji | Kategori | Hasil Uji | Status |
|:---:|---|---|---|:---:|
| 1 | Login berhasil dengan kredensial valid (`25K10036` / `password123`) | Authentication | Sesi terbuat, redirect dashboard | **PASS** |
| 2 | Login gagal dengan password salah | Authentication | Pesan error muncul, sesi ditolak | **PASS** |
| 3 | Verifikasi hash Bcrypt password di database | Authentication | Password tersimpan sebagai hash aman | **PASS** |
| 4 | Logout membersihkan seluruh sesi | Authentication | Sesi terhapus, redirect login | **PASS** |
| 5 | Akun nonaktif ditolak saat mencoba login | Authentication | Ditolak dengan pesan blokir akun | **PASS** |
| 6 | Manajer dapat mengakses manajemen pengguna | Authorization | Halaman `/pengguna` terbuka (HTTP 200) | **PASS** |
| 7 | Staf Gudang ditolak mengakses manajemen pengguna | Authorization | Akses ditolak (HTTP 403) | **PASS** |
| 8 | Auditor ditolak mengakses tambah barang | Authorization | Akses ditolak (HTTP 403) | **PASS** |
| 9 | Mutasi masuk menambah stok barang secara konsisten | Transaction | Saldo fisik bertambah & log tersimpan | **PASS** |
| 10 | Barang keluar ditolak jika melebihi stok yang ada | Transaction | Transaksi gagal, mencegah stok negatif | **PASS** |
| 11 | Upload foto profil format JPG/PNG berhasil | Upload | File tersimpan di `public/uploads/profil/` | **PASS** |
| 12 | Upload file non-gambar (PDF) ditolak | Upload | Validasi MIME mendeteksi & menolak | **PASS** |
| 13 | Login REST API menghasilkan Bearer Token Sanctum | REST API | Token dihasilkan (HTTP 200) | **PASS** |
| 14 | Endpoint identitas mengembalikan data & foto dinamis | REST API | Data NIM, nama, foto sesuai (HTTP 200) | **PASS** |
| 15 | Akses API tanpa token menghasilkan HTTP 401 | REST API | Akses ditolak (HTTP 401 Unauthenticated) | **PASS** |

**Ringkasan Pengujian:** 15 Test Case Berhasil (100% Passed, 46 Assertions).

---

## 11. BUKTI IMPLEMENTASI & TANGKAPAN LAYAR (SCREENSHOTS)

Berikut adalah panduan tangkapan layar implementasi nyata aplikasi SiGudang – 036:

### A. Halaman Login
Menampilkan logo SiGudang – 036, form login, toggle intip password, quick login tester 3 role, serta foto identitas Leonard Sihol Pardede di footer kartu login.
*(Screenshot: `login_page.png`)*

### B. Dashboard Masing-Masing Role
1. **Dashboard Manajer:** Menampilkan ringkasan total barang, total stok, stok menipis, mitra supplier, pengguna aktif, grafik transaksi bulanan masuk/keluar, dan live mutasi feed.
2. **Dashboard Staf Gudang:** Menampilkan ringkasan operasional stok, barang masuk terbaru, barang keluar terbaru, dan tombol cepat catat mutasi.
3. **Dashboard Auditor:** Menampilkan ringkasan inventaris, grafik aktivitas gudang, dan log aktivitas sistem terkini secara read-only.

### C. Manajemen Data Barang (CRUD)
Tampilan katalog barang dengan indikator stok aman (hijau), stok kritis (kuning), dan stok habis (merah), filter kategori, filter supplier, serta form edit yang mengunci pengubahan kuantitas langsung.

### D. Manajemen Data Supplier (CRUD)
Tabel mitra supplier, form pendaftaran pemasok, dan proteksi penghapusan jika supplier masih memiliki barang aktif.

### E. Manajemen Mutasi Stok & Pencegahan Stok Negatif
Formulir pencatatan mutasi yang secara interaktif menampilkan stok tersedia dan menampilkan peringatan jika kuantitas keluar melebihi stok fisik. Bukti transaksi mutasi menampilkan komparasi saldo sebelum dan sesudah mutasi.

### F. Pengujian Akses Ditolak (HTTP 403)
Tampilan halaman 403 kustom dengan pesan wajib: *"Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini."*

### G. Profil Pengguna & Unggah Foto
Halaman profil tempat pengguna dapat melihat informasi akun, mengganti password, dan mengunggah foto profil fisik berukuran maksimal 2 MB dengan preview dinamis.

### H. Struktur Database MySQL (`uts_25K10036`)
Struktur seluruh tabel di phpMyAdmin / MySQL CLI yang menunjukkan seluruh tabel menggunakan prefix `t036_` dan data awal tepat 16 baris record.

### I. Pengujian RESTful API di Postman
Tangkapan layar pengujian login API, eksekusi Bearer Token, pengambilan data barang, serta endpoint wajib `GET /api/identitas`.

---

## 12. KESIMPULAN

Aplikasi **SiGudang – 036** telah berhasil dirancang dan dibangun secara menyeluruh memenuhi seluruh spesifikasi dan kriteria Ujian Tengah Semester (UTS) 2026:
1. Identitas pembuat (**Leonard Sihol Pardede**, NIM **25K10036**, Tahun **2026**) tersemat secara konsisten pada judul tab browser, navbar, footer seluruh halaman, profil, dan endpoint API.
2. Seluruh tabel dalam database `uts_25K10036` menggunakan prefix `t036_` tanpa kecuali.
3. Total data awal seeder dibatasi secara ketat menjadi **tepat 16 record keseluruhan**.
4. Pembagian tiga role pengguna (**Manajer**, **Staf Gudang**, **Auditor**) berfungsi dengan matriks hak akses RBAC yang ditegakkan di sisi antarmuka, routing middleware, controller, dan API.
5. Transaksi mutasi stok terbukti aman dari *race condition* dan berhasil mencegah timbulnya nilai stok negatif melalui *pessimistic row locking*.
6. REST API berjalan dengan autentikasi Sanctum Bearer Token dan menyajikan endpoint identitas pembuat dinamis.
7. Seluruh berkas pendukung mulai dari file SQL ekspor (`uts_25K10036.sql`), dokumentasi CSS (`DOKUMENTASI_CSS.md`), matriks akses (`MATRIKS_HAK_AKSES.md`), diagram ERD (`ERD.md`), dokumentasi API (`DOKUMENTASI_API.md`), hingga koleksi Postman (`POSTMAN_COLLECTION.json`) telah disediakan secara lengkap.

Sistem **SiGudang – 036** siap didemonstrasikan dan dipertanggungjawabkan secara teknis.

---
**Semarang / Jakarta, 2026**  
*Mahasiswa Pengembang,*  

**Leonard Sihol Pardede**  
NIM: 25K10036
