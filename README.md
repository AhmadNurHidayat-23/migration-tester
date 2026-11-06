# Migration Tester

Pengujian migration database Laravel milik aplikasi ini dengan database SQLite baru pada setiap run.

## Fitur

- Pilih migration tepercaya di database/test-migrations, jalankan up, uji down/rollback, dan bandingkan schema nyata.
- Daftar/status migration, error SQL, riwayat privat pengguna, serta laporan JSON.
- Menolak upload/path/koneksi bebas dan environment produksi. Database aplikasi atau proyek Laravel lain tidak digunakan sebagai target.
- Kasus negatif kolom duplikat disediakan dengan label jelas untuk menguji pelaporan error.

## Teknologi

Laravel 13, Blade, Tailwind CSS, TypeScript, SQLite, PHPUnit.

## Cara menjalankan

Memerlukan PHP 8.4+, Composer, dan Node.js 22.12+. Arahkan document root Laragon ke folder `public`.

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm ci
npm run build
php artisan serve
```

Buka http://127.0.0.1:8000. Jalankan `php artisan test` untuk pengujian backend.

Buat akun melalui halaman pendaftaran. Tambahan migration hanya dilakukan oleh pengelola source code melalui direktori dan registry tepercaya; aplikasi tidak menjalankan PHP unggahan. File database per run berada di storage/app/testing/migration-runs dan tidak masuk Git.
