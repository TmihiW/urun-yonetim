<?php
include 'veritabani.php';

$urun_id = $_POST["urun_id"];
$miktar = floatval($_POST["miktar"]);
$islem_turu = $_POST["islem_turu"];
$stokAciklama = $_POST["stokAciklama"];

$urun = $baglanti->query("SELECT kalan_stok FROM urunler WHERE id = $urun_id")->fetch_assoc();
// if else
$yeni_stok = $islem_turu == "giris" ? $urun["kalan_stok"] + $miktar : $urun["kalan_stok"] - $miktar;

if ($yeni_stok < 0) $yeni_stok = 0;

$baglanti->query("UPDATE urunler SET kalan_stok = $yeni_stok WHERE id = $urun_id");
$baglanti->query("INSERT INTO stok_hareketleri set urun_id='$urun_id', miktar='$miktar', islem_turu='$islem_turu', stokAciklama='$stokAciklama'");
?>
