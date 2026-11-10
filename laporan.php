<?php
$judul = 'Laporan Penjualan';
require __DIR__ . '/includes/header.php';
?>
<div class="card">
  <h2>1. View: view_laporan_penjualan</h2>
  <p class="ket">View hasil JOIN tbl_transaksi dan tbl_pelanggan.</p>
  <pre class="sql">SELECT * FROM view_laporan_penjualan ORDER BY id_transaksi;</pre>
  <?= tabel(ambil($pdo, 'SELECT * FROM view_laporan_penjualan ORDER BY id_transaksi'), ['total_belanja']) ?>
</div>

<div class="card">
  <h2>2. View agregasi: view_total_belanja</h2>
  <p class="ket">COUNT + SUM per pelanggan dan kategori dari Function <code>KategoriBelanja()</code>.</p>
  <pre class="sql">SELECT * FROM view_total_belanja ORDER BY total_belanja DESC;</pre>
  <?= tabel(ambil($pdo, 'SELECT * FROM view_total_belanja ORDER BY total_belanja DESC'), ['total_belanja']) ?>
</div>

<div class="card">
  <h2>3. JOIN 5 tabel: view_detail_transaksi</h2>
  <p class="ket">Detail transaksi + pelanggan + produk + kategori.</p>
  <pre class="sql">SELECT * FROM view_detail_transaksi ORDER BY id_transaksi;</pre>
  <?= tabel(ambil($pdo, 'SELECT * FROM view_detail_transaksi ORDER BY id_transaksi, nama_produk'), ['harga', 'subtotal']) ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
