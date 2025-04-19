<?php include 'veritabani.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .nav-tabs {
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 1000;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
</head>
<body class="p-4">
<?php include 'navbar.php'; ?>

    <div class="container" style="margin-top: 80px;">
        <div id="icerik"></div>
    </div>

    <!--

    <div class="container">
        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="urunler-tab" data-bs-toggle="tab" data-bs-target="#urunler" type="button" role="tab" aria-controls="urunler" aria-selected="true">Ürün Listesi</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="stokHareketleri-tab" data-bs-toggle="tab" data-bs-target="#stokHareketleri" type="button" role="tab" aria-controls="stokHareketleri" aria-selected="false">Stok Hareketleri</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="urunler" role="tabpanel" aria-labelledby="urunler-tab">
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
            <div class="tab-pane fade" id="stokHareketleri" role="tabpanel" aria-labelledby="stokHareketleri-tab">
                <h2>Stok Hareketleri</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Tarihi</th>
                                <th>Türü</th>
                                <th>Adı</th>
                                <th>İşlem Miktarı</th>
                                <th>Ürün Açıklaması</th>
                                <th>İşlem Açıklaması</th>
                            </tr>
                        </thead>
                        <tbody id="stokHareketleriListesi">
                            <!-- Stok hareketleri buraya dinamik olarak eklenecek -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php /*include 'modal/urun_ekle_modal.php';*/?>
    <?php /*include 'modal/stok_modal.php'; */ ?>
    -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="js/app2.js"></script>
</body>
</html>
