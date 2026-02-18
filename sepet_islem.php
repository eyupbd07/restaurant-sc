<?php
session_start();
include 'inc/db.php';

// İşlem Kontrolü
if (isset($_GET['islem'])) {
    $islem = $_GET['islem'];

    // 1. ÜRÜN EKLEME
    if ($islem == 'ekle' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        
        // Ürün bilgilerini veritabanından çek
        $urun = $db->prepare("SELECT * FROM vibe_urunler WHERE id = ?");
        $urun->execute([$id]);
        $sonuc = $urun->fetch(PDO::FETCH_ASSOC);

        if ($sonuc) {
            // Sepet boşsa oluştur
            if (!isset($_SESSION['sepet'])) { $_SESSION['sepet'] = []; }

            // Ürün sepette var mı? Varsa adet artır, yoksa ekle
            if (isset($_SESSION['sepet'][$id])) {
                $_SESSION['sepet'][$id]['adet']++;
            } else {
                $_SESSION['sepet'][$id] = [
                    'baslik' => $sonuc['baslik'],
                    'fiyat' => $sonuc['fiyat'],
                    'resim' => $sonuc['resim'],
                    'adet' => 1
                ];
            }
        }
        // Geldiği yere (Menüye veya Sepete) geri gönder
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // 2. ÜRÜN SİLME (Adet azaltma)
    if ($islem == 'sil' && isset($_GET['id'])) {
        $id = $_GET['id'];
        if (isset($_SESSION['sepet'][$id])) {
            if ($_SESSION['sepet'][$id]['adet'] > 1) {
                $_SESSION['sepet'][$id]['adet']--;
            } else {
                unset($_SESSION['sepet'][$id]); // 1 tane kaldıysa komple sil
            }
        }
        header("Location: sepet.php");
        exit;
    }

    // 3. SEPETİ BOŞALT
    if ($islem == 'bosalt') {
        unset($_SESSION['sepet']);
        header("Location: index.php");
        exit;
    }
}
?>