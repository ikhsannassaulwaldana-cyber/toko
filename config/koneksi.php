<?php
/**
 * KONEKSI DATABASE (PDO)  -  db_toko
 *
 * Pengaturan bawaan XAMPP: user "root", password kosong, port 3306.
 * Ubah konstanta di bawah ini jika pengaturan MySQL/MariaDB Anda berbeda.
 *
 * Cara memakai di halaman lain:
 *     require_once __DIR__ . '/config/koneksi.php';
 *     $rows = $pdo->query("SELECT * FROM tbl_pelanggan")->fetchAll();
 */

const DB_HOST = 'localhost';
const DB_PORT = 3306;
const DB_NAME = 'db_toko';
const DB_USER = 'root';
const DB_PASS = '';          // XAMPP default: kosong

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME),
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // error jadi exception
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // hasil berupa array asosiatif
            PDO::ATTR_EMULATE_PREPARES   => false,                    // prepared statement asli
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit(
        '<h3 style="font-family:sans-serif">Koneksi database gagal</h3>' .
        '<p style="font-family:sans-serif">Pastikan <b>MySQL</b> di XAMPP sudah <b>Start</b> ' .
        'dan database <b>' . DB_NAME . '</b> sudah di-import (database/db_toko.sql).</p>' .
        '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>'
    );
}
