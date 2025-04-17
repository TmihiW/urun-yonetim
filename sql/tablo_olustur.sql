CREATE TABLE urunler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(100),
    aciklama TEXT,
    birim ENUM('kg', 'adet'),
    kalan_stok FLOAT DEFAULT 0
);

CREATE TABLE stok_hareketleri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    urun_id INT,
    miktar FLOAT,
    islem_turu ENUM('giris', 'cikis'),
    tarih TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (urun_id) REFERENCES urunler(id)
);
