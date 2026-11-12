# DOKUMENTASI RESTFUL JSON API
## SiGudang – 036 | Sistem Manajemen Inventaris Barang

> **Penulis:** Leonard Sihol Pardede  
> **NIM:** 25K10036  
> **Base URL Lokal:** `http://127.0.0.1:8000/api`  
> **Format Data:** JSON (`Content-Type: application/json`, `Accept: application/json`)  
> **Sistem Autentikasi:** Laravel Sanctum (Header `Authorization: Bearer <TOKEN>`)  
> **Tahun:** 2026  

---

## 1. Ikhtisar Endpoint REST API

| Method | Endpoint | Fungsi | Role yang Diizinkan | Autentikasi |
|---|---|---|---|:---:|
| `POST` | `/api/login` | Login pengguna & mendapatkan token Bearer | Publik (Semua) | — |
| `POST` | `/api/logout` | Mencabut dan menghapus Bearer Token aktif | Semua yang login | Bearer Token |
| `GET` | `/api/me` | Mengambil data akun pengguna yang login | Semua yang login | Bearer Token |
| `GET` | `/api/identitas` | **Identitas Pembuat Aplikasi (WAJIB)** | Semua yang login | Bearer Token |
| `GET` | `/api/barang` | Mendapatkan daftar seluruh barang & filter | Manajer, Staf, Auditor | Bearer Token |
| `GET` | `/api/barang/{id}` | Mendapatkan detail spesifikasi barang | Manajer, Staf, Auditor | Bearer Token |
| `POST` | `/api/barang` | Menambah barang baru ke inventaris | Manajer, Staf Gudang | Bearer Token |
| `PUT` | `/api/barang/{id}` | Mengubah data barang secara lengkap | Manajer, Staf Gudang | Bearer Token |
| `PATCH`| `/api/barang/{id}` | Mengubah sebagian atribut data barang | Manajer, Staf Gudang | Bearer Token |
| `DELETE`| `/api/barang/{id}`| Menghapus barang (jika belum bermutasi)| Manajer Saja | Bearer Token |
| `GET` | `/api/supplier` | Mendapatkan daftar supplier aktif | Manajer, Staf, Auditor | Bearer Token |
| `GET` | `/api/mutasi` | Mengambil riwayat mutasi pergerakan stok | Manajer, Staf, Auditor | Bearer Token |
| `POST` | `/api/mutasi` | Mencatat transaksi mutasi stok masuk/keluar | Manajer, Staf Gudang | Bearer Token |

---

## 2. Standar Respons & Status Code HTTP

Seluruh respons API mengembalikan struktur JSON konsisten:

### Respons Berhasil (HTTP 200 / 201)
```json
{
  "success": true,
  "message": "Pesan deskripsi keberhasilan operasi",
  "data": {}
}
```

### Respons Akses Ditolak (HTTP 403)
```json
{
  "success": false,
  "message": "Akses ditolak. Anda tidak memiliki izin untuk mengakses resource ini."
}
```

### Respons Token Tidak Valid / Belum Login (HTTP 401)
```json
{
  "success": false,
  "message": "Belum login atau token tidak valid."
}
```

### Respons Validasi Gagal (HTTP 422)
```json
{
  "success": false,
  "message": "Validasi input gagal.",
  "errors": {
    "kode_barang": ["Kode barang sudah digunakan."]
  }
}
```

### Respons Data Tidak Ditemukan (HTTP 404)
```json
{
  "success": false,
  "message": "Data barang tidak ditemukan."
}
```

---

## 3. Rincian Endpoint Utama

### 3.1. Autentikasi API: Login Pengguna
- **Method:** `POST`
- **URL:** `/api/login`
- **Request Body:**
```json
{
  "username": "25K10036",
  "password": "password123"
}
```
- **Response Sukses (HTTP 200):**
```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token": "1|qXyK6mP9... (plainTextToken)",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "username": "25K10036",
      "name": "Leonard Sihol Pardede",
      "email": "leonard.pardede@sigudang.test",
      "role": "Manajer",
      "profile_photo": "http://127.0.0.1:8000/uploads/profil/leonard-25k10036.png"
    }
  }
}
```

---

### 3.2. Endpoint Identitas Pembuat Aplikasi (WAJIB UTS)
- **Method:** `GET`
- **URL:** `/api/identitas`
- **Header:** `Authorization: Bearer <TOKEN>`
- **Deskripsi:** Mengembalikan identitas resmi mahasiswa pembuat aplikasi dengan URL foto dinamis yang diambil langsung dari database `t036_users`.
- **Response Sukses (HTTP 200):**
```json
{
  "nim": "25K10036",
  "nama": "Leonard Sihol Pardede",
  "foto": "http://127.0.0.1:8000/uploads/profil/leonard-25k10036.png",
  "aplikasi": "SiGudang – 036"
}
```

---

### 3.3. Endpoint Barang

#### A. Ambil Daftar Barang
- **Method:** `GET`
- **URL:** `/api/barang` (Dapat menerima query params: `?q=scanner&kategori=Elektronik&supplier_id=3`)
- **Header:** `Authorization: Bearer <TOKEN>`
- **Response Sukses (HTTP 200):**
```json
{
  "success": true,
  "message": "Daftar data barang berhasil diambil.",
  "data": [
    {
      "id": 1,
      "kode_barang": "BRG-001",
      "nama_barang": "Barcode Scanner Wireless 2D",
      "kategori": "Elektronik & Perangkat",
      "satuan": "Unit",
      "stok": 45,
      "stok_minimum": 10,
      "lokasi_penyimpanan": "Rak A-01",
      "supplier_id": 3,
      "deskripsi": "Scanner barcode nirkabel frekuensi 2.4GHz...",
      "supplier": {
        "id": 3,
        "nama_supplier": "PT Sumber Elektronik Mandiri"
      }
    }
  ]
}
```

#### B. Tambah Barang Baru
- **Method:** `POST`
- **URL:** `/api/barang`
- **Header:** `Authorization: Bearer <TOKEN>` (Hanya Manajer & Staf Gudang)
- **Request Body:**
```json
{
  "kode_barang": "BRG-006",
  "nama_barang": "Label Barcode Direct Thermal 80x50",
  "kategori": "Packaging",
  "satuan": "Roll",
  "stok": 50,
  "stok_minimum": 15,
  "lokasi_penyimpanan": "Rak A-04",
  "supplier_id": 1,
  "deskripsi": "Sticker barcode thermal tahan air isi 1000 pcs per roll."
}
```
- **Response Sukses (HTTP 201 Created):**
```json
{
  "success": true,
  "message": "Data barang berhasil ditambahkan.",
  "data": {
    "id": 6,
    "kode_barang": "BRG-006",
    "nama_barang": "Label Barcode Direct Thermal 80x50",
    "stok": 50
  }
}
```

---

### 3.4. Endpoint Mutasi Stok

#### A. Catat Transaksi Mutasi Stok
- **Method:** `POST`
- **URL:** `/api/mutasi`
- **Header:** `Authorization: Bearer <TOKEN>` (Hanya Manajer & Staf Gudang)
- **Request Body (Contoh Barang Keluar):**
```json
{
  "barang_id": 1,
  "tipe_mutasi": "keluar",
  "jumlah": 5,
  "tanggal_mutasi": "2026-03-15 10:00:00",
  "keterangan": "Distribusi scanner barcode ke packing station 2"
}
```
- **Response Sukses (HTTP 201 Created):**
```json
{
  "success": true,
  "message": "Mutasi stok keluar berhasil dicatat. Stok terkini: 40 Unit.",
  "data": {
    "id": 3,
    "barang_id": 1,
    "tipe_mutasi": "keluar",
    "jumlah": 5,
    "stok_sebelum": 45,
    "stok_sesudah": 40,
    "keterangan": "Distribusi scanner barcode ke packing station 2"
  }
}
```

#### B. Penolakan Mutasi Stok Negatif (HTTP 400 Bad Request)
Jika permintaan keluar melebihi stok fisik saat itu:
```json
{
  "success": false,
  "message": "Stok tidak mencukupi. Stok saat ini 45 Unit, sedangkan permintaan pengeluaran 100 Unit."
}
```

---

## 4. Contoh Pengujian Menggunakan cURL

### 1. Login untuk Mendapatkan Token
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"username":"25K10036","password":"password123"}'
```

### 2. Mengakses Endpoint Identitas Wajib
```bash
curl -X GET http://127.0.0.1:8000/api/identitas \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 3. Mengambil Data Barang
```bash
curl -X GET http://127.0.0.1:8000/api/barang \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---
**© 2026 Leonard Sihol Pardede – 25.K1.0036**
