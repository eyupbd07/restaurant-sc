<?php
session_start();
include '../inc/db.php';
if (!isset($_SESSION['vibe_admin'])) { header("Location: login.php"); exit; }

$mesaj = "";
if ($_POST) {
    $yeni_kadi = htmlspecialchars($_POST['kullanici_adi']);
    $yeni_sifre = $_POST['sifre'];
    
    if (!empty($yeni_sifre)) {
        // Şifre boş değilse hem kullanıcı adını hem şifreyi güncelle
        $sorgu = $db->prepare("UPDATE vibe_admin SET kullanici_adi = ?, sifre = ? WHERE id = 1");
        $sonuc = $sorgu->execute([$yeni_kadi, md5($yeni_sifre)]);
    } else {
        // Şifre boşsa sadece kullanıcı adını güncelle
        $sorgu = $db->prepare("UPDATE vibe_admin SET kullanici_adi = ? WHERE id = 1");
        $sonuc = $sorgu->execute([$yeni_kadi]);
    }

    if ($sonuc) {
        $mesaj = '<div class="alert alert-success border-0 bg-success text-white"><i class="fa-solid fa-check-circle me-2"></i> Bilgiler güncellendi. Giriş sayfasına yönlendiriliyorsunuz...</div>';
        session_destroy();
        header("Refresh: 2; url=login.php");
    } else {
        $mesaj = '<div class="alert alert-danger border-0 bg-danger text-white"><i class="fa-solid fa-triangle-exclamation me-2"></i> Güncelleme başarısız.</div>';
    }
}
$admin = $db->query("SELECT * FROM vibe_admin WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profil Ayarları | VibePanel</title>
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
            gap: 10px;
        }

        /* --- Form Elemanları --- */
        .form-label {
            color: var(--accent);
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-control {
            background-color: #1f1f22 !important;
            border: 1px solid var(--border-color) !important;
            color: #ffffff !important; /* Yazı Rengi BEYAZ */
            padding: 12px 15px;
            border-radius: 10px;
        }
        
        .form-control:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1) !important;
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
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        .btn-gold:hover { background: #fff; transform: translateY(-2px); }
        
        .form-text { color: var(--text-muted); font-size: 0.8rem; margin-top: 5px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="card-custom">
                <div class="card-header">
                    <i class="fa-solid fa-user-shield text-warning fa-lg"></i>
                    <span>YÖNETİCİ BİLGİLERİNİ GÜNCELLE</span>
                </div>
                
                <div class="card-body p-5">
                    <?php echo $mesaj; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label">Kullanıcı Adı</label>
                            <input type="text" name="kullanici_adi" class="form-control" value="<?php echo $admin['kullanici_adi']; ?>" required>
                        </div>
                        
                        <div class="mb-5">
                            <label class="form-label">Yeni Şifre</label>
                            <input type="password" name="sifre" class="form-control" placeholder="******">
                            <div class="form-text"><i class="fa-solid fa-circle-info me-1"></i> Şifrenizi değiştirmek istemiyorsanız bu alanı boş bırakın.</div>
                        </div>
                        
                        <button type="submit" class="btn-gold shadow-lg">
                            <i class="fa-solid fa-rotate me-2"></i> GÜNCELLE
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>