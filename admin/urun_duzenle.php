<?php
session_start();
include '../inc/db.php';

// Güvenlik ve ID Kontrolü
if (!isset($_SESSION['vibe_admin']) || !isset($_GET['id'])) { 
    header("Location: urunler.php"); 
    exit; 
}

$id = intval($_GET['id']);
$urun = $db->prepare("SELECT * FROM vibe_urunler WHERE id = ?");
$urun->execute([$id]);
$u = $urun->fetch(PDO::FETCH_ASSOC);

if (!$u) { header("Location: urunler.php"); exit; }

// Güncelleme İşlemi
if ($_POST) {
    $baslik = htmlspecialchars($_POST['baslik']);
    $fiyat = $_POST['fiyat'];
    $aciklama = htmlspecialchars($_POST['aciklama']);
    $kategori_id = $_POST['kategori_id'];
    
    // Resim Güncelleme
    $resimYolu = $u['resim']; 
    if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {
        $uzanti = pathinfo($_FILES['resim']['name'], PATHINFO_EXTENSION);
        $yeniIsim = uniqid() . "." . $uzanti;
        $hedef = "../assets/img/" . $yeniIsim;
        
        if (move_uploaded_file($_FILES['resim']['tmp_name'], $hedef)) {
            // Eski resmi sil (Opsiyonel ama önerilir)
            if (!empty($u['resim']) && file_exists("../assets/img/".$u['resim'])) {
                unlink("../assets/img/".$u['resim']);
            }
            $resimYolu = $yeniIsim;
        }
    }

    $guncelle = $db->prepare("UPDATE vibe_urunler SET kategori_id=?, baslik=?, aciklama=?, fiyat=?, resim=? WHERE id=?");
    if ($guncelle->execute([$kategori_id, $baslik, $aciklama, $fiyat, $resimYolu, $id])) {
        header("Location: urunler.php?durum=guncellendi");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Düzenle | VibePanel</title>
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
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .card-header {
            background: rgba(255,255,255,0.02);
            border-bottom: 1px solid var(--border-color);
            padding: 20px;
            color: var(--text-white);
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* --- Form Elemanları (Renk Çakışması Önleyici) --- */
        .form-label {
            color: var(--accent); /* Etiketler Gold */
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-control, .form-select {
            background-color: #1f1f22 !important; /* Koyu Gri Zemin */
            border: 1px solid var(--border-color) !important;
            color: #ffffff !important; /* Yazı Kesinlikle BEYAZ */
            padding: 12px 15px;
            border-radius: 10px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1) !important;
            background-color: #252528 !important;
        }

        /* Mevcut Resim Alanı */
        .current-img-box {
            background: rgba(255,255,255,0.03);
            border: 1px dashed var(--border-color);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 15px;
        }
        .current-img {
            max-width: 150px;
            border-radius: 8px;
            border: 2px solid var(--border-color);
        }

        /* Butonlar */
        .btn-gold {
            background: var(--accent);
            color: #000;
            font-weight: 700;
            border: none;
            padding: 14px;
            border-radius: 10px;
            width: 100%;
            transition: 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-gold:hover { background: #fff; transform: translateY(-2px); }
        
        .btn-back {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .btn-back:hover { color: var(--text-white); }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="text-white mb-0 fw-bold">Ürün Düzenle</h4>
                <a href="urunler.php" class="btn-back"><i class="fa-solid fa-arrow-left me-2"></i> Geri Dön</a>
            </div>

            <div class="card-custom">
                <div class="card-header">
                    <span><i class="fa-solid fa-pen-to-square text-warning me-2"></i> <?php echo $u['baslik']; ?></span>
                </div>
                <div class="card-body p-4">
                    <form method="POST" enctype="multipart/form-data">
                        
                        <?php if(!empty($u['resim'])): ?>
                        <div class="current-img-box">
                            <label class="form-label d-block mb-2 text-muted small">Mevcut Görsel</label>
                            <img src="../assets/img/<?php echo $u['resim']; ?>" class="current-img">
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori_id" class="form-select">
                                <?php
                                $kats = $db->query("SELECT * FROM vibe_kategoriler ORDER BY sira ASC");
                                while($k = $kats->fetch(PDO::FETCH_ASSOC)){
                                    $selected = ($k['id'] == $u['kategori_id']) ? "selected" : "";
                                    echo '<option value="'.$k['id'].'" '.$selected.'>'.$k['ad'].'</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Ürün Adı</label>
                                <input type="text" name="baslik" class="form-control" value="<?php echo $u['baslik']; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fiyat (₺)</label>
                                <input type="number" step="0.01" name="fiyat" class="form-control" value="<?php echo $u['fiyat']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Açıklama</label>
                            <textarea name="aciklama" class="form-control" rows="4"><?php echo $u['aciklama']; ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Yeni Görsel Yükle (Opsiyonel)</label>
                            <input type="file" name="resim" class="form-control">
                            <div class="form-text text-muted small mt-2">* Sadece görseli değiştirmek istiyorsanız dosya seçin.</div>
                        </div>

                        <button type="submit" class="btn-gold shadow-lg">
                            <i class="fa-solid fa-save me-2"></i> DEĞİŞİKLİKLERİ KAYDET
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