# SimaPro - Deployment Guide

## Panduan Deploy ke Production Server

Dokumen ini berisi panduan lengkap untuk mempersiapkan dan menjalankan aplikasi SimaPro di server production.

---

## 📋 Daftar Isi

1. [Persiapan Server](#persiapan-server)
2. [Dependencies](#dependencies)
3. [Upload Files](#upload-files)
4. [Konfigurasi Environment](#konfigurasi-environment)
5. [Install Dependencies](#install-dependencies)
6. [Setup Database](#setup-database)
7. [Setup Storage](#setup-storage)
8. [Build Assets](#build-assets)
9. [Running Application](#running-application)
10. [Troubleshooting](#troubleshooting)

---

## Persiapan Server

### Minimum Requirements

- **PHP**: >= 8.2
- **Database**: PostgreSQL / MySQL
- **Web Server**: Nginx / Apache
- **Node.js**: >= 18.x
- **Composer**: >= 2.x
- **RAM**: Minimal 2GB
- **Storage**: Minimal 5GB

### PHP Extensions Required

Pastikan extension berikut ter-install:

```bash
php-pgsql / php-mysql
php-gd
php-xml
php-mbstring
php-zip
php-curl
php-bcmath
php-json
```

### Install PHP Extensions (Ubuntu/Debian)

```bash
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-common php8.2-pgsql php8.2-gd \
php8.2-xml php8.2-mbstring php8.2-zip php8.2-curl php8.2-bcmath php8.2-dev
```

---

## Dependencies

### PHP Dependencies (Composer)

```bash
composer install --no-dev --optimize-autoloader
```

### JavaScript Dependencies (NPM)

```bash
npm install --production
```

### Package Khusus

- **barryvdh/laravel-dompdf** - Untuk generate PDF laporan harian
- **maatwebsite/excel** - Untuk import/export Excel
- **laravel/ui** - Untuk authentication UI

---

## Upload Files

### Metode Upload

#### 1. Via Git (Recommended)

```bash
cd /var/www/simapro
git clone <repository-url> .
git checkout main  # atau branch production
```

#### 2. Via FTP/SFTP

Upload semua file **kecuali**:
- `node_modules/`
- `vendor/`
- `.git/`
- `storage/` (kecuali .gitignore)

#### 3. Via ZIP Archive

```bash
# Di local
zip -r simapro.zip . -x "node_modules/*" -x "vendor/*" -x ".git/*"

# Upload ke server
scp simapro.zip user@server:/var/www/

# Extract di server
unzip simapro.zip -d /var/www/simapro
```

---

## Konfigurasi Environment

### 1. Copy Environment File

```bash
cd /var/www/simapro
cp .env.example .env
```

### 2. Edit .env File

```bash
nano .env
```

### 3. Konfigurasi Penting

```env
APP_NAME=SimaPro
APP_ENV=production
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=simapro-laravel
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
```

### 4. Generate App Key

```bash
php artisan key:generate
```

---

## Install Dependencies

### 1. Install Composer Dependencies

```bash
cd /var/www/simapro
composer install --no-dev --optimize-autoloader
```

### 2. Install NPM Dependencies

```bash
npm install --production
```

### 3. Install DOMPDF (Jika Gagal)

```bash
composer require barryvdh/laravel-dompdf --ignore-platform-req=ext-gd
```

---

## Setup Database

### 1. Buat Database

```bash
# PostgreSQL
sudo -u postgres psql
CREATE DATABASE "simapro-laravel";
CREATE USER your_username WITH PASSWORD 'your_password';
GRANT ALL PRIVILEGES ON DATABASE "simapro-laravel" TO your_username;
\q
```

### 2. Run Migrations

```bash
php artisan migrate --force
```

### 3. Seed Data (Optional)

```bash
php artisan db:seed --force
```

---

## Setup Storage

### 1. Create Storage Link

```bash
php artisan storage:link
```

### 2. Set Permissions

```bash
# Ownership
sudo chown -R www-data:www-data /var/www/simapro

# Permissions
sudo chmod -R 755 /var/www/simapro
sudo chmod -R 775 /var/www/simapro/storage
sudo chmod -R 775 /var/www/simapro/bootstrap/cache
```

### 3. Configure File Upload Limit

Edit `php.ini`:

```ini
upload_max_filesize = 10M
post_max_size = 12M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
```

---

## Build Assets

### 1. Build untuk Production

```bash
npm run build
```

### 2. Clear Cache

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 3. Clear Old Cache (Jika Update)

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## Running Application

### 1. Configure Web Server

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/simapro/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/simapro/public

    <Directory /var/www/simapro>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

### 2. Enable SSL (Recommended)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Get SSL Certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

### 3. Restart Web Server

```bash
# Nginx
sudo systemctl restart nginx

# Apache
sudo systemctl restart apache2
```

### 4. Setup Supervisor (Untuk Queue)

```bash
# Install Supervisor
sudo apt install supervisor

# Create Configuration
sudo nano /etc/supervisor/conf.d/simapro-worker.conf
```

**Content:**

```ini
[program:simapro-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/simapro/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasuser=false
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/simapro/storage/logs/worker.log
stopwaitsecs=3600
```

**Start Supervisor:**

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start simapro-worker:*
```

### 5. Setup Cron Job

```bash
# Edit crontab
crontab -e

# Add this line
* * * * * cd /var/www/simapro && php artisan schedule:run >> /dev/null 2>&1
```

---

## Troubleshooting

### 1. Permission Denied

```bash
sudo chown -R www-data:www-data /var/www/simapro
sudo chmod -R 775 /var/www/simapro/storage
sudo chmod -R 775 /var/www/simapro/bootstrap/cache
```

### 2. Class Not Found

```bash
composer dump-autoload --optimize
php artisan config:clear
php artisan cache:clear
```

### 3. Route Not Found

```bash
php artisan route:clear
php artisan route:cache
php artisan route:list
```

### 4. Migration Error

```bash
php artisan migrate:status
php artisan migrate:rollback
php artisan migrate --force
```

### 5. Assets Not Loading

```bash
npm run build
php artisan view:clear
```

### 6. PDF Generation Error

```bash
# Check if dompdf installed
composer show barryvdh/laravel-dompdf

# Reinstall if needed
composer require barryvdh/laravel-dompdf
```

### 7. File Upload Failed

Check `php.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 12M
```

Check folder permissions:
```bash
sudo chmod -R 775 /var/www/simapro/storage/app/public
```

### 8. Database Connection Failed

```bash
# Test connection
php artisan db:show

# Check .env
cat .env | grep DB_
```

---

## Checklist Pre-Deployment

- [ ] PHP version >= 8.2
- [ ] All PHP extensions installed
- [ ] Composer installed
- [ ] Node.js & NPM installed
- [ ] Database created
- [ ] .env file configured
- [ ] APP_KEY generated
- [ ] Storage link created
- [ ] Permissions set correctly
- [ ] Migrations run
- [ ] Assets built (`npm run build`)
- [ ] Cache cleared & cached
- [ ] Web server configured
- [ ] SSL certificate installed
- [ ] Supervisor configured (untuk queue)
- [ ] Cron job set up
- [ ] Error logging enabled
- [ ] Test upload file
- [ ] Test PDF generation
- [ ] Test email sending

---

## Post-Deployment Testing

### 1. Test Homepage

```bash
curl https://your-domain.com
```

### 2. Test Login

```
URL: https://your-domain.com/login
Username: admin@example.com
Password: your-password
```

### 3. Test Features

- [ ] Create Project
- [ ] Add Task
- [ ] Add Worker
- [ ] Create Attendance
- [ ] Create Daily Report
- [ ] Upload Photos
- [ ] Download PDF Report
- [ ] Import Excel

### 4. Monitor Logs

```bash
# Real-time log monitoring
tail -f /var/www/simapro/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/error.log

# PHP-FPM logs
tail -f /var/log/php-fpm/error.log
```

---

## Maintenance Mode

### Enable Maintenance Mode

```bash
php artisan down
```

### Disable Maintenance Mode

```bash
php artisan up
```

### Maintenance dengan Secret Access

```bash
php artisan down --secret="your-secret-key"
```

Access via: `https://your-domain.com/your-secret-key`

---

## Backup Database

### Manual Backup

```bash
# PostgreSQL
pg_dump -U username -h localhost simapro-laravel > backup_$(date +%Y%m%d).sql

# MySQL
mysqldump -u username -p simapro-laravel > backup_$(date +%Y%m%d).sql
```

### Automated Backup (Cron)

```bash
# Add to crontab
0 2 * * * pg_dump -U username -h localhost simapro-laravel > /backups/simapro_$(date +\%Y\%m\%d).sql
```

---

## Update Aplikasi

### 1. Pull Latest Code

```bash
cd /var/www/simapro
git pull origin main
```

### 2. Install New Dependencies

```bash
composer install --no-dev --optimize-autoloader
npm install --production
```

### 3. Run Migrations

```bash
php artisan migrate --force
```

### 4. Rebuild Assets

```bash
npm run build
```

### 5. Clear Cache

```bash
php artisan optimize:clear
php artisan optimize
```

### 6. Restart Services

```bash
sudo systemctl restart nginx
sudo supervisorctl restart simapro-worker:*
```

---

## Security Recommendations

1. **Enable HTTPS/SSL** - Gunakan Let's Encrypt
2. **Set APP_DEBUG=false** - Disable debug mode
3. **Strong Database Password** - Gunakan password kompleks
4. **Regular Updates** - Update dependencies secara berkala
5. **Backup Regularly** - Backup database & files
6. **Monitor Logs** - Setup log monitoring
7. **Firewall** - Configure UFW/iptables
8. **Rate Limiting** - Enable di Laravel
9. **CSRF Protection** - Sudah enabled by default
10. **File Upload Validation** - Sudah implemented

---

## Support & Contact

Jika mengalami masalah, hubungi:
- **Email**: support@your-domain.com
- **Documentation**: `/DOKUMENTASI_LAPORAN_HARIAN.md`

---

**Last Updated**: 28 Februari 2026  
**Version**: 1.0.0
