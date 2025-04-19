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
    }

    // Stok hareketlerini yükle
    function stokHareketleriniYukle() {
        console.log('Stok hareketleri yükleniyor...');
        $.ajax({
            url: 'stok_hareketleri.php',
            type: 'GET',
            success: function(response) {
                console.log('Gelen veri:', response);
                let html = '';
                if (response && response.length > 0) {
                    response.forEach(function(hareket) {
                        html += `
                            <tr>
                                <td>${hareket.tarih}</td>
                                <td>${hareket.tur}</td>
                                <td>${hareket.ad}</td>
                                <td>${hareket.miktar}</td>
                                <td>${hareket.urun_aciklama}</td>
                                <td>${hareket.islem_aciklama}</td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm sil-btn" data-id="${hareket.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="7" class="text-center">Henüz stok hareketi bulunmamaktadır.</td></tr>';
                }
                $('#stokHareketleriListesi').html(html);

                // Silme butonlarına event listener ekle
                $('.sil-btn').on('click', function() {
                    const id = $(this).data('id');
                    stokHareketiSil(id);
                });
            },
            error: function(xhr, status, error) {
                console.error('Stok hareketleri yüklenirken hata oluştu:', error);
                console.error('XHR:', xhr);
                console.error('Status:', status);
                $('#stokHareketleriListesi').html('<tr><td colspan="7" class="text-center text-danger">Veriler yüklenirken bir hata oluştu.</td></tr>');
            }
        });
    }

    // Stok hareketi silme fonksiyonu
    function stokHareketiSil(id) {
        if (confirm('Bu stok hareketini silmek istediğinizden emin misiniz?')) {
            console.log('Silme isteği gönderiliyor, ID:', id);
            $.ajax({
                url: 'stok_sil.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    console.log('Sunucu yanıtı:', response);
                    if (response.success) {
                        alert('Kayıt başarıyla silindi');
                        stokHareketleriniYukle(); // Tabloyu yenile
                    } else {
                        let hataMesaji = 'Silme işlemi sırasında bir hata oluştu:\n';
                        hataMesaji += response.error + '\n\n';
                        if (response.details) {
                            hataMesaji += 'Detaylar:\n';
                            hataMesaji += JSON.stringify(response.details, null, 2);
                        }
                        alert(hataMesaji);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax hatası:', {xhr, status, error});
                    let hataMesaji = 'Silme işlemi sırasında bir hata oluştu:\n';
                    hataMesaji += 'Durum: ' + status + '\n';
                    hataMesaji += 'Hata: ' + error + '\n\n';
                    if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            hataMesaji += 'Sunucu yanıtı:\n' + JSON.stringify(response, null, 2);
                        } catch (e) {
                            hataMesaji += 'Sunucu yanıtı:\n' + xhr.responseText;
                        }
                    }
                    alert(hataMesaji);
                }
            });
        }
    }

    // Sayfa yüklendiğinde ve stok hareketleri sekmesi açıldığında verileri yükle
    // İlk yükleme
    stokHareketleriniYukle();
    
    // Sekme değiştiğinde yeniden yükle
    $('#stokHareketleri-tab').on('shown.bs.tab', function (e) {
        stokHareketleriniYukle();
    });
});
