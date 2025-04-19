<?php
include 'veritabani.php';

$stoklar = $baglanti->query("
    SELECT sh.id, sh.tarih, sh.islem_turu, sh.miktar, sh.stokAciklama, 
           u.ad AS urun_adi, u.aciklama AS urun_aciklama, u.birim
    FROM stok_hareketleri sh
    JOIN urunler u ON u.id = sh.urun_id
    ORDER BY sh.tarih DESC
");

while ($stok = $stoklar->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . date("d.m.Y H:i", strtotime($stok['tarih'])) . "</td>";
    echo "<td>" . htmlspecialchars($stok['islem_turu']) . "</td>";
    echo "<td>" . htmlspecialchars($stok['urun_adi']) . "</td>";
    echo "<td>" . number_format($stok['miktar'], 2) . " " . $stok['birim'] . "</td>";
    echo "<td>" . htmlspecialchars($stok['urun_aciklama']) . "</td>";
    echo "<td>" . htmlspecialchars($stok['stokAciklama']) . "</td>";
    echo "<td><button class='btn btn-danger btn-sm stokSil' data-id='" . $stok['id'] . "'>Sil</button></td>";
    echo "</tr>";
}

?>
