# Docker Setup untuk SimaPro

## 📦 Struktur Docker

Project ini menggunakan Docker Compose dengan services berikut:

| Service | Image | Port | Deskripsi |
|---------|-------|------|-----------|
| **nginx** | nginx:alpine | 8080 | Web server |
| **app** | php:8.2-fpm | 9000 | PHP-FPM application |
| **postgresql** | postgres:16-alpine | 5432 | Database server |
| **redis** | redis:alpine | 6379 | Cache server (optional) |
| **node** | node:20-alpine | - | Asset compilation |

## 🚀 Quick Start

### Option 1: Automatic Setup (Recommended)

**Windows:**
```bash
setup-docker.bat
```

**Linux/Mac:**
```bash
chmod +x setup-docker.sh
./setup-docker.sh
```

Script ini akan otomatis:
1. ✅ Clone project dari GitHub (jika belum ada)
2. ✅ Pull perubahan terbaru (jika sudah ada)
3. ✅ Build dan start Docker containers
4. ✅ Install semua dependencies
5. ✅ Setup Laravel (migrate, key:generate, storage:link)
6. ✅ Install NPM dan build assets

### Option 2: Manual Setup

#### 1. Clone Repository
```bash
git clone https://github.com/baydevincent/SimaPro.git
cd SimaPro
```

#### 2. Copy Environment File
```bash
cp .env.docker .env
```

#### 3. Build dan Start Containers
```bash
docker-compose up -d --build
```

#### 4. Install Dependencies & Setup Laravel
```bash
# Install PHP dependencies
docker-compose exec app composer install

# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate

# Seed database (optional)
docker-compose exec app php artisan db:seed

# Create storage symlink
docker-compose exec app php artisan storage:link

# Install NPM dependencies
docker-compose exec node npm install

# Build assets (development)
docker-compose exec node npm run dev

# Build assets (production)
docker-compose exec node npm run build
```

### Option 3: Using Make (Linux/Mac)

```bash
make setup      # Setup lengkap otomatis
make up         # Start containers
make down       # Stop containers
make shell      # Access app container
make db-shell   # Access PostgreSQL
make migrate    # Run migrations
make pull       # Pull latest changes
make backup-db  # Backup database
```

### 5. Akses Aplikasi
Buka browser dan akses: **http://localhost:8080**

## 🛠️ Perintah Umum

### Menjalankan Containers
```bash
# Start semua services
docker-compose up -d

# Stop semua services
docker-compose down

# Restart services
docker-compose restart

# Lihat logs
docker-compose logs -f

# Lihat logs service tertentu
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f postgresql
```

### Menjalankan Artisan Commands
```bash
# Masuk ke container app
docker-compose exec app bash

# Jalankan artisan command
docker-compose exec app php artisan migrate
docker-compose exec app php artisan make:controller NamaController
docker-compose exec app php artisan route:list
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
```

### Database
```bash
# Akses PostgreSQL
docker-compose exec postgresql psql -U simapro -d simapro

# Export database
docker-compose exec postgresql pg_dump -U simapro simapro > backup.sql

# Import database
docker-compose exec postgresql psql -U simapro -d simapro < backup.sql
```

### Asset Compilation
```bash
# Development (watch mode)
docker-compose exec node npm run dev

# Production build
docker-compose exec node npm run build

# Watch untuk development
docker-compose exec node npm run watch
```

## 📁 Struktur File Docker

```
CI-simapro/
├── docker-compose.yml          # Docker Compose configuration
├── Dockerfile                  # PHP-FPM image configuration
├── .env.docker                 # Environment variables untuk Docker
├── setup-docker.sh             # Auto setup script (Linux/Mac)
├── setup-docker.bat            # Auto setup script (Windows)
├── Makefile                    # Make commands (Linux/Mac)
└── docker/
    └── nginx/
        └── default.conf        # Nginx configuration
```

## 🔧 Troubleshooting

### Permission Issues
```bash
# Fix permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/bootstrap/cache
```

### Clear All Cache
```bash
docker-compose exec app php artisan optimize:clear
```

### Rebuild Containers
```bash
# Force rebuild tanpa cache
docker-compose build --no-cache
docker-compose up -d
```

### Reset Database
```bash
# Drop semua tables dan migrate ulang
docker-compose exec app php artisan migrate:fresh --seed
```

### Update dari GitHub
```bash
# Pull perubahan terbaru
git pull origin main

# Restart app container
docker-compose restart app

# Run migrations jika ada perubahan database
docker-compose exec app php artisan migrate
```

## 🗑️ Cleanup

### Hapus Semua Containers dan Volumes
```bash
# Warning: Ini akan menghapus semua data database!
docker-compose down -v

# Hapus semua images juga
docker-compose down -v --rmi all
```

## 📝 Notes

- **Storage Path**: `/var/www/html` di dalam container
- **Web Root**: `/var/www/html/public`
- **Database Host**: `postgresql` (bukan `localhost`)
- **Redis Host**: `redis` (bukan `localhost`)
- **Default Port**: 8080 untuk web, 5432 untuk PostgreSQL, 6379 untuk Redis

## 🔐 Security

Untuk production, pastikan untuk:
1. Ganti semua password di `docker-compose.yml`
2. Set `APP_ENV=production`
3. Set `APP_DEBUG=false`
4. Gunakan secrets management yang proper
5. Jangan commit `.env` file ke repository

## 🔄 Update Project dari GitHub

### Cara 1: Menggunakan Script
```bash
# Linux/Mac
./setup-docker.sh

# Windows
setup-docker.bat
```

Script akan otomatis pull perubahan terbaru dan restart containers.

### Cara 2: Manual
```bash
# Pull perubahan terbaru
git pull origin main

# Rebuild jika ada perubahan Docker
docker-compose up -d --build

# Restart app saja
docker-compose restart app

# Run migrations jika perlu
docker-compose exec app php artisan migrate
```

### Cara 3: Menggunakan Make (Linux/Mac)
```bash
make pull      # Pull dan restart
make rebuild   # Rebuild semua container
```
