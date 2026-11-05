# 📚 Perpustakaan - Final Project Sistem Informasi Perpustakaan

> **Perpustakaan** adalah aplikasi manajemen perpustakaan berbasis web yang dirancang menggunakan framework **Laravel**, **PHP**, dan **TailwindCSS** dengan skema warna *Dark Emerald & Cream*.

---

## 📌 Developer
* **Chery Ardin Dimalta** (D4 Teknik Informatika - PENS)
---

## 🚀 Fitur Utama

- [x] **Autentikasi Berbasis Role**: Akses khusus untuk Admin dan Anggota/Siswa.
- [x] **Manajemen Buku**: Pengelolaan data buku, kategori, dan pencarian cepat.
- [x] **Peminjaman Otomatis (+7 Hari)**: Kalkulasi otomatis tanggal pengembalian buku menggunakan JavaScript event listener pada form `create.blade.php`.
- [x] **Manajemen Stok Otomatis**: Stok berkurang saat dipinjam dan otomatis bertambah saat dikembalikan.
- [x] **Sistem Denda**: Manajemen denda pengembalian terlambat.
- [x] **Dashboard & Laporan**: Ringkasan data statistik perpustakaan.

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