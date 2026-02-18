<?php
session_start();
include 'inc/db.php';

// 1. Kategorileri Çek
$kategoriler_sorgu = $db->query("SELECT * FROM vibe_kategoriler ORDER BY sira ASC");
$kategoriler = $kategoriler_sorgu->fetchAll(PDO::FETCH_ASSOC);

// 2. Site Ayarlarını Çek
$ayar = $db->query("SELECT * FROM vibe_ayarlar WHERE id=1")->fetch(PDO::FETCH_ASSOC);

// Ayar yoksa varsayılanlar (Hata önleyici)
if (!$ayar) {
    $ayar = [
        'site_baslik' => 'VibeScript Restaurant',
        'aciklama' => 'Admin panelinden ayarları yapınız.',
        'telefon' => '05XX XXX XX XX',
        'adres' => 'Adres Girilmedi',
        'instagram' => 'vibescript',
        'wifi_sifre' => 'Yok'
    ];
}

$sepet_adet = 0;
if(isset($_SESSION['sepet'])) {
    foreach($_SESSION['sepet'] as $item) {
        $sepet_adet += $item['adet'];
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $ayar['site_baslik']; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --accent: #d4af37; 
            --bg: #0a0a0a; 
            --surface: #1a1a1a; 
            --text-pure: #ffffff;
            --text-dim: #b0b3b8;
        }

        body { 
            background-color: var(--bg); 
            color: var(--text-pure); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            scroll-behavior: smooth; 
        }

        /* --- OKUNABİLİRLİK AYARLARI --- */
        h1, h2, h3, h4, h5, h6, .nav-link { color: var(--text-pure) !important; }
        .text-warning { color: var(--accent) !important; }
        .text-muted { color: var(--text-dim) !important; }

        /* Navbar */
        .navbar { 
            background: rgba(0,0,0,0.95) !important; 
            backdrop-filter: blur(10px); 
            border-bottom: 1px solid rgba(212,175,55,0.3); 
            padding: 15px 0; 
        }
        .navbar-brand { font-family: 'Playfair Display', serif; color: var(--accent) !important; font-weight: 700; font-size: 1.5rem; }

        /* Hero Alanı */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=1920');
            background-size: cover; background-position: center; height: 80vh; display: flex; align-items: center; justify-content: center; text-align: center;
        }
        .hero-title { font-family: 'Playfair Display', serif; font-size: 4rem; font-weight: 700; color: var(--accent); margin-bottom: 20px; }

        /* Kategori Tabları */
        .nav-pills .nav-link {
            background: #222; color: #fff !important; border: 1px solid #333; margin: 5px; border-radius: 50px; padding: 12px 25px; transition: 0.3s;
        }
        .nav-pills .nav-link.active { 
            background: var(--accent) !important; 
            color: #000 !important; 
            border-color: var(--accent); 
            font-weight: 700;
        }

        /* Ürün Kartları */
        .menu-item {
            background: var(--surface); border-radius: 20px; overflow: hidden; border: 1px solid #333; height: 100%; transition: 0.3s;
        }
        .menu-item:hover { transform: translateY(-5px); border-color: var(--accent); }
        .menu-img { height: 220px; object-fit: cover; width: 100%; }
        
        .product-desc { 
            color: var(--text-dim) !important; 
            font-size: 0.9rem; 
            min-height: 45px;
            margin-bottom: 15px;
        }

        .btn-add-to-cart {
            background: var(--accent);
            color: #000 !important;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 800;
            width: 100%;
            transition: 0.3s;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        .btn-add-to-cart:hover { background: #fff; transform: scale(1.02); }

        /* Rezervasyon Formu */
        .rez-card { 
            background: #121212 !important; 
            border: 1px solid #333 !important; 
            border-radius: 20px; 
        }
        .form-label { 
            color: var(--accent) !important; 
            font-weight: 600; 
            font-size: 0.85rem;
            margin-bottom: 8px;
        }
        .form-control, .form-select { 
            background: #1f1f1f !important; 
            border: 1px solid #444 !important; 
            color: #ffffff !important; /* Yazı Rengi Saf Beyaz */
            padding: 14px; 
            border-radius: 10px;
        }
        .form-control:focus { border-color: var(--accent) !important; box-shadow: none; }
        .form-control::placeholder { color: #666 !important; }

        /* Footer */
        footer { background: #050505; padding: 80px 0 40px; border-top: 1px solid #222; }
        .footer-heading { color: #fff; font-family: 'Playfair Display', serif; margin-bottom: 20px; font-size: 1.5rem; }
        .wifi-box {
            border: 1px dashed var(--accent);
            padding: 15px;
            border-radius: 10px;
            background: rgba(212, 175, 55, 0.05);
            display: inline-block;
            margin-top: 10px;
        }

        /* Yüzen Sepet */
        .floating-cart {
            position: fixed; bottom: 30px; right: 30px; width: 65px; height: 65px; background: var(--accent);
            color: #000 !important; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1000;
            box-shadow: 0 10px 30px rgba(212,175,55,0.4); text-decoration: none; transition: 0.3s;
        }
        .floating-cart:hover { transform: scale(1.1); }
        .cart-badge { position: absolute; top: -5px; right: -5px; background: #ff3b30; color: #fff; border-radius: 50%; width: 24px; height: 24px; font-size: 0.75rem; display: flex; align-items: center; justify-content: center; font-weight: bold; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fa-solid fa-utensils me-2"></i><?php echo $ayar['site_baslik']; ?></a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa-solid fa-bars-staggered text-warning"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Anasayfa</a></li>
                    <li class="nav-item"><a class="nav-link" href="#hakkimizda">Hakkımızda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#menu">Menü</a></li>
                    <li class="nav-item"><a class="nav-link" href="#rezervasyon">Rezervasyon</a></li>
                    <li class="nav-item"><a class="nav-link" href="#iletisim">İletişim</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">VibeScript Kitchen</h1>
            <p class="lead mb-5 opacity-75 fw-light" style="letter-spacing: 1px; color: #fff; max-width: 700px; margin: 0 auto;"><?php echo $ayar['aciklama']; ?></p>
            <a href="#menu" class="btn btn-warning rounded-pill px-5 py-3 fw-bold text-dark shadow-lg">MENÜYÜ KEŞFET</a>
        </div>
    </section>

    <section id="hakkimizda" class="py-5" style="background: #0a0a0a;">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4">
                    <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?q=80&w=800" class="img-fluid rounded-4 shadow-lg border border-secondary" alt="Biz Kimiz">
                </div>
                <div class="col-md-6 ps-md-5">
                    <h6 class="text-warning fw-bold text-uppercase" style="letter-spacing: 3px;">Biz Kimiz?</h6>
                    <h2 class="display-5 fw-bold mb-4">Gerçek Lezzet, <br>Gerçek Tutku.</h2>
                    <p class="text-muted fs-5">VibeScript olarak, sadece yemek pişirmiyoruz; her tabakta unutulmaz bir hikaye sunuyoruz. En taze malzemelerle hazırlanan reçetelerimizle sizi lezzet yolculuğuna davet ediyoruz.</p>
                    <div class="d-flex gap-4 mt-4">
                        <div>
                            <h3 class="text-warning fw-bold mb-0">10+</h3>
                            <small class="text-muted">Yıllık Deneyim</small>
                        </div>
                        <div>
                            <h3 class="text-warning fw-bold mb-0">100%</h3>
                            <small class="text-muted">Taze Ürün</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="menu" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Özel Menümüz</h2>
                <div class="mx-auto" style="width: 50px; height: 3px; background: var(--accent); margin-top: 10px;"></div>
            </div>

            <ul class="nav nav-pills mb-5 justify-content-center" id="pills-tab" role="tablist">
                <?php $first = true; foreach($kategoriler as $kat): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo $first ? 'active' : ''; ?>" 
                            id="tab-<?php echo $kat['id']; ?>" 
                            data-bs-toggle="pill" 
                            data-bs-target="#kat-<?php echo $kat['id']; ?>" 
                            type="button" role="tab"><?php echo $kat['ad']; ?></button>
                </li>
                <?php $first = false; endforeach; ?>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <?php $first = true; foreach($kategoriler as $kat): ?>
                <div class="tab-pane fade <?php echo $first ? 'show active' : ''; ?>" 
                     id="kat-<?php echo $kat['id']; ?>" role="tabpanel">
                    <div class="row g-4">
                        <?php
                        $kat_id = $kat['id'];
                        $urunler = $db->query("SELECT * FROM vibe_urunler WHERE kategori_id = $kat_id");
                        
                        if($urunler->rowCount() == 0) {
                            echo '<div class="col-12 text-center text-muted py-5">Bu kategoride henüz ürün yok.</div>';
                        }

                        while($urun = $urunler->fetch(PDO::FETCH_ASSOC)):
                            $resim = !empty($urun['resim']) ? 'assets/img/'.$urun['resim'] : 'https://via.placeholder.com/600x400?text=VibeScript';
                        ?>
                        <div class="col-md-4">
                            <div class="menu-item p-3">
                                <img src="<?php echo $resim; ?>" class="menu-img rounded-4 mb-3" alt="<?php echo $urun['baslik']; ?>">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="fw-bold mb-0 text-white"><?php echo $urun['baslik']; ?></h5>
                                    <span class="text-warning fw-bold fs-5"><?php echo $urun['fiyat']; ?>₺</span>
                                </div>
                                <p class="product-desc"><?php echo $urun['aciklama']; ?></p>
                                <a href="sepet_islem.php?islem=ekle&id=<?php echo $urun['id']; ?>" class="btn btn-add-to-cart">
                                    <i class="fa-solid fa-cart-plus me-2"></i> SEPETE EKLE
                                </a>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php $first = false; endforeach; ?>
            </div>
        </div>
    </section>

    <section id="rezervasyon" class="py-5">
        <div class="container py-5">
            <div class="rez-card p-4 p-md-5 shadow-lg">
                <div class="row align-items-center">
                    <div class="col-lg-5 mb-5 mb-lg-0">
                        <h2 class="display-6 fw-bold mb-3 text-white">Masanızı <span class="text-warning">Ayırtın</span></h2>
                        <p class="text-muted">Özel günleriniz veya keyifli bir akşam yemeği için yerinizi şimdiden ayırtın.</p>
                        <hr class="border-secondary my-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fa-solid fa-phone text-warning me-3 fa-lg"></i>
                            <span class="fs-5 fw-bold text-white"><?php echo $ayar['telefon']; ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-location-dot text-warning me-3 fa-lg"></i>
                            <span class="text-muted"><?php echo $ayar['adres']; ?></span>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <form action="rezervasyon_yap.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Ad Soyad</label>
                                    <input type="text" name="isim" class="form-control" placeholder="İsminiz" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telefon</label>
                                    <input type="tel" name="telefon" class="form-control" placeholder="05xx..." required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tarih</label>
                                    <input type="date" name="tarih" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Saat</label>
                                    <input type="time" name="saat" class="form-control" required>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-warning w-100 py-3 fw-bold text-dark rounded-pill shadow-lg">
                                        REZERVASYONU TAMAMLA
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer id="iletisim">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <h3 class="footer-heading text-warning"><?php echo $ayar['site_baslik']; ?></h3>
                    <p class="text-muted mb-4"><?php echo $ayar['aciklama']; ?></p>
                    <div class="d-flex gap-3">
                        <a href="https://instagram.com/<?php echo $ayar['instagram']; ?>" target="_blank" class="btn btn-outline-light rounded-circle">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="tel:<?php echo $ayar['telefon']; ?>" class="btn btn-outline-light rounded-circle">
                            <i class="fa-solid fa-phone"></i>
                        </a>
                        <a href="#rezervasyon" class="btn btn-outline-light rounded-circle">
                            <i class="fa-solid fa-calendar-days"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4">
                    <h4 class="text-white mb-4">İletişim</h4>
                    <p class="mb-2 text-muted"><i class="fa-solid fa-location-dot text-warning me-2"></i> <?php echo $ayar['adres']; ?></p>
                    <p class="mb-2 text-white fw-bold"><i class="fa-solid fa-phone text-warning me-2"></i> <?php echo $ayar['telefon']; ?></p>
                    <p class="mb-2 text-muted"><i class="fa-solid fa-envelope text-warning me-2"></i> info@vibescript.com</p>
                    <p class="mb-2 text-muted"><i class="fa-solid fa-clock text-warning me-2"></i> 09:00 - 23:00 (Her Gün)</p>
                </div>

                <div class="col-lg-4">
                    <h4 class="text-white mb-4">Müşteri Hizmetleri</h4>
                    <p class="text-muted">Restoranımızda ücretsiz yüksek hızlı internetin keyfini çıkarın.</p>
                    
                    <div class="wifi-box text-center w-100">
                        <i class="fa-solid fa-wifi text-warning fa-2x mb-2"></i>
                        <h5 class="text-white mb-1">Wi-Fi Şifresi</h5>
                        <div class="bg-dark p-2 rounded text-warning fw-bold border border-secondary">
                            <?php echo $ayar['wifi_sifre']; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top border-secondary mt-5 pt-4 text-center">
                <p class="text-muted small mb-0">&copy; <?php echo date('Y'); ?> <?php echo $ayar['site_baslik']; ?>. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <a href="sepet.php" class="floating-cart shadow">
        <i class="fa-solid fa-basket-shopping fa-xl"></i>
        <?php if($sepet_adet > 0): ?>
            <span class="cart-badge"><?php echo $sepet_adet; ?></span>
        <?php endif; ?>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>