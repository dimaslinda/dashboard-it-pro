# Troubleshooting File Upload Invoice

## Masalah yang Ditemukan

File invoice tidak bisa diupload karena ada ketidaksesuaian antara konfigurasi PHP dan aplikasi:

- **PHP upload_max_filesize**: 2M
- **PHP post_max_size**: 2M  
- **Aplikasi maxSize sebelumnya**: 5120KB (5MB)

## Solusi yang Diterapkan

### 1. Penyesuaian Ukuran File Upload
- **InvoiceResource.php**: Mengurangi `maxSize` dari 5MB (5120 KB) menjadi 1MB (1024 KB)
- **ExpiryNotificationWidget.php**: 
  - Pembayaran domain: Mengurangi `maxSize` dari 5MB menjadi 1MB
  - Pembayaran hosting: Mengurangi `maxSize` dari 5MB menjadi 1MB
- **WifiExpiryNotificationWidget.php**: Mengurangi `maxSize` dari 5MB menjadi 1MB untuk pembayaran kontrak provider

### 2. Penambahan Helper Text
Menambahkan `helperText` yang informatif pada semua field upload:
```
'Upload bukti pembayaran (PDF, JPG, PNG - Max 1MB)'
```

### 3. Perbaikan Model dan Service
- **Website.php**: Menambahkan implementasi `HasMedia` interface dan `InteractsWithMedia` trait untuk mendukung upload file
- **WifiExpiryNotificationWidget.php**: Memperbaiki penggunaan FontteService dari `new FontteService()` menjadi `app(FontteService::class)` untuk konsistensi dependency injection
- **AppServiceProvider.php**: Menambahkan morph map untuk polymorphic relationships guna mengatasi error "Class website not found"

### 4. Penyesuaian Konfigurasi Aplikasi

Beberapa file telah diperbarui untuk menyesuaikan dengan limit PHP:

**File `app/Filament/Resources/InvoiceResource.php`:**

```php
Forms\Components\SpatieMediaLibraryFileUpload::make('invoice_file')
    ->label('Upload File Invoice')
    ->collection('invoices')
    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
    ->maxSize(1024) // 1MB - sesuai dengan PHP upload limit
    ->helperText('Maksimal ukuran file: 1MB. Format yang didukung: PDF, JPG, PNG')
    ->columnSpanFull(),
```

**File `app/Filament/Widgets/ExpiryNotificationWidget.php`:**
```php
SpatieMediaLibraryFileUpload::make('invoice')
    ->label('Upload Invoice/Bukti Pembayaran')
    ->collection('invoices')
    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
    ->maxSize(1024) // 1MB - sesuai dengan PHP upload limit
    ->helperText('Upload bukti pembayaran (PDF, JPG, PNG - Max 1MB)')
    ->required()
```

**File `app/Filament/Widgets/WifiExpiryNotificationWidget.php`:**
```php
SpatieMediaLibraryFileUpload::make('invoice')
    ->label('Upload Invoice/Bukti Pembayaran')
    ->collection('invoices')
    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
    ->maxSize(1024) // 1MB - sesuai dengan PHP upload limit
    ->helperText('Upload bukti pembayaran kontrak internet (PDF, JPG, PNG - Max 1MB)')
    ->required()
```

### 2. Opsi untuk Meningkatkan Limit PHP (Opsional)

Jika Anda ingin mengizinkan file yang lebih besar, edit file `php.ini`:

```ini
; Tingkatkan limit upload
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
```

Setelah mengubah `php.ini`, restart web server.

### 3. Untuk Herd (Laravel Herd)

Jika menggunakan Laravel Herd, Anda dapat:

1. Buka Herd settings
2. Pilih PHP version yang digunakan
3. Edit PHP configuration
4. Ubah nilai `upload_max_filesize` dan `post_max_size`
5. Restart Herd

## Verifikasi

Untuk memverifikasi konfigurasi PHP saat ini:

```bash
php -i | findstr upload_max_filesize
php -i | findstr post_max_size
```

## Catatan Penting

- File upload sekarang dibatasi 1MB untuk memastikan kompatibilitas
- Format yang didukung: PDF, JPG, PNG
- Helper text ditambahkan untuk memberikan informasi kepada user
- Jika perlu file lebih besar, tingkatkan konfigurasi PHP terlebih dahulu

## Testing

Setelah perubahan ini:

1. Coba upload file invoice dengan ukuran < 1MB
2. Pastikan file tersimpan di storage/app/public/
3. Verifikasi file dapat diakses melalui browser

## Troubleshooting Tambahan

Jika masih ada masalah:

1. **Periksa permission direktori**:
   ```bash
   # Pastikan direktori storage dapat ditulis
   chmod -R 775 storage/
   ```

2. **Periksa symbolic link**:
   ```bash
   php artisan storage:link
   ```

3. **Periksa log error**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Clear cache**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```