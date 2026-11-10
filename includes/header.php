<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/helpers.php';

$menu = [
    'index.php'     => 'Beranda',
    'pelanggan.php' => 'Pelanggan',
    'transaksi.php' => 'Transaksi',
    'laporan.php'   => 'Laporan',
    'analisis.php'  => 'Subquery',
    'fungsi.php'    => 'Function',
    'tcl.php'       => 'TCL',
    'explain.php'   => 'EXPLAIN',
    'log.php'       => 'Log Trigger',
];
$halaman = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($judul ?? 'Toko') ?> - Sistem Penjualan Toko</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="top">
  <div class="wrap">
    <strong class="brand">Toko<span>Ku</span></strong>
    <nav>
      <?php foreach ($menu as $file => $nama): ?>
        <a href="<?= $file ?>" class="<?= $file === $halaman ? 'aktif' : '' ?>"><?= e($nama) ?></a>
      <?php endforeach; ?>
    </nav>
  </div>
</header>
<main class="wrap">
  <h1><?= e($judul ?? '') ?></h1>
