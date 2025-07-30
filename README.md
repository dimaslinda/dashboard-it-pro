# 🏢 Dashboard IT Pro

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Filament-3.x-F59E0B?style=for-the-badge&logo=php&logoColor=white" alt="Filament">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

<p align="center">
  <strong>Sistem Manajemen IT Terpadu untuk Perusahaan Modern</strong>
</p>

---

## 📋 Tentang Dashboard IT Pro

Dashboard IT Pro adalah sistem manajemen IT komprehensif yang dirancang untuk membantu perusahaan mengelola aset teknologi, infrastruktur jaringan, dan operasional IT secara efisien. Sistem ini dibangun dengan Laravel dan Filament untuk memberikan pengalaman pengguna yang modern dan intuitif.

### ✨ Fitur Utama

#### 🏗️ **Multi-Tenant Architecture**
- **IT Dashboard**: Panel utama untuk manajemen infrastruktur IT
- **Asset Survey**: Panel khusus untuk survei dan audit aset
- Isolasi data antar tenant untuk keamanan maksimal

#### 📦 **Manajemen Aset**
- **Asset Management**: Pencatatan dan pelacakan aset IT
- **Asset Survey**: Survei berkala untuk audit aset
- **Asset Loan**: Sistem peminjaman aset internal
- **Asset Procurement**: Manajemen pengadaan aset baru
- Import data dari Excel untuk migrasi cepat

#### 🌐 **Infrastruktur Jaringan**
- **Internet Provider Management**: Kelola kontrak ISP
- **WiFi Network Management**: Monitoring jaringan WiFi
- **Equipment Tracking**: Pelacakan perangkat jaringan
- **Website Management**: Monitoring website dan domain

#### 💰 **Manajemen Keuangan**
- **Invoice Management**: Pencatatan dan pelacakan tagihan
- **Cost Analysis**: Analisis biaya operasional IT
- **Contract Management**: Manajemen kontrak vendor
- **Expiry Notifications**: Notifikasi kontrak yang akan berakhir

#### 👥 **Manajemen Pengguna & Akses**
- **Role-Based Access Control (RBAC)**: Kontrol akses berbasis peran
- **Filament Shield Integration**: Manajemen izin granular
- **Multi-Company Support**: Dukungan multi-perusahaan
- **User Activity Tracking**: Pelacakan aktivitas pengguna

#### 📊 **Reporting & Analytics**
- **Dashboard Widgets**: Widget informatif untuk overview cepat
- **Export to Excel**: Export data ke format Excel
- **Custom Reports**: Laporan yang dapat disesuaikan
- **Real-time Monitoring**: Monitoring real-time sistem

---

## 🚀 Instalasi & Setup

### Prasyarat
- PHP 8.2 atau lebih tinggi
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Web server (Apache/Nginx)

### Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd dashboard-it-pro
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   - Edit file `.env` dengan konfigurasi database Anda
   - Jalankan migrasi:
   ```bash
   php artisan migrate
   ```

5. **Generate Filament Shield Permissions**
   ```bash
   php artisan shield:generate --all
   php artisan shield:generate --all --panel=asset-survey
   ```

6. **Seed Database**
   ```bash
   php artisan db:seed
   php artisan db:seed --class=TenantSeeder
   ```

7. **Build Assets**
   ```bash
   npm run build
   ```

8. **Start Development Server**
   ```bash
   php artisan serve
   ```

---

## 👤 Akun Default

Setelah seeding, Anda dapat login dengan akun berikut:

### Super Admin
- **Email**: `superadmin@dashboard.com`
- **Password**: `password`
- **Panel**: IT Dashboard (akses penuh)

### Asset Survey User
- **Email**: `assetsurvey@dashboard.com`
- **Password**: `password`
- **Panel**: Asset Survey

---

## 🏗️ Struktur Proyek

```
app/
├── Filament/
│   ├── AssetSurvey/          # Panel Asset Survey
│   ├── Resources/            # Resources untuk IT Dashboard
│   └── Widgets/              # Dashboard Widgets
├── Models/                   # Eloquent Models
├── Policies/                 # Authorization Policies
└── Providers/                # Service Providers

database/
├── migrations/               # Database Migrations
└── seeders/                  # Database Seeders

resources/
├── views/                    # Blade Templates
└── css/                      # Styling
```

---

## 🔧 Konfigurasi

### Multi-Tenant Setup
Sistem menggunakan multi-tenant architecture dengan dua panel utama:
- **IT Dashboard**: Panel utama untuk manajemen IT
- **Asset Survey**: Panel khusus untuk survei aset

### Role & Permissions
Sistem menggunakan Spatie Laravel Permission dengan Filament Shield:
- `super_admin`: Akses penuh ke semua fitur
- `admin`: Akses administratif (tanpa manajemen user)
- `manager`: Akses create, read, update
- `user`: Akses read-only
- `asset_survey`: Akses khusus untuk panel asset survey

---

## 📚 Dokumentasi Tambahan

- [Setup Notifikasi WhatsApp](WHATSAPP_NOTIFICATION_SETUP.md)
- [Setup Notifikasi Payment](PAYMENT_NOTIFICATION_SETUP.md)
- [Fitur WiFi Cost Restructure](WIFI_COST_RESTRUCTURE.md)
- [Fitur WiFi Expiry](WIFI_EXPIRY_FEATURE.md)
- [Troubleshooting File Upload](FILE_UPLOAD_TROUBLESHOOTING.md)

---

## 🤝 Kontribusi

Kami menyambut kontribusi dari komunitas! Silakan:

1. Fork repository ini
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

---

## 🐛 Melaporkan Bug

Jika Anda menemukan bug atau masalah, silakan buat issue di repository ini dengan informasi:
- Deskripsi masalah
- Langkah untuk mereproduksi
- Screenshot (jika diperlukan)
- Environment details

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - Framework PHP yang luar biasa
- [Filament](https://filamentphp.com) - Admin panel yang modern
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) - Package manajemen permission
- [Laravel Media Library](https://spatie.be/docs/laravel-medialibrary) - Manajemen file upload

---

<p align="center">
  <strong>Dibuat dengan ❤️ untuk kemudahan manajemen IT</strong>
</p>
