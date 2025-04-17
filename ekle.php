<?php
include 'veritabani.php';

// kaydet ürün
if ($_POST["islem"] == "ekle") {
    $ad = $_POST["ad"];
    $aciklama = $_POST["aciklama"];
    $birim = $_POST["birim"];
    $baglanti->query("INSERT INTO urunler (ad, aciklama, birim) VALUES ('$ad', '$aciklama', '$birim')");
}

// Ürünleri yükle
if ($_POST["islem"] == "listele") {
    $arama = trim($_POST["arama"]);
    $sorgu = "SELECT * FROM urunler";

    if (!empty($arama)) {
        $arrayData = explode(" ", $arama);
        $sorgu .= " WHERE ";

        $aramaKosullari = [];
        foreach ($arrayData as $key) {
            $key = $baglanti->real_escape_string($key);
            $aramaKosullari[] = "(
                ad COLLATE utf8_turkish_ci LIKE '%$key%' OR 
                birim COLLATE utf8_turkish_ci LIKE '%$key%' OR 
                kalan_stok LIKE '%$key%'
            )";
        }

        $sorgu .= implode(" AND ", $aramaKosullari);
    }

    $sonuc = $baglanti->query($sorgu);

    echo '<table class="table table-bordered"><tr><th>Ad</th><th>Açıklama</th><th>Birim</th><th>Stok</th></tr>';
    while ($satir = $sonuc->fetch_assoc()) {
        echo "<tr>
            <td>{$satir['ad']}</td>
            <td>{$satir['aciklama']}</td>
            <td>{$satir['birim']}</td>
            <td>{$satir['kalan_stok']}</td>
        </tr>";
    }
    echo '</table>';
}



if ($_POST["islem"] == "urunSecimi") {
    $sonuc = $baglanti->query("SELECT id, ad FROM urunler");
    echo "<option value=''>Ürün Seçin</option>";
    while ($s = $sonuc->fetch_assoc()) {
        echo "<option value='{$s['id']}'>{$s['ad']}</option>";
    }
}
?>
