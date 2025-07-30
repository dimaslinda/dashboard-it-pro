# Setup Notifikasi Pembayaran WhatsApp

## Deskripsi
Sistem notifikasi WhatsApp otomatis untuk konfirmasi pembayaran domain, hosting, dan kontrak provider menggunakan layanan Fonnte.

## Fitur
- ✅ Notifikasi konfirmasi pembayaran domain
- ✅ Notifikasi konfirmasi pembayaran hosting
- ✅ Notifikasi konfirmasi pembayaran kontrak provider
- ✅ Format pesan yang informatif dengan emoji
- ✅ Integrasi dengan sistem invoice
- ✅ Logging untuk tracking pengiriman

## Cara Kerja

Ketika admin melakukan konfirmasi pembayaran melalui widget dashboard:

1. **Domain Payment**: Setelah konfirmasi pembayaran domain, sistem akan:
   - Membuat record invoice baru
   - Menyimpan file bukti pembayaran
   - Memperbarui tanggal expiry domain
   - Mengirim notifikasi WhatsApp konfirmasi pembayaran

2. **Hosting Payment**: Setelah konfirmasi pembayaran hosting, sistem akan:
   - Membuat record invoice baru
   - Menyimpan file bukti pembayaran
   - Memperbarui tanggal expiry hosting
   - Mengirim notifikasi WhatsApp konfirmasi pembayaran

3. **Provider Contract Payment**: Setelah konfirmasi pembayaran kontrak, sistem akan:
   - Membuat record invoice baru
   - Menyimpan file bukti pembayaran
   - Memperbarui tanggal expiry kontrak
   - Mengirim notifikasi WhatsApp konfirmasi pembayaran

## Format Pesan Notifikasi

### Domain Payment
```
✅ *KONFIRMASI PEMBAYARAN DOMAIN* ✅

📌 *Website:* Nama Website
🌐 *Domain:* domain.com
💰 *Jumlah Pembayaran:* Rp 150.000
📅 *Tanggal Perpanjangan:* 15/01/2026
🏢 *Registrar:* Nama Registrar

🎉 Domain telah berhasil diperpanjang dan pembayaran telah dicatat!
```

### Hosting Payment
```
✅ *KONFIRMASI PEMBAYARAN HOSTING* ✅

📌 *Website:* Nama Website
🌐 *URL:* https://website.com
💰 *Jumlah Pembayaran:* Rp 300.000
📅 *Tanggal Perpanjangan:* 15/01/2026
🏢 *Provider:* Nama Provider

🎉 Hosting telah berhasil diperpanjang dan pembayaran telah dicatat!
```

### Provider Contract Payment
```
✅ *KONFIRMASI PEMBAYARAN KONTRAK PROVIDER* ✅

🏢 *Provider:* Nama Provider
🏢 *Perusahaan:* Nama Perusahaan
💰 *Jumlah Pembayaran:* Rp 500.000
📅 *Tanggal Perpanjangan:* 15/02/2025
📶 *Layanan:* Internet/WiFi

🎉 Kontrak provider telah berhasil diperpanjang dan pembayaran telah dicatat!
```

## Konfigurasi

### File .env
Pastikan konfigurasi Fontte sudah benar di file `.env`:

```env
# Fontte WhatsApp API Configuration
FONTTE_TOKEN=your_actual_fontte_token_here
FONTTE_NOTIFICATION_TARGET=628123456789
```

### Penjelasan Konfigurasi
- `FONTTE_TOKEN`: Token API dari dashboard Fontte
- `FONTTE_NOTIFICATION_TARGET`: Nomor WhatsApp tujuan dalam format internasional (628xxxxxxxxx)

## File yang Dimodifikasi

1. **FontteService.php**: Ditambahkan metode notifikasi pembayaran
   - `sendDomainPaymentNotification()`
   - `sendHostingPaymentNotification()`
   - `sendProviderContractPaymentNotification()`

2. **ExpiryNotificationWidget.php**: Ditambahkan notifikasi setelah konfirmasi pembayaran
   - Domain payment action
   - Hosting payment action

3. **WifiExpiryNotificationWidget.php**: Ditambahkan notifikasi setelah konfirmasi pembayaran
   - Provider contract payment action

## Troubleshooting

### Notifikasi Tidak Terkirim
1. **Periksa Token Fontte**:
   - Pastikan `FONTTE_TOKEN` di file `.env` sudah benar
   - Token tidak boleh ada spasi

2. **Periksa Nomor Target**:
   - Pastikan `FONTTE_NOTIFICATION_TARGET` dalam format yang benar
   - Format: 628xxxxxxxxx (untuk Indonesia)

3. **Periksa Log**:
   - Cek file log Laravel untuk error notifikasi
   - Path: `storage/logs/laravel.log`

4. **Periksa Saldo Fontte**:
   - Pastikan saldo Fontte mencukupi
   - Login ke dashboard Fontte untuk mengecek saldo

### Testing Notifikasi
Gunakan tombol "Test Notifikasi" di widget untuk memastikan konfigurasi Fontte berjalan dengan baik.

## Keamanan
- Token Fontte disimpan di file `.env` (tidak di-commit ke repository)
- Notifikasi hanya dikirim jika konfigurasi target tersedia
- Error handling untuk mencegah crash aplikasi jika notifikasi gagal

## Maintenance
- Monitor log secara berkala untuk memastikan notifikasi terkirim
- Periksa saldo Fontte secara rutin
- Update token jika diperlukan