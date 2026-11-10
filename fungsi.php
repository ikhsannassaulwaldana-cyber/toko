<?php
$judul = 'Uji Stored Function';
require __DIR__ . '/includes/header.php';

$harga  = (float) ($_GET['harga']  ?? 1000000);
$jumlah = (int)   ($_GET['jumlah'] ?? 5);
$diskon = (float) ($_GET['diskon'] ?? 10);
$nilai  = (int)   ($_GET['nilai']  ?? 85);

$hasil = ambil($pdo, 'SELECT
    HitungDiskon(:h1, :d)        AS `HitungDiskon(harga, diskon%)`,
    HitungTotal(:h2, :j)         AS `HitungTotal(harga, jumlah)`,
    HitungPajak(:h3)             AS `HitungPajak(harga) 11%`,
    KategoriBelanja(:h4)         AS `KategoriBelanja(harga)`,
    KonversiNilai(:n)            AS `KonversiNilai(nilai)`', [
    ':h1' => $harga, ':d' => $diskon, ':h2' => $harga, ':j' => $jumlah,
    ':h3' => $harga, ':h4' => $harga, ':n' => $nilai,
])[0];
?>
<div class="card">
  <h2>Masukkan nilai uji</h2>
  <form method="get" class="inline">
    <label>Harga / Total <input type="number" name="harga" value="<?= e($harga) ?>" step="1000"></label>
    <label>Jumlah <input type="number" name="jumlah" value="<?= e($jumlah) ?>"></label>
    <label>Diskon (%) <input type="number" name="diskon" value="<?= e($diskon) ?>" step="0.5"></label>
    <label>Nilai (0-100) <input type="number" name="nilai" value="<?= e($nilai) ?>" min="0" max="100"></label>
    <button>Hitung</button>
  </form>
</div>

<div class="card">
  <h2>Hasil dari database</h2>
  <p class="ket">Dihitung oleh function di MariaDB/MySQL, bukan oleh PHP.</p>
  <div class="grid">
    <?php foreach ($hasil as $nama => $nilaiHasil): ?>
      <div class="stat"><b style="font-size:20px"><?= e(is_numeric($nilaiHasil) ? rp($nilaiHasil) : $nilaiHasil) ?></b><span><?= e($nama) ?></span></div>
    <?php endforeach; ?>
  </div>
  <p class="ket">Contoh dari modul: HitungPajak(1.000.000) = Rp110.000; HitungTotal(50.000, 5) = Rp250.000.</p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
