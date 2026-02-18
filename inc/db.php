<?php
// Karakter seti ayarı (Türkçe karakter sorunu yaşamamak için)
header('Content-Type: text/html; charset=utf-8');

// VibeScript - Akıllı Bağlantı Ayarı
$host = 'localhost';
$dbname = 'vibescript_db';
$username = 'root';
$password = '';

// Eğer canlı sunucudaysak (Domain aldıgında burayı doldurursun)
if ($_SERVER['HTTP_HOST'] != 'localhost' && $_SERVER['HTTP_HOST'] != '127.0.0.1') {
    $host = 'sunucu_ip_adresi';
    $dbname = 'sunucu_db_adi';
    $username = 'sunucu_kullanici';
    $password = 'sunucu_sifre';
}

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "VibeScript Bağlantısı Başarılı!"; // Test için açabilirsin
} catch (PDOException $e) {
    die("VibeScript Veritabanı Hatası: " . $e->getMessage());
}
?>