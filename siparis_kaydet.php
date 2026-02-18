<?php
session_start();
include 'inc/db.php';

if (!isset($_SESSION['sepet']) || empty($_SESSION['sepet']) || !$_POST) {
    header("Location: index.php");
    exit;
}

// 1. Verileri Al
$masa_no = htmlspecialchars($_POST['masa_no']);
// Eğer paket servisse gelen verileri al, değilse boş bırak
$mus_isim = isset($_POST['musteri_isim']) ? htmlspecialchars($_POST['musteri_isim']) : null;
$mus_tel  = isset($_POST['musteri_tel']) ? htmlspecialchars($_POST['musteri_tel']) : null;
$mus_adres = isset($_POST['musteri_adres']) ? htmlspecialchars($_POST['musteri_adres']) : null;

$toplam_tutar = 0;
foreach ($_SESSION['sepet'] as $urun) {
    $toplam_tutar += $urun['fiyat'] * $urun['adet'];
}

// 2. Siparişi Kaydet (Müşteri bilgileriyle beraber)
$sorgu = $db->prepare("INSERT INTO vibe_siparisler (masa_no, toplam_tutar, musteri_isim, musteri_tel, musteri_adres, durum) VALUES (?, ?, ?, ?, ?, 0)");
$sorgu->execute([$masa_no, $toplam_tutar, $mus_isim, $mus_tel, $mus_adres]);

$siparis_id = $db->lastInsertId();

// 3. Detayları Kaydet
$detay_sorgu = $db->prepare("INSERT INTO vibe_siparis_detay (siparis_id, urun_id, urun_adi, fiyat, adet) VALUES (?, ?, ?, ?, ?)");

foreach ($_SESSION['sepet'] as $id => $urun) {
    $detay_sorgu->execute([
        $siparis_id,
        $id,
        $urun['baslik'],
        $urun['fiyat'],
        $urun['adet']
    ]);
}

unset($_SESSION['sepet']);
header("Location: index.php?durum=siparis_ok");
exit;
?>