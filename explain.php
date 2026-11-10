<?php
$judul = 'Analisis Query dengan EXPLAIN';
require __DIR__ . '/includes/header.php';

$kasus = [
    ['Dengan index idx_transaksi_tanggal',
     "SELECT * FROM tbl_transaksi WHERE tanggal = '2026-10-05'"],
    ['Tanpa index (IGNORE INDEX) -> bandingkan kolom type dan rows',
     "SELECT * FROM tbl_transaksi IGNORE INDEX (idx_transaksi_tanggal) WHERE tanggal = '2026-10-05'"],
    ['JOIN pelanggan - transaksi',
     'SELECT p.nama_pelanggan, t.total_belanja
FROM tbl_pelanggan p JOIN tbl_transaksi t ON t.id_pelanggan = p.id_pelanggan
WHERE p.nama_pelanggan = \'Andi\''],
    ['View detail transaksi untuk satu transaksi',
     'SELECT * FROM view_detail_transaksi WHERE id_transaksi = 19'],
];
?>
<div class="card">
  <p class="ket">Perhatikan kolom <b>type</b> (ALL = baca seluruh tabel), <b>key</b> (index yang dipakai), dan <b>rows</b> (perkiraan baris yang dibaca).
  Dengan data sedikit hasilnya mirip; tambahkan ribuan baris untuk melihat perbedaan nyata.</p>
</div>

<?php foreach ($kasus as $i => [$nama, $sql]): ?>
  <div class="card">
    <h2><?= $i + 1 ?>. <?= e($nama) ?></h2>
    <pre class="sql">EXPLAIN <?= e($sql) ?></pre>
    <?= tabel(ambil($pdo, 'EXPLAIN ' . $sql)) ?>
  </div>
<?php endforeach; ?>

<div class="card">
  <h2>Daftar index tbl_transaksi</h2>
  <pre class="sql">SHOW INDEX FROM tbl_transaksi</pre>
  <?= tabel(array_map(
        fn($r) => array_intersect_key($r, array_flip(['Table', 'Non_unique', 'Key_name', 'Seq_in_index', 'Column_name'])),
        $pdo->query('SHOW INDEX FROM tbl_transaksi')->fetchAll()
  )) ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
