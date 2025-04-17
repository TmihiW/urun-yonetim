# 📘 Proje Açıklaması – Ürün Yönetim Sistemi

Bu döküman, projedeki dosyaların ne işe yaradığını ve işlemlerin nasıl çalıştığını açıklar.

---

## 🔁 GENEL MANTIĞI

Tüm işlemler `index.php` üzerinden gerçekleşir. Sayfa yenilenmez. AJAX kullanılarak veriler arka planda PHP dosyalarına gönderilir ve cevap alınır.

---

## 🏠 Ana Sayfa – Ürün Listesi

**Dosya:** `index.php` + `ekle.php`

- Sayfa açıldığında `urunleriYukle()` fonksiyonu çalışır.
- Bu fonksiyon, `ekle.php` dosyasına AJAX ile `islem=listele` verisi gönderir.
- `ekle.php`, ürünleri veritabanından çekip HTML tablo olarak geri gönderir.
- Liste sayfada gösterilir.

---

## 🔍 Ürün Arama

**Dosya:** `index.php` + `ekle.php`

- Arama kutusuna yazdıkça `urunleriYukle(query)` fonksiyonu çalışır.
- AJAX ile yine `ekle.php`'ye `arama` parametresi gönderilir.
- PHP'de `explode()` ve `LIKE` sorgusu ile Türkçe karakter duyarlı arama yapılır.
- Sonuçlar filtrelenmiş olarak geri döner.

---

## ➕ Yeni Ürün Ekleme

**Dosya:** `index.php` + `ekle.php`

- Ürün ekle modalında form doldurulup "Kaydet" tıklanır.
- AJAX ile `ekle.php`'ye `islem=ekle` + ürün verileri gönderilir.
- PHP tarafında INSERT işlemi yapılır.
- Modal kapanır, liste güncellenir.

---

## 🔄 Stok Giriş / Çıkış

**Dosya:** `index.php` + `stok_islem.php`

- Modal açılır, ürün seçilir, miktar ve işlem türü girilir.
- AJAX ile `stok_islem.php` dosyasına veriler gönderilir:
    - `urun_id`, `miktar`, `islem_turu`
- PHP dosyası:
    - Eğer `giris`: `kalan_stok + miktar`
    - Eğer `cikis`: `kalan_stok - miktar`
- İşlem sonrası liste güncellenir.

---

## 📂 Dosya Yapısı

```
/urun-yonetim
├── index.php              -> Ana sayfa ve arayüz
├── ekle.php               -> Ürün listeleme ve ekleme
├── stok_islem.php         -> Stok giriş/çıkış işlemleri
├── db.php                 -> Veritabanı bağlantı ayarı
├── urunler.sql            -> Veritabanı tablo yapısı
├── js/
│   └── app.js             -> AJAX işlemleri
└── doc/
    └── açıklama.md        -> (bu döküman)
```

---

Hazırlayan: [TmihiW](https://github.com/TmihiW) 🤲