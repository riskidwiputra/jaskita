# Jaskita Laravel + Livewire

Aplikasi manajemen penyewaan jas hasil migrasi dari backend Go dan frontend terpisah menjadi aplikasi Laravel monolith dengan Livewire.

## Menjalankan

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run dev
php artisan serve
```

Akun bawaan:
- Email: `owner@jaskita.local`
- Password: `password`

## Modul

Dashboard, barang, penyewa, transaksi sewa, pengembalian barang, denda, pengeluaran, dan notifikasi jatuh tempo. Halaman ulasan pelanggan sementara dinonaktifkan.

Konfigurasi database dapat menggunakan SQLite bawaan untuk pengembangan atau MySQL/TiDB untuk produksi melalui `.env`.

## Feed Instagram

Landing page mengambil hingga 12 postingan terbaru melalui Instagram Graph API. Isi `INSTAGRAM_ACCESS_TOKEN` di `.env` dengan access token Instagram profesional yang memiliki izin `instagram_business_basic`. Token dibaca server-side, jangan gunakan awalan `VITE_` dan jangan commit nilainya. Versi API default adalah `v22.0` dan dapat diubah lewat `INSTAGRAM_GRAPH_API_VERSION`. Feed yang berhasil dimuat disimpan di cache selama satu jam.

## Ulasan Google Maps

Ulasan publik lengkap memakai Google Business Profile APIs (bukan Places API yang hanya mengembalikan beberapa ulasan). Profil harus terverifikasi dan aktif setidaknya 60 hari; proyek Google Cloud perlu mendapat persetujuan akses Business Profile APIs. Ikuti [prasyarat resmi Google](https://developers.google.com/my-business/content/prereqs), lalu aktifkan Google My Business API, My Business Account Management API, dan My Business Business Information API di proyek yang disetujui.

Buat OAuth client jenis Web application. Tambahkan redirect URI persis `https://domain-aplikasi/admin/google-business/callback` (untuk lokal, gunakan nilai `APP_URL` lokal), dan pada consent screen minta scope `https://www.googleapis.com/auth/business.manage`. Isi `GOOGLE_BUSINESS_CLIENT_ID`, `GOOGLE_BUSINESS_CLIENT_SECRET`, serta opsional `GOOGLE_BUSINESS_REDIRECT_URI` di `.env`. Jangan commit kredensial OAuth.

Jalankan `php artisan migrate`, login sebagai owner, lalu buka `/admin/google-business` dan hubungkan akun Google pemilik/pengelola. Pilih lokasi bisnis yang sesuai. Token koneksi disimpan terenkripsi di database. Penayangan ulasan di landing page dan halaman publik sedang dinonaktifkan; endpoint pelanggan `/ulasan` dan `/ulasan-google` akan mengembalikan 404. Detail API ulasan ada di [dokumentasi Google](https://developers.google.com/my-business/reference/rest/v4/accounts.locations.reviews/list).

QR untuk pelanggan tersedia di `/cetak-qr-ulasan`; halaman dapat dicetak langsung atau PDF A5 dan PNG resolusi tinggi dapat diunduh dari `public/`. QR yang saat ini digunakan membuka profil Google Maps JasKita. Setelah halaman profil terbuka, pelanggan memilih **Ulasan** lalu **Tulis ulasan**. Google juga dapat menyediakan link formulir ulasan khusus melalui fitur **Minta ulasan** pada Business Profile.
# jaskita
