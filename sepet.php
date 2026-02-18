<?php
session_start();
include 'inc/db.php';

// Site ayarlarını çek
$ayar = $db->query("SELECT * FROM vibe_ayarlar WHERE id=1")->fetch(PDO::FETCH_ASSOC);

// Sepet toplamını hesapla
$toplam = 0;
if(isset($_SESSION['sepet'])) { 
    foreach($_SESSION['sepet'] as $u) { 
        $toplam += $u['fiyat'] * $u['adet']; 
    } 
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Detayı | <?php echo $ayar['site_baslik']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #d4af37; /* Gold */
            --accent-hover: #b8972f;
            --bg-deep: #0a0a0b;
            --card-surface: #161618;
            --input-bg: #1e1e21;
            --text-main: #ffffff;
            --text-muted: #a0a0ab;
            --border-color: #2d2d32;
        }

        body {
            background-color: var(--bg-deep);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.01em;
        }

        /* --- Header Tasarımı --- */
        .page-header {
            padding: 50px 0 30px;
            background: linear-gradient(180deg, rgba(212, 175, 55, 0.08) 0%, rgba(10, 10, 11, 0) 100%);
        }

        /* --- Ürün Listesi Kartı --- */
        .cart-glass-card {
            background: var(--card-surface);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .cart-item {
            padding: 24px 30px;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.2s ease;
        }
        .cart-item:last-child { border-bottom: none; }
        .cart-item:hover { background: rgba(255,255,255,0.02); }

        .item-name { font-weight: 700; font-size: 1.15rem; color: var(--text-main); }
        .item-price-unit { color: var(--accent); font-weight: 600; font-size: 0.95rem; }

        /* --- Miktar Kontrolü --- */
        .qty-wrapper {
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 5px;
            display: inline-flex;
            align-items: center;
        }
        .qty-btn {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            color: var(--text-main);
            text-decoration: none;
            transition: 0.2s;
        }
        .qty-btn:hover { background: var(--accent); color: #000; }
        .qty-val { width: 40px; text-align: center; font-weight: 800; font-size: 1rem; }

        /* --- Sipariş Özet Paneli --- */
        .summary-panel {
            background: var(--card-surface);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 30px;
            position: sticky;
            top: 30px;
        }

        .total-display {
            background: rgba(212, 175, 55, 0.05);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* --- Modern Form Elemanları --- */
        .form-label { 
            color: var(--text-muted); 
            font-weight: 600; 
            font-size: 0.85rem; 
            text-transform: uppercase; 
            margin-bottom: 8px;
            letter-spacing: 0.05em;
        }
        .form-control, .form-select {
            background: var(--input-bg) !important;
            border: 1px solid var(--border-color) !important;
            color: #ffffff !important;
            border-radius: 12px !important;
            padding: 14px 16px !important;
            transition: 0.3s;
        }
        .form-control:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.15) !important;
        }

        /* --- Paket Servis Alanı --- */
        #paket-info {
            background: rgba(255,255,255,0.02);
            border-radius: 16px;
            padding: 20px;
            margin-top: 20px;
            border: 1px dashed var(--border-color);
        }

        /* --- Tamamla Butonu --- */
        .btn-confirm {
            background: var(--accent);
            color: #000;
            border: none;
            border-radius: 14px;
            padding: 18px;
            font-weight: 800;
            font-size: 1.1rem;
            width: 100%;
            transition: 0.3s;
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.25);
        }
        .btn-confirm:hover {
            transform: translateY(-3px);
            background: var(--accent-hover);
            box-shadow: 0 12px 25px rgba(212, 175, 55, 0.35);
        }

        @media (max-width: 768px) {
            .cart-item { flex-direction: column; text-align: center; gap: 20px; }
            .qty-wrapper { margin: 0 auto; }
        }
    </style>
</head>
<body>

<header class="page-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-800 m-0 text-white">SİPARİŞ ÖZETİ</h1>
            <p class="text-muted small m-0 fw-600">Tercihlerinizi gözden geçirin</p>
        </div>
        <a href="index.php" class="btn btn-outline-light rounded-pill px-4 btn-sm fw-700 border-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> ALIŞVERİŞE DEVAM
        </a>
    </div>
</header>

<div class="container mb-5 mt-3">
    <?php if(empty($_SESSION['sepet'])): ?>
        <div class="text-center py-5 cart-glass-card">
            <div class="mb-4 opacity-10"><i class="fa-solid fa-basket-shopping fa-6x"></i></div>
            <h2 class="fw-800">Henüz bir lezzet seçmediniz</h2>
            <p class="text-muted mb-4">Menümüze göz atarak harika tatlar keşfedebilirsiniz.</p>
            <a href="index.php" class="btn btn-warning rounded-pill px-5 py-3 fw-800">MENÜYÜ İNCELE</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cart-glass-card shadow-lg">
                    <?php foreach($_SESSION['sepet'] as $id => $u): ?>
                    <div class="cart-item d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="item-circle me-4 d-none d-md-flex" style="width: 60px; height: 60px; background: #1e1e21; border-radius: 50%; align-items: center; justify-content: center; color: var(--accent);">
                                <i class="fa-solid fa-utensils fa-lg"></i>
                            </div>
                            <div>
                                <div class="item-name"><?php echo $u['baslik']; ?></div>
                                <div class="item-price-unit"><?php echo $u['fiyat']; ?> ₺ / adet</div>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-4">
                            <div class="qty-wrapper">
                                <a href="sepet_islem.php?islem=sil&id=<?php echo $id; ?>" class="qty-btn"><i class="fa-solid fa-minus"></i></a>
                                <div class="qty-val"><?php echo $u['adet']; ?></div>
                                <a href="sepet_islem.php?islem=ekle&id=<?php echo $id; ?>" class="qty-btn"><i class="fa-solid fa-plus"></i></a>
                            </div>
                            <div class="fw-800 fs-5 text-end" style="min-width: 100px;">
                                <?php echo number_format($u['fiyat'] * $u['adet'], 2); ?> ₺
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-panel shadow-lg">
                    <div class="total-display">
                        <span class="fw-700 text-muted">ÖDENECEK TUTAR</span>
                        <span class="fs-2 fw-800 text-warning"><?php echo number_format($toplam, 2); ?> ₺</span>
                    </div>

                    <form action="siparis_kaydet.php" method="POST">
                        <div class="mb-4">
                            <label class="form-label">Teslimat Tercihi</label>
                            <select name="masa_no" id="masa_sec" class="form-select fw-700" onchange="checkPaket()" required>
                                <option value="">Lütfen Seçiniz</option>
                                <option value="Paket Servis" style="color: #ffc107;">📦 PAKET SERVİS</option>
                                <optgroup label="Masalar">
                                    <?php 
                                    $aktif_masalar = $db->query("SELECT * FROM vibe_masalar WHERE durum = 1 ORDER BY id ASC");
                                    while($m = $aktif_masalar->fetch(PDO::FETCH_ASSOC)){
                                        $selected = (isset($_SESSION['aktif_masa']) && $_SESSION['aktif_masa'] == $m['masa_adi']) ? "selected" : "";
                                        echo "<option value='{$m['masa_adi']}' $selected>{$m['masa_adi']}</option>";
                                    }
                                    ?>
                                </optgroup>
                            </select>
                        </div>

                        <div id="paket-info" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">Ad Soyad</label>
                                <input type="text" name="musteri_isim" id="m_isim" class="form-control" placeholder="Örn: Ahmet Yılmaz">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="tel" name="musteri_tel" id="m_tel" class="form-control" placeholder="05xx xxx xx xx">
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Teslimat Adresi</label>
                                <textarea name="musteri_adres" id="m_adres" class="form-control" rows="3" placeholder="Mahalle, Sokak, No..."></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn-confirm mt-4">
                            Siparişi Gönder <i class="fa-solid fa-paper-plane ms-2"></i>
                        </button>
                    </form>
                    
                    <div class="mt-4 pt-3 border-top border-secondary text-center opacity-50">
                        <i class="fa-solid fa-shield-check me-1"></i> <small class="fw-600">Hijyen ve Güvenlik Garantili</small>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function checkPaket() {
    var s = document.getElementById('masa_sec').value;
    var info = document.getElementById('paket-info');
    var fields = ['m_isim', 'm_tel', 'm_adres'];

    if(s === 'Paket Servis') {
        info.style.display = 'block';
        fields.forEach(id => document.getElementById(id).required = true);
    } else {
        info.style.display = 'none';
        fields.forEach(id => document.getElementById(id).required = false);
    }
}
window.onload = checkPaket;
</script>
</body>
</html>