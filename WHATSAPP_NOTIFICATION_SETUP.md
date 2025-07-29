# Setup Notifikasi WhatsApp untuk Domain & Hosting Expiry

## Deskripsi
Sistem notifikasi WhatsApp otomatis untuk memberitahu domain dan hosting yang akan expired menggunakan layanan Fonnte.

## Fitur
- ✅ Notifikasi domain akan expired
- ✅ Notifikasi hosting akan expired
- ✅ Pengiriman otomatis 3 hari sebelum expired
- ✅ Pengecekan tambahan di hari yang sama
- ✅ Format pesan yang informatif dengan emoji
- ✅ Logging untuk tracking pengiriman

## Cara Setup Lengkap

### Langkah 1: Setup Fonnte WhatsApp API

#### 1.1 Daftar dan Konfigurasi Fonnte
1. **Kunjungi Website Fonnte**
   - Buka [https://fonnte.com](https://fontte.com)
   - Klik "Daftar" untuk membuat akun baru

2. **Verifikasi Akun**
   - Masukkan nomor WhatsApp yang akan digunakan untuk mengirim notifikasi
   - Verifikasi nomor melalui kode OTP yang dikirim
   - Pastikan WhatsApp Web aktif di perangkat yang sama

3. **Dapatkan Token API**
   - Login ke dashboard Fonnte
   - Masuk ke menu "API" atau "Token"
   - Copy token API yang diberikan
   - **PENTING**: Simpan token ini dengan aman

4. **Setup Device/Nomor Pengirim**
   - Pastikan nomor WhatsApp sudah terkoneksi
   - Test koneksi dengan mengirim pesan test dari dashboard
   - Catat status "Connected" pada dashboard

#### 1.2 Konfigurasi Environment Laravel

1. **Edit File .env**
   ```bash
   # Buka file .env di root project
   nano .env
   # atau
   code .env
   ```

2. **Tambahkan Konfigurasi Fontte**
   ```env
   # Fonnte WhatsApp API Configuration
   FONTTE_TOKEN=your_actual_fontte_token_here
   FONTTE_NOTIFICATION_TARGET=628123456789
   ```

3. **Penjelasan Konfigurasi:**
   - `FONTTE_TOKEN`: Token API dari dashboard Fontte (tanpa spasi)
   - `FONTTE_NOTIFICATION_TARGET`: Nomor WhatsApp tujuan dalam format internasional
     - Format: 628xxxxxxxxx (untuk Indonesia)
     - Contoh: 628123456789 (untuk nomor 0812-3456-789)
     - Hapus angka 0 di depan dan ganti dengan 62

### Langkah 2: Verifikasi Instalasi

#### 2.1 Cek Konfigurasi
```bash
# Clear cache konfigurasi
php artisan config:clear

# Cek apakah konfigurasi sudah terbaca
php artisan tinker
# Dalam tinker, jalankan:
config('services.fontte.token')
config('services.fontte.notification_target')
# Tekan Ctrl+C untuk keluar
```

#### 2.2 Test Koneksi API
```bash
# Test command dengan dry run
php artisan expiry:check --days=30
```

**Output yang diharapkan:**
- Jika berhasil: "Notifikasi berhasil dikirim" atau "Tidak ada domain/hosting yang akan expired"
- Jika gagal: "WhatsApp notification target not configured" atau error API

### Langkah 3: Setup Scheduler (Opsional)

#### 3.1 Setup Cron Job di Server
```bash
# Edit crontab
crontab -e

# Tambahkan baris berikut:
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1

# Ganti /path/to/your/project dengan path absolut project Anda
# Contoh: /var/www/html/dashboard-it-pro
```

#### 3.2 Test Scheduler
```bash
# Test manual scheduler
php artisan schedule:run

# Lihat daftar scheduled commands
php artisan schedule:list
```

### Langkah 4: Konfigurasi Data Website

#### 4.1 Pastikan Data Domain/Hosting Lengkap
1. **Login ke Dashboard**
   - Akses: `http://your-domain.com/secret`
   - Login dengan akun admin

2. **Cek Data Website**
   - Masuk ke menu "Websites"
   - Pastikan field berikut terisi:
     - `domain_expiry`: Tanggal expired domain
     - `hosting_expiry`: Tanggal expired hosting
     - `domain_registrar`: Nama registrar domain
     - `hosting_provider`: Nama provider hosting

3. **Format Tanggal**
   - Gunakan format: YYYY-MM-DD
   - Contoh: 2024-12-31

#### 4.2 Test dengan Data Real
```bash
# Test dengan website yang akan expired dalam 30 hari
php artisan expiry:check --days=30

# Test dengan website yang akan expired dalam 3 hari
php artisan expiry:check --days=3
```

## Penggunaan

### Manual Testing
Untuk test pengiriman notifikasi secara manual:

```bash
# Test notifikasi 3 hari sebelum expired
php artisan expiry:check --days=3

# Test notifikasi untuk hari ini
php artisan expiry:check --days=0

# Test dengan custom days
php artisan expiry:check --days=7
```

### Automatic Scheduling
Sistem akan berjalan otomatis dengan jadwal:
- **09:00 WIB**: Cek domain/hosting yang expired dalam 3 hari
- **14:00 WIB**: Cek domain/hosting yang expired hari ini

Untuk menjalankan scheduler Laravel:
```bash
php artisan schedule:run
```

Atau setup cron job di server:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Format Notifikasi

### Domain Expiry
```
🚨 *PERINGATAN DOMAIN AKAN EXPIRED* 🚨

📌 *Website:* Company Website
🌐 *Domain:* company.com
📅 *Tanggal Expired:* 15/01/2024
⏰ *Sisa Waktu:* 3 hari
🏢 *Registrar:* Namecheap

⚠️ Segera lakukan perpanjangan domain untuk menghindari website down!
```

### Hosting Expiry
```
🚨 *PERINGATAN HOSTING AKAN EXPIRED* 🚨

📌 *Website:* Company Website
🌐 *URL:* https://company.com
📅 *Tanggal Expired:* 15/01/2024
⏰ *Sisa Waktu:* 3 hari
🏢 *Provider:* Niagahoster

⚠️ Segera lakukan perpanjangan hosting untuk menghindari website down!
```

## Troubleshooting

### 1. Error "WhatsApp notification target not configured"
**Penyebab:** Konfigurasi environment belum benar

**Solusi:**
```bash
# 1. Cek file .env
cat .env | grep FONTTE

# 2. Pastikan ada kedua baris ini:
# FONTTE_TOKEN=your_token_here
# FONTTE_NOTIFICATION_TARGET=628xxxxxxxxx

# 3. Clear cache konfigurasi
php artisan config:clear

# 4. Test lagi
php artisan expiry:check --days=30
```

### 2. Token Tidak Valid / API Error
**Penyebab:** Token Fontte salah atau expired

**Solusi:**
```bash
# 1. Login ke dashboard Fontte
# 2. Cek status device (harus "Connected")
# 3. Generate token baru jika perlu
# 4. Update .env dengan token baru
# 5. Clear cache
php artisan config:clear
```

**Test manual API:**
```bash
# Test dengan curl
curl -X POST https://api.fonnte.com/send \
  -H "Authorization: your_token_here" \
  -d "target=628xxxxxxxxx" \
  -d "message=Test message"
```

### 3. Nomor Tujuan Tidak Valid
**Penyebab:** Format nomor salah atau nomor tidak aktif

**Solusi:**
```bash
# Format yang benar:
# Indonesia: 628xxxxxxxxx (hapus 0 di depan, ganti dengan 62)
# Contoh: 0812-3456-789 → 628123456789

# Update .env:
FONTTE_NOTIFICATION_TARGET=628123456789

# Clear cache:
php artisan config:clear
```

### 4. Pesan Tidak Terkirim
**Diagnosis:**
```bash
# 1. Cek log aplikasi
tail -f storage/logs/laravel.log

# 2. Test dengan verbose
php artisan expiry:check --days=30 -v

# 3. Cek koneksi internet
ping google.com

# 4. Test API manual (lihat poin 2)
```

**Kemungkinan Penyebab:**
- Saldo Fontte habis
- Device WhatsApp disconnect
- Nomor tujuan diblokir
- Rate limit API

### 5. Scheduler Tidak Berjalan
**Diagnosis:**
```bash
# 1. Cek apakah cron job aktif
crontab -l

# 2. Test manual scheduler
php artisan schedule:run

# 3. Lihat daftar scheduled commands
php artisan schedule:list

# 4. Cek timezone
php artisan tinker
# Dalam tinker:
date_default_timezone_get()
now()->format('Y-m-d H:i:s')
```

**Solusi:**
```bash
# 1. Setup cron job yang benar
crontab -e
# Tambahkan:
* * * * * cd /full/path/to/project && php artisan schedule:run >> /dev/null 2>&1

# 2. Pastikan path absolut benar
pwd  # untuk melihat current directory

# 3. Test permission
ls -la artisan  # pastikan executable
chmod +x artisan  # jika perlu
```

### 6. Data Website Tidak Ditemukan
**Penyebab:** Tidak ada website dengan tanggal expiry yang sesuai

**Solusi:**
```bash
# 1. Cek data di database
php artisan tinker
# Dalam tinker:
use App\Models\Website;
Website::whereNotNull('domain_expiry')->count()
Website::whereNotNull('hosting_expiry')->count()

# 2. Cek website yang akan expired
Website::domainExpiringIn(now()->addDays(3))->get()
Website::hostingExpiringIn(now()->addDays(3))->get()
```

### 7. Error Permission / File Not Found
**Solusi:**
```bash
# 1. Cek permission storage
chmod -R 775 storage
chown -R www-data:www-data storage  # di server

# 2. Regenerate autoload
composer dump-autoload

# 3. Clear semua cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 8. Debug Mode
**Untuk debugging lebih detail:**
```bash
# 1. Aktifkan debug mode di .env
APP_DEBUG=true
LOG_LEVEL=debug

# 2. Jalankan dengan verbose
php artisan expiry:check --days=30 -vvv

# 3. Monitor log real-time
tail -f storage/logs/laravel.log

# 4. Matikan debug setelah selesai
APP_DEBUG=false
```

## Monitoring

### Log Files
Semua aktivitas notifikasi dicatat di:
- `storage/logs/laravel.log`

### Command Output
Jalankan command dengan verbose untuk melihat detail:
```bash
php artisan expiry:check --days=3 -v
```

## Customization

### Mengubah Jadwal
Edit file `app/Console/Kernel.php`:
```php
$schedule->command('expiry:check --days=3')
         ->dailyAt('09:00'); // Ubah jam sesuai kebutuhan
```

### Mengubah Template Pesan
Edit file `app/Services/FontteService.php` pada method:
- `sendDomainExpiryNotification()`
- `sendHostingExpiryNotification()`

### Menambah Penerima
Untuk multiple penerima, modifikasi service untuk loop multiple targets atau gunakan grup WhatsApp.

## FAQ (Frequently Asked Questions)

### Q: Apakah bisa mengirim ke multiple nomor?
**A:** Ya, ada beberapa cara:
1. **Grup WhatsApp:** Buat grup dan gunakan ID grup sebagai target
2. **Multiple Target:** Modifikasi service untuk loop multiple nomor
3. **Broadcast List:** Gunakan fitur broadcast Fontte

```php
// Contoh untuk multiple target di FontteService.php
$targets = ['628123456789', '628987654321'];
foreach ($targets as $target) {
    $this->sendMessage($target, $message);
}
```

### Q: Berapa biaya penggunaan Fontte?
**A:** Fontte menggunakan sistem kredit:
- Pesan domestik: ~Rp 150-300 per pesan
- Pesan internasional: ~Rp 500-1000 per pesan
- Cek harga terbaru di dashboard Fontte

### Q: Apakah bisa custom format pesan?
**A:** Ya, edit file `app/Services/FontteService.php`:
```php
public function sendDomainExpiryNotification($website, $daysLeft)
{
    $message = "🚨 DOMAIN ALERT 🚨\n";
    $message .= "Website: {$website->name}\n";
    $message .= "Domain: {$website->domain}\n";
    // Custom format sesuai kebutuhan
}
```

### Q: Bagaimana jika server down saat jadwal notifikasi?
**A:** Beberapa solusi:
1. **Backup Scheduler:** Setup di multiple server
2. **Queue System:** Gunakan Laravel Queue untuk retry
3. **External Cron:** Gunakan service seperti cron-job.org
4. **Monitoring:** Setup monitoring untuk cron job

### Q: Apakah bisa integrasi dengan sistem lain?
**A:** Ya, command dapat dipanggil dari:
- API endpoint custom
- Webhook dari sistem lain
- Manual trigger dari dashboard
- External monitoring tools

### Q: Bagaimana cara backup konfigurasi?
**A:** 
```bash
# Backup file penting
cp .env .env.backup
cp app/Services/FontteService.php app/Services/FontteService.php.backup
cp app/Console/Commands/CheckExpiryNotifications.php app/Console/Commands/CheckExpiryNotifications.php.backup
```

## Tips Optimasi

### 1. Performance
```php
// Gunakan chunk untuk data besar
Website::domainExpiringIn($targetDate)
    ->chunk(100, function ($websites) {
        foreach ($websites as $website) {
            // Process notification
        }
    });
```

### 2. Rate Limiting
```php
// Tambahkan delay antar pesan
sleep(1); // 1 detik delay
// atau
usleep(500000); // 0.5 detik delay
```

### 3. Error Handling
```php
try {
    $this->fontteService->sendDomainExpiryNotification($website, $daysLeft);
} catch (Exception $e) {
    Log::error('Failed to send notification', [
        'website_id' => $website->id,
        'error' => $e->getMessage()
    ]);
}
```

### 4. Monitoring & Alerting
```bash
# Setup log monitoring
tail -f storage/logs/laravel.log | grep "notification"

# Setup alert jika command gagal
# Tambahkan ke cron:
* * * * * cd /path/to/project && php artisan schedule:run || echo "Scheduler failed" | mail admin@domain.com
```

## Security Notes
- ❌ **JANGAN** commit token Fontte ke repository
- ✅ **GUNAKAN** environment variables untuk konfigurasi sensitif
- ✅ **PASTIKAN** file `.env` tidak dapat diakses publik
- ✅ **ROTASI** token secara berkala untuk keamanan
- ✅ **BATASI** akses ke dashboard admin
- ✅ **MONITOR** log untuk aktivitas mencurigakan
- ✅ **BACKUP** konfigurasi secara rutin

## Checklist Setup

### ✅ Pre-Installation
- [ ] Akun Fontte sudah dibuat
- [ ] Nomor WhatsApp sudah diverifikasi
- [ ] Token API sudah didapat
- [ ] Target nomor sudah ditentukan

### ✅ Installation
- [ ] File `.env` sudah dikonfigurasi
- [ ] Cache sudah di-clear
- [ ] Command test berhasil
- [ ] Data website sudah lengkap

### ✅ Production
- [ ] Cron job sudah disetup
- [ ] Scheduler berjalan normal
- [ ] Log monitoring aktif
- [ ] Backup konfigurasi tersimpan

## Support
Jika mengalami masalah:
1. 📖 **Cek dokumentasi:** [Fontte Docs](https://docs.fontte.com)
2. 🔍 **Review log:** `storage/logs/laravel.log`
3. 🧪 **Test manual:** `php artisan expiry:check --days=30 -v`
4. 🔧 **Cek konfigurasi:** Pastikan semua environment variables benar
5. 💬 **Hubungi support:** Fontte atau developer sistem

---

**📝 Dokumentasi ini dibuat untuk memudahkan setup dan troubleshooting sistem notifikasi WhatsApp. Update dokumentasi ini jika ada perubahan konfigurasi atau fitur baru.**