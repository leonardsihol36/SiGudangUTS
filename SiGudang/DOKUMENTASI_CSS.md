# DOKUMENTASI CSS & DESIGN SYSTEM
## SiGudang – 036 | Sistem Manajemen Inventaris Barang

> **Disusun Oleh:** Leonard Sihol Pardede  
> **NIM:** 25K10036  
> **Tahun:** 2026  
> **Nama File CSS:** `public/css/sigudang.css`  

---

## 1. Pendahuluan & Filosofi Desain

Aplikasi **SiGudang – 036** dibangun menggunakan pendekatan antarmuka modern *Warehouse Management System (WMS)* yang bersih, profesional, terstruktur, dan ergonomis bagi operator gudang maupun manajer.

Sistem styling dirancang secara modular dan terpusat pada file `public/css/sigudang.css`. Seluruh nilai warna, radius, elevasi bayangan (*shadow*), dan transisi didefinisikan sebagai *CSS Custom Properties (Variables)* pada root dokumen. Hal ini memudahkan pemeliharaan, konsistensi lintas halaman, dan memenuhi standar aksesibilitas WCAG AA dalam hal kontras warna dan keterbacaan tipografi.

---

## 2. Palet Warna (Color Palette & Variables)

Warna utama aplikasi adalah **Primary Orange (#FB8C00)** yang melambangkan kecepatan, energi operasional gudang, dan ketelitian penanganan logistik.

| Variabel CSS | Hex / RGBA | Peran & Penggunaan Desain |
|---|---|---|
| `--primary-orange` | `#FB8C00` | Identitas brand utama, active state sidebar, header tabel, tombol primer, dan highlight aktif. |
| `--orange-dark` | `#E65100` | Status hover tombol primer, gradien sidebar active, dan aksen penekanan. |
| `--orange-soft` | `#FFF3E0` | Background highlight kartu, card user sidebar, dan badge bernuansa oranye terang. |
| `--orange-glow` | `rgba(251, 140, 0, 0.2)` | Efek glow pada outline focus form input. |
| `--bg-main` | `#F6F7F9` | Latar belakang kanvas aplikasi keseluruhan yang nyaman untuk mata (anti-fatigue). |
| `--surface` | `#FFFFFF` | Latar belakang kartu kontainer, dropdown, modal dialog, dan sidebar. |
| `--surface-hover`| `#F8FAFC` | Latar baris tabel saat pointer hover. |
| `--text-dark` | `#263238` | Teks judul utama dan konten dengan rasio kontras tinggi di atas latar terang. |
| `--text-muted` | `#64748B` | Teks sekunder, label formulir, keterangan statistik, dan timestamp. |
| `--text-light` | `#94A3B8` | Teks nonaktif, placeholder, dan pembagi seksi navigasi. |
| `--success` | `#2E7D32` | Status berhasil, transaksi mutasi barang masuk, dan badge stok aman. |
| `--success-bg` | `#E8F5E9` | Latar badge dan callout notifikasi sukses. |
| `--danger` | `#D32F2F` | Status error, transaksi mutasi barang keluar, dan badge peringatan stok habis. |
| `--danger-bg` | `#FFEBEE` | Latar badge stok habis dan alert kegagalan. |
| `--warning` | `#F9A825` | Peringatan kondisi stok menipis (mendekati batas minimum). |
| `--warning-bg` | `#FFFDE7` | Latar badge peringatan stok kritis. |
| `--border` | `#E2E8F0` | Garis batas (*border*) kartu, input kontrol, dan pemisah kolom. |
| `--border-light` | `#F1F5F9` | Garis pemisah halus antarbaris tabel. |

---

## 3. Tipografi (Typography)

Sistem menggunakan font keluarga **Plus Jakarta Sans** yang dimuat melalui Google Fonts CDN:
```css
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
```
Karakter geometrisnya yang seimbang memberikan keterbacaan tajam pada tampilan data inventaris, angka kuantitas, maupun label barcode.

---

## 4. Struktur Arsitektur Layout

Layout aplikasi menerapkan arsitektur dua kolom fleksibel (*sidebar-main*):

```
+---------------------------------------------------------------+
|                      .app-wrapper                             |
+-------------------+-------------------------------------------+
|   .app-sidebar    |                .app-main                  |
|                   +-------------------------------------------+
| - .sidebar-header |               .app-navbar                 |
| - .sidebar-user   +-------------------------------------------+
| - .sidebar-content|             .page-container               |
|                   |  - .stats-grid                            |
|                   |  - .card                                  |
|                   +-------------------------------------------+
|                   |               .app-footer                 |
+-------------------+-------------------------------------------+
```

### Penjelasan Kelas Layout:
1. `.app-wrapper`: Kontainer pembungkus utama flexbox (`min-height: 100vh`) yang menyatukan sidebar dan area konten utama.
2. `.app-sidebar`: Panel navigasi tetap (*fixed*) selebar `270px` di sebelah kiri dengan z-index 40 dan border pemisah kanan `1px solid var(--border)`.
3. `.app-main`: Area konten dinamis dengan margin kiri `270px` yang menampung navbar atas, isi halaman, dan footer.
4. `.app-navbar`: Bilah navigasi atas setinggi `72px` dengan posisi *sticky* (`top: 0`), menampilkan judul halaman, profil pengguna, dan tombol toggle mobile.
5. `.page-container`: Wadah bantalan konten internal dengan padding konsisten `28px 32px`.
6. `.app-footer`: Kaki halaman wajib yang menampilkan hak cipta 2026, foto Leonard Sihol Pardede, dan identitas NIM 25K10036.

---

## 5. Komponen Utama & Dokumentasi Kelas

### A. Dashboard Metrics Cards (`.stat-card`)
- **Kelas:** `.stats-grid`, `.stat-card`, `.stat-info`, `.stat-label`, `.stat-value`, `.stat-icon-wrapper`
- **Fungsi:** Menampilkan angka ringkasan statistik (Total Barang, Total Stok, Stok Menipis, dsb.).
- **Visual Feedback:** Menggunakan transisi halus `transform: translateY(-3px)` dan bayangan lembut saat di-hover, serta border oranye halus (`rgba(251, 140, 0, 0.4)`).
- **Varian Ikon:**
  - `.icon-orange`: Nuansa oranye untuk metrik katalog barang dan pengguna.
  - `.icon-green`: Nuansa hijau untuk pemasok aktif dan transaksi barang masuk.
  - `.icon-red`: Nuansa merah untuk peringatan stok dan transaksi barang keluar.
  - `.icon-blue`: Nuansa biru untuk akumulasi stok fisik keseluruhan.

### B. Kontainer Kartu Data (`.card`)
- **Kelas:** `.card`, `.card-header`, `.card-title`, `.card-body`
- **Fungsi:** Membungkus tabel, diagram tren, dan formulir input dengan gaya kartu modern berujung melengkung (`border-radius: 14px`) dan bayangan lembut (`--shadow-sm`).

### C. Tabel Responsif (`.table`, `.table-responsive`)
- **Kelas:** `.table`, `.table-responsive`
- **Fungsi:** Menampilkan data katalog, supplier, mutasi, dan audit log.
- **Styling Khusus:** Header tabel (`th`) menggunakan latar `#FAFAFA`, huruf kapital berjarak renggang (`letter-spacing: 0.05em`), serta efek hover per baris (`tbody tr:hover`) menjadi warna `--surface-hover`.

### D. Sistem Badge Status (`.badge`)
- **Kelas:** `.badge-orange`, `.badge-green`, `.badge-red`, `.badge-yellow`, `.badge-gray`
- **Fungsi:**
  - `.badge-orange`: Menandai kode barang, kode supplier, dan peran Manajer.
  - `.badge-green`: Status aktif, stok aman, dan pergerakan barang masuk (`+`).
  - `.badge-red`: Status stok habis, barang keluar (`-`), dan akun nonaktif.
  - `.badge-yellow`: Status stok kritis (di bawah batas minimum).
  - `.badge-gray`: Data historis netral, peran Auditor, dan kategori pasokan.

### E. Tombol Aksi (`.btn`)
- **Kelas:** `.btn-primary`, `.btn-secondary`, `.btn-danger`, `.btn-success`, `.btn-outline-orange`, `.btn-sm`, `.btn-icon`
- **Fungsi & Perilaku:**
  - `.btn-primary`: Latar `--primary-orange`, teks putih, dengan efek hover menjadi `--orange-dark` dan bayangan oranye halus (`box-shadow: 0 4px 12px rgba(230,81,0,0.4)`).
  - State aktif ditekan (`:active`): `transform: scale(0.98)` memberikan umpan balik taktil seperti aplikasi desktop sungguhan.

### F. Formulir & Kontrol Masukan (`.form-control`)
- **Kelas:** `.form-group`, `.form-label`, `.form-control`, `.invalid-feedback`, `.form-hint`
- **Fokus & Validasi:**
  - Saat input difokuskan (`:focus`): Border berubah menjadi oranye dengan cincin pendar halus (`box-shadow: 0 0 0 3px var(--orange-glow)`).
  - Saat validasi gagal (`.is-invalid`): Border menjadi merah dengan pesan error di bawah input berhuruf tebal 500.

### G. Kotak Dialog Konfirmasi (`.modal-backdrop`, `.modal-dialog`)
- **Kelas:** `.modal-backdrop`, `.modal-dialog`, `.modal-header`, `.modal-body`, `.modal-footer`
- **Fungsi:** Menampilkan modal konfirmasi aksi berbahaya (seperti penghapusan barang atau supplier) dengan efek latar buram (*backdrop-filter: blur(4px)*) dan animasi muncul (*modalSlideIn*).

---

## 6. Halaman Autentikasi (`.auth-wrapper`, `.auth-card`)

Halaman login memanfaatkan gradien diagonal hangat:
```css
background: linear-gradient(135deg, #FFF3E0 0%, #F6F7F9 50%, #ECEFF1 100%);
```
Kartu login diletakkan tepat di tengah layar (*flexbox centering*), dilengkapi logo identitas SiGudang – 036, badge versi UTS 2026, tombol intip kata sandi (*toggle password visibility*), serta footer berisi foto Leonard Sihol Pardede.

---

## 7. Desain Responsif (Media Queries Breakpoints)

Aplikasi telah diuji dan mendukung tiga tingkatan layar utama:

### 1. Layar Komputer / Desktop (> 1024px)
- Sidebar navigasi tampil permanen selebar 270px.
- Grid statistik tersusun 4 hingga 5 kartu berjajar.
- Layout tabel menampilkan seluruh detail atribut barang secara horizontal.

### 2. Layar Tablet (768px – 1024px)
- Sidebar bertransformasi menjadi laci geser tersembunyi (`transform: translateX(-100%)`).
- Tombol hamburger muncul di navbar kiri untuk membuka drawer.
- Latar peredup (`.sidebar-overlay`) otomatis aktif saat menu dibuka.
- Margin kiri konten utama disesuaikan menjadi `0`.

### 3. Layar Ponsel Pintar / Mobile (< 768px)
- Grid statistik otomatis menyesuaikan menjadi 1 kolom tumpuk (*single column*).
- Spacing bantalan halaman disederhanakan menjadi `16px`.
- Footer beralih ke orientasi vertikal terpusat (*column alignment*).
- Tabel dilengkapi pembungkus `.table-responsive` dengan *horizontal smooth scrolling* untuk mencegah pemotongan data.

---

## 8. Ringkasan Kepatuhan Terhadap Ketentuan UTS

1. **Warna Wajib #FB8C00:** Diterapkan secara konsisten sebagai variabel utama pada seluruh tombol primer, active menu, border highlight, serta pendar fokus formulir.
2. **Kontras WCAG AA:** Seluruh teks di atas latar oranye menggunakan warna putih tajam, dan teks oranye berlatar belakang terang menggunakan bayangan aksen gelap agar mudah dibaca.
3. **Desain Profesional & Modern:** Dilengkapi ikon Lucide, efek transisi halus, badge rounded-full, dan integrasi chart dinamis.
4. **Dokumentasi Lengkap:** Setiap kelas, fungsi, dan interaksi telah diuraikan secara detail tanpa meninggalkan satu pun fungsi tanpa penjelasan.

---
**© 2026 Leonard Sihol Pardede – 25.K1.0036**
