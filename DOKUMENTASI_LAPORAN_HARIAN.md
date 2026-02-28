# Fitur Laporan Harian Project

## Gambaran Umum
Fitur laporan harian memungkinkan pengguna untuk mencatat aktivitas harian project dengan dokumentasi foto.

## Lokasi Fitur
Tab "Laporan Harian" terdapat di halaman detail project (`/project/{id}`).

## Database Schema

### Tabel `daily_reports`
- `id` - Primary key
- `project_id` - Foreign key ke projects
- `tanggal` - Tanggal laporan
- `uraian_kegiatan` - Deskripsi kegiatan (text)
- `cuaca` - Cuaca saat ini (nullable)
- `jumlah_pekerja` - Jumlah pekerja yang hadir
- `catatan` - Catatan tambahan (nullable)
- `timestamps` - Created at & Updated at

### Tabel `daily_report_images`
- `id` - Primary key
- `daily_report_id` - Foreign key ke daily_reports
- `image_path` - Path file gambar
- `caption` - Keterangan foto (nullable)
- `timestamps` - Created at & Updated at

## Cara Menggunakan

### 1. Menambah Laporan Harian
1. Buka detail project
2. Klik tab "Laporan Harian"
3. Klik tombol "Tambah Laporan"
4. Isi form:
   - Tanggal (wajib)
   - Uraian Kegiatan (wajib)
   - Cuaca (opsional)
   - Jumlah Pekerja (opsional)
   - Catatan Tambahan (opsional)
   - Upload Foto Dokumentasi (opsional, bisa multiple)
5. Klik "Simpan Laporan"

### 2. Melihat Detail Laporan
1. Klik tombol "Lihat" (ikon mata) pada baris laporan
2. Modal akan menampilkan:
   - Tanggal, Cuaca, Jumlah Pekerja
   - Uraian Kegiatan lengkap
   - Catatan (jika ada)
   - Semua foto dokumentasi dengan keterangan

### 3. Mengedit Laporan
1. Klik tombol "Edit" (ikon pensil) pada baris laporan
2. Update data yang diperlukan
3. Untuk foto existing:
   - Edit keterangan foto
   - Centang "Hapus" untuk menghapus foto
4. Tambah foto baru jika diperlukan
5. Klik "Update Laporan"

### 4. Menghapus Laporan
1. Klik tombol "Hapus" (ikon trash) pada baris laporan
2. Konfirmasi penghapusan
3. Semua foto terkait juga akan dihapus

## Fitur Upload Gambar

### Spesifikasi
- Format: JPEG, PNG, JPG, GIF
- Max size: 2MB per file
- Multiple upload: Bisa upload beberapa foto sekaligus
- Storage: `storage/app/public/daily-reports/{project_id}/`

### Akses Gambar
Gambar dapat diakses via: `/storage/{image_path}`

## Permissions
- **View**: Semua user authenticated
- **Create/Edit/Delete**: Hanya administrator

## Routes

```
GET     /project/{project}/daily-reports         → index (tampil list)
POST    /project/{project}/daily-reports         → store (simpan baru)
GET     /project/{project}/daily-reports/{report} → show (detail JSON)
PUT     /project/{project}/daily-reports/{report} → update (edit)
DELETE  /project/{project}/daily-reports/{report} → destroy (hapus)
```

## File yang Dibuat

### Models
- `app/Models/DailyReport.php`
- `app/Models/DailyReportImage.php`

### Controllers
- `app/Http/Controllers/DailyReportController.php`

### Views
- `resources/views/daily-reports/index.blade.php`

### Migrations
- `database/migrations/2026_02_28_011654_create_daily_reports_table.php`
- `database/migrations/2026_02_28_011802_create_daily_report_images_table.php`

## Contoh Penggunaan

### Tambah Laporan dengan Foto
```php
$report = DailyReport::create([
    'project_id' => 1,
    'tanggal' => '2026-02-28',
    'uraian_kegiatan' => 'Pekerjaan pondasi dan bekisting',
    'cuaca' => 'Cerah',
    'jumlah_pekerja' => 15,
    'catatan' => 'Pekerjaan berjalan lancar'
]);

// Upload foto
$request->file('images')[0]->store('daily-reports/1', 'public');
```

### Tampilkan Laporan dengan Foto
```php
$report = DailyReport::with('images')->find(1);

foreach ($report->images as $image) {
    echo $image->image_path; // daily-reports/1/abc123.jpg
    echo $image->caption;    // Keterangan foto
}
```

## Troubleshooting

### Gambar Tidak Muncul
1. Pastikan storage link sudah dibuat: `php artisan storage:link`
2. Cek file ada di `storage/app/public/daily-reports/`
3. Pastikan permission folder storage benar

### Upload Gagal
1. Cek `upload_max_filesize` di php.ini
2. Cek `post_max_size` di php.ini
3. Pastikan folder storage writable

## Tanggal Implementasi
28 Februari 2026
