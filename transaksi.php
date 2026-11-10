<?php
$judul = 'Tambah Transaksi';
require __DIR__ . '/includes/header.php';

$pesan = $error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Stored Procedure dengan TCL (START TRANSACTION ... COMMIT / ROLLBACK) di dalamnya
        ambil($pdo, 'CALL TambahTransaksi(?, ?, ?, ?, @id_baru)', [
            (int) $_POST['pelanggan'],
            (int) $_POST['produk'],
            (int) $_POST['jumlah'],
            (float) $_POST['diskon'],
        ]);
        $id = $pdo->query('SELECT @id_baru')->fetchColumn();
        $pesan = "Transaksi #$id berhasil disimpan (COMMIT). Stok produk otomatis berkurang.";
    } catch (PDOException $ex) {
        $error = 'Transaksi dibatalkan (ROLLBACK): ' . pesan_error($ex);
    }
}

$pelanggan = $pdo->query('SELECT id_pelanggan, nama_pelanggan FROM tbl_pelanggan ORDER BY nama_pelanggan')->fetchAll();
$produk    = $pdo->query('SELECT id_produk, nama_produk, harga, stok FROM tbl_produk ORDER BY nama_produk')->fetchAll();
?>
<?php if ($pesan): ?><div class="alert ok"><?= e($pesan) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>

<div class="card">
  <h2>Form Transaksi</h2>
  <p class="ket">Memanggil <code>CALL TambahTransaksi(pelanggan, produk, jumlah, diskon%, @id)</code>.
     Coba jumlah lebih besar dari stok untuk melihat ROLLBACK.</p>
  <form method="post" class="inline">
    <label>Pelanggan
      <select name="pelanggan" required>
        <?php foreach ($pelanggan as $p): ?>
          <option value="<?= (int) $p['id_pelanggan'] ?>"><?= e($p['nama_pelanggan']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Produk
      <select name="produk" required>
        <?php foreach ($produk as $p): ?>
          <option value="<?= (int) $p['id_produk'] ?>">
            <?= e($p['nama_produk']) ?> &mdash; <?= e(rp($p['harga'])) ?> (stok <?= (int) $p['stok'] ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Jumlah <input type="number" name="jumlah" value="1" min="1" required></label>
    <label>Diskon (%) <input type="number" name="diskon" value="0" min="0" max="100" step="0.5"></label>
    <button>Simpan Transaksi</button>
  </form>
</div>

<div class="card">
  <h2>10 Transaksi Terakhir</h2>
  <?= tabel(ambil($pdo, 'SELECT * FROM view_laporan_penjualan ORDER BY id_transaksi DESC LIMIT 10'), ['total_belanja']) ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
