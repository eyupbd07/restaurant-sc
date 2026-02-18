<?php
// Veritabanı bağlantısını dahil et
include 'inc/db.php';

// Form gönderilmiş mi kontrol et
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 1. Verileri Al ve Güvenlik Temizliği Yap (XSS Koruması)
    $isim = htmlspecialchars(trim($_POST['isim']));
    $telefon = htmlspecialchars(trim($_POST['telefon']));
    $tarih = $_POST['tarih'];
    $saat = $_POST['saat'];
    $kisi = intval($_POST['kisi_sayisi']); // Sadece sayı olmasını garanti et
    $not = htmlspecialchars(trim($_POST['not']));

    // 2. Boş Alan Kontrolü (Basit Validasyon)
    if (empty($isim) || empty($telefon) || empty($tarih) || empty($saat)) {
        // Eksik bilgi varsa hata ile geri gönder
        header("Location: index.php?durum=eksik#rezervasyon");
        exit;
    }

    // 3. Veritabanına Ekleme (SQL Injection Korumalı - PDO Prepare)
    // Durum: 0 (Beklemede) olarak kaydediyoruz.
    $sql = "INSERT INTO vibe_rezervasyonlar (isim, telefon, tarih, saat, kisi_sayisi, notlar, durum) 
            VALUES (:isim, :tel, :tarih, :saat, :kisi, :not, 0)";
    
    $sorgu = $db->prepare($sql);
    
    $sonuc = $sorgu->execute([
        ':isim' => $isim,
        ':tel' => $telefon,
        ':tarih' => $tarih,
        ':saat' => $saat,
        ':kisi' => $kisi,
        ':not' => $not
    ]);

    // 4. Sonuca Göre Yönlendirme
    if ($sonuc) {
        // Başarılı! Anasayfaya 'ok' parametresiyle dön
        header("Location: index.php?durum=ok#rezervasyon");
    } else {
        // Bir hata oldu
        header("Location: index.php?durum=hata#rezervasyon");
    }

} else {
    // Eğer biri bu sayfaya form doldurmadan direkt girmeye çalışırsa anasayfaya at
    header("Location: index.php");
}
?>