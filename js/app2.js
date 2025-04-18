function urunleriYukle(query = "") {
    $.ajax({
        url: "ekle.php",
        method: "POST",
        data: { islem: "listele", arama: query },
        success: function (veri) {
            $("#urunListesi").html(veri);
            stokUrunleriDoldur();
        }
    });
};
function stoklariYukle() {
    $.ajax({
        url: "stok_listele.php",
        method: "POST",
        success: function (veri) {
            $("#stokHareketleriListesi").html(veri);
        }
    });
};













$(document).ready(function () {
    // Navbar tıklamaları
    $("#navUrunler").click(function (e) {
        e.preventDefault();
        sayfaYukle('urun_listele.php');
    });

    $("#navStok").click(function (e) {
        sayfaYukle('stok_hareketleri.php');
    });
    function sayfaYukle(sayfa) {
        $("#icerik").load(sayfa);
    }

    
    // Arama kutusu
    $("#arama").on("input", function () {
        urunleriYukle($(this).val());
    });
    
    // Ürün ekle
    $("#kaydetUrun").click(function () {
        const ad = $("#urunAdi").val().trim();
        const aciklama = $("#urunAciklama").val().trim();
        const birim = $("#urunBirim").val();

        if (!ad || !aciklama || !birim) {
            alert("Tüm alanları doldurunuz.");
            return;
        }

        $.ajax({
            url: "ekle.php",
            method: "POST",
            data: {
                islem: "ekle",
                ad: ad,
                aciklama: aciklama,
                birim: birim
            },
            success: function (sonuc) {
                $("#urunEkleModal").modal("hide");
                urunleriYukle();
                $("#urunAdi, #urunAciklama").val("");
                $("#urunBirim").val("");
            }
        });
    });
    // Stok giriş çıkış
    $("#kaydetStok").click(function () {
        const urun_id = $("#stokUrun").val();
        const miktar = parseFloat($("#stokMiktar").val());
        const islem_turu = $("#stokIslem").val();
        const stokAciklama = $("#stokAciklama").val();

        if (!urun_id || isNaN(miktar) || miktar <= 0) {
            alert("Geçerli miktar giriniz.");
            return;
        }

        $.ajax({
            url: "stok_islem.php",
            method: "POST",
            data: {
                urun_id: urun_id,
                miktar: miktar,
                islem_turu: islem_turu,
                stokAciklama: stokAciklama
            },
            success: function () {
                $("#stokModal").modal("hide");
                urunleriYukle();
                $("#stokMiktar").val("");
            }
        });
    });
    //Stok giriş/çıkış modal için ürünleri yükle (urunleriYukle);
    function stokUrunleriDoldur() {
        $.ajax({
            url: "ekle.php",
            method: "POST",
            data: { islem: "urunSecimi" },
            success: function (veri) {
                $("#stokUrun").html(veri);
            }
        });
    };

    $(document).on("click", ".stokSil", function () {
        const id = $(this).data("id");

        if (confirm("Bu stok kaydını silmek istiyor musunuz?")) {
            $.ajax({
                url: "stok_islem.php",
                method: "POST",
                data: { islem: "sil", id: id },
                success: function (cevap) {
                    if (cevap.trim() === "ok") {
                        stoklariYukle(); // Listeyi yenile
                    } else {
                        alert("Silme işlemi başarısız oldu.");
                    }
                }
            });
        }
    });
});
