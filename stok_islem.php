<?php
include 'veritabani.php';

$urun_id = $_POST["urun_id"];
$miktar = floatval($_POST["miktar"]);
$islem_turu = $_POST["islem_turu"];

$urun = $baglanti->query("SELECT kalan_stok FROM urunler WHERE id = $urun_id")->fetch_assoc();
$yeni_stok = $islem_turu == "giris" ? $urun["kalan_stok"] + $miktar : $urun["kalan_stok"] - $miktar;

if ($yeni_stok < 0) $yeni_stok = 0;

$baglanti->query("UPDATE urunler SET kalan_stok = $yeni_stok WHERE id = $urun_id");
$baglanti->query("INSERT INTO stok_hareketleri (urun_id, miktar, islem_turu) VALUES ($urun_id, $miktar, '$islem_turu')");
?>
