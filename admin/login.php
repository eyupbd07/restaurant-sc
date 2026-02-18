<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VibeScript Panel Girişi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a; /* Koyu Tema */
            color: #fff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify_content: center;
        }
        .login-card {
            background-color: #2c2c2c;
            border: 1px solid #d4af37; /* Gold Çerçeve */
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
        }
        .btn-gold {
            background-color: #d4af37;
            color: #000;
            font-weight: bold;
        }
        .btn-gold:hover {
            background-color: #b39028;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card login-card p-4">
                <div class="text-center mb-4">
                    <h3>VibeScript</h3>
                    <small class="text-muted">Yönetici Paneli</small>
                </div>
                
                <form action="login_control.php" method="POST">
                    <div class="mb-3">
                        <label>Kullanıcı Adı</label>
                        <input type="text" name="kadi" class="form-control" required placeholder="admin">
                    </div>
                    <div class="mb-3">
                        <label>Şifre</label>
                        <input type="password" name="sifre" class="form-control" required placeholder="******">
                    </div>
                    <button type="submit" class="btn btn-gold w-100">Giriş Yap</button>
                </form>
                <?php 
                if(isset($_GET['durum']) && $_GET['durum'] == 'hata'){
                    echo '<div class="alert alert-danger mt-3">Kullanıcı adı veya şifre hatalı!</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>