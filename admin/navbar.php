<?php
// Oturum kontrolü
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

$active_page = basename($_SERVER['PHP_SELF']);

// Bildirim Sayılarını Veritabanından Çek
$bekleyen_siparis = 0;
$bekleyen_rez = 0;

if(isset($db)){
    // Henüz onaylanmamış (durum=0) kayıtları say
    $bekleyen_siparis = $db->query("SELECT COUNT(*) FROM vibe_siparisler WHERE durum = 0")->fetchColumn();
    $bekleyen_rez = $db->query("SELECT COUNT(*) FROM vibe_rezervasyonlar WHERE durum = 0")->fetchColumn();
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-warning shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold text-warning" href="index.php">
        <i class="fa-solid fa-fire me-2"></i>VibePanel
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
            <a class="nav-link <?php echo ($active_page == 'index.php') ? 'active text-warning fw-bold' : ''; ?>" href="index.php">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
        </li>
        
        <li class="nav-item position-relative me-3">
            <a class="nav-link <?php echo ($active_page == 'siparisler.php') ? 'active text-warning fw-bold' : ''; ?>" href="siparisler.php">
                <i class="fa-solid fa-bell-concierge"></i> Siparişler
                <?php if($bekleyen_siparis > 0): ?>
                    <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size: 0.65rem;">
                        <?php echo $bekleyen_siparis; ?>
                    </span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-item position-relative me-2">
            <a class="nav-link <?php echo ($active_page == 'rezervasyonlar.php') ? 'active text-warning fw-bold' : ''; ?>" href="rezervasyonlar.php">
                <i class="fa-solid fa-calendar-check"></i> Rezervasyon
                <?php if($bekleyen_rez > 0): ?>
                    <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size: 0.65rem;">
                        <?php echo $bekleyen_rez; ?>
                    </span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ($active_page == 'urunler.php') ? 'active text-warning fw-bold' : ''; ?>" href="urunler.php">
                <i class="fa-solid fa-utensils"></i> Menü
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo ($active_page == 'kategoriler.php') ? 'active text-warning fw-bold' : ''; ?>" href="kategoriler.php">
                <i class="fa-solid fa-list"></i> Kategoriler
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ($active_page == 'masalar.php') ? 'active text-warning fw-bold' : ''; ?>" href="masalar.php">
                <i class="fa-solid fa-chair"></i> Masalar
            </a>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item"><a class="nav-link" href="qr.php"><i class="fa-solid fa-qrcode"></i> QR</a></li>
        <li class="nav-item"><a class="nav-link" href="ayarlar.php"><i class="fa-solid fa-gears"></i> Ayarlar</a></li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo ($active_page == 'profil.php') ? 'active text-warning fw-bold' : ''; ?>" href="profil.php">
                <i class="fa-solid fa-user-shield"></i> Profil
            </a>
        </li>

        <li class="nav-item ms-lg-2">
            <a class="btn btn-sm btn-outline-danger" href="logout.php"><i class="fa-solid fa-power-off"></i></a>
        </li>
        <li class="nav-item ms-lg-2">
            <a class="btn btn-sm btn-warning fw-bold text-dark" href="../" target="_blank">Siteyi Gör</a>
        </li>
      </ul>
    </div>
  </div>
</nav>