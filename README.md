# Sistem Informasi Manajemen Stok Berbasis Website pada Mebel Fauz

Tugas Akhir (TA) — Program Studi Manajemen Informatika, Politeknik Negeri Jember

**Nama** : Zakaria
**NIM** : E31231972
**Judul TA** : Rancang Bangun Sistem Informasi Manajemen Stok Berbasis Website pada Mebel Fauz

---

## 📋 Deskripsi

Sistem Informasi Manajemen Stok ini dibangun untuk membantu proses pencatatan dan pengelolaan stok barang pada usaha mebel Fauz, yang meliputi pencatatan barang masuk, barang keluar, mutasi stok, hingga pelaporan barang hilang. Sistem dikembangkan menggunakan metode pengembangan perangkat lunak **Waterfall** dan dilengkapi dengan fitur ekspor laporan ke format PDF dan Excel.

## 🛠️ Teknologi yang Digunakan

- **Framework**: Laravel
- **Bahasa Pemrograman**: PHP
- **Basis Data**: MySQL
- **Metode Pengembangan**: Waterfall
- **Export Laporan**: `barryvdh/laravel-dompdf` (PDF), `maatwebsite/excel` (Excel)

## ✨ Fitur Utama

- Manajemen data barang dan kategori barang
- Pencatatan barang masuk dan barang keluar
- Pencatatan mutasi stok dan barang hilang
- Role-based access control (Admin & Owner)
- Filter laporan berdasarkan rentang tanggal
- Ekspor laporan ke PDF dan Excel
- Validasi input berbahasa Indonesia

## 🗂️ Struktur Basis Data

Sistem ini menggunakan 8 tabel utama:

| Tabel | Keterangan |
|---|---|
| `users` | Data pengguna (admin/owner) |
| `kategori_barang` | Kategori/jenis barang mebel |
| `barang` | Data master barang |
| `mutasi_stok` | Riwayat perubahan stok |
| `barang_masuk` | Pencatatan barang masuk |
| `barang_keluar` | Pencatatan barang keluar |
| `barang_hilang` | Pencatatan barang hilang |
| `sessions` | Data sesi login pengguna |

## 🚀 Instalasi & Menjalankan Proyek

```bash
# Clone repository
git clone https://github.com/Zakaria04/TA_Mebel.git
cd TA_Mebel

# Install dependency PHP
composer install

# Salin file environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Konfigurasi database di file .env, lalu jalankan migrasi
php artisan migrate

# (Opsional) jalankan seeder jika tersedia
php artisan db:seed

# Jalankan server lokal
php artisan serve
```

## 👤 Role Pengguna

- **Admin**: mengelola data barang, transaksi barang masuk/keluar, dan mutasi stok
- **Owner**: memantau laporan stok dan mengakses ringkasan data

## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademik (Tugas Akhir) di Politeknik Negeri Jember.
