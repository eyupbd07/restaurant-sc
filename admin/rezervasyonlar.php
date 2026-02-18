<?php
session_start();
include '../inc/db.php';

// Güvenlik
if (!isset($_SESSION['vibe_admin'])) { header("Location: login.php"); exit; }

// Durum Güncelleme
if (isset($_GET['islem']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $islem = $_GET['islem'];
    $yeni_durum = ($islem == 'onayla') ? 1 : 2; // 1: Onaylı, 2: İptal
    
    $db->prepare("UPDATE vibe_rezervasyonlar SET durum = :durum WHERE id = :id")
       ->execute([':durum' => $yeni_durum, ':id' => $id]);
       
    header("Location: rezervasyonlar.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezervasyonlar | VibePanel</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* --- Modern Tablo --- */
        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-custom thead th {
            background: rgba(255,255,255,0.03);
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
        }
        .table-custom tbody td {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            color: var(--text-white);
            font-size: 0.95rem;
        }
        .table-custom tbody tr:last-child td { border-bottom: none; }
        .table-custom tbody tr:hover { background: rgba(255,255,255,0.02); }

        /* Bilgi Alanları */
        .user-info { display: flex; align-items: center; gap: 12px; }
        .user-avatar {
            width: 40px; height: 40px;
            background: #252528; color: var(--accent);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 700;
        }
        .user-meta div { line-height: 1.4; }
        .user-name { font-weight: 600; color: #fff; }
        .user-phone { font-size: 0.85rem; color: var(--text-muted); text-decoration: none; }
        .user-phone:hover { color: var(--accent); }

        /* Durum Badge'leri */
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status-pending { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .status-approved { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-cancelled { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }

        /* Butonlar */
        .btn-icon {
            width: 36px; height: 36px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: none; transition: 0.2s;
            color: #fff;
        }
        .btn-approve { background: #10b981; } .btn-approve:hover { background: #059669; transform: translateY(-2px); }
        .btn-reject { background: #ef4444; } .btn-reject:hover { background: #dc2626; transform: translateY(-2px); }
        .btn-secondary-custom { background: #333; color: #aaa; width: auto; padding: 5px 15px; font-size: 0.8rem; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1">Rezervasyonlar</h2>
            <p class="text-muted mb-0">Masa rezervasyonlarını buradan yönetebilirsiniz.</p>
        </div>
        <a href="rezervasyonlar.php" class="btn btn-outline-light rounded-pill px-4">
            <i class="fa-solid fa-rotate me-2"></i> Yenile
        </a>
    </div>

    <div class="card-custom">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Müşteri</th>
                        <th>Tarih & Saat</th>
                        <th>Kişi</th>
                        <th>Notlar</th>
                        <th>Durum</th>
                        <th class="text-end">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Tarihe göre sırala (En yeni en üstte)
                    $sorgu = $db->query("SELECT * FROM vibe_rezervasyonlar ORDER BY tarih DESC, saat ASC");
                    
                    if ($sorgu->rowCount() > 0) {
                        while($row = $sorgu->fetch(PDO::FETCH_ASSOC)) {
                            // Durum Belirleme
                            if ($row['durum'] == 0) {
                                $durum_html = '<span class="status-badge status-pending"><i class="fa-regular fa-clock"></i> Bekliyor</span>';
                                $btn_group = '
                                    <a href="?islem=onayla&id='.$row['id'].'" class="btn-icon btn-approve me-2" title="Onayla"><i class="fa-solid fa-check"></i></a>
                                    <a href="?islem=iptal&id='.$row['id'].'" class="btn-icon btn-reject" title="Reddet" onclick="return confirm(\'İptal etmek istiyor musunuz?\')"><i class="fa-solid fa-xmark"></i></a>
                                ';
                            } elseif ($row['durum'] == 1) {
                                $durum_html = '<span class="status-badge status-approved"><i class="fa-solid fa-check-circle"></i> Onaylandı</span>';
                                $btn_group = '<a href="?islem=iptal&id='.$row['id'].'" class="btn-icon btn-secondary-custom" onclick="return confirm(\'İptal etmek istiyor musunuz?\')">İptal Et</a>';
                            } else {
                                $durum_html = '<span class="status-badge status-cancelled"><i class="fa-solid fa-ban"></i> İptal</span>';
                                $btn_group = '<span class="text-muted small">İşlem Yok</span>';
                            }
                            
                            // Baş harfi al (Avatar için)
                            $avatar_letter = strtoupper(mb_substr($row['isim'], 0, 1, 'UTF-8'));
                    ?>
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar"><?php echo $avatar_letter; ?></div>
                                <div class="user-meta">
                                    <div class="user-name"><?php echo $row['isim']; ?></div>
                                    <a href="tel:<?php echo $row['telefon']; ?>" class="user-phone"><?php echo $row['telefon']; ?></a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-white"><?php echo date("d.m.Y", strtotime($row['tarih'])); ?></div>
                            <div class="text-muted small"><?php echo date("H:i", strtotime($row['saat'])); ?></div>
                        </td>
                        <td>
                            <span class="badge bg-dark border border-secondary text-white px-3 py-2 rounded-pill">
                                <i class="fa-solid fa-user-group me-1 text-warning"></i> <?php echo $row['kisi_sayisi']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if(!empty($row['notlar'])): ?>
                                <span class="text-white small fst-italic">"<?php echo $row['notlar']; ?>"</span>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $durum_html; ?></td>
                        <td class="text-end">
                            <?php echo $btn_group; ?>
                        </td>
                    </tr>
                    <?php 
                        } 
                    } else {
                        echo '<tr><td colspan="6" class="text-center py-5 text-muted"><i class="fa-regular fa-calendar-xmark fa-2x mb-3 d-block"></i>Henüz rezervasyon talebi yok.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>