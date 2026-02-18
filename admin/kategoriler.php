<?php
session_start();
include '../inc/db.php';

if (!isset($_SESSION['vibe_admin'])) { header("Location: login.php"); exit; }

// Kategori Sil
if (isset($_GET['sil'])) {
    $db->prepare("DELETE FROM vibe_kategoriler WHERE id = ?")->execute([$_GET['sil']]);
    header("Location: kategoriler.php"); exit;
}

// Kategori Ekle
if ($_POST) {
    $ad = htmlspecialchars($_POST['ad']);
    $sira = intval($_POST['sira']);
    $db->prepare("INSERT INTO vibe_kategoriler (ad, sira) VALUES (?, ?)")->execute([$ad, $sira]);
    header("Location: kategoriler.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategoriler | VibePanel</title>
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

        /* --- Form Elemanları --- */
        .form-label {
            color: var(--accent);
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-control {
            background-color: #1f1f22 !important;
            border: 1px solid var(--border-color) !important;
            color: #ffffff !important; /* Yazı rengi beyaz */
            padding: 12px 15px;
            border-radius: 10px;
        }
        
        .form-control:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1) !important;
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

        /* Butonlar */
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
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
    <div class="row g-4">
        
        <div class="col-md-4">
            <div class="card-custom sticky-top" style="top: 100px; z-index: 1;">
                <div class="card-header">
                    <span><i class="fa-solid fa-plus-circle text-warning me-2"></i> YENİ KATEGORİ</span>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Kategori Adı</label>
                            <input type="text" name="ad" class="form-control" placeholder="Örn: İçecekler" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Sıralama (Opsiyonel)</label>
                            <input type="number" name="sira" class="form-control" value="0">
                        </div>
                        <button type="submit" class="btn-gold shadow-lg">
                            <i class="fa-solid fa-check me-2"></i> EKLE
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card-custom">
                <div class="card-header">
                    <span><i class="fa-solid fa-list-ul text-warning me-2"></i> MEVCUT KATEGORİLER</span>
                </div>
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Sıra</th>
                                <th>Kategori Adı</th>
                                <th class="text-end">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $kats = $db->query("SELECT * FROM vibe_kategoriler ORDER BY sira ASC");
                            if($kats->rowCount() > 0):
                                while($k = $kats->fetch(PDO::FETCH_ASSOC)){
                            ?>
                            <tr>
                                <td><span class="badge bg-dark border border-secondary"><?php echo $k['sira']; ?></span></td>
                                <td class="fs-6"><?php echo $k['ad']; ?></td>
                                <td class="text-end">
                                    <a href="?sil=<?php echo $k['id']; ?>" class="btn-icon-delete" onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')" title="Sil">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php 
                                } 
                            else: 
                            ?>
                            <tr><td colspan="3" class="text-center py-5 text-muted">Henüz kategori eklenmemiş.</td></tr>
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