<?php
session_start();
include '../inc/db.php';

if (!isset($_SESSION['vibe_admin'])) {
    header("Location: login.php");
    exit;
}

// İlk açılış verileri
$bugun = date('Y-m-d');
$en_cok_satanlar = $db->query("SELECT urun_adi, COUNT(*) as adet FROM vibe_siparis_detay GROUP BY urun_adi ORDER BY adet DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VibePanel | Yönetim Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- Dark Mode Ayarları --- */
        :root { --accent-gold: #d4af37; --dark-bg: #0a0a0a; --card-bg: #1a1a1a; --text-pure: #ffffff; --text-dim: #b0b3b8; }
        body { background-color: var(--dark-bg); color: var(--text-pure); font-family: 'Inter', sans-serif; margin: 0; padding: 0; }

        /* Kart Tasarımı */
        .stat-card {
            background: var(--card-bg); border: 1px solid #333; border-radius: 16px; padding: 1.8rem;
            position: relative; overflow: hidden; transition: all 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); border-color: var(--accent-gold); box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        
        .stat-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--accent-gold) !important; font-weight: 800; display: block; margin-bottom: 0.6rem; }
        .stat-value { font-size: 2.2rem; font-weight: 800; color: #ffffff !important; margin: 0; }
        .stat-icon { position: absolute; right: -10px; bottom: -10px; font-size: 4.5rem; opacity: 0.12; color: var(--accent-gold); }

        /* Uyarı Animasyonu (Pulse) */
        .pulse-alert { animation: pulse-red 2s infinite; border: 1px solid #ff4d4d !important; }
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(255, 77, 77, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(255, 77, 77, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 77, 77, 0); }
        }

        /* Hızlı Linkler */
        .quick-link {
            text-decoration: none; padding: 1.2rem; border-radius: 12px; background: #252525;
            color: #ffffff !important; display: flex; align-items: center; transition: 0.3s; border: 1px solid #444; font-weight: 600;
        }
        .quick-link:hover { background: var(--accent-gold); color: #000000 !important; border-color: var(--accent-gold); }
        
        .card-custom { background: var(--card-bg) !important; border: 1px solid #333 !important; border-radius: 16px; }
        .list-group-item { background: transparent !important; border-color: #333 !important; color: #ffffff !important; padding: 1.2rem 0; font-size: 1.1rem; }
        .live-badge { background: rgba(25, 135, 84, 0.2); color: #2ecc71 !important; border: 1px solid #2ecc71; padding: 0.6rem 1.2rem; border-radius: 50px; font-size: 0.85rem; font-weight: bold; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="mb-1">Yönetim Paneli</h1>
            <p style="color: var(--text-dim);">İşletmenizin performansını anlık takip edin.</p>
        </div>
        <div class="live-badge">
            <i class="fa-solid fa-circle fa-fade me-2"></i> ANLIK VERİ AKTİF
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-3">
            <div class="stat-card">
                <span class="stat-label">Günlük Kazanç</span>
                <div class="stat-value text-success" id="stat-kazanc">-- ₺</div>
                <i class="fa-solid fa-money-bill-1-wave stat-icon"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card" id="card-rezervasyon">
                <span class="stat-label">Bekleyen Rezv.</span>
                <div class="stat-value text-warning" id="stat-rez">--</div>
                <i class="fa-solid fa-calendar-check stat-icon"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card" id="card-bekleyen">
                <span class="stat-label">Bekleyen Sipariş</span>
                <div class="stat-value text-danger" id="stat-bekleyen">--</div>
                <i class="fa-solid fa-bell-concierge stat-icon"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <span class="stat-label">Aktif Masalar</span>
                <div class="stat-value text-info" id="stat-masa">--</div>
                <i class="fa-solid fa-utensils stat-icon"></i>
            </div>
        </div>
    </div>

    <div class="row mt-5 gy-4">
        <div class="col-lg-7">
            <div class="card card-custom p-4">
                <h5 class="mb-4 d-flex align-items-center">
                    <i class="fa-solid fa-crown text-warning me-3"></i> En Çok Satanlar
                </h5>
                <div class="list-group list-group-flush">
                    <?php if($en_cok_satanlar->rowCount() > 0): ?>
                        <?php while($urun = $en_cok_satanlar->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="text-muted fw-bold me-3">#</span>
                                <span class="fw-bold fs-5 text-white"><?php echo $urun['urun_adi']; ?></span>
                            </div>
                            <span class="badge bg-success rounded-pill px-3 py-2 fw-bold">
                                <?php echo $urun['adet']; ?> Satış
                            </span>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center py-4" style="color: var(--text-dim);">Henüz sipariş verisi bulunmuyor.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <h5 class="mb-4 px-2">Hızlı Yönetim</h5>
            <div class="row g-3">
                <div class="col-6"><a href="siparisler.php" class="quick-link"><i class="fa-solid fa-list-check me-3"></i> Siparişler</a></div>
                <div class="col-6"><a href="rezervasyonlar.php" class="quick-link"><i class="fa-solid fa-calendar-days me-3"></i> Rezv.</a></div>
                <div class="col-6"><a href="urunler.php" class="quick-link"><i class="fa-solid fa-pizza-slice me-3"></i> Menü</a></div>
                <div class="col-6"><a href="ayarlar.php" class="quick-link"><i class="fa-solid fa-sliders me-3"></i> Ayarlar</a></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function verileriGuncelle() {
    $.getJSON('api_stats.php', function(data) {
        // Değerleri Güncelle
        $('#stat-kazanc').text(data.kazanc);
        $('#stat-rez').text(data.rezervasyon);
        $('#stat-bekleyen').text(data.bekleyen);
        $('#stat-masa').text(data.masa);

        // Bekleyen Sipariş Uyarısı (Kırmızı Pulse)
        if(parseInt(data.bekleyen) > 0) {
            $('#card-bekleyen').addClass('pulse-alert');
        } else {
            $('#card-bekleyen').removeClass('pulse-alert');
        }

        // Bekleyen Rezervasyon Uyarısı (Kırmızı Pulse)
        if(parseInt(data.rezervasyon) > 0) {
            $('#card-rezervasyon').addClass('pulse-alert');
        } else {
            $('#card-rezervasyon').removeClass('pulse-alert');
        }
    }).fail(function() {
        console.log("Veri çekme hatası: api_stats.php");
    });
}

$(document).ready(function() {
    verileriGuncelle(); 
    setInterval(verileriGuncelle, 5000); // 5 saniyede bir kontrol et
});
</script>

</body>
</html>