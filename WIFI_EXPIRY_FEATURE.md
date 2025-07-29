# WiFi Network Expiry Management Feature

## Overview
Fitur ini menambahkan kemampuan untuk mengelola tanggal kadaluarsa layanan WiFi network dengan notifikasi otomatis melalui WhatsApp.

## Fitur yang Ditambahkan

### 1. Database Schema
- **Tabel `internet_providers`**: Menyimpan data provider internet
- **Kolom baru di `wifi_networks`**:
  - `provider_id`: Foreign key ke tabel internet_providers
  - `service_expiry_date`: Tanggal berakhir layanan
  - `monthly_cost`: Biaya bulanan
  - `contract_start_date`: Tanggal mulai kontrak

### 2. Models
- **InternetProvider**: Model untuk provider internet
- **WifiNetwork**: Ditambahkan relasi dan accessor untuk status expiry

### 3. Filament Resources
- **InternetProviderResource**: Manajemen data provider
- **WifiNetworkResource**: Ditambahkan field baru untuk provider dan tanggal

### 4. Widget Dashboard
- **WifiExpiryNotificationWidget**: Widget untuk monitoring WiFi yang akan expired
  - Kategori: Sudah Expired, Expired Hari Ini, 7 Hari Lagi, 30 Hari Lagi
  - Tombol test notifikasi WhatsApp

### 5. Command & Scheduling
- **Command**: `php artisan wifi:expiry-check --days=X`
- **Scheduling**: Otomatis berjalan setiap hari
  - 09:30: Check WiFi expiring in 3 days
  - 14:30: Check WiFi expiring today

### 6. WhatsApp Notifications
- Integrasi dengan FontteService
- Notifikasi otomatis untuk WiFi yang akan expired
- Format pesan mencakup: nama network, lokasi, tanggal expired, provider, biaya

## Penggunaan

### Menambah Provider Internet
1. Masuk ke menu "Internet Providers"
2. Klik "New Internet Provider"
3. Isi data provider (nama, kontak, website, dll)

### Mengelola WiFi Network
1. Masuk ke menu "WiFi Networks"
2. Edit network yang ada atau buat baru
3. Pilih provider dari dropdown
4. Isi tanggal mulai kontrak dan tanggal berakhir layanan
5. Isi biaya bulanan

### Monitoring Expiry
1. Lihat widget "WiFi Network Expiry Alerts" di dashboard
2. Widget menampilkan kategorisasi berdasarkan status expiry
3. Klik "Test WiFi Notification" untuk test notifikasi WhatsApp

### Manual Check
```bash
# Check WiFi expiring today
php artisan wifi:expiry-check --days=0

# Check WiFi expiring in 7 days
php artisan wifi:expiry-check --days=7

# Check WiFi expiring in 30 days
php artisan wifi:expiry-check --days=30
```

## Status Color Indicators
- **Merah**: Sudah expired atau akan expired dalam 7 hari
- **Kuning**: Akan expired dalam 30 hari
- **Hijau**: Masih aman (lebih dari 30 hari)

## Konfigurasi WhatsApp
Pastikan FontteService sudah dikonfigurasi dengan benar untuk notifikasi WhatsApp.

## Data Test
Gunakan seeder `WifiNetworkTestSeeder` untuk membuat data test dengan berbagai status expiry.

```bash
php artisan db:seed --class=WifiNetworkTestSeeder
```