<?php
$baglanti = new mysqli("localhost", "root", "", "urun_yonetim");
$baglanti->set_charset("utf8");
if ($baglanti->connect_error) {
    die("Bağlantı hatası: " . $baglanti->connect_error);
}
?>
