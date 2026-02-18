<?php
session_start();
include '../inc/db.php';

if (!isset($_SESSION['vibe_admin'])) { header("Location: login.php"); exit; }

// ÜRÜN SİLME
if (isset($_GET['sil'])) {
    $id = intval($_GET['sil']);
    $db->prepare("DELETE FROM vibe_urunler WHERE id = :id")->execute([':id' => $id]);
    header("Location: urunler.php?durum=silindi");
    exit;
}

// ÜRÜN EKLEME
$mesaj = "";
if ($_POST) {
    $baslik = htmlspecialchars($_POST['baslik']);
    $fiyat = $_POST['fiyat'];
    $aciklama = htmlspecialchars($_POST['aciklama']);
    $kategori_id = $_POST['kategori_id'];
    
    $resimYolu = "";
    if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {
        $uzanti = pathinfo($_FILES['resim']['name'], PATHINFO_EXTENSION);
        $yeniIsim = uniqid() . "." . $uzanti; 
        $hedef = "../assets/img/" . $yeniIsim;
        if (!file_exists('../assets/img')) { mkdir('../assets/img', 0777, true); }
        if (move_uploaded_file($_FILES['resim']['tmp_name'], $hedef)) { $resimYolu = $yeniIsim; }
    }

    $ekle = $db->prepare("INSERT INTO vibe_urunler (kategori_id, baslik, aciklama, fiyat, resim) VALUES (?, ?, ?, ?, ?)");
    if ($ekle->execute([$kategori_id, $baslik, $aciklama, $fiyat, $resimYolu])) {
        $mesaj = '<div class="alert alert-success border-0 bg-success text-white mb-4"><i class="fa-solid fa-check-circle me-2"></i> Ürün başarıyla eklendi!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Menü Yönetimi | VibePanel</title>
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
            justify-content: space-between;
        }

        /* --- Form Elemanları (Renk Çakışması Önleyici) --- */
        .form-label {
            color: var(--accent) !important; /* Etiketler Gold */
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-control, .form-select {
            background-color: #252528 !important; /* Daha açık gri zemin */
            border: 1px solid var(--border-color) !important;
            color: #ffffff !important; /* Yazı kesinlikle BEYAZ */
            padding: 12px 15px;
            border-radius: 10px;
        }
        
        .form-control::placeholder { color: #666 !important; }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.15) !important;
            background-color: #2a2a2d !important;
        }

        /* --- Tablo (Renk Çakışması Önleyici) --- */
        .table-responsive { background: var(--card-bg); }
        
        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; }
        
        .table-custom thead th {
            background: rgba(255,255,255,0.03);
            color: var(--text-muted) !important;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
            letter-spacing: 1px;
        }
        
        .table-custom tbody td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            color: #ffffff !important; /* Hücre yazıları BEYAZ */
            font-size: 0.95rem;
        }
        
        .table-custom tbody tr:hover td {
            background: rgba(255,255,255,0.03) !important; /* Hover efekti */
        }
        
        .product-img { 
            width: 50px; height: 50px; 
            object-fit: cover; 
            border-radius: 8px; 
            border: 1px solid var(--border-color); 
            background: #000;
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
            font-size: 0.9rem;
        }
        .btn-gold:hover { background: #fff; transform: translateY(-2px); }

        .btn-icon {
            width: 36px; height: 36px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: none; transition: 0.2s;
            color: #fff; text-decoration: none;
        }
        .btn-edit { background: #3b82f6; } .btn-edit:hover { background: #2563eb; transform: scale(1.1); }
        .btn-delete { background: #ef4444; } .btn-delete:hover { background: #dc2626; transform: scale(1.1); }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card-custom sticky-top" style="top: 100px; z-index: 1;">
                <div class="card-header">
                    <span><i class="fa-solid fa-plus-circle text-warning me-2"></i> YENİ ÜRÜN EKLE</span>
                </div>
                <div class="card-body p-4">
                    <?php echo $mesaj; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Kategori Seçin</label>
                            <select name="kategori_id" class="form-select" required>
                                <option value="">Listeden Seçiniz...</option>
                                <?php
                                $kats = $db->query("SELECT * FROM vibe_kategoriler ORDER BY sira ASC");
                                while($row = $kats->fetch(PDO::FETCH_ASSOC)){
                                    echo '<option value="'.$row['id'].'">'.$row['ad'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ürün İsmi</label>
                            <input type="text" name="baslik" class="form-control" placeholder="Örn: Karışık Pizza" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fiyat (₺)</label>
                            <input type="number" step="0.01" name="fiyat" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ürün Açıklaması</label>
                            <textarea name="aciklama" class="form-control" rows="3" placeholder="İçindekiler vb..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Ürün Görseli</label>
                            <input type="file" name="resim" class="form-control">
                        </div>
                        <button type="submit" class="btn-gold shadow-lg">
                            <i class="fa-solid fa-check me-2"></i> ÜRÜNÜ KAYDET
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header">
                    <span><i class="fa-solid fa-list text-warning me-2"></i> MENÜ LİSTESİ</span>
                </div>
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Görsel</th>
                                <th>Ürün Adı</th>
                                <th>Fiyat</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $urunler = $db->query("SELECT * FROM vibe_urunler ORDER BY id DESC");
                            if($urunler->rowCount() > 0):
                                while($row = $urunler->fetch(PDO::FETCH_ASSOC)):
                                    $resim = $row['resim'] ? "../assets/img/".$row['resim'] : "https://via.placeholder.com/50x50?text=Resim";
                            ?>
                                <tr>
                                    <td><img src="<?php echo $resim; ?>" class="product-img"></td>
                                    <td class="fw-bold fs-6"><?php echo $row['baslik']; ?></td>
                                    <td class="text-warning fw-bold fs-5"><?php echo $row['fiyat']; ?> ₺</td>
                                    <td class="text-end">
                                        <a href="urun_duzenle.php?id=<?php echo $row['id']; ?>" class="btn-icon btn-edit me-2" title="Düzenle">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="?sil=<?php echo $row['id']; ?>" onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?')" class="btn-icon btn-delete" title="Sil">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-box-open fa-3x mb-3 opacity-25"></i><br>
                                    Henüz menüye ürün eklenmemiş.
                                </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>