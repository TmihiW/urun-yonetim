<?php
include 'veritabani.php';

// kaydet ürün
if ($_POST["islem"] == "ekle") {
    $ad = $_POST["ad"];
    $aciklama = $_POST["aciklama"];
    $birim = $_POST["birim"];
    $baglanti->query("INSERT INTO urunler (ad, aciklama, birim) VALUES ('$ad', '$aciklama', '$birim')");
}


// Ürünleri yükle türkçe karakter tanıma ve foreach parçalı arama

/* 
if ($_POST["islem"] == "listele") {
    $arama = trim($_POST["arama"]);
    $sorgu = "SELECT * FROM urunler";

    if (isset($arama) && trim($arama) !== "") {
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
*/



//0 koşulu yok sadece stokda varsa göster
/*
if ($_POST["islem"] == "listele") {
    $arama = trim($_POST["arama"]);
    $sorgu = "SELECT * FROM urunler";

    if (isset($arama) && trim($arama) !== "") {
        $arrayData = explode(" ", $arama);
        $sorgu .= " WHERE ";

        $aramaKosullari = [];
        foreach ($arrayData as $key) {
            if (is_numeric($key)) {
                $miktar = (int)$key; // stok sayısı
            } else {
                $key = $baglanti->real_escape_string($key);
                $filtreler[] = "(ad LIKE '%$key%' COLLATE utf8_turkish_ci 
                                OR birim LIKE '%$key%' COLLATE utf8_turkish_ci)";
            }
        }
        if ($miktar !== null) {
            $filtreler[] = "kalan_stok >= $miktar";
        }

        $where = implode(" AND ", $filtreler);
        $sorgu = "SELECT * FROM urunler WHERE $where ORDER BY ad ASC";
    } else {
        $sorgu = "SELECT * FROM urunler ORDER BY ad ASC";
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
*/


// 0 koşulu + stok varsa göster
if ($_POST["islem"] == "listele") {
    $arama = trim($_POST["arama"]);
    $sorgu = "SELECT * FROM urunler";

    if (isset($arama) && trim($arama) !== "") {
        $arrayData = explode(" ", $arama);
        $sorgu .= " WHERE ";

        $aramaKosullari = [];
        $filtreler = [];

        // Aramayı ayrı olarak analiz ediyoruz
        $aramaMiktar = null;
        foreach ($arrayData as $key) {
            if (is_numeric($key)) {
                $aramaMiktar = (int)$key; // stok sayısı
            } else {
                $key = $baglanti->real_escape_string($key);
                $filtreler[] = "(ad LIKE '%$key%' COLLATE utf8_turkish_ci 
                                 OR birim LIKE '%$key%' COLLATE utf8_turkish_ci)";
            }
        }

        // Miktar varsa, kalan_stok ile filtreleme yapıyoruz
        if ($aramaMiktar !== null) {
            if ($aramaMiktar === 0) {
                $aramaKosullari[] = "kalan_stok = 0"; // Stokta 0 olanları listele
            } else {
                $aramaKosullari[] = "kalan_stok >= $aramaMiktar"; // Stok miktarına göre filtrele
            }
        }

        // Ad ve stok koşullarını birleştiriyoruz
        if (!empty($filtreler)) {
            $aramaKosullari[] = "(" . implode(" and ", $filtreler) . ")";
        }

        // Eğer arama koşulları varsa, WHERE kısmını oluşturuyoruz
        if (!empty($aramaKosullari)) {
            $where = implode(" AND ", $aramaKosullari);
            $sorgu = "SELECT * FROM urunler WHERE $where ORDER BY ad ASC";
        } else {
            $sorgu = "SELECT * FROM urunler ORDER BY ad ASC";
        }
    } else {
        $sorgu = "SELECT * FROM urunler ORDER BY ad ASC";
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
