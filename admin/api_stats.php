<?php
session_start();
include '../inc/db.php';

if (!isset($_SESSION['vibe_admin'])) { exit; }

$bugun = date('Y-m-d');

// Verileri Hesapla
$gunluk_kazanc = $db->query("SELECT SUM(toplam_tutar) FROM vibe_siparisler WHERE DATE(tarih) = '$bugun' AND durum = 2")->fetchColumn() ?? 0;
$bekleyen_siparis = $db->query("SELECT COUNT(*) FROM vibe_siparisler WHERE durum = 0")->fetchColumn();
$bekleyen_rez = $db->query("SELECT COUNT(*) FROM vibe_rezervasyonlar WHERE durum = 0")->fetchColumn(); // YENİ EKLENDİ
$aktif_masa = $db->query("SELECT COUNT(*) FROM vibe_masalar WHERE durum = 1")->fetchColumn() ?? 0;

// JSON Döndür
echo json_encode([
    'kazanc' => number_format($gunluk_kazanc, 2) . " ₺",
    'bekleyen' => $bekleyen_siparis,
    'rezervasyon' => $bekleyen_rez, // JSON'a eklendi
    'masa' => $aktif_masa
]);