# 🍽️ VibeScript - Modern Restoran & QR Menü Yönetim Sistemi

![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**VibeScript**, restoranlar, kafeler ve bistrolar için geliştirilmiş; sipariş, rezervasyon ve masa yönetimini tek bir yerden sağlayan açık kaynaklı bir web otomasyonudur.

**Premium Dark Mode** (Siyah & Gold) tasarımı ile işletmelere modern bir dijital kimlik kazandırır.

## 🚀 Özellikler

### 👤 Müşteri Arayüzü (Frontend)
* 📱 **Tam Responsive Tasarım:** Mobil, tablet ve masaüstü uyumlu modern arayüz.
* 📃 **QR Menü:** Kategorilere ayrılmış, resimli ve açıklamalı dijital menü.
* 🛒 **Sepet & Sipariş:** Müşterilerin masadan ürün seçip sipariş verebilmesi.
* 📅 **Online Rezervasyon:** Tarih, saat ve kişi sayısı seçerek rezervasyon talebi oluşturma.
* 📶 **Wi-Fi & İletişim:** İşletme Wi-Fi şifresi ve sosyal medya bilgilerinin gösterimi.

### 🛡️ Yönetim Paneli (Backend)
* 📊 **Dashboard:** Günlük ciro, bekleyen siparişler ve rezervasyon özetleri.
* 🔔 **Canlı Mutfak Ekranı (KDS):** Siparişlerin anlık olarak mutfak ekranına düşmesi ve durum takibi (Hazırlanıyor/Tamamlandı).
* 🍔 **Menü Yönetimi:** Ürün ve kategori ekleme, düzenleme, fiyat güncelleme.
* 🪑 **Masa Yönetimi:** Masaları aktif/pasif yapma ve yönetme.
* 📷 **QR Kod Oluşturucu:** Masaya özel veya genel menü için otomatik QR kod üretme ve yazdırma.
* ⚙️ **Site Ayarları:** Site başlığı, iletişim bilgileri, Wi-Fi şifresi vb. ayarların panelden yönetimi.

## 📸 Ekran Görüntüleri
![Uploading Ekran görüntüsü 2026-02-19 001223.png…]()


## 🛠️ Kurulum

Projeyi yerel sunucunuzda (Localhost) veya hostinginizde çalıştırmak için:

1.  **Dosyaları Yükleyin:** Proje dosyalarını sunucunuzun ana dizinine atın.
2.  **Veritabanını Kurun:**
    * Yeni bir MySQL veritabanı oluşturun (Örn: `vibescript`).
    * Proje içerisindeki `.sql` dosyasını phpMyAdmin üzerinden içe aktarın.
3.  **Bağlantı Ayarları:**
    * `inc/db.php` dosyasını açın ve veritabanı bilgilerinizi (host, dbname, username, password) düzenleyin.

## 🔑 Varsayılan Giriş Bilgileri

Yönetim paneline erişmek için: **`/admin`**

* **Kullanıcı Adı:** `admin`
* **Şifre:** `123456`

> **Not:** Güvenliğiniz için giriş yaptıktan sonra "Profil" sayfasından şifrenizi değiştirin.

## 💻 Kullanılan Teknolojiler

* **Backend:** PHP (PDO)
* **Database:** MySQL
* **Frontend:** HTML5, CSS3, Bootstrap 5.3
* **Scripting:** JavaScript (jQuery, AJAX)
* **Icons:** FontAwesome 6
* **Font:** Google Fonts (Plus Jakarta Sans & Playfair Display)

## 🤝 Katkıda Bulunma

Projeyi geliştirmek isterseniz Fork'layıp Pull Request gönderebilirsiniz. Her türlü katkıya açığız!

1.  Projeyi Forklayın.
2.  Yeni bir Branch oluşturun (`git checkout -b feature/YeniOzellik`).
3.  Değişikliklerinizi Commit edin (`git commit -m 'Yeni özellik eklendi'`).
4.  Branch'i Pushlayın (`git push origin feature/YeniOzellik`).
5.  Pull Request açın.

## 📄 Lisans

Bu proje [MIT](LICENSE) lisansı ile lisanslanmıştır.
