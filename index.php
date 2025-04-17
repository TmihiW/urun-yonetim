<?php include 'veritabani.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>Ürün Listesi</h2>
        <div class="mb-3 d-flex justify-content-between">
            <input type="text" class="form-control w-50" id="arama" placeholder="Ad, birim, stok ara...">
            <div>
                <button class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#urunEkleModal">Yeni Ürün</button>
                <button class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#stokModal">Stok Giriş/Çıkış</button>
            </div>
        </div>
        <div id="urunListesi"></div>
    </div>
    <?php include 'modal/urun_ekle_modal.php'; ?>
    <?php include 'modal/stok_modal.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="js/app2.js"></script>
</body>
</html>
