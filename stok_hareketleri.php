<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'veritabani.php';

$sql = "SELECT 
    sh.id,
    sh.tarih,
    sh.islem_turu as tur,
    u.ad,
    sh.miktar,
    u.aciklama as urun_aciklama,
    sh.stokAciklama as islem_aciklama
FROM stok_hareketleri sh
JOIN urunler u ON sh.urun_id = u.id
ORDER BY sh.tarih DESC";

$result = $baglanti->query($sql);

if (!$result) {
    die("Sorgu hatası: " . $baglanti->error);
}

$hareketler = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $hareketler[] = array(
            'id' => $row['id'],
            'tarih' => date('d.m.Y H:i', strtotime($row['tarih'])),
            'tur' => $row['tur'] == 'giris' ? 'Giriş' : 'Çıkış',
            'ad' => $row['ad'],
            'miktar' => number_format($row['miktar'], 2, ',', '.') . ' adet',
            'urun_aciklama' => $row['urun_aciklama'],
            'islem_aciklama' => $row['islem_aciklama']
        );
    }
}

header('Content-Type: application/json');
echo json_encode($hareketler);
?> 