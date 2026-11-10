<?php
$judul = 'Analisis dengan Subquery';
require __DIR__ . '/includes/header.php';

$analisis = [
    ['Transaksi di atas rata-rata',
     'SELECT * FROM tbl_transaksi
WHERE total_belanja > (SELECT AVG(total_belanja) FROM tbl_transaksi)
ORDER BY total_belanja DESC',
     ['total_belanja']],

    ['Pelanggan yang belum pernah bertransaksi (NOT EXISTS)',
     'SELECT * FROM tbl_pelanggan p
WHERE NOT EXISTS (SELECT 1 FROM tbl_transaksi t WHERE t.id_pelanggan = p.id_pelanggan)',
     []],

    ['Pelanggan dengan transaksi terbanyak',
     'SELECT nama_pelanggan, total_transaksi FROM view_total_belanja
WHERE total_transaksi = (SELECT MAX(total_transaksi) FROM view_total_belanja)',
     []],

    ['Produk yang pernah terjual (IN)',
     'SELECT nama_produk, harga FROM tbl_produk
WHERE id_produk IN (SELECT id_produk FROM tbl_detail_transaksi)',
     ['harga']],

    ['Lima transaksi dengan nilai terbesar',
     'SELECT * FROM view_laporan_penjualan ORDER BY total_belanja DESC LIMIT 5',
     ['total_belanja']],

    ['Gabungan data dengan UNION',
     "SELECT nama_pelanggan AS nama, 'Pelanggan' AS sumber FROM tbl_pelanggan
UNION
SELECT nama_produk, 'Produk' FROM tbl_produk",
     []],
];
?>
<?php foreach ($analisis as $i => [$nama, $sql, $uang]): ?>
  <div class="card">
    <h2><?= $i + 1 ?>. <?= e($nama) ?></h2>
    <pre class="sql"><?= e($sql) ?></pre>
    <?= tabel(ambil($pdo, $sql), $uang) ?>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
