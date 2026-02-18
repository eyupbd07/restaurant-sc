<?php
session_start(); // Oturumu başlat
include '../inc/db.php'; // Veritabanı bağlantısını çağır

if ($_POST) {
    $kadi = htmlspecialchars($_POST['kadi']);
    $sifre = md5($_POST['sifre']); // MD5 ile şifreledik

    // Veritabanında bu kullanıcı var mı?
    $sorgu = $db->prepare("SELECT * FROM vibe_admin WHERE kullanici_adi=:k AND sifre=:s");
    $sorgu->execute([':k' => $kadi, ':s' => $sifre]);
    
    // Eğer sonuç 1 ise giriş başarılı
    if ($sorgu->rowCount() == 1) {
        $_SESSION['vibe_admin'] = $kadi; // Oturumu oluştur
        header("Location: index.php"); // Panele yönlendir
        exit;
    } else {
        header("Location: login.php?durum=hata"); // Hata mesajı ile geri gönder
        exit;
    }
}
?>