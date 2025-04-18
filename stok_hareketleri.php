<?php include 'veritabani.php'; ?>

<div class="container">
    <!-- Stok hareketlerini listeleme -->
    <h3 class="mt-4">Geçmiş Stok Hareketleri</h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Tarih</th>
                <th>Türü</th>
                <th>Ürün Adı</th>
                <th>İşlem Miktarı</th>
                <th>Ürün Açıklaması</th>
                <th>İşlem Açıklaması</th>
            </tr>
        </thead>
        <tbody id="stokHareketleriListesi">
            <!-- AJAX ile doldurulacak -->
        </tbody>
    </table>
</div>
