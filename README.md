# Trans Berjaya Khatulistiwa (TBK) - Sistem Buku Besar & Laba Rugi Akuntansi

Sistem buku besar akuntansi (*ledger system*) berbasis web berkinerja tinggi yang dibangun menggunakan **Laravel 11 (Backend)** dan **Nuxt 3 (Frontend)**. Aplikasi ini menyediakan pelacakan keuangan real-time, pengaman entri ganda (*double-entry bookkeeping safeguards*), kalkulasi Laba & Rugi dinamis, serta dasbor analitik yang dioptimalkan untuk skala besar (diuji dengan 150.000+ data transaksi).

---

## 🛠️ Persyaratan Sistem

Sebelum melakukan pengaturan proyek, pastikan Anda telah memasang perangkat lunak berikut di komputer Anda:
* **PHP:** `>= 8.2`
* **Composer:** `>= 2.x`
* **Node.js:** `>= 18.x` (Direkomendasikan: `v20.x`)
* **NPM:** `>= 9.x`
* **Sistem Database:** SQLite (default untuk pengembangan) atau MySQL/PostgreSQL.

---

## 🚀 Pemasangan & Pengaturan Lokal

### 1. Pengaturan Backend (Laravel)
1. Masuk ke direktori backend:
   ```bash
   cd backend
   ```
2. Pasang dependensi PHP:
   ```bash
   composer install
   ```
3. Salin file konfigurasi lingkungan (*environment*) dan buat *application key*:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Atur database. Secara default, aplikasi menggunakan **SQLite**. Buat file SQLite kosong jika belum ada:
   ```bash
   touch database/database.sqlite
   ```
5. Jalankan migrasi tabel database beserta seeder bawaan (ini akan membuat akun COA dan kategori awal):
   ```bash
   php artisan migrate --seed
   ```
6. Pengaturan Zona Waktu WIB (Waktu Indonesia Barat):
   Zona waktu aplikasi telah diset ke `'Asia/Jakarta'` di dalam `config/app.php` untuk memastikan pencatatan `created_at` dan `updated_at` akurat sesuai zona waktu lokal.
7. Jalankan server pengembangan Laravel:
   ```bash
   php artisan serve
   ```

### 2. Pengaturan Frontend (Nuxt 3)
1. Masuk ke direktori frontend:
   ```bash
   cd ../frontend
   ```
2. Pasang dependensi Node packages:
   ```bash
   npm install
   ```
3. Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   *(Pastikan nilai `NUXT_PUBLIC_API_BASE=http://127.0.0.1:8000/api/v1` sesuai dengan alamat server backend Laravel Anda)*
4. Jalankan server pengembangan Nuxt:
   ```bash
   npm run dev
   ```
5. Buka browser Anda dan akses `http://localhost:3000`.

### ⚡ Menggenerasi Data Uji Coba Massal (150.000+ Transaksi)
Untuk menguji performa sistem di bawah beban data besar, kami telah menyediakan perintah khusus (*Artisan Command*) berkinerja tinggi yang memanfaatkan SQL bulk insert per 5.000 baris untuk memasukkan 150.000 data dalam hitungan detik:
```bash
cd backend
php artisan app:generate-huge-data 150000
```
Perintah ini akan menyebarkan 150.000 transaksi secara acak di antara rentang tanggal **1 Januari hingga 31 Agustus 2026**.

---

## 💼 Alur Bisnis & Arsitektur Akuntansi

Sistem ini dirancang dengan mematuhi standar akuntansi profesional (**GAAP / IFRS**) dan menyertakan modul keuangan khusus sebagai berikut:

### 1. Manajemen Kategori (Pendapatan & Pengeluaran)
* Kategori membagi klasifikasi Akun Buku Besar (COA) ke dalam kelompok **Income** (Pendapatan) atau **Expense** (Beban/Biaya).
* Memanfaatkan Eloquent enums (`CategoryType`) untuk mencegah korupsi data tipe.
* CRUD penuh dengan pelacak pencarian (*real-time watcher*) langsung ke backend.

### 2. Klasifikasi Chart of Accounts (COA)
* Akun Buku Besar dikelompokkan di bawah kategori induknya (misal, Kode `4001` - Pendapatan Gaji di bawah kategori `Income`, Kode `5001` - Pengeluaran Keluarga di bawah kategori `Expense`).
* Ketika akun dihapus, sistem memvalidasi relasi transaksi terlebih dahulu di backend. Jika sudah digunakan, penghapusan dicegah menggunakan Modal Konfirmasi Kustom untuk menjaga integritas data keuangan.

### 3. Transaksi Jurnal (Buku Besar Ledger)
* **Pengaman Entri Ganda (Double-Entry Bookkeeping Safeguards):** Form entri transaksi secara dinamis mengunci kolom input berdasarkan tipe akun yang dipilih:
  * Memilih akun bertipe **Income** akan mengunci input **Debit** (karena pendapatan bertambah di posisi Kredit).
  * Memilih akun bertipe **Expense** akan mengunci input **Credit** (karena beban/biaya bertambah di posisi Debit).
* Pengaman visual ini divalidasi ulang di backend menggunakan Laravel Form Request untuk memastikan tidak ada kesalahan input posisi keuangan.
* Dilengkapi dengan navigasi halaman (*pagination*) dinamis serta pengaturan jumlah baris per halaman (10, 25, 50, 100) dengan default **25 baris per halaman** untuk kenyamanan berselancar di ratusan ribu data.

### 4. Laporan Laba Rugi (Profit & Loss Statement)
* Menghitung total Laba Rugi bersih secara real-time berdasarkan rentang tanggal yang dipilih.
* **Optimasi Kinerja Tinggi:** Agregasi penjumlahan dilakukan langsung di tingkat database menggunakan Raw SQL Joins (`SUM`, `CASE WHEN`, dan `GROUP BY`) alih-alih koleksi PHP. Hal ini memastikan proses kalkulasi 150.000+ data selesai di bawah **100ms** tanpa memakan memori server.
* Laporan bersifat dinamis: penambahan kategori baru di halaman Categories otomatis akan merinci pengelompokannya di Laba Rugi tanpa perlu mengubah kode frontend.

### 5. Ekspor Dokumen Kustom
* **Cetak PDF:** Menggunakan CSS media cetak kustom (`@media print`). Saat mencetak, sidebar navigasi utama, tombol aksi, dan form filter akan disembunyikan secara otomatis untuk menghasilkan cetakan laporan keuangan akuntansi yang bersih dan rapi.
* **Ekspor Excel HTML:** Mengekspor grid laporan secara presisi ke berkas Excel dengan angka nominal yang terformat menggunakan gaya Rupiah (`Rp 100.000`) dan penulisan minus akuntansi bertanda kurung `(Rp 50.000)`.

---

## 🔍 Mengapa Transaksi Sengaja Tidak Bisa Diedit / Dihapus?

Salah satu fitur krusial dari sistem buku besar ini adalah **tidak adanya fitur Edit dan Delete pada data transaksi yang sudah dicatat**.
* **Integritas Jejak Audit (Audit Trail):** Sesuai standar audit akuntansi profesional, jurnal keuangan yang sudah masuk ke buku besar tidak boleh diubah/dihapus secara bebas demi mencegah kecurangan (*manipulasi saldo/fraud*).
* **Prosedur Koreksi Saldo:** Apabila terjadi kesalahan pencatatan, akuntan harus membuat **Jurnal Pembalik (Reversing Entry)** untuk menihilkan transaksi yang salah, lalu membuat **Jurnal Penyesuaian (Adjusting Entry)** baru dengan nilai yang benar.
* Oleh karena itu, antarmuka transaksi didesain tanpa tombol edit/hapus untuk mematuhi kepatuhan standar audit ini.
