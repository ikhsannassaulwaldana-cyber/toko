-- =====================================================================
--  PROYEK TERINTEGRASI : SISTEM INFORMASI PENJUALAN TOKO
--  Database : db_toko        (XAMPP / MariaDB / MySQL)
--
--  Cara pakai (phpMyAdmin): tab Import -> pilih file ini -> Go.
--  Atau lewat terminal   : mysql -u root < db_toko.sql
--
--  PERHATIAN: script ini menghapus lalu membuat ulang 6 tabel proyek
--  (tbl_kategori, tbl_pelanggan, tbl_produk, tbl_transaksi,
--  tbl_detail_transaksi, tbl_log) di database db_toko.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS db_toko
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_toko;

-- ---------------------------------------------------------------------
-- 0. BERSIH-BERSIH (agar script bisa dijalankan berulang kali)
-- ---------------------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP VIEW IF EXISTS view_laporan_penjualan, view_detail_transaksi, view_total_belanja;
DROP TABLE IF EXISTS tbl_detail_transaksi, tbl_transaksi, tbl_produk,
                     tbl_kategori, tbl_pelanggan, tbl_log;
SET FOREIGN_KEY_CHECKS = 1;

DROP FUNCTION  IF EXISTS HitungDiskon;
DROP FUNCTION  IF EXISTS HitungTotal;
DROP FUNCTION  IF EXISTS HitungPajak;
DROP FUNCTION  IF EXISTS KategoriBelanja;
DROP FUNCTION  IF EXISTS KonversiNilai;
DROP PROCEDURE IF EXISTS TampilPelanggan;
DROP PROCEDURE IF EXISTS CariPelanggan;
DROP PROCEDURE IF EXISTS TambahPelanggan;
DROP PROCEDURE IF EXISTS HitungPelanggan;
DROP PROCEDURE IF EXISTS LaporanTransaksi;
DROP PROCEDURE IF EXISTS TambahTransaksi;

-- ---------------------------------------------------------------------
-- 1. STRUKTUR TABEL (6 tabel sesuai Bab 6)
-- ---------------------------------------------------------------------
CREATE TABLE tbl_kategori (
  id_kategori   INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE tbl_pelanggan (
  id_pelanggan   INT AUTO_INCREMENT PRIMARY KEY,
  nama_pelanggan VARCHAR(100) NOT NULL,
  kota           VARCHAR(50)
) ENGINE=InnoDB;

CREATE TABLE tbl_produk (
  id_produk   INT AUTO_INCREMENT PRIMARY KEY,
  nama_produk VARCHAR(100) NOT NULL,
  id_kategori INT NOT NULL,
  harga       DECIMAL(12,2) NOT NULL,
  stok        INT NOT NULL DEFAULT 0,
  FOREIGN KEY (id_kategori) REFERENCES tbl_kategori(id_kategori)
) ENGINE=InnoDB;

CREATE TABLE tbl_transaksi (
  id_transaksi  INT AUTO_INCREMENT PRIMARY KEY,
  tanggal       DATE NOT NULL,
  id_pelanggan  INT NOT NULL,
  total_belanja DECIMAL(12,2) NOT NULL DEFAULT 0,
  FOREIGN KEY (id_pelanggan) REFERENCES tbl_pelanggan(id_pelanggan)
) ENGINE=InnoDB;

CREATE TABLE tbl_detail_transaksi (
  id_detail    INT AUTO_INCREMENT PRIMARY KEY,
  id_transaksi INT NOT NULL,
  id_produk    INT NOT NULL,
  jumlah       INT NOT NULL,
  harga        DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (id_transaksi) REFERENCES tbl_transaksi(id_transaksi),
  FOREIGN KEY (id_produk)    REFERENCES tbl_produk(id_produk)
) ENGINE=InnoDB;

CREATE TABLE tbl_log (
  id_log     INT AUTO_INCREMENT PRIMARY KEY,
  tabel      VARCHAR(50)  NOT NULL,
  aktivitas  VARCHAR(30)  NOT NULL,
  id_data    INT,
  keterangan VARCHAR(255),
  waktu      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. DATA AWAL (DML)  -> 10 pelanggan, 12 produk, 20 transaksi
--    (trigger dibuat SETELAH data awal, supaya tbl_log tidak penuh)
-- ---------------------------------------------------------------------
INSERT INTO tbl_kategori (nama_kategori) VALUES
('Sembako'), ('Minuman'), ('Makanan Ringan'),
('Perlengkapan Mandi'), ('Kebersihan Rumah');

INSERT INTO tbl_pelanggan (nama_pelanggan, kota) VALUES
('Andi',   'Ponorogo'),  ('Budi',  'Madiun'),
('Citra',  'Surabaya'),  ('Dewi',  'Ponorogo'),
('Eko',    'Magetan'),   ('Fitri', 'Madiun'),
('Gilang', 'Ngawi'),     ('Hana',  'Surabaya'),
('Indra',  'Ponorogo'),  ('Joko',  'Kediri');   -- Indra & Joko belum bertransaksi

INSERT INTO tbl_produk (nama_produk, id_kategori, harga, stok) VALUES
('Beras 5 Kg',        1, 65000, 200),
('Minyak Goreng 2 L', 1, 38000, 200),
('Gula Pasir 1 Kg',   1, 17000, 200),
('Kopi Sachet',       2, 12000, 200),
('Teh Botol',         2,  6000, 200),
('Air Mineral',       2,  3000, 200),
('Keripik Singkong',  3, 15000, 200),
('Biskuit',           3, 11000, 200),
('Sabun Mandi',       4,  5000, 200),
('Shampo',            4, 22000, 200),
('Deterjen 1 Kg',     5, 28000, 200),
('Pembersih Lantai',  5, 18000, 200);

INSERT INTO tbl_transaksi (tanggal, id_pelanggan) VALUES
('2026-10-01',1),('2026-10-02',2),('2026-10-03',1),('2026-10-04',3),
('2026-10-05',4),('2026-10-06',5),('2026-10-07',2),('2026-10-08',6),
('2026-10-09',7),('2026-10-10',1),('2026-10-11',8),('2026-10-12',3),
('2026-10-13',4),('2026-10-14',2),('2026-10-15',5),('2026-10-16',1),
('2026-10-17',6),('2026-10-18',7),('2026-10-19',3),('2026-10-20',8);

-- detail: (id_transaksi, id_produk, jumlah) ; harga diambil dari tbl_produk
INSERT INTO tbl_detail_transaksi (id_transaksi, id_produk, jumlah, harga)
SELECT v.t, v.p, v.j, pr.harga
FROM (
  SELECT  1 t, 1 p, 2 j UNION ALL SELECT  1, 4, 3 UNION ALL
  SELECT  2, 2, 1 UNION ALL SELECT  2, 3, 2 UNION ALL
  SELECT  3, 5, 6 UNION ALL
  SELECT  4, 1, 3 UNION ALL SELECT  4,11, 1 UNION ALL
  SELECT  5, 7, 4 UNION ALL SELECT  5, 8, 2 UNION ALL
  SELECT  6,10, 2 UNION ALL SELECT  6, 9, 5 UNION ALL
  SELECT  7, 1, 1 UNION ALL
  SELECT  8, 2, 2 UNION ALL SELECT  8,12, 1 UNION ALL
  SELECT  9, 6,12 UNION ALL SELECT  9, 5, 6 UNION ALL
  SELECT 10, 1, 4 UNION ALL SELECT 10, 2, 2 UNION ALL
  SELECT 11,11, 2 UNION ALL
  SELECT 12, 4, 5 UNION ALL SELECT 12, 7, 3 UNION ALL
  SELECT 13, 1, 2 UNION ALL SELECT 13, 3, 3 UNION ALL
  SELECT 14, 8, 5 UNION ALL SELECT 14,10, 1 UNION ALL
  SELECT 15, 2, 3 UNION ALL
  SELECT 16, 1, 3 UNION ALL SELECT 16,12, 2 UNION ALL
  SELECT 17, 9,10 UNION ALL SELECT 17,10, 2 UNION ALL
  SELECT 18, 4, 2 UNION ALL
  SELECT 19, 1, 5 UNION ALL SELECT 19, 2, 2 UNION ALL SELECT 19,11, 1 UNION ALL
  SELECT 20, 3, 4 UNION ALL SELECT 20, 5, 5
) v
JOIN tbl_produk pr ON pr.id_produk = v.p;

-- hitung total_belanja tiap transaksi dari rincian detailnya
UPDATE tbl_transaksi t
SET t.total_belanja = (SELECT SUM(d.jumlah * d.harga)
                       FROM tbl_detail_transaksi d
                       WHERE d.id_transaksi = t.id_transaksi);

-- ---------------------------------------------------------------------
-- 3. STORED FUNCTION  (Bab 4)
-- ---------------------------------------------------------------------
DELIMITER //

CREATE FUNCTION HitungDiskon(harga DECIMAL(12,2), diskon DECIMAL(5,2))
RETURNS DECIMAL(12,2)
DETERMINISTIC
NO SQL
BEGIN
  -- diskon dalam persen, hasil = nominal potongan
  RETURN harga * diskon / 100;
END //

CREATE FUNCTION HitungTotal(harga DECIMAL(12,2), jumlah INT)
RETURNS DECIMAL(12,2)
DETERMINISTIC
NO SQL
BEGIN
  RETURN harga * jumlah;
END //

CREATE FUNCTION HitungPajak(harga DECIMAL(12,2))
RETURNS DECIMAL(12,2)
DETERMINISTIC
NO SQL
BEGIN
  RETURN harga * 0.11;          -- pajak 11 %
END //

CREATE FUNCTION KategoriBelanja(total DECIMAL(12,2))
RETURNS VARCHAR(30)
DETERMINISTIC
NO SQL
BEGIN
  RETURN CASE
    WHEN total >= 1000000 THEN 'Pelanggan Gold'
    WHEN total >=  500000 THEN 'Pelanggan Silver'
    ELSE 'Pelanggan Reguler'
  END;
END //

CREATE FUNCTION KonversiNilai(nilai INT)
RETURNS VARCHAR(5)
DETERMINISTIC
NO SQL
BEGIN
  RETURN CASE
    WHEN nilai >= 90 THEN 'A'
    WHEN nilai >= 80 THEN 'B'
    WHEN nilai >= 70 THEN 'C'
    WHEN nilai >= 60 THEN 'D'
    ELSE 'E'
  END;
END //

DELIMITER ;

-- ---------------------------------------------------------------------
-- 4. VIEW  (Bab 1 & 2)
-- ---------------------------------------------------------------------
-- View laporan penjualan (JOIN transaksi + pelanggan)
CREATE VIEW view_laporan_penjualan AS
SELECT t.id_transaksi, t.tanggal, p.id_pelanggan, p.nama_pelanggan,
       p.kota, t.total_belanja
FROM tbl_transaksi t
JOIN tbl_pelanggan p ON t.id_pelanggan = p.id_pelanggan;

-- View detail transaksi (JOIN 5 tabel)
CREATE VIEW view_detail_transaksi AS
SELECT t.id_transaksi, t.tanggal, p.nama_pelanggan,
       k.nama_kategori, pr.nama_produk,
       d.jumlah, d.harga,
       HitungTotal(d.harga, d.jumlah) AS subtotal
FROM tbl_detail_transaksi d
JOIN tbl_transaksi t  ON d.id_transaksi = t.id_transaksi
JOIN tbl_pelanggan p  ON t.id_pelanggan = p.id_pelanggan
JOIN tbl_produk    pr ON d.id_produk    = pr.id_produk
JOIN tbl_kategori  k  ON pr.id_kategori = k.id_kategori;

-- View total belanja per pelanggan (agregasi + function)
CREATE VIEW view_total_belanja AS
SELECT p.id_pelanggan, p.nama_pelanggan,
       COUNT(t.id_transaksi)             AS total_transaksi,
       COALESCE(SUM(t.total_belanja), 0) AS total_belanja,
       KategoriBelanja(COALESCE(SUM(t.total_belanja), 0)) AS kategori
FROM tbl_pelanggan p
LEFT JOIN tbl_transaksi t ON p.id_pelanggan = t.id_pelanggan
GROUP BY p.id_pelanggan, p.nama_pelanggan;

-- ---------------------------------------------------------------------
-- 5. STORED PROCEDURE  (Bab 3)
-- ---------------------------------------------------------------------
DELIMITER //

CREATE PROCEDURE TampilPelanggan()
BEGIN
  SELECT * FROM tbl_pelanggan ORDER BY id_pelanggan;
END //

CREATE PROCEDURE CariPelanggan(IN p_id INT)
BEGIN
  SELECT * FROM tbl_pelanggan WHERE id_pelanggan = p_id;
END //

CREATE PROCEDURE TambahPelanggan(IN p_nama VARCHAR(100), IN p_kota VARCHAR(50))
BEGIN
  INSERT INTO tbl_pelanggan (nama_pelanggan, kota) VALUES (p_nama, p_kota);
END //

CREATE PROCEDURE HitungPelanggan(OUT jumlah INT)
BEGIN
  SELECT COUNT(*) INTO jumlah FROM tbl_pelanggan;
END //

CREATE PROCEDURE LaporanTransaksi(IN p_id_pelanggan INT)
BEGIN
  SELECT id_transaksi, tanggal, nama_pelanggan, kota, total_belanja
  FROM view_laporan_penjualan
  WHERE id_pelanggan = p_id_pelanggan
  ORDER BY tanggal;
END //

-- Proses tambah transaksi (pakai TCL: COMMIT / ROLLBACK)
CREATE PROCEDURE TambahTransaksi(
  IN  p_id_pelanggan INT,
  IN  p_id_produk    INT,
  IN  p_jumlah       INT,
  IN  p_diskon       DECIMAL(5,2),     -- persen
  OUT p_id_transaksi INT
)
BEGIN
  DECLARE v_harga    DECIMAL(12,2) DEFAULT NULL;
  DECLARE v_stok     INT DEFAULT NULL;
  DECLARE v_subtotal DECIMAL(12,2);
  DECLARE v_total    DECIMAL(12,2);

  -- jika ada error apa pun: batalkan semua perubahan lalu teruskan error
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    SET p_id_transaksi = NULL;
    RESIGNAL;
  END;

  START TRANSACTION;

  IF p_jumlah IS NULL OR p_jumlah <= 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah harus lebih dari 0';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM tbl_pelanggan WHERE id_pelanggan = p_id_pelanggan) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pelanggan tidak ditemukan';
  END IF;

  SELECT harga, stok INTO v_harga, v_stok
  FROM tbl_produk WHERE id_produk = p_id_produk FOR UPDATE;

  IF v_harga IS NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk tidak ditemukan';
  END IF;

  IF v_stok < p_jumlah THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok produk tidak mencukupi';
  END IF;

  SET v_subtotal = HitungTotal(v_harga, p_jumlah);
  SET v_total    = v_subtotal - HitungDiskon(v_subtotal, p_diskon);

  INSERT INTO tbl_transaksi (tanggal, id_pelanggan, total_belanja)
  VALUES (CURDATE(), p_id_pelanggan, v_total);
  SET p_id_transaksi = LAST_INSERT_ID();

  INSERT INTO tbl_detail_transaksi (id_transaksi, id_produk, jumlah, harga)
  VALUES (p_id_transaksi, p_id_produk, p_jumlah, v_harga);

  UPDATE tbl_produk SET stok = stok - p_jumlah WHERE id_produk = p_id_produk;

  COMMIT;
END //

DELIMITER ;

-- ---------------------------------------------------------------------
-- 6. TRIGGER  (Bab 5) -> audit perubahan data ke tbl_log
-- ---------------------------------------------------------------------
DELIMITER //

CREATE TRIGGER after_insert_pelanggan
AFTER INSERT ON tbl_pelanggan
FOR EACH ROW
BEGIN
  INSERT INTO tbl_log (tabel, aktivitas, id_data, keterangan)
  VALUES ('tbl_pelanggan', 'INSERT', NEW.id_pelanggan,
          CONCAT('Pelanggan baru: ', NEW.nama_pelanggan, ' (', IFNULL(NEW.kota,'-'), ')'));
END //

CREATE TRIGGER after_update_pelanggan
AFTER UPDATE ON tbl_pelanggan
FOR EACH ROW
BEGIN
  INSERT INTO tbl_log (tabel, aktivitas, id_data, keterangan)
  VALUES ('tbl_pelanggan', 'UPDATE', NEW.id_pelanggan,
          CONCAT('Nama: ', OLD.nama_pelanggan, ' -> ', NEW.nama_pelanggan,
                 ' | Kota: ', IFNULL(OLD.kota,'-'), ' -> ', IFNULL(NEW.kota,'-')));
END //

CREATE TRIGGER after_delete_pelanggan
AFTER DELETE ON tbl_pelanggan
FOR EACH ROW
BEGIN
  INSERT INTO tbl_log (tabel, aktivitas, id_data, keterangan)
  VALUES ('tbl_pelanggan', 'DELETE', OLD.id_pelanggan,
          CONCAT('Pelanggan dihapus: ', OLD.nama_pelanggan));
END //

CREATE TRIGGER before_insert_transaksi
BEFORE INSERT ON tbl_transaksi
FOR EACH ROW
BEGIN
  IF NEW.total_belanja < 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Total belanja tidak boleh negatif';
  END IF;
END //

CREATE TRIGGER after_insert_transaksi
AFTER INSERT ON tbl_transaksi
FOR EACH ROW
BEGIN
  INSERT INTO tbl_log (tabel, aktivitas, id_data, keterangan)
  VALUES ('tbl_transaksi', 'INSERT', NEW.id_transaksi,
          CONCAT('Transaksi baru, total Rp', FORMAT(NEW.total_belanja, 0)));
END //

DELIMITER ;

-- ---------------------------------------------------------------------
-- 7. INDEX  (optimasi query). Kolom FK sudah otomatis ber-index di InnoDB.
-- ---------------------------------------------------------------------
CREATE INDEX idx_transaksi_tanggal  ON tbl_transaksi(tanggal);
CREATE INDEX idx_pelanggan_nama     ON tbl_pelanggan(nama_pelanggan);
CREATE INDEX idx_produk_nama        ON tbl_produk(nama_produk);

-- ---------------------------------------------------------------------
-- Selesai. Cek cepat:
-- ---------------------------------------------------------------------
SELECT 'db_toko siap dipakai' AS status,
       (SELECT COUNT(*) FROM tbl_pelanggan) AS pelanggan,
       (SELECT COUNT(*) FROM tbl_transaksi) AS transaksi;
