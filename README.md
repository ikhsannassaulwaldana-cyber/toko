# Proyek Database: Sistem Informasi Penjualan Toko

Proyek terintegrasi (Bab 6) untuk modul **SQL Lanjutan: Advanced Database Programming**.
Teknologi: XAMPP (Apache + MariaDB/MySQL), phpMyAdmin, PHP (PDO), Visual Studio Code.

## Struktur folder

```
proyek_toko/
├─ config/koneksi.php        <- KONEKSI DATABASE (PDO)
├─ database/
│   ├─ db_toko.sql           <- buat database, tabel, data, view, function, procedure, trigger, index
│   └─ pengujian.sql         <- query untuk 8 pengujian akhir (untuk screenshot)
├─ includes/                 <- header, footer, fungsi bantu
├─ assets/css/style.css
├─ index.php                 <- beranda + status koneksi
├─ pelanggan.php             <- procedure + trigger (INSERT/UPDATE/DELETE)
├─ transaksi.php             <- procedure TambahTransaksi (COMMIT/ROLLBACK di dalamnya)
├─ laporan.php               <- View + JOIN
├─ analisis.php              <- Subquery, UNION
├─ fungsi.php                <- Stored Function
├─ tcl.php                   <- COMMIT dan ROLLBACK
├─ explain.php               <- EXPLAIN + Index
└─ log.php                   <- isi tbl_log dari trigger
```

## Cara menjalankan

1. Buka **XAMPP Control Panel**, klik **Start** pada **Apache** dan **MySQL**.
2. Salin folder `proyek_toko` ke `C:\xampp\htdocs\`.
3. Buka **http://localhost/phpmyadmin** > tab **Import** > pilih `database/db_toko.sql` > **Import**.
   (Database `db_toko` akan dibuat otomatis. Jangan buat manual.)
4. Buka folder `proyek_toko` di **VS Code** (File > Open Folder) untuk melihat/mengubah kode.
5. Buka **http://localhost/proyek_toko/** di browser. Halaman beranda harus menampilkan
   "Koneksi database berhasil".

## Pengaturan koneksi

Berkas `config/koneksi.php`. Bawaan XAMPP:

| Setelan  | Nilai       |
|----------|-------------|
| Host     | `localhost` |
| Port     | `3306`      |
| Database | `db_toko`   |
| User     | `root`      |
| Password | *(kosong)*  |

Jika password root MySQL Anda diubah, ubah `DB_PASS`. Jika port berbeda (mis. 3307), ubah `DB_PORT`.

## Pemetaan ke persyaratan proyek

| Kompetensi       | Implementasi                                                         |
|------------------|----------------------------------------------------------------------|
| View             | `view_laporan_penjualan`, `view_detail_transaksi`, `view_total_belanja` |
| JOIN             | `view_detail_transaksi` (5 tabel), LEFT JOIN pelanggan-transaksi      |
| Subquery         | `analisis.php` (AVG, NOT EXISTS, IN, MAX), UNION                      |
| Stored Procedure | `TampilPelanggan`, `CariPelanggan`, `TambahPelanggan`, `HitungPelanggan`, `LaporanTransaksi`, `TambahTransaksi` |
| Function         | `HitungDiskon`, `HitungTotal`, `HitungPajak`, `KategoriBelanja`, `KonversiNilai` |
| Trigger          | `after_insert/update/delete_pelanggan`, `before_insert_transaksi`, `after_insert_transaksi` -> `tbl_log` |
| TCL              | `START TRANSACTION`, `COMMIT`, `ROLLBACK` di `TambahTransaksi` dan `tcl.php` |
| Index            | `idx_transaksi_tanggal`, `idx_pelanggan_nama`, `idx_produk_nama`      |

## Masalah umum

- **"Koneksi database gagal"**: MySQL belum Start, atau `db_toko.sql` belum di-import.
- **Error saat import function/procedure**: pastikan memakai file `db_toko.sql` asli dan import lewat phpMyAdmin
  (baris `DELIMITER //` sudah didukung). Jika memakai MySQL 8 dengan binary log aktif, jalankan dulu
  `SET GLOBAL log_bin_trust_function_creators = 1;`.
- **Halaman kosong / kode PHP tampil sebagai teks**: file dibuka langsung (`file://`). Buka lewat `http://localhost/...`.
