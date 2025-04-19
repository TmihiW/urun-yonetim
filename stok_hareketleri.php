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

<script>
    stoklariYukle();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="js/app2.js"></script>