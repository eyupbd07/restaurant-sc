<?php
session_start();
session_destroy(); // Tüm oturumları öldür
header("Location: login.php"); // Giriş sayfasına postala
exit;
?>