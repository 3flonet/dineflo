# 🌐 Production Deployment Installation Guide

This guide is designed for deploying **Dineflo** to a production server (Apache/Nginx with PHP 8.2+).

---

## 🏗️ Server Requirements

Ensure your server meets the following:
- **PHP 8.2+** with necessary extensions (intl, gd, bcmath, pdo_mysql).
- **MySQL 8.0+** or MariaDB.
- **Node.js 18+** (for building assets).
- **HTTPS Certificate** (required for secure payment gateway and PWA).
- **Composer** (v2+).

---

## 🚀 Deployment Steps

### 1. Preparation
Upload the project folder to your server (`/var/www/dineflo` or similar).

### 2. Environment Setup
Create your production environment file:
```bash
cp .env.example .env
php artisan key:generate --force
```
Update your `.env` with production data:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PASSWORD`, etc. (SMTP connection).

### 3. File Permissions
Ensure the following directories are writable by the web server (e.g., `www-data`):
```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 4. Install Dependencies
```bash
# Clone to server root first
composer install --no-dev --optimize-autoloader
```

### 5. Compile Assets
Compile your JS/CSS for production (Minified):
```bash
npm install
npm run build
```

### 6. Administrative Tasks
Link the storage to the public directory:
```bash
php artisan storage:link
```
Run the standard Laravel optimization:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## ⚡ Real-time & Scheduler (Required)

### 1. Scheduler Cron Job
Add the following entry to your server's crontab (`crontab -e -u www-data`):
```bash
* * * * * cd /var/www/dineflo && php artisan schedule:run >> /dev/null 2>&1
```
This enables daily license pings, expiration warnings, and automated reports.

### 2. Reverb & WebSockets (Real-time Features) - Opsional

Dineflo v2 mendukung dua jenis koneksi real-time yang dapat diatur melalui **Admin Dashboard > Settings > Manage Settings > Real-time (Broadcasting)**.

#### **A. Menggunakan Pusher (Rekomendasi Shared Hosting / cPanel)**
Jika Anda menggunakan hosting biasa, gunakan Pusher untuk kemudahan instalasi:
1. Pilih **Cloud Service (Pusher)** di panel admin.
2. Masukkan API Key dari pusher.com.
3. Klik Simpan. Tidak perlu konfigurasi server tambahan.

#### **B. Menggunakan Laravel Reverb (Rekomendasi VPS)**
Jika Anda menggunakan VPS, Anda bisa menggunakan Reverb (gratis):
1. Pilih **Internal Server (Laravel Reverb)** di panel admin.
2. Jalankan server Reverb di terminal VPS Anda:
   ```bash
   php artisan reverb:start
   ```
3. Agar tetap berjalan di latar belakang, gunakan **nohup** atau **Supervisord**:

**Akses via Supervisord (Produksi):**
Buat file `/etc/supervisor/conf.d/dineflo-reverb.conf`:
```ini
[program:dineflo-reverb]
command=php /var/www/dineflo/artisan reverb:start
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/dineflo/storage/logs/reverb.log
```
Lalu aktifkan:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start dineflo-reverb
```

---

## 🔑 Production Activation & Smart Wizard

1. Navigate to `https://your-domain.com`.
2. The **Web Installer** will automatically launch because `installed.lock` is missing.
3. Follow the same activation steps as in [Localhost Installation](LOCALHOST_INSTALLATION.md) with enhanced features:
   - **Auto-SSL Detection:** The installer detects `https` in your `APP_URL` and automatically configures Reverb to use **Secure WebSockets (WSS)** on port **443**.
   - **Background Engine Startup:** Upon finalization, the installer attempts to run `nohup php artisan reverb:start &` to start real-time features immediately.
   - **Permission Sync:** The installer automatically triggers `php artisan dineflo:sync-permissions` to ensure all roles are correctly configured.
   - **License Verification:** Authenticate your production instance with the 3FLO Central Hub.
4. Once completed, the system will be locked for production use.

> [!IMPORTANT]
> While the installer attempts a background startup, we still **highly recommend** setting up **Supervisor** (Step 2 above) to ensure the Reverb engine restarts automatically if the server reboots or crashes.

---

## 🛡️ Security Best Practices
- **Turn off Debug Mode:** Always keep `APP_DEBUG=false` in production.
- **Cloudflare/SSL:** Recommended for DDoS protection and encrypted connections.
- **Rate Limiting:** Pre-configured in the API with 60 requests/minute.
