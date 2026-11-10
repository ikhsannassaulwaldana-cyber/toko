<?php
$judul = 'Data Pelanggan';
require __DIR__ . '/includes/header.php';

$pesan = $error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $aksi = $_POST['aksi'] ?? '';
        if ($aksi === 'tambah') {
            // Stored Procedure -> memicu trigger AFTER INSERT
            ambil($pdo, 'CALL TambahPelanggan(?, ?)', [trim($_POST['nama']), trim($_POST['kota'])]);
            $pesan = 'Pelanggan ditambahkan lewat procedure TambahPelanggan(). Trigger AFTER INSERT mencatat ke tbl_log.';
        } elseif ($aksi === 'ubah') {
            // UPDATE biasa -> memicu trigger AFTER UPDATE
            $st = $pdo->prepare('UPDATE tbl_pelanggan SET nama_pelanggan = ?, kota = ? WHERE id_pelanggan = ?');
            $st->execute([trim($_POST['nama']), trim($_POST['kota']), (int) $_POST['id']]);
            $pesan = 'Data diubah. Trigger AFTER UPDATE mencatat nilai lama dan baru ke tbl_log.';
        } elseif ($aksi === 'hapus') {
            // DELETE -> memicu trigger AFTER DELETE
            $st = $pdo->prepare('DELETE FROM tbl_pelanggan WHERE id_pelanggan = ?');
            $st->execute([(int) $_POST['id']]);
            $pesan = 'Pelanggan dihapus. Trigger AFTER DELETE mencatat ke tbl_log.';
        }
    } catch (PDOException $ex) {
        $error = ($ex->getCode() === '23000')
            ? 'Pelanggan tidak bisa dihapus karena sudah punya transaksi (foreign key).'
            : pesan_error($ex);
    }
}

$daftar = ambil($pdo, 'CALL TampilPelanggan()');             // Stored Procedure
ambil($pdo, 'CALL HitungPelanggan(@j)');                      // OUT parameter
$total = $pdo->query('SELECT @j')->fetchColumn();
?>
<?php if ($pesan): ?><div class="alert ok"><?= e($pesan) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>

<div class="card">
  <h2>Tambah Pelanggan</h2>
  <p class="ket">Memanggil <code>CALL TambahPelanggan(nama, kota)</code>.</p>
  <form method="post" class="inline">
    <input type="hidden" name="aksi" value="tambah">
    <label>Nama <input name="nama" required maxlength="100"></label>
    <label>Kota <input name="kota" required maxlength="50"></label>
    <button>Tambah</button>
  </form>
</div>

<div class="card">
  <h2>Daftar Pelanggan (<?= (int) $total ?>)</h2>
  <p class="ket">Data dari <code>CALL TampilPelanggan()</code>; jumlah dari <code>CALL HitungPelanggan(@j)</code>.</p>
  <div class="tabel-wrap">
  <table>
    <thead><tr><th>ID</th><th>Nama</th><th>Kota</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($daftar as $p): $f = 'f' . (int) $p['id_pelanggan']; ?>
      <tr>
        <td>
          <?= (int) $p['id_pelanggan'] ?>
          <form id="<?= $f ?>" method="post"><input type="hidden" name="id" value="<?= (int) $p['id_pelanggan'] ?>"></form>
        </td>
        <td><input form="<?= $f ?>" name="nama" value="<?= e($p['nama_pelanggan']) ?>" required></td>
        <td><input form="<?= $f ?>" name="kota" value="<?= e($p['kota']) ?>" required></td>
        <td>
          <button form="<?= $f ?>" name="aksi" value="ubah" class="kecil hijau">Simpan</button>
          <button form="<?= $f ?>" name="aksi" value="hapus" class="kecil merah" onclick="return confirm('Hapus pelanggan ini?')">Hapus</button>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
