<?php
session_start();
include '../inc/db.php';
if (!isset($_SESSION['vibe_admin'])) { header("Location: login.php"); exit; }

// Masa Ekle
if (isset($_POST['masa_ekle'])) {
    $ad = htmlspecialchars($_POST['masa_adi']);
    $db->prepare("INSERT INTO vibe_masalar (masa_adi) VALUES (?)")->execute([$ad]);
    header("Location: masalar.php"); exit;
}

// Durum Değiştir (Aktif/Pasif)
if (isset($_GET['durum_id'])) {
    $yeni_durum = $_GET['st'] == 1 ? 0 : 1;
    $db->prepare("UPDATE vibe_masalar SET durum = ? WHERE id = ?")->execute([$yeni_durum, $_GET['durum_id']]);
    header("Location: masalar.php"); exit;
}

// Masa Sil
if (isset($_GET['sil'])) {
    $db->prepare("DELETE FROM vibe_masalar WHERE id = ?")->execute([$_GET['sil']]);
    header("Location: masalar.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Masa Yönetimi | VibePanel</title>
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

        /* --- Form Elemanları (Renk Çakışması Çözümü) --- */
        .form-label {
            color: var(--accent) !important; /* Etiket Rengi Altın */
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-control {
            background-color: #1f1f22 !important; /* Koyu Gri Zemin */
            border: 1px solid var(--border-color) !important;
            color: #ffffff !important; /* Yazı Rengi BEYAZ */
            padding: 12px 15px;
            border-radius: 10px;
        }
        
        .form-control:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1) !important;
            background-color: #252528 !important;
        }
        
        .form-control::placeholder {
            color: #666 !important; /* Placeholder Gri */
        }

        /* --- Tablo --- */
        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-custom thead th {
            background: rgba(255,255,255,0.03);
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .table-custom tbody td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            color: var(--text-white);
            font-weight: 500;
        }
        .table-custom tbody tr:hover { background: rgba(255,255,255,0.02); }

        /* Butonlar & Rozetler */
        .btn-gold {
            background: var(--accent);
            color: #000;
            font-weight: 800;
            border: none;
            padding: 12px;
            border-radius: 10px;
            width: 100%;
            transition: 0.2s;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
        .btn-gold:hover { background: #fff; transform: translateY(-2px); }

        .btn-icon-delete {
            width: 36px; height: 36px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: none; transition: 0.2s;
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            text-decoration: none;
        }
        .btn-icon-delete:hover { background: #ef4444; color: #fff; transform: scale(1.1); }

        /* Durum Toggle */
        .status-link {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }
        .status-active { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-active:hover { background: #10b981; color: #fff; }
        
        .status-passive { background: rgba(107, 114, 128, 0.15); color: #9ca3af; border: 1px solid rgba(107, 114, 128, 0.3); }
        .status-passive:hover { background: #6b7280; color: #fff; }

        .table-icon { color: var(--accent); margin-right: 10px; background: rgba(212,175,55,0.1); padding: 8px; border-radius: 8px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
    <div class="row g-4">
        
        <div class="col-md-4">
            <div class="card-custom sticky-top" style="top: 100px; z-index: 1;">
                <div class="card-header">
                    <span><i class="fa-solid fa-plus-circle text-warning me-2"></i> YENİ MASA EKLE</span>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label">Masa İsmi / No</label>
                            <input type="text" name="masa_adi" class="form-control" placeholder="Örn: Masa 5, Bahçe 1..." required>
                        </div>
                        <button type="submit" name="masa_ekle" class="btn-gold shadow-lg">
                            <i class="fa-solid fa-chair me-2"></i> MASAYI KAYDET
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card-custom">
                <div class="card-header">
                    <span><i class="fa-solid fa-chair text-warning me-2"></i> MASA LİSTESİ</span>
                </div>
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Masa Adı</th>
                                <th>Durum</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $masalar = $db->query("SELECT * FROM vibe_masalar ORDER BY id ASC");
                            if($masalar->rowCount() > 0):
                                while($m = $masalar->fetch(PDO::FETCH_ASSOC)){
                                    $durumClass = $m['durum'] == 1 ? "status-active" : "status-passive";
                                    $durumText  = $m['durum'] == 1 ? '<i class="fa-solid fa-check-circle"></i> Aktif' : '<i class="fa-solid fa-ban"></i> Pasif';
                            ?>
                            <tr>
                                <td>
                                    <i class="fa-solid fa-chair table-icon"></i>
                                    <span class="fs-6 fw-bold"><?php echo $m['masa_adi']; ?></span>
                                </td>
                                <td>
                                    <a href="?durum_id=<?php echo $m['id']; ?>&st=<?php echo $m['durum']; ?>" class="status-link <?php echo $durumClass; ?>">
                                        <?php echo $durumText; ?>
                                    </a>
                                </td>
                                <td class="text-end">
                                    <a href="?sil=<?php echo $m['id']; ?>" class="btn-icon-delete" onclick="return confirm('Bu masayı silmek istediğinize emin misiniz?')" title="Sil">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php 
                                } 
                            else: 
                            ?>
                            <tr><td colspan="3" class="text-center py-5 text-muted">Henüz masa eklenmemiş.</td></tr>
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