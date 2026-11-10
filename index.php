<?php
$judul = 'Beranda';
require __DIR__ . '/includes/header.php';

$versi = $pdo->query('SELECT VERSION()')->fetchColumn();
$db    = $pdo->query('SELECT DATABASE()')->fetchColumn();

$tabel = ['tbl_kategori', 'tbl_pelanggan', 'tbl_produk', 'tbl_transaksi', 'tbl_detail_transaksi', 'tbl_log'];
?>
<div class="alert ok">
  Koneksi database <b>berhasil</b> &mdash; database <b><?= e($db) ?></b>, server <b><?= e($versi) ?></b>.
</div>

<section class="hero">
  <span class="hero-tag">Ringkasan sistem</span>
  <h2>Semua aktivitas tokomu, dalam satu tampilan.</h2>
  <p>Pantau data dan jelajahi modul sistem penjualan dengan lebih mudah. Setiap bagian terhubung langsung ke database.</p>
  <a class="btn hero-link" href="laporan.php">Lihat laporan penjualan</a>
</section>

<div class="grid">
  <?php foreach ($tabel as $t): ?>
    <div class="stat">
      <b><?= (int) $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn() ?></b>
      <span><?= e($t) ?></span>
    </div>
  <?php endforeach; ?>
</div>

<div class="card">
  <h2>Pengujian Akhir</h2>
  <p class="ket">Setiap poin pengujian di modul Bab 6 punya halaman demonstrasi sendiri.</p>
  <ol class="cek">
    <li>View menampilkan laporan &rarr; <a href="laporan.php">Laporan</a></li>
    <li>JOIN beberapa tabel &rarr; <a href="laporan.php">Laporan (detail transaksi)</a></li>
    <li>Subquery untuk analisis &rarr; <a href="analisis.php">Subquery</a></li>
    <li>Stored Procedure dipanggil &rarr; <a href="transaksi.php">Transaksi</a> &amp; <a href="pelanggan.php">Pelanggan</a></li>
    <li>Function menghitung dengan benar &rarr; <a href="fungsi.php">Function</a></li>
    <li>Trigger berjalan otomatis &rarr; <a href="pelanggan.php">Pelanggan</a> &amp; <a href="log.php">Log Trigger</a></li>
    <li>COMMIT dan ROLLBACK &rarr; <a href="tcl.php">TCL</a></li>
    <li>Analisis query dengan EXPLAIN &rarr; <a href="explain.php">EXPLAIN</a></li>
  </ol>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
