# 📚 Libria Perpustakaan - Final Project Sistem Informasi Perpustakaan

> **Libria** adalah aplikasi manajemen perpustakaan berbasis web untuk mengelola katalog buku, data anggota, transaksi peminjaman, denda, serta halaman laporan peminjaman yang dirancang menggunakan framework **Laravel**, **PHP**, dan **TailwindCSS** dengan skema warna *Dark Emerald & Cream*.

---

## 📌 Developer
* **Chery Ardin Dimalta** (D4 Teknik Informatika - PENS)
---

## 🚀 Fitur Utama

- [x] **Autentikasi Berbasis Role**: 
    - Akses khusus untuk Admin dan Anggota/Siswa.

- [x] **Dashboard Interaktif & Statistik**:
    - Ringkasan totoal buku, total stok, jumlah anggota, dan peminjaman aktif.
    - Filter Kategori Interakti: Memilih dan menyering daftar buku berdasarkan kategori secara langsung di dashboard.
    - Tampilan *Best Seller* dan daftar katalog buku modern.

- [x] **Manajemen Buku (CRUD & Upload Cover)**:
    - Tambah, edit, lihat detail, dan hapus data buku.
    - Pengelolaan data buku, kategori, dan pencarian cepat.

- [x] **Peminjaman Otomatis (+7 Hari)**: 
    - Kalkulasi otomatis tanggal pengembalian buku menggunakan JavaScript event listener pada form `create.blade.php`.

- [x] **Manajemen Stok Otomatis**: 
    - Stok berkurang saat dipinjam dan otomatis bertambah saat dikembalikan.

- [x] **Sistem Denda**: 
    - Manajemen denda pengembalian terlambat.

- [x] **Manajemen Anggota (CRUD & Pencarian Cepat)**:
  - Kelola data mahasiswa/anggota perpustakaan beserta status keaktifan.
  - Fitur pencarian cepat berdasarkan **Nama**, **NIM**, atau **Email** (`?search=`) lengkap dengan tombol *Reset* dan *pagination*.

- [x] **Peminjaman Otomatis (+7 Hari) & Laporan API**: 
  - Kalkulasi otomatis tanggal pengembalian buku menggunakan JavaScript event listener pada form `create.blade.php`.
  - Halaman **Laporan Peminjaman (`/loans/report`)** berbasis REST API (`/api/loans`) dengan *pagination* dinamis dan *fallback* nama petugas default **Admin PENS**.

---

## 🛠️ Stack Teknologi

* **Framework Backend**: Laravel (PHP)
* **Styling / Frontend**: TailwindCSS (Dark Emerald & Cream theme), Blade Views, JavaScript
* **Database**: PostgreSQL / MySQL
* **Tools**: Git, VS Code, XAMPP

---

## 📂 Struktur Direktori Proyek

```text
perpus-final-project/
├── app/          # Core Logic (Controllers, Models)
├── config/       # Konfigurasi Aplikasi
├── database/     # Migrations, Seeders, & Factories
├── public/       # Asset Kompilasi (CSS, JS, Images)
├── resources/    # Views (Blade templates) & Raw Assets
├── routes/       # Route Web & API
└── README.md     # Laporan & Dokumentasi Proyek

```
---

## Changelog & Development Log

### Minggu, 8 November 2026 
- **Fitur Cover Buku:** Berhasil mengintegrasikan upload gambar *cover* buku (form *create* & *edit*) menggunakan Laravel Storage ('Storage:Link').
- **Database Migration:** Menambahkan kolom 'cover' secara dinamis ke dalam tabel 'books'.
- **Dashboard & Filter Interaktif:** Membuat tombol kategori di *dashboard* menjadi interaktif dan menampilkan buku ter Best Seller.


---

## ⚙️ Cara Menjalankan Project

### 1. Instalasi & Konfigurasi Awal
1. Masuk ke direktori proyek dan install dependensi:
   ```bash
   composer install
    npm install && npm run build
    cp .env.example .env
    php artisan key:generate
    php artisan migrate:fresh --seed
    php artisan storage:link

    # Terminal 1 — Server Utama (Trafik Browser, Port 8000)
    php artisan serve

    # Terminal 2 — Server Kedua / Internal API (Contoh Port 8011)
    php artisan serve --port=8011

```