<?php
/** Fungsi bantu yang dipakai semua halaman. */

/** Escape output HTML (cegah XSS). */
function e($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

/** Format rupiah. */
function rp($angka): string
{
    return 'Rp' . number_format((float) $angka, 0, ',', '.');
}

/** Jalankan query (SELECT / CALL) dan kembalikan semua baris. */
function ambil(PDO $pdo, string $sql, array $params = []): array
{
    $st = $pdo->prepare($sql);
    $st->execute($params);
    $rows = $st->columnCount() ? $st->fetchAll() : [];
    $st->closeCursor();          // wajib agar CALL procedure tidak mengunci koneksi
    return $rows;
}

/** Tampilkan array hasil query sebagai tabel HTML. */
function tabel(array $rows, array $uang = []): string
{
    if (!$rows) {
        return '<p class="kosong">Tidak ada data.</p>';
    }
    $h = '<div class="tabel-wrap"><table><thead><tr>';
    foreach (array_keys($rows[0]) as $kolom) {
        $h .= '<th>' . e($kolom) . '</th>';
    }
    $h .= '</tr></thead><tbody>';
    foreach ($rows as $r) {
        $h .= '<tr>';
        foreach ($r as $kolom => $nilai) {
            $teks = in_array($kolom, $uang, true) ? rp($nilai) : ($nilai ?? '-');
            $h .= '<td>' . e($teks) . '</td>';
        }
        $h .= '</tr>';
    }
    return $h . '</tbody></table></div>';
}

/** Ambil pesan error yang ramah dari PDOException (mis. dari SIGNAL trigger/procedure). */
function pesan_error(PDOException $ex): string
{
    return $ex->errorInfo[2] ?? $ex->getMessage();
}
