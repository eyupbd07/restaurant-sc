<?php
session_start();
include '../inc/db.php';

// Güvenlik
if (!isset($_SESSION['vibe_admin'])) { header("Location: login.php"); exit; }

// 1. AYARLARI GÜNCELLEME İŞLEMİ
$mesaj = "";
if ($_POST) {
    $baslik = $_POST['site_baslik'];
    $tel = $_POST['telefon'];
    $adres = $_POST['adres'];
    $insta = $_POST['instagram'];
    $wifi = $_POST['wifi_sifre'];
    $aciklama = $_POST['aciklama'];

    // Tek satırımız olduğu için ID=1 olanı güncelliyoruz
    $guncelle = $db->prepare("UPDATE vibe_ayarlar SET site_baslik=?, telefon=?, adres=?, instagram=?, wifi_sifre=?, aciklama=? WHERE id=1");
    $sonuc = $guncelle->execute([$baslik, $tel, $adres, $insta, $wifi, $aciklama]);

    if ($sonuc) {
        $mesaj = '<div class="alert alert-success border-0 bg-success text-white"><i class="fa-solid fa-check-circle me-2"></i> Ayarlar başarıyla güncellendi!</div>';
    } else {
        $mesaj = '<div class="alert alert-danger border-0 bg-danger text-white"><i class="fa-solid fa-triangle-exclamation me-2"></i> Güncelleme başarısız.</div>';
    }
}

// 2. MEVCUT AYARLARI ÇEKME
$ayar = $db->query("SELECT * FROM vibe_ayarlar WHERE id=1")->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Site Ayarları | VibePanel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-dark: #0a0a0a;
            --card-bg: #161618;
            --border-color: #2d2d30;
            --accent: #d4af37;
            --text-white: #ffffff;
            --text-muted: #9ca3af;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-white);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* --- Kart Yapısı --- */
        .card-custom {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .card-header {
            background: rgba(255,255,255,0.02);
            border-bottom: 1px solid var(--border-color);
            padding: 20px;
            color: var(--text-white);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* --- Form Elemanları --- */
        .form-label {
            color: var(--accent);
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-control {
            background-color: #1f1f22 !important;
            border: 1px solid var(--border-color) !important;
            color: #ffffff !important;
            padding: 12px 15px;
            border-radius: 10px;
        }
        
        .form-control:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1) !important;
        }

        .input-group-text {
            background-color: #2a2a2d;
            border: 1px solid var(--border-color);
            color: var(--accent);
            font-weight: bold;
        }

        /* Butonlar */
        .btn-gold {
            background: var(--accent);
            color: #000;
            font-weight: 800;
            border: none;
            padding: 14px;
            border-radius: 10px;
            width: 100%;
            transition: 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        .btn-gold:hover { background: #fff; transform: translateY(-2px); }
        
        .text-hint { font-size: 0.8rem; color: var(--text-muted); margin-top: 5px; display: block; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="card-custom">
                <div class="card-header">
                    <i class="fa-solid fa-sliders text-warning fa-lg"></i>
                    <span>GENEL SİTE AYARLARI</span>
                </div>
                
                <div class="card-body p-5">
                    <?php echo $mesaj; ?>
                    
                    <form method="POST">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Site Başlığı</label>
                                <input type="text" name="site_baslik" class="form-control" value="<?php echo $ayar['site_baslik']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kısa Açıklama (Slogan)</label>
                                <input type="text" name="aciklama" class="form-control" value="<?php echo $ayar['aciklama']; ?>">
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Telefon Numarası</label>
                                <input type="text" name="telefon" class="form-control" value="<?php echo $ayar['telefon']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Instagram</label>
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" name="instagram" class="form-control" value="<?php echo $ayar['instagram']; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Adres Bilgisi</label>
                            <textarea name="adres" class="form-control" rows="3"><?php echo $ayar['adres']; ?></textarea>
                        </div>

                        <div class="mb-5">
                            <label class="form-label" style="color: #2ecc71;">Müşteriler için Wi-Fi Şifresi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-wifi"></i></span>
                                <input type="text" name="wifi_sifre" class="form-control" value="<?php echo $ayar['wifi_sifre']; ?>">
                            </div>
                            <small class="text-hint">* Bu bilgi menünün en altında görünebilir.</small>
                        </div>

                        <button type="submit" class="btn-gold shadow-lg">
                            <i class="fa-solid fa-save me-2"></i> AYARLARI KAYDET
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>