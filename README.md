# Aplikasi Akuntansi - Trans Berjaya Khatulistiwa (TBK)

Aplikasi manajemen keuangan dan buku besar akuntansi (*general ledger*) profesional berbasis web yang dibangun dengan arsitektur terpisah antara **Laravel 13 REST API (Backend)** dan **Nuxt 3 (Frontend)**. 

Sistem ini dioptimalkan khusus untuk menangani skala data transaksi besar (diuji dengan 150.000+ data transaksi) dengan performa tinggi, validasi aturan akuntansi yang ketat, visualisasi analitik interaktif, serta kemampuan ekspor laporan profesional.

---

## 🛠️ Tech Stack

### Backend
* **PHP:** `8.3`
* **Framework:** Laravel `13`
* **Database:** MySQL `9` (Mendukung pengindeksan performa tinggi)
* **ORM:** Laravel Eloquent ORM
* **Format API:** REST API (JSON)
* **Pengujian:** PHPUnit / Laravel Testing Suite
* **Zona Waktu:** Asia/Jakarta (WIB) untuk pencatatan riwayat audit.

### Frontend
* **Node.js:** `22 LTS`
* **Framework:** Nuxt `3` (Vue 3, Nuxt Composables, SSR-compatible)
* **Bahasa Pemrograman:** TypeScript
* **Styling (CSS):** Tailwind CSS & Custom CSS `@media print`
* **Visualisasi Grafik:** Chart.js (diintegrasikan secara aman lewat pemuatan dinamis)

---

## 📐 Arsitektur Sistem

Aplikasi ini menggunakan arsitektur pemisahan tanggung jawab penuh antara frontend dan backend:

```
                    ┌──────────────────────┐
                    │      Nuxt 3 FE       │
                    │ Vue 3 + TypeScript   │
                    └──────────┬───────────┘
                               │
                          REST API
                               │
                    ┌──────────▼───────────┐
                    │    Laravel 13 API    │
                    │                      │
                    │ Controllers          │
                    │ Form Requests        │
                    │ Services             │
                    │ Models               │
                    └──────────┬───────────┘
                               │
                           Eloquent
                               │
                    ┌──────────▼───────────┐
                    │        MySQL         │
                    └──────────────────────┘
```

Frontend bertanggung jawab penuh atas presentasi visual dan interaksi pengguna, sedangkan aturan bisnis (*business rules*) serta perhitungan keuangan dihitung sepenuhnya oleh backend guna mencegah manipulasi.

---

## 🗄️ Desain Database & Aturan Bisnis

Relasi utama antar entitas di dalam database:

```
Category (Kategori)
   │
   │ 1:N
   ▼
Chart of Account (Akun COA)
   │
   │ 1:N
   ▼
Transaction (Transaksi Buku Besar)
```

### 1. Klasifikasi Kategori (Income & Expense)
Setiap kategori memiliki tipe (*type*) tegas yang membedakan:
* **income** (Pendapatan)
* **expense** (Beban/Pengeluaran)

Klasifikasi ini disimpan di tingkat kategori untuk mencegah redudansi data di setiap transaksi. Alurnya adalah:
`Transaction` ➜ `Chart of Account` ➜ `Category` ➜ `Income / Expense`.

### 2. Aturan Ketat Debit dan Kredit
Sistem mematuhi aturan baku pencatatan akuntansi ganda (*double-entry bookkeeping*):
* **Kategori Income (Pendapatan):** Bertambah di posisi **Credit** (Debit harus bernilai `0`).
* **Kategori Expense (Beban):** Bertambah di posisi **Debit** (Credit harus bernilai `0`).

**Validasi Form Otomatis (Frontend):**
* Memilih akun bertipe *Income* otomatis **mengunci kolom input Debit** (memaksa nilai `0`).
* Memilih akun bertipe *Expense* otomatis **mengunci kolom input Credit** (memaksa nilai `0`).

**Validasi Keamanan API (Backend):**
Backend melakukan validasi berlapis untuk mencegah manipulasi request API:
* Transaksi tidak boleh memiliki nilai Debit dan Credit sekaligus.
* Transaksi tidak boleh bernilai nol (`0`) pada kedua kolom sekaligus.

---

## ⚡ Fitur Utama & Optimasi Performa

### 1. Optimasi Kalkulasi Laporan Laba Rugi (Profit & Loss)
* **Sebelumnya:** Perhitungan dilakukan di tingkat aplikasi dengan mengambil seluruh data transaksi ke PHP, lalu melakukan penyaringan menggunakan Laravel Collection. Ini berpotensi memicu *memory exhaustion* jika data berjumlah besar.
* **Setelah Optimasi:** Kalkulasi dipindahkan sepenuhnya ke database menggunakan **Raw SQL Joins** dan agregasi `SUM(CASE WHEN ...)` dengan pengelompokan `GROUP BY`.
* **Kategori Dinamis:** Rincian kategori laba rugi bersifat dinamis. Kategori baru yang ditambahkan di menu *Categories* akan otomatis terhitung dan merinci bagian Pendapatan/Pengeluaran pada Laporan Laba Rugi tanpa mengubah kode apa pun.
* **Kecepatan Tinggi:** Proses kalkulasi pada **150.000+ data transaksi** selesai dalam waktu **di bawah 100ms**!

### 2. Navigasi Halaman & Batas Data Dinamis (Pagination)
* Buku besar transaksi dilengkapi dengan sistem paginasi dinamis.
* Pengguna dapat memilih jumlah data per halaman: **10, 25, 50, atau 100 data**.
* Default diatur ke **25 data per halaman** untuk kenyamanan akses performa tinggi.

### 3. Ekspor Dokumen Keuangan
* **Ekspor Excel:** Menghasilkan file spreadsheet dengan format tabel akuntansi yang rapi. Seluruh nominal angka diekspor dengan format mata uang **Rupiah lengkap** (misal: `Rp 5.000.000`) dan penulisan minus akuntansi bertanda kurung `(Rp 250.000)` untuk bagian pengeluaran.
* **Cetak PDF Rapi:** Menggunakan CSS `@media print` kustom. Saat tombol cetak ditekan, sidebar menu navigasi, form saringan tanggal, tombol aksi, dan ornamen web lainnya akan disembunyikan otomatis, menyisakan lembar laporan keuangan bersih yang pas untuk cetakan fisik/PDF.

### 4. Dasbor Interaktif (Dashboard)
* **Grafik Batang Perbandingan Bulanan:** Menampilkan grafik batang interaktif (Chart.js) yang membandingkan total *Income* vs *Expense* per bulan sepanjang tahun dengan **Filter Tahun Dinamis** (2024 - 2027).
* **Recent Transactions:** Panel sisi kanan menampilkan **5 transaksi buku besar terakhir** yang dicatat di database beserta tanggal, keterangan, dan nilai (+ Hijau / - Merah).

### 5. Komponen UI Premium
* **Loading Spinner:** Animasi pemuat reaktif kustom di setiap halaman saat pencarian, pergantian tahun, pergantian halaman, atau proses saringan tanggal sedang berlangsung.
* **ConfirmModal Hapus Kustom:** Mengganti dialog konfirmasi bawaan browser (`confirm()`) dengan modal pop-up kustom yang memperingatkan integritas relasi data (misalnya: memperingatkan jika akun COA tidak dapat dihapus karena sudah memiliki transaksi).

---

## 🔍 Kebijakan Integritas: Mengapa Transaksi Tidak Bisa Diedit / Dihapus?

Aplikasi ini menonaktifkan fitur Edit dan Hapus pada lembar Buku Besar Transaksi yang telah disimpan:
* **Jejak Audit (Audit Trail):** Untuk mematuhi prinsip pencegahan manipulasi keuangan (*anti-fraud*), transaksi yang sudah dibukukan tidak boleh dihilangkan.
* **Mekanisme Koreksi:** Kesalahan entri diperbaiki dengan membuat **Jurnal Pembalik (Reversing Entry)** untuk menihilkan kesalahan, dilanjutkan dengan mencatat **Jurnal Penyesuaian (Adjusting Entry)** baru yang benar.

---

## 🚀 Panduan Instalasi Lokal

### 1. Backend (Laravel)
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

**Konfigurasi Database `.env` (MySQL):**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=backend
DB_USERNAME=root
DB_PASSWORD=password_anda
```

**Jalankan Migrasi & Menghasilkan Data Uji:**
```bash
# Migrasi tabel dan seeder awal (COA & Categories)
php artisan migrate --seed

# Menggenerasi 150.000 data transaksi uji coba secara otomatis (Jan - Agt 2026)
php artisan app:generate-huge-data 150000

# Jalankan server
php artisan serve
```

### 2. Frontend (Nuxt 3)
Buka terminal baru di folder proyek utama:
```bash
cd frontend
npm install
cp .env.example .env
```
*(Pastikan berkas `.env` berisi URL API backend: `NUXT_PUBLIC_API_BASE=http://127.0.0.1:8000/api/v1`)*

```bash
# Jalankan server frontend
npm run dev
```
Aplikasi akan siap diakses lewat browser di alamat `http://localhost:3000`.

---

## 🧪 Perintah Pengembangan

### Backend
* Jalankan server: `php artisan serve`
* Migrasi ulang dari awal (menghapus semua data): `php artisan migrate:fresh --seed`
* Menjalankan unit testing otomatis: `php artisan test`

### Frontend
* Mode Pengembangan: `npm run dev`
* Membuat *Production Build*: `npm run build`
* Preview hasil build: `npm run preview`
