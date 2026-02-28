# Backup Pre-Vite Migration

Folder ini berisi backup konfigurasi Laravel Mix sebelum migrasi ke Vite.

## File Backup
- `package.json` - Package configuration dengan Laravel Mix
- `webpack.mix.js` - Webpack configuration
- `resources/js/app.js` - Entry point JavaScript (CommonJS)
- `resources/js/bootstrap.js` - Bootstrap setup (CommonJS)
- `resources/sass/app.scss` - Sass entry point

## Cara Restore ke Laravel Mix

Jalankan script berikut dari root project:

```bash
restore-laravel-mix.bat
```

Script ini akan:
1. Mengcopy semua file backup ke lokasi asli
2. Menghapus `vite.config.js`
3. Restore `resources/views/layouts/app.blade.php` via git
4. Install dependencies Laravel Mix

Setelah restore, gunakan perintah:
```bash
npm run dev       # Development build
npm run watch     # Watch for changes
npm run prod      # Production build
```

## Cara Menjalankan Aplikasi (Vite)

**Development (1 perintah - Laravel + Vite):**
```bash
npm run dev
```
Aplikasi akan berjalan di: `http://localhost:8000`

**Atau dengan 2 terminal terpisah:**
- Terminal 1: `php artisan serve`
- Terminal 2: `npm run dev` (hanya Vite)

**Production:**
```bash
npm run build
php artisan serve
```

## Tanggal Backup
Backup dibuat pada: 26 Februari 2026
