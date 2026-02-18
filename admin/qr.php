<?php
session_start();
include '../inc/db.php';

// Güvenlik
if (!isset($_SESSION['vibe_admin'])) { header("Location: login.php"); exit; }

// Sitenin o anki adresini (URL) otomatik bul
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
// Eğer localhost/vibescript/admin içindeysek, ana sayfayı (vibescript/) bulmak için dizini düzeltelim
$path = str_replace('/admin', '', dirname($_SERVER['PHP_SELF'])); 
$menu_url = $protocol . "://" . $host . $path; // Örn: http://localhost/vibescript
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Menü | VibePanel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    
    <style>
        :root {
            --bg-dark: #0a0a0a;
            --card-bg: #161618;
            --border-color: #2d2d30;
            --accent: #d4af37;
            --text-white: #ffffff;
            --text-silver: #c0c0c0; /* Okunabilir gri */
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
            text-align: center;
        }

        .card-header {
            background: rgba(255,255,255,0.02);
            border-bottom: 1px solid var(--border-color);
            padding: 20px;
            color: var(--text-white);
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* --- QR Kod Alanı --- */
        .qr-container {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 25px;
            border: 4px solid var(--accent);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
        }
        
        #qrcode img {
            margin: 0 auto;
            display: block;
        }

        /* --- Link Kutusu (Okunabilirlik Düzeltildi) --- */
        .link-box {
            background: #252528; /* Daha açık bir gri */
            border: 1px dashed var(--accent);
            padding: 15px;
            border-radius: 10px;
            color: var(--text-white) !important; /* Yazı rengi zorunlu beyaz */
            font-family: monospace;
            font-size: 1rem;
            margin-bottom: 30px;
            word-break: break-all;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .link-box i { color: var(--accent); }
        .link-box span { color: #fff; font-weight: 600; }

        /* Açıklama Yazısı */
        .info-text {
            color: var(--text-silver) !important;
            font-size: 0.9rem;
            margin-bottom: 25px;
            opacity: 0.8;
        }

        /* Butonlar */
        .btn-gold {
            background: var(--accent);
            color: #000;
            font-weight: 800;
            border: none;
            padding: 14px 30px;
            border-radius: 10px;
            transition: 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        .btn-gold:hover { background: #fff; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,255,255,0.2); }

        /* Yazdırma Modu */
        @media print {
            body * { visibility: hidden; }
            .card-body, .card-body * { visibility: visible; }
            .card-body { position: absolute; left: 0; top: 0; width: 100%; text-align: center; }
            .btn-gold, .card-header, nav, .info-text { display: none; }
            .link-box { border: 1px solid #000; color: #000 !important; background: #fff; }
            .link-box span { color: #000 !important; }
            .qr-container { border: 2px solid #000; box-shadow: none; }
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="card-custom">
                <div class="card-header">
                    <i class="fa-solid fa-qrcode text-warning fa-lg"></i>
                    <span>MASA ÜSTÜ QR MENÜ</span>
                </div>
                
                <div class="card-body p-5">
                    <h5 class="text-white fw-bold mb-4">Müşterileriniz İçin Menü Linki</h5>
                    
                    <div class="link-box">
                        <i class="fa-solid fa-link"></i>
                        <span><?php echo $menu_url; ?></span>
                    </div>

                    <div class="qr-container">
                        <div id="qrcode"></div>
                    </div>

                    <p class="info-text">
                        Bu QR kodu indirip yazdırarak masalarınıza, broşürlerinize<br> veya sosyal medya hesaplarınıza ekleyebilirsiniz.
                    </p>
                    
                    <button onclick="yazdir()" class="btn-gold shadow-lg">
                        <i class="fa-solid fa-print me-2"></i> YAZDIR / PDF KAYDET
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Sayfa yüklendiğinde QR kodu otomatik oluştur
    var menuLink = "<?php echo $menu_url; ?>";
    
    // QR Code.js kütüphanesini kullanıyoruz
    new QRCode(document.getElementById("qrcode"), {
        text: menuLink,
        width: 200,
        height: 200,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    function yazdir() {
        window.print();
    }
</script>

</body>
</html>