-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 18 Şub 2026, 22:11:08
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `vibescript_db`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `vibe_admin`
--

CREATE TABLE `vibe_admin` (
  `id` int(11) NOT NULL,
  `kullanici_adi` varchar(50) NOT NULL,
  `sifre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `vibe_admin`
--

INSERT INTO `vibe_admin` (`id`, `kullanici_adi`, `sifre`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `vibe_ayarlar`
--

CREATE TABLE `vibe_ayarlar` (
  `id` int(11) NOT NULL,
  `site_baslik` varchar(255) DEFAULT NULL,
  `aciklama` text DEFAULT NULL,
  `telefon` varchar(50) DEFAULT NULL,
  `adres` text DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `wifi_sifre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `vibe_ayarlar`
--

INSERT INTO `vibe_ayarlar` (`id`, `site_baslik`, `aciklama`, `telefon`, `adres`, `instagram`, `wifi_sifre`) VALUES
(1, 'VibeScript Restaurant', 'Şehrin en seçkin lezzet durağı.', '0532 123 45 67', 'Deniz Caddesi, No:10, İstanbul', 'vibescript_tr', 'vibe20265');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `vibe_kategoriler`
--

CREATE TABLE `vibe_kategoriler` (
  `id` int(11) NOT NULL,
  `ad` varchar(100) NOT NULL,
  `sira` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `vibe_kategoriler`
--

INSERT INTO `vibe_kategoriler` (`id`, `ad`, `sira`) VALUES
(1, 'Tatlılar', 0),
(3, 'Yemekler', 1),
(4, 'İçecekler', 2);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `vibe_masalar`
--

CREATE TABLE `vibe_masalar` (
  `id` int(11) NOT NULL,
  `masa_adi` varchar(50) NOT NULL,
  `durum` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `vibe_masalar`
--

INSERT INTO `vibe_masalar` (`id`, `masa_adi`, `durum`) VALUES
(1, 'Masa 1', 1),
(2, 'Masa 2', 1),
(3, 'Masa 3', 1),
(4, 'Masa 4', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `vibe_rezervasyonlar`
--

CREATE TABLE `vibe_rezervasyonlar` (
  `id` int(11) NOT NULL,
  `isim` varchar(100) NOT NULL,
  `telefon` varchar(20) NOT NULL,
  `tarih` date NOT NULL,
  `saat` varchar(10) NOT NULL,
  `kisi_sayisi` int(11) NOT NULL,
  `notlar` text DEFAULT NULL,
  `durum` tinyint(4) DEFAULT 0,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `vibe_rezervasyonlar`
--

INSERT INTO `vibe_rezervasyonlar` (`id`, `isim`, `telefon`, `tarih`, `saat`, `kisi_sayisi`, `notlar`, `durum`, `olusturma_tarihi`) VALUES
(1, 'test', '2', '2919-03-29', '23:01', 2, '3', 2, '2026-02-18 11:42:23'),
(2, 'Eyyüp', '050000000', '2026-12-12', '12:30', 0, '', 1, '2026-02-18 12:33:41');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `vibe_siparisler`
--

CREATE TABLE `vibe_siparisler` (
  `id` int(11) NOT NULL,
  `masa_no` varchar(50) DEFAULT NULL,
  `toplam_tutar` decimal(10,2) DEFAULT NULL,
  `tarih` timestamp NOT NULL DEFAULT current_timestamp(),
  `durum` tinyint(4) DEFAULT 0,
  `musteri_isim` varchar(100) DEFAULT NULL,
  `musteri_tel` varchar(20) DEFAULT NULL,
  `musteri_adres` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `vibe_siparisler`
--

INSERT INTO `vibe_siparisler` (`id`, `masa_no`, `toplam_tutar`, `tarih`, `durum`, `musteri_isim`, `musteri_tel`, `musteri_adres`) VALUES
(1, 'Paket Servis', 300.00, '2026-02-18 12:04:31', 2, 'Eyyüp Bademci', '087379383189821', 'huzur mahallesi '),
(2, 'Paket Servis', 100.00, '2026-02-18 12:06:17', 2, 'Test', '8268919092', 'test\r\n'),
(3, 'Masa 1', 350.00, '2026-02-18 12:28:11', 2, '', '', ''),
(4, 'Paket Servis', 550.00, '2026-02-18 13:29:20', 2, 'tst5', 'fghd', 'jııjk\r\n');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `vibe_siparis_detay`
--

CREATE TABLE `vibe_siparis_detay` (
  `id` int(11) NOT NULL,
  `siparis_id` int(11) DEFAULT NULL,
  `urun_id` int(11) DEFAULT NULL,
  `urun_adi` varchar(255) DEFAULT NULL,
  `fiyat` decimal(10,2) DEFAULT NULL,
  `adet` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `vibe_siparis_detay`
--

INSERT INTO `vibe_siparis_detay` (`id`, `siparis_id`, `urun_id`, `urun_adi`, `fiyat`, `adet`) VALUES
(1, 1, 1, 'Pasta', 100.00, 3),
(2, 2, 1, 'Pasta', 100.00, 1),
(3, 3, 1, 'Pasta', 100.00, 1),
(4, 3, 2, 'Kebap', 200.00, 1),
(5, 3, 3, 'FuseTea', 50.00, 1),
(6, 4, 1, 'Pasta', 100.00, 3),
(7, 4, 2, 'Kebap', 200.00, 1),
(8, 4, 3, 'FuseTea', 50.00, 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `vibe_urunler`
--

CREATE TABLE `vibe_urunler` (
  `id` int(11) NOT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `baslik` varchar(100) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `fiyat` decimal(10,2) NOT NULL,
  `resim` varchar(255) DEFAULT NULL,
  `durum` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `vibe_urunler`
--

INSERT INTO `vibe_urunler` (`id`, `kategori_id`, `baslik`, `aciklama`, `fiyat`, `resim`, `durum`) VALUES
(1, 1, 'Pasta', 'çilek,çikolata,muz', 100.00, '6995a7d05fe72.jpeg', 1),
(2, 3, 'Kebap', 'Kebap\r\n', 200.00, '6995afef6e532.jpeg', 1),
(3, 4, 'FuseTea', '', 50.00, '6995b0427cc3c.jpeg', 1);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `vibe_admin`
--
ALTER TABLE `vibe_admin`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `vibe_ayarlar`
--
ALTER TABLE `vibe_ayarlar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `vibe_kategoriler`
--
ALTER TABLE `vibe_kategoriler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `vibe_masalar`
--
ALTER TABLE `vibe_masalar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `vibe_rezervasyonlar`
--
ALTER TABLE `vibe_rezervasyonlar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `vibe_siparisler`
--
ALTER TABLE `vibe_siparisler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `vibe_siparis_detay`
--
ALTER TABLE `vibe_siparis_detay`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `vibe_urunler`
--
ALTER TABLE `vibe_urunler`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `vibe_admin`
--
ALTER TABLE `vibe_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `vibe_ayarlar`
--
ALTER TABLE `vibe_ayarlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `vibe_kategoriler`
--
ALTER TABLE `vibe_kategoriler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `vibe_masalar`
--
ALTER TABLE `vibe_masalar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `vibe_rezervasyonlar`
--
ALTER TABLE `vibe_rezervasyonlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `vibe_siparisler`
--
ALTER TABLE `vibe_siparisler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `vibe_siparis_detay`
--
ALTER TABLE `vibe_siparis_detay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `vibe_urunler`
--
ALTER TABLE `vibe_urunler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
