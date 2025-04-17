$(document).ready(function () {
    urunleriYukle();

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
    }

    $("#arama").on("input", function () {
        urunleriYukle($(this).val());
    });

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

    $("#kaydetStok").click(function () {
        const urun_id = $("#stokUrun").val();
        const miktar = parseFloat($("#stokMiktar").val());
        const islem_turu = $("#stokIslem").val();

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
                islem_turu: islem_turu
            },
            success: function () {
                $("#stokModal").modal("hide");
                urunleriYukle();
                $("#stokMiktar").val("");
            }
        });
    });

    function stokUrunleriDoldur() {
        $.ajax({
            url: "ekle.php",
            method: "POST",
            data: { islem: "urunSecimi" },
            success: function (veri) {
                $("#stokUrun").html(veri);
            }
        });
    }
});
