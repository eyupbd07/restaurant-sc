<?php
session_start();
include '../inc/db.php';

// Güvenlik
if (!isset($_SESSION['vibe_admin'])) { header("Location: login.php"); exit; }

// Durum Güncelleme
if (isset($_GET['islem']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $yeni_durum = intval($_GET['islem']);
    $db->prepare("UPDATE vibe_siparisler SET durum = ? WHERE id = ?")->execute([$yeni_durum, $id]);
    header("Location: siparisler.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Takip | VibePanel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <meta http-equiv="refresh" content="30">

    <style>
        :root {
            --bg-dark: #0a0a0a;
            --card-bg: #141414;
            --border-color: #2a2a2a;
            --accent-gold: #d4af37;
            --text-white: #ffffff;
            --text-muted: #a0a0a0;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-white);
            font-family: 'Inter', sans-serif;
        }

        /* --- Modern Sipariş Kartı --- */
        .order-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
        }
        .order-card:hover { transform: translateY(-5px); border-color: #444; }

        /* Durum Renk Çizgisi (Üstte) */
        .status-line { height: 6px; width: 100%; }
        .line-new { background: #ffc107; } /* Sarı */
        .line-prep { background: #0dcaf0; } /* Mavi */
        .line-done { background: #198754; } /* Yeşil */

        /* Kart Başlığı */
        .card-header-custom {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex; justify-content: space-between; align-items: center;
        }
        .table-info { font-size: 1.2rem; font-weight: 800; color: var(--text-white); display: flex; align-items: center; }
        .table-info i { margin-right: 10px; color: var(--accent-gold); }
        .order-time { font-size: 0.9rem; color: var(--text-muted); font-weight: 600; }

        /* Müşteri Bilgi Kutusu (Net Okunur) */
        .customer-box {
            background: #1f1f1f;
            border-radius: 12px;
            padding: 15px;
            margin: 20px;
            border: 1px solid #333;
        }
        .info-row { display: flex; margin-bottom: 10px; align-items: flex-start; }
        .info-row:last-child { margin-bottom: 0; }
        .info-icon { color: var(--accent-gold); width: 25px; margin-top: 2px; }
        .info-content { flex: 1; }
        .info-label { display: block; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        .info-value { font-size: 1rem; color: var(--text-white); font-weight: 600; display: block; }
        .info-value a { color: var(--text-white); text-decoration: none; border-bottom: 1px dotted var(--accent-gold); }

        /* Ürün Listesi */
        .order-list { list-style: none; padding: 0; margin: 20px; }
        .order-list li {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 0; border-bottom: 1px solid var(--border-color);
        }
        .item-name { font-weight: 600; color: var(--text-white); font-size: 1.05rem; }
        .item-qty { color: var(--accent-gold); font-weight: 800; margin-right: 8px; }
        .item-price { font-weight: 700; color: var(--text-white); }

        /* Alt Bilgi & Buton */
        .card-footer-custom { padding: 20px; background: rgba(0,0,0,0.3); border-top: 1px solid var(--border-color); }
        .total-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .total-label { font-size: 1rem; font-weight: 600; color: var(--text-muted); }
        .total-amount { font-size: 2rem; font-weight: 900; color: var(--accent-gold); }

        .btn-action { width: 100%; padding: 15px; border-radius: 50px; border: none; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; font-size: 0.9rem; }
        .btn-start { background: #0dcaf0; color: #000; } .btn-start:hover { background: #0bb5d8; box-shadow: 0 5px 15px rgba(13, 202, 240, 0.3); }
        .btn-finish { background: #198754; color: #fff; } .btn-finish:hover { background: #157347; box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3); }
        .btn-archive { background: #333; color: #777; cursor: not-allowed; }

        .live-badge { background: rgba(25, 135, 84, 0.2); color: #2ecc71; border: 1px solid #2ecc71; padding: 8px 15px; border-radius: 50px; font-weight: bold; font-size: 0.85rem; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold text-white mb-2">Mutfak Ekranı (KDS)</h1>
            <p class="text-muted mb-0 fs-5">Gelen siparişlerin anlık takibi ve yönetimi.</p>
        </div>
        <div class="live-badge">
            <i class="fa-solid fa-arrows-rotate fa-spin me-2"></i> CANLI VERİ (30sn)
        </div>
    </div>

    <div class="row">
        <?php
        // Siparişleri çek (Önce durum, sonra tarih)
        $siparisler = $db->query("SELECT * FROM vibe_siparisler ORDER BY durum ASC, id DESC");

        if ($siparisler->rowCount() == 0) {
            echo '<div class="col-12 text-center py-5 mt-5">
                    <i class="fa-solid fa-clipboard-check fa-6x text-muted opacity-25 mb-4"></i>
                    <h3 class="text-white fw-bold">Şu an bekleyen sipariş yok.</h3>
                    <p class="text-muted">Yeni sipariş geldiğinde burada görünecek.</p>
                  </div>';
        }

        while ($s = $siparisler->fetch(PDO::FETCH_ASSOC)) {
            // Durum Değişkenleri
            $line_class = "line-new";
            $status_badge = '<span class="badge bg-warning text-dark fs-6">YENİ</span>';
            $btn_html = '<a href="?islem=1&id='.$s['id'].'" class="btn btn-action btn-start"><i class="fa-solid fa-fire-burner me-2"></i> Hazırlamaya Başla</a>';

            if ($s['durum'] == 1) {
                $line_class = "line-prep";
                $status_badge = '<span class="badge bg-info text-dark fs-6">HAZIRLANIYOR</span>';
                $btn_html = '<a href="?islem=2&id='.$s['id'].'" class="btn btn-action btn-finish"><i class="fa-solid fa-check-double me-2"></i> Tamamla / Servis Et</a>';
            } elseif ($s['durum'] == 2) {
                $line_class = "line-done";
                $status_badge = '<span class="badge bg-success fs-6">TAMAMLANDI</span>';
                // BURASI GÜNCELLENDİ: Yazı "Tamamlandı" oldu.
                $btn_html = '<button class="btn btn-action btn-archive"><i class="fa-solid fa-check-double me-2"></i> Tamamlandı</button>';
            }
        ?>
        <div class="col-lg-4 col-md-6">
            <div class="order-card">
                <div class="status-line <?php echo $line_class; ?>"></div>
                
                <div class="card-header-custom">
                    <div class="table-info">
                        <i class="fa-solid <?php echo ($s['masa_no'] == 'Paket Servis') ? 'fa-motorcycle' : 'fa-chair'; ?>"></i>
                        <?php echo $s['masa_no']; ?>
                    </div>
                    <div class="text-end">
                        <?php echo $status_badge; ?>
                        <div class="order-time mt-2">
                            <i class="fa-regular fa-clock me-1"></i> <?php echo date("H:i", strtotime($s['tarih'])); ?>
                        </div>
                    </div>
                </div>

                <?php if($s['masa_no'] == 'Paket Servis'): ?>
                <div class="customer-box">
                    <div class="info-row">
                        <i class="fa-solid fa-user info-icon"></i>
                        <div class="info-content">
                            <span class="info-label">Müşteri Adı</span>
                            <span class="info-value"><?php echo $s['musteri_isim']; ?></span>
                        </div>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-phone info-icon"></i>
                        <div class="info-content">
                            <span class="info-label">Telefon</span>
                            <span class="info-value"><a href="tel:<?php echo $s['musteri_tel']; ?>"><?php echo $s['musteri_tel']; ?></a></span>
                        </div>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-location-dot info-icon"></i>
                        <div class="info-content">
                            <span class="info-label">Teslimat Adresi</span>
                            <span class="info-value" style="font-size: 0.95rem;"><?php echo $s['musteri_adres']; ?></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <ul class="order-list">
                    <?php
                    $detaylar = $db->prepare("SELECT * FROM vibe_siparis_detay WHERE siparis_id = ?");
                    $detaylar->execute([$s['id']]);
                    while($d = $detaylar->fetch(PDO::FETCH_ASSOC)){
                    ?>
                    <li>
                        <div>
                            <span class="item-qty"><?php echo $d['adet']; ?>x</span>
                            <span class="item-name"><?php echo $d['urun_adi']; ?></span>
                        </div>
                        <span class="item-price"><?php echo number_format($d['fiyat'] * $d['adet'], 2); ?> ₺</span>
                    </li>
                    <?php } ?>
                </ul>

                <div class="card-footer-custom">
                    <div class="total-section">
                        <span class="total-label">TOPLAM TUTAR</span>
                        <span class="total-amount"><?php echo number_format($s['toplam_tutar'], 2); ?> ₺</span>
                    </div>
                    <?php echo $btn_html; ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>