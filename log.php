<?php
$judul = 'Log Aktivitas (Trigger)';
require __DIR__ . '/includes/header.php';
?>
<div class="card">
  <p class="ket">Tabel <code>tbl_log</code> diisi otomatis oleh trigger saat pelanggan atau transaksi ditambah, diubah, dan dihapus.
     Coba di halaman <a href="pelanggan.php">Pelanggan</a> atau <a href="transaksi.php">Transaksi</a>, lalu lihat di sini.</p>
  <?= tabel(ambil($pdo, 'SELECT * FROM tbl_log ORDER BY id_log DESC LIMIT 50')) ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
