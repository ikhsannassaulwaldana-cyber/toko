<?php
$judul = 'TCL: COMMIT dan ROLLBACK';
require __DIR__ . '/includes/header.php';

$hitung = fn() => (int) $pdo->query("SELECT COUNT(*) FROM tbl_pelanggan WHERE kota = 'Uji'")->fetchColumn();
$langkah = [];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    try {
        if ($aksi === 'commit' || $aksi === 'rollback') {
            $langkah[] = 'Data "Uji" sebelum transaksi: ' . $hitung();
            $pdo->beginTransaction();                                  // START TRANSACTION
            $st = $pdo->prepare("INSERT INTO tbl_pelanggan (nama_pelanggan, kota) VALUES (?, 'Uji')");
            $st->execute(['Uji ' . strtoupper($aksi) . ' ' . date('H:i:s')]);
            $langkah[] = 'INSERT dijalankan. Data "Uji" di dalam transaksi: ' . $hitung();
            if ($aksi === 'commit') {
                $pdo->commit();                                        // COMMIT
                $langkah[] = 'COMMIT: perubahan disimpan permanen.';
            } else {
                $pdo->rollBack();                                      // ROLLBACK
                $langkah[] = 'ROLLBACK: perubahan dibatalkan (tbl_log dari trigger juga ikut batal).';
            }
            $langkah[] = 'Data "Uji" setelah transaksi: ' . $hitung();
        } elseif ($aksi === 'bersih') {
            $n = $pdo->exec("DELETE FROM tbl_pelanggan WHERE kota = 'Uji'");
            $langkah[] = "$n data uji dihapus.";
        }
    } catch (PDOException $ex) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        $error = pesan_error($ex);
    }
}
?>
<?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>

<div class="card">
  <h2>Simulasi transaksi database</h2>
  <p class="ket">Tiap tombol menjalankan: <code>START TRANSACTION &rarr; INSERT &rarr; COMMIT / ROLLBACK</code>.</p>
  <form method="post" class="inline">
    <button name="aksi" value="commit"   class="hijau">Uji COMMIT</button>
    <button name="aksi" value="rollback" class="merah">Uji ROLLBACK</button>
    <button name="aksi" value="bersih"   class="kecil" style="background:#64748b">Hapus data uji</button>
  </form>
</div>

<?php if ($langkah): ?>
<div class="card">
  <h2>Hasil</h2>
  <ol class="cek">
    <?php foreach ($langkah as $l): ?><li><?= e($l) ?></li><?php endforeach; ?>
  </ol>
</div>
<?php endif; ?>

<div class="card">
  <h2>Data uji saat ini (kota = 'Uji')</h2>
  <?= tabel(ambil($pdo, "SELECT * FROM tbl_pelanggan WHERE kota = 'Uji'")) ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
