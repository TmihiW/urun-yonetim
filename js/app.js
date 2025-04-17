$(document).ready(function () {
    urunleriYukle();

    function urunleriYukle(query = "") {
        $.post("ekle.php", { islem: "listele", arama: query }, function (veri) {
            $("#urunListesi").html(veri);
            stokUrunleriDoldur();
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

        $.post("ekle.php", { islem: "ekle", ad, aciklama, birim }, function (sonuc) {
            $("#urunEkleModal").modal("hide");
            urunleriYukle();
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

        $.post("stok_islem.php", { urun_id, miktar, islem_turu }, function () {
            $("#stokModal").modal("hide");
            urunleriYukle();
        });
    });

    function stokUrunleriDoldur() {
        $.post("ekle.php", { islem: "urunSecimi" }, function (veri) {
            $("#stokUrun").html(veri);
        });
    }
});
