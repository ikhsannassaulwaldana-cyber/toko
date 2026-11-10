-- =====================================================================
--  PENGUJIAN AKHIR (Bab 6.D)  -- jalankan satu per satu di phpMyAdmin
--  Ambil screenshot hasil tiap nomor untuk dikumpulkan.
--  Jalankan dulu db_toko.sql
-- =====================================================================
USE db_toko;

-- 1. VIEW dapat menampilkan laporan -----------------------------------
SELECT * FROM view_laporan_penjualan ORDER BY id_transaksi;
SELECT * FROM view_total_belanja ORDER BY total_belanja DESC;

-- 2. JOIN beberapa tabel (5 tabel) ------------------------------------
SELECT * FROM view_detail_transaksi ORDER BY id_transaksi;

-- (jobsheet 2) pelanggan + transaksi, termasuk yang belum bertransaksi
SELECT p.nama_pelanggan, t.id_transaksi, t.tanggal, t.total_belanja
FROM tbl_pelanggan p
LEFT JOIN tbl_transaksi t ON p.id_pelanggan = t.id_pelanggan
ORDER BY p.id_pelanggan, t.tanggal;

-- 3. SUBQUERY -> analisis data ----------------------------------------
-- 3a. transaksi di atas rata-rata
SELECT * FROM tbl_transaksi
WHERE total_belanja > (SELECT AVG(total_belanja) FROM tbl_transaksi);

-- 3b. pelanggan yang belum pernah bertransaksi
SELECT * FROM tbl_pelanggan p
WHERE NOT EXISTS (SELECT 1 FROM tbl_transaksi t
                  WHERE t.id_pelanggan = p.id_pelanggan);

-- 3c. pelanggan dengan transaksi terbanyak
SELECT nama_pelanggan, total_transaksi FROM view_total_belanja
WHERE total_transaksi = (SELECT MAX(total_transaksi) FROM view_total_belanja);

-- 3d. produk yang pernah terjual (IN)
SELECT nama_produk FROM tbl_produk
WHERE id_produk IN (SELECT id_produk FROM tbl_detail_transaksi);

-- 3e. lima transaksi terbesar
SELECT * FROM tbl_transaksi ORDER BY total_belanja DESC LIMIT 5;

-- 3f. UNION
SELECT nama_pelanggan AS nama, 'Pelanggan' AS sumber FROM tbl_pelanggan
UNION
SELECT nama_produk, 'Produk' FROM tbl_produk;

-- 4. STORED PROCEDURE dapat dipanggil ---------------------------------
CALL TampilPelanggan();
CALL CariPelanggan(1);
CALL TambahPelanggan('Kiki', 'Ponorogo');
CALL HitungPelanggan(@jumlah);  SELECT @jumlah AS jumlah_pelanggan;
CALL LaporanTransaksi(1);

CALL TambahTransaksi(1, 1, 2, 10, @id_baru);   -- pelanggan 1, produk 1, qty 2, diskon 10%
SELECT @id_baru AS id_transaksi_baru;
SELECT * FROM view_detail_transaksi WHERE id_transaksi = @id_baru;

-- uji gagal (stok tidak cukup) -> harus error & otomatis ROLLBACK
CALL TambahTransaksi(1, 1, 99999, 0, @x);

-- 5. FUNCTION menghasilkan perhitungan benar --------------------------
SELECT HitungDiskon(1000000, 10) AS diskon_10_persen,   -- 100000
       HitungTotal(50000, 5)     AS total,              -- 250000
       HitungPajak(1000000)      AS pajak_11_persen,    -- 110000
       KategoriBelanja(750000)   AS kategori,           -- Pelanggan Silver
       KonversiNilai(85)         AS nilai_huruf;        -- B

-- 6. TRIGGER berjalan otomatis ----------------------------------------
INSERT INTO tbl_pelanggan (nama_pelanggan, kota) VALUES ('Rina', 'Ponorogo');   -- AFTER INSERT
UPDATE tbl_pelanggan SET kota = 'Madiun' WHERE nama_pelanggan = 'Rina';          -- AFTER UPDATE
DELETE FROM tbl_pelanggan WHERE nama_pelanggan = 'Rina';                         -- AFTER DELETE
SELECT * FROM tbl_log ORDER BY id_log DESC;

-- BEFORE INSERT: harus ditolak (total negatif)
INSERT INTO tbl_transaksi (tanggal, id_pelanggan, total_belanja)
VALUES ('2026-11-01', 1, -50000);

-- 7. TCL : COMMIT dan ROLLBACK ----------------------------------------
-- ROLLBACK : data batal
START TRANSACTION;
INSERT INTO tbl_pelanggan (nama_pelanggan, kota) VALUES ('Uji Rollback', 'Uji');
SELECT * FROM tbl_pelanggan WHERE kota = 'Uji';      -- terlihat di dalam transaksi
ROLLBACK;
SELECT * FROM tbl_pelanggan WHERE kota = 'Uji';      -- kosong

-- COMMIT : data tersimpan permanen
START TRANSACTION;
INSERT INTO tbl_pelanggan (nama_pelanggan, kota) VALUES ('Uji Commit', 'Uji');
COMMIT;
SELECT * FROM tbl_pelanggan WHERE kota = 'Uji';      -- ada
DELETE FROM tbl_pelanggan WHERE kota = 'Uji';        -- bersihkan

-- 8. EXPLAIN : analisis query & index ---------------------------------
EXPLAIN SELECT * FROM tbl_transaksi WHERE tanggal = '2026-10-05';
-- bandingkan tanpa index (perhatikan kolom type=ALL dan rows):
EXPLAIN SELECT * FROM tbl_transaksi IGNORE INDEX (idx_transaksi_tanggal)
WHERE tanggal = '2026-10-05';

EXPLAIN SELECT * FROM view_detail_transaksi WHERE id_transaksi = 19;
SHOW INDEX FROM tbl_transaksi;
