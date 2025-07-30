# Restructure Biaya WiFi Networks ke Internet Providers

## Overview
Perubahan struktur database untuk memindahkan informasi biaya dari tabel `wifi_networks` ke tabel `internet_providers`. Hal ini dilakukan karena WiFi network hanya merupakan device router yang menggunakan provider tertentu, sedangkan biaya seharusnya terkait dengan provider layanan internet.

## Perubahan Database

### 1. Migrasi: `2025_01_15_000002_move_cost_to_providers.php`

**Penambahan field di tabel `internet_providers`:**
- `monthly_cost` (decimal) - Biaya bulanan layanan
- `installation_cost` (decimal) - Biaya instalasi
- `speed_package` (string) - Paket kecepatan (contoh: "100 Mbps Unlimited")
- `bandwidth_mbps` (integer) - Bandwidth dalam Mbps
- `connection_type` (enum) - Jenis koneksi: fiber, cable, dsl, wireless, satellite

**Penghapusan field dari tabel `wifi_networks`:**
- `monthly_cost` - Dipindahkan ke tabel providers

### 2. Migrasi: `2025_01_15_000003_move_contract_dates_to_providers.php`

**Penambahan field di tabel `internet_providers`:**
- `service_expiry_date` (date) - Tanggal berakhir layanan
- `contract_start_date` (date) - Tanggal mulai kontrak
- `contract_duration_months` (integer) - Durasi kontrak dalam bulan
- `contract_status` (enum) - Status kontrak: active, expired, cancelled

**Penghapusan field dari tabel `wifi_networks`:**
- `service_expiry_date` - Dipindahkan ke tabel providers
- `contract_start_date` - Dipindahkan ke tabel providers

### 3. Update Model

**InternetProvider Model:**
- Menambahkan field baru ke `$fillable`: monthly_cost, installation_cost, speed_package, bandwidth_mbps, connection_type, service_expiry_date, contract_start_date, contract_duration_months, contract_status
- Menambahkan `$casts` untuk format data
- Menambahkan scope methods: `scopeExpiringWithin()`, `scopeExpired()`
- Menambahkan helper methods: `isExpiringSoon()`, `isExpired()`, `getContractStatusColorAttribute()`

**WifiNetwork Model:**
- Menghapus field `monthly_cost`, `service_expiry_date`, `contract_start_date` dari `$fillable`
- Mengubah relasi untuk mengakses data dari provider

### 4. Update Filament Resources

**InternetProviderResource.php:**
- Menambahkan section "Service & Pricing" pada form dengan field: speed_package, bandwidth_mbps, connection_type, monthly_cost, installation_cost
- Menambahkan section "Contract Information" pada form dengan field: contract_start_date, contract_duration_months, service_expiry_date, contract_status
- Menambahkan kolom baru pada tabel: speed_package, bandwidth_mbps, connection_type, monthly_cost, contract_start_date, service_expiry_date, contract_status
- Menambahkan color coding untuk status kontrak dan tanggal expiry

**WifiNetworkResource.php:**
- Menghapus field `monthly_cost`, `service_expiry_date`, `contract_start_date` dari form
- Mengubah kolom tabel untuk menampilkan `provider.monthly_cost` dan `provider.service_expiry_date`

### 5. Update Notifikasi

**WifiExpiryNotificationWidget.php:**
- Mengubah query untuk menggunakan `whereHas('provider')` dengan kondisi `service_expiry_date`
- Mengubah sumber biaya dari `$wifi->monthly_cost` menjadi `$wifi->provider->monthly_cost`
- Mengubah sumber tanggal expiry dari `$wifi->service_expiry_date` menjadi `$wifi->provider->service_expiry_date`
- Update action untuk mark as paid menggunakan provider expiry date

**FontteService.php:**
- Mengubah sumber biaya dan tanggal expiry dalam pesan notifikasi
- Menambahkan null check untuk tanggal expiry

**CheckWifiExpiryNotifications.php (Command):**
- Mengubah query untuk menggunakan `whereHas('provider')` dengan kondisi `service_expiry_date`
- Mengubah sumber tanggal expiry dari wifi ke provider

## Data Seeder

### 1. InternetProviderUpdateSeeder.php
Seeder untuk memperbarui data provider yang sudah ada dengan informasi biaya dan spesifikasi:
- Telkom Indonesia (Fiber, 100 Mbps, Rp 350.000)
- IndiHome (Fiber, 50 Mbps, Rp 300.000)
- Biznet (Fiber, 75 Mbps, Rp 450.000)
- First Media (Cable, 100 Mbps, Rp 400.000)
- MyRepublic (Fiber, 100 Mbps, Rp 380.000)

### 2. InternetProviderContractSeeder.php
Seeder untuk memperbarui data provider dengan informasi kontrak:
- Random contract start date (6-12 bulan yang lalu)
- Contract duration (12 atau 24 bulan)
- Service expiry date berdasarkan start date + duration
- Contract status (active/expired)
- 2 provider di-set untuk testing dengan expiry date dalam 5-25 hari

## Cara Menjalankan

```bash
# Jalankan migrasi
php artisan migrate

# Jalankan seeder untuk update data provider dengan biaya
php artisan db:seed --class=InternetProviderUpdateSeeder

# Jalankan seeder untuk update data provider dengan kontrak
php artisan db:seed --class=InternetProviderContractSeeder
```

## Keuntungan Perubahan

1. **Struktur Data Lebih Logis**: Biaya terkait dengan provider, bukan device WiFi
2. **Menghindari Duplikasi**: Satu provider bisa digunakan multiple WiFi networks
3. **Manajemen Lebih Mudah**: Update biaya cukup di satu tempat (provider)
4. **Informasi Lebih Lengkap**: Provider memiliki informasi spesifikasi layanan
5. **Skalabilitas**: Mudah menambah provider baru dengan spesifikasi lengkap

## Struktur Relasi

```
InternetProvider (1) -----> (N) WifiNetwork
- monthly_cost              - provider_id
- installation_cost         - service_expiry_date
- speed_package             - contract_start_date
- bandwidth_mbps            - (device info)
- connection_type
```

## Catatan Penting

- WiFi Network sekarang hanya menyimpan informasi device (router, SSID, password, dll)
- Semua informasi biaya dan spesifikasi layanan ada di Internet Provider
- Notifikasi kedaluwarsa tetap berfungsi dengan mengambil biaya dari provider
- Backward compatibility terjaga melalui relasi `provider.monthly_cost`