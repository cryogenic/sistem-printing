# Panduan Install — PrintFlow (Sistem Printing)

Panduan ini untuk memasang sistem ini pada PC client (Windows + Laragon) atau pada hosting client (Linux/cPanel/VPS).

---

## 1. Keperluan Server

| Komponen | Keperluan | Nota |
|---|---|---|
| PHP | **8.3 atau lebih baru** | Wajib. PHP 8.2 ke bawah akan gagal (composer minta `^8.3`) |
| Database | **MySQL 8** atau **MariaDB 10.6+** | Migration guna `enum()` dan `->after()` — **tidak** menyokong PostgreSQL/SQLite |
| Composer | 2.x | |
| Node.js | 20+ | Untuk build aset (Vite). Tidak perlu di server production jika aset sudah di-build |
| Web server | Nginx / Apache | Document root mesti halakan ke folder `public/` |

### Ekstensi PHP yang diperlukan

```
pdo_mysql  mbstring  openssl  fileinfo  gd  zip  dom  curl  tokenizer  xml  ctype  json  bcmath
```

Semak dengan:
```bash
php -m
```

---

## 2. Dapatkan Kod

```bash
git clone https://github.com/cryogenic/sistem-printing.git
cd sistem-printing
```

---

## 3. Install Dependency

```bash
composer install --no-dev --optimize-autoloader
```

---

## 4. Cipta Database

**MySQL / MariaDB:**
```sql
CREATE DATABASE sistem_printing
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

Cipta user database khusus (jangan guna `root` pada production):
```sql
CREATE USER 'printflow'@'localhost' IDENTIFIED BY 'kata-laluan-kuat';
GRANT ALL PRIVILEGES ON sistem_printing.* TO 'printflow'@'localhost';
FLUSH PRIVILEGES;
```

---

## 5. Konfigurasi `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Kemudian sunting `.env`:

```env
APP_NAME=PrintFlow
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-client.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_printing
DB_USERNAME=printflow
DB_PASSWORD=kata-laluan-kuat

# ToyyibPay — kredensial milik client, bukan dev
TOYYIBPAY_URL=https://toyyibpay.com
TOYYIBPAY_SECRET=secret-production-client
TOYYIBPAY_CATEGORY=category-production-client

# Akaun admin pertama (dipakai oleh seeder)
ADMIN_EMAIL=pemilik@domain-client.com
ADMIN_PASSWORD=KataLaluanKuat123!
```

> **PENTING:** `APP_URL` mesti URL sebenar yang boleh diakses dari internet. Webhook ToyyibPay menggunakan nilai ini untuk menghantar callback — jika salah, order tidak akan ditandakan sebagai `paid` secara automatik.

---

## 6. Migration dan Admin Pertama

```bash
php artisan migrate --force
php artisan db:seed --class=RoleAndAdminSeeder --force
```

Seeder akan mencipta:
- 4 peranan Spatie: `super_admin`, `staf_kilang`, `ejen`, `awam`
- 1 akaun admin (ikut `ADMIN_EMAIL` / `ADMIN_PASSWORD` dalam `.env`)

Seeder ini selamat dijalankan berulang kali.

> **JANGAN** jalankan `php artisan db:seed` tanpa nama class — `DatabaseSeeder` akan mencipta user ujian `test@example.com`.

---

## 7. Build Aset

```bash
npm install
npm run build
```

Folder `public/build` akan tercipta. Fail ini mesti ada, jika tidak semua CSS/JS akan gagal dimuatkan.

Jika server client tiada Node.js, build di mesin awak dan upload folder `public/build` bersama kod.

---

## 8. Storage Link dan Permission

```bash
php artisan storage:link
```

**Linux / hosting:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Windows (Laragon):** permission biasanya sudah betul, tiada arahan tambahan diperlukan.

---

## 9. Optimize untuk Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Selepas ini, **setiap kali** `.env` diubah, jalankan semula `php artisan config:cache` — jika tidak perubahan tidak akan dibaca.

Untuk buang cache semasa troubleshoot:
```bash
php artisan optimize:clear
```

---

## 10. Konfigurasi Web Server

### Nginx

```nginx
server {
    listen 80;
    server_name domain-client.com;
    root /var/www/sistem-printing/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache

Pastikan `mod_rewrite` diaktifkan. Laravel sudah ada `public/.htaccess` yang diperlukan.

Document root **mesti** `/var/www/sistem-printing/public`, bukan root projek.

---

## 11. Pemasangan pada Windows (Laragon)

1. Install [Laragon Full](https://laragon.org/download/) — pastikan PHP **8.3+** dipilih (Menu → PHP → Version)
2. Letak projek dalam `D:/laragon/www/sistem-printing`
3. Klik kanan tray Laragon → **Quick app** → **Laravel** (auto-create vhost), atau tambah manual
4. Buat database melalui **Database** → HeidiSQL
5. Ikut langkah 3–9 di atas dari terminal Laragon

> **Nota PHP:** Laragon kadangkala menetapkan PHP 8.0 sebagai default. Semak dengan `php -v`. Jika masih 8.0, tukar ke 8.3 melalui Menu → PHP → Version.

---

## 12. Senarai Semak Selepas Install

Buka sistem dan sahkan:

- [ ] Boleh log masuk dengan akaun admin dari `.env`
- [ ] `/admin/orders` boleh dibuka (jika tidak, kolum `users.role` bukan `admin`)
- [ ] `/admin/settings` → isi **Maklumat Syarikat (Invois)** → Simpan
- [ ] Kalkulator printing boleh kira harga
- [ ] Buat order ujian → bayar dengan wallet → muat turun invois PDF
- [ ] Alamat kedai muncul pada invois
- [ ] Top-up wallet → muat turun resit PDF
- [ ] ToyyibPay: buat bayaran ujian RM 1 dan sahkan order bertukar kepada `paid`

---

## 13. Masalah Lazim

| Masalah | Punca & Penyelesaian |
|---|---|
| `Composer detected issues... requires PHP >= 8.3` | PHP default masih 8.0/8.2. Tukar versi PHP, atau panggil path penuh PHP 8.3 |
| `Table 'users' doesn't exist` semasa migrate | Migration lama sebelum pembetulan. Pastikan commit `1923e22` atau lebih baru |
| Halaman putih / tiada CSS | `public/build` tiada. Jalankan `npm run build` |
| Imej artwork tidak keluar | Jalankan `php artisan storage:link` |
| Order tidak bertukar `paid` selepas bayar | `APP_URL` salah atau domain tidak boleh diakses dari internet |
| `Class "IntlDateFormatter" not found` | Ekstensi `intl` tidak dipasang |
| Ralat permission pada `storage/` | `chmod -R 775 storage bootstrap/cache` |
| Perubahan `.env` tidak berkesan | Jalankan `php artisan config:cache` semula |

---

## 14. Backup

Sistem ini menyimpan data perniagaan sebenar. Tetapkan backup harian untuk:

- **Database** — guna `mysqldump`:
  ```bash
  mysqldump -u printflow -p sistem_printing > backup-$(date +%Y%m%d).sql
  ```
- **Folder `storage/app`** — menyimpan fail artwork pelanggan

---

## 15. Kemas Kini Versi Baharu

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm install && npm run build
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Nota Keselamatan

- Tukar kata laluan admin **serta-merta** selepas log masuk pertama
- `APP_DEBUG=false` pada production — jika `true`, mesej ralat dan kredensial boleh terdedah
- Jangan commit `.env` ke git (sudah di-ignore)
- Kredensial ToyyibPay setiap client adalah milik mereka sendiri
