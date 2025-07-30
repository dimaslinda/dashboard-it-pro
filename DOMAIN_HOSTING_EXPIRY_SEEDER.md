# Domain & Hosting Expiry Seeder

Seeder ini menyediakan data testing untuk fitur notifikasi domain dan hosting expiry alerts.

## 📋 Data yang Dibuat

Seeder `WebsiteExpirySeeder` akan membuat **14 website** dengan berbagai tanggal expiry:

### Domain Expiry
- **Hari ini**: Company Website (company.com)
- **3 hari**: E-commerce Store (store.example.com)
- **7 hari**: Blog Website (blog.example.com)
- **15 hari**: Portfolio Site (portfolio.dev)
- **30 hari**: API Service (api.service.com)

### Hosting Expiry
- **Hari ini**: Landing Page (landing.example.com)
- **3 hari**: Documentation Site (docs.example.com)
- **7 hari**: Demo Application (demo.app.com)
- **15 hari**: Staging Environment (staging.example.com)
- **30 hari**: Analytics Dashboard (analytics.company.com)

### Domain & Hosting Keduanya
- **Client Portal**: Domain 5 hari, Hosting 8 hari
- **Support System**: Domain 12 hari, Hosting 10 hari

### Website Aman (Tidak Expiring)
- **Main Corporate Site**: 6 bulan
- **Mobile App Backend**: 1 tahun

## 🚀 Cara Menggunakan

### 1. Jalankan Seeder
```bash
php artisan db:seed --class=WebsiteExpirySeeder
```

### 2. Test Notifikasi
```bash
# Test notifikasi hari ini
php artisan expiry:check --days=0

# Test notifikasi 3 hari ke depan
php artisan expiry:check --days=3

# Test notifikasi 7 hari ke depan
php artisan expiry:check --days=7

# Test notifikasi 30 hari ke depan
php artisan expiry:check --days=30
```

### 3. Lihat di Dashboard
- Buka: `http://localhost:8000`
- Widget "Domain & Hosting Expiry Alerts" akan menampilkan data
- Test tombol "Sudah Bayar" untuk domain dan hosting

## 📱 Expected Results

### Notifikasi Hari Ini (--days=0)
```
✅ Domain notification sent for Company Website
✅ Hosting notification sent for Landing Page
📱 Total notifications sent: 2
```

### Notifikasi 3 Hari (--days=3)
```
✅ Domain notification sent for E-commerce Store
✅ Hosting notification sent for Documentation Site
📱 Total notifications sent: 2
```

### Dashboard Widget
- **Expiring Today**: 2 items (1 domain, 1 hosting)
- **Expiring in 7 Days**: 4 items (2 domain, 2 hosting)
- **Expiring in 30 Days**: 8 items (4 domain, 4 hosting)
- **Safe**: 2 items

## 🔧 Customization

Untuk menambah atau mengubah data testing:

1. Edit file: `database/seeders/WebsiteExpirySeeder.php`
2. Tambah/ubah array `$websites`
3. Jalankan ulang seeder

## 🗑️ Reset Data

Untuk menghapus data testing:
```bash
# Hapus semua data websites
php artisan migrate:refresh

# Atau hapus manual dari database
# DELETE FROM websites WHERE domain LIKE '%.example.com' OR domain LIKE '%.dev';
```

## 📝 Notes

- Data menggunakan provider hosting dan registrar yang realistis
- Tanggal expiry dihitung relatif dari hari ini
- Seeder menggunakan `updateOrCreate()` untuk menghindari duplikasi
- Kolom `notes` digunakan untuk deskripsi website