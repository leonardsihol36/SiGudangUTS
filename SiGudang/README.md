# SiGudang – 036 | Sistem Manajemen Inventaris Barang Gudang

> **Proyek Ujian Tengah Semester (UTS) Pemrograman Web Terintegrasi 2026**  
> **Aplikasi:** SiGudang – 036  
> **Pengembang / Mahasiswa:** Leonard Sihol Pardede  
> **NIM:** 25K10036  
> **Database:** `uts_25K10036`  
> **Prefix Tabel:** `t036_`  
> **Tahun Rilis:** 2026  

---

## 1. Deskripsi Aplikasi

**SiGudang – 036** adalah sistem manajemen inventaris barang berbasis web (Warehouse Management System / WMS) tingkat enterprise yang dirancang untuk mengelola stok fisik, transaksi pergerakan barang (masuk/keluar), kemitraan supplier, manajemen akun pengguna bertingkat, serta audit log aktivitas secara menyeluruh dan real-time.

Aplikasi ini mengimplementasikan kontrol akses berbasis peran (*Role-Based Access Control / RBAC*) dengan 3 tingkat otoritas (Manajer, Staf Gudang, Auditor), pencatatan mutasi atomik dengan *row locking* untuk mencegah stok negatif, antarmuka responsif bernuansa *Primary Orange (#FB8C00)*, serta RESTful JSON API yang diamankan menggunakan token bearer Laravel Sanctum.

---

## 2. Identitas Proyek & Ketentuan Wajib

| Ketentuan | Nilai Spesifikasi | Keterangan |
|---|---|---|
| **Nama Aplikasi** | SiGudang – 036 | Ditampilkan pada `<title>` tab browser & navbar |
| **NIM** | 25K10036 | NIM resmi pembuat |
| **Nama Pembuat** | Leonard Sihol Pardede | Ditampilkan pada footer seluruh halaman |
| **Database** | `uts_25K10036` | Database MySQL proyek |
| **Prefix Tabel** | `t036_` | Wajib untuk seluruh tabel tanpa pengecualian |
| **Tahun** | 2026 | Tahun operasional & hak cipta |
| **Role Tertinggi** | Manajer | Akun pengelola penuh sistem |
| **Footer Wajib** | `© 2026 Leonard Sihol Pardede – 25.K1.0036` | Disertai foto profil pembuat proporsional |

---

## 3. Tech Stack

- **Backend:** Laravel 10.x (PHP 8.1+)
- **Frontend:** Laravel Blade Templating Engine
- **Database:** MySQL (Engine InnoDB, Charset utf8mb4)
- **Styling:** Custom Modular CSS Design System (`public/css/sigudang.css`) dengan Palet Warna SiGudang (Primary Orange `#FB8C00`)
- **Interaktivitas:** Vanilla JavaScript & Alpine.js
- **Visualisasi & Grafik:** Chart.js
- **Ikonografi:** Lucide Icons
- **Autentikasi Web:** Laravel Session Authentication (Bcrypt Hashing, CSRF Guard)
- **Autentikasi API:** Laravel Sanctum (Bearer Token)
- **Lingkungan Pengembangan:** Laragon Localhost (Windows x64)

---

## 4. Kredensial Login Pengguna (3 Role)

Aplikasi telah dilengkapi data awal (seeder) sebanyak **tepat 16 record** yang mencakup akun untuk masing-masing role:

| Role | Username | Password | Nama Lengkap | Deskripsi Hak Akses |
|---|---|---|---|---|
| **Manajer (Utama)** | `25K10036` | `password123` | Leonard Sihol Pardede | Akses penuh (CRUD Pengguna, Barang, Supplier, Mutasi, Laporan, Audit Log) |
| **Staf Gudang** | `staf036` | `password123` | Budi Santoso | Operasional harian (CRU Barang, R Supplier, CR Mutasi, R Laporan) |
| **Auditor** | `auditor036` | `password123` | Siti Nurhaliza | Pengawasan (Read-Only seluruh data inventaris & Audit Log) |

---

## 5. Kebutuhan Sistem

- PHP `>= 8.1` dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd`
- MySQL Server `>= 8.0` atau MariaDB `>= 10.4`
- Composer `>= 2.2`
- Web Server: Apache / Nginx / Built-in PHP CLI

---

## 6. Panduan Instalasi & Konfigurasi

### Langkah 1: Clone atau Buka Folder Proyek
Pastikan direktori proyek berada pada lingkungan web server lokal, misalnya:
```bash
cd E:\LARAGON\laragon\www\SiGudang
```

### Langkah 2: Salin File Konfigurasi `.env`
Jika file `.env` belum ada, salin dari `.env.example`:
```bash
cp .env.example .env
```
Pastikan pengaturan koneksi database pada `.env` adalah sebagai berikut:
```env
APP_NAME="SiGudang – 036"
APP_ENV=local
APP_KEY=base64:UtUhDwZY86lnn5lXd+Ai87tyQJBPTik0uxLQWYrLrS8=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=uts_25K10036
DB_USERNAME=root
DB_PASSWORD=
```

### Langkah 3: Install Dependensi Composer
```bash
composer install
```

---

## 7. Inisialisasi Database (PILIH SALAH SATU)

> [!IMPORTANT]
> Pilih **salah satu metode** inisialisasi di bawah ini. Jangan menjalankan kedua metode secara berurutan pada database yang sudah terisi untuk mencegah duplikasi data!

### Metode A: Menggunakan Laravel Migration & Seeder (Rekomendasi)
Metode ini akan membuat seluruh tabel dengan prefix `t036_` dan mengisi tepat 16 data awal:
```bash
# 1. Buat database di MySQL (bila belum ada)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS uts_25K10036;"

# 2. Jalankan migrasi dan seeder
php artisan migrate:fresh --seed
```

### Metode B: Mengimpor File SQL (`uts_25K10036.sql`)
File SQL mandiri telah disediakan pada root direktori:
```bash
mysql -u root uts_25K10036 < uts_25K10036.sql
```
Atau buka **phpMyAdmin** (`http://localhost/phpmyadmin`), buat database `uts_25K10036`, lalu klik tab **Import** dan pilih file `uts_25K10036.sql`.

---

## 8. Menjalankan Aplikasi

Jalankan development server Laravel:
```bash
php artisan serve --port=8000
```
Buka browser pada alamat:
👉 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 9. Struktur Tabel & Batasan 16 Record Seeder

Sesuai ketentuan ketat UTS, total baris record awal yang dimasukkan oleh seeder aplikasi adalah **maksimal 16 baris secara keseluruhan**:

| Nama Tabel | Deskripsi | Jumlah Baris Awal |
|---|---|:---:|
| `t036_roles` | Master level pengguna (Manajer, Staf Gudang, Auditor) | 3 |
| `t036_users` | Data akun pengguna aktif berpassword bcrypt | 3 |
| `t036_supplier` | Mitra penyedia logistik dan barang | 3 |
| `t036_barang` | Master katalog barang fisik dan batas minimum | 5 |
| `t036_mutasi_stok` | Transaksi awal barang masuk & barang keluar | 2 |
| `t036_audit_logs` | Jejak kronologis aktivitas (awal kosong) | 0 |
| **TOTAL KESELURUHAN** | | **16** |

---

## 10. Pengujian Otomatis (Feature Tests)

Proyek ini telah dilengkapi dengan 15 skenario pengujian otomatis end-to-end yang mencakup autentikasi, otorisasi RBAC, transaksi stok, validasi upload file, dan REST API. Jalankan pengujian kapan saja dengan perintah:

```bash
php artisan test --filter=SiGudangTest
```
*Hasil: 15 passed, 46 assertions.*

---

## 11. Pengujian REST API

Aplikasi menyediakan REST API berbasis JSON pada prefix `/api/` yang diamankan menggunakan Laravel Sanctum Bearer Token.

Koleksi Postman siap pakai telah disediakan di file:
📁 **`POSTMAN_COLLECTION.json`**

### Endpoint Identitas Wajib:
- **URL:** `GET /api/identitas`
- **Header:** `Authorization: Bearer <TOKEN>`
- **Response Contoh:**
```json
{
  "nim": "25K10036",
  "nama": "Leonard Sihol Pardede",
  "foto": "http://127.0.0.1:8000/uploads/profil/leonard-25k10036.png",
  "aplikasi": "SiGudang – 036"
}
```

---

## 12. Berkas Dokumentasi Tambahan

1. **`DOKUMENTASI_CSS.md`** — Penjelasan rinci arsitektur CSS, palet warna, komponen, responsivitas, dan efek visual.
2. **`MATRIKS_HAK_AKSES.md`** — Matriks RBAC per modul dan aksi, implementasi middleware, serta proteksi HTTP 403.
3. **`ERD.md`** — Diagram Entity Relationship format Mermaid beserta spesifikasi relasi foreign key & kardinalitas.
4. **`DOKUMENTASI_API.md`** — Spesifikasi teknis endpoint REST API, payload JSON, status code, dan cURL examples.
5. **`POSTMAN_COLLECTION.json`** — File koleksi Postman importable dengan *environment variables*.
6. **`LAPORAN_UTS.md`** — Draft laporan komprehensif ujian tengah semester lengkap dengan bab pendahuluan hingga kesimpulan.

---

**© 2026 Leonard Sihol Pardede – 25.K1.0036**  
*SiGudang – 036: Sistem Manajemen Inventaris Barang Gudang*
