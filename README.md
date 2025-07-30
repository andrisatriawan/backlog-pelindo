# Backend Post Audit Monitoring & Report Setup Guide

Panduan ini berisi langkah-langkah untuk menyiapkan dan menjalankan aplikasi backend Post Audit Monitoring & Report di VPS untuk pertama kali.

## ✅ Kebutuhan Sistem

Pastikan VPS/server Anda memiliki:

-   PHP >= 8.x dengan ekstensi yang dibutuhkan Laravel:

    -   `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `bcmath`, dll.

-   Composer
-   PostgreSQL
-   Web server (Nginx)

## 📦 Langkah-Langkah Instalasi

### 1. Install Dependency Laravel

```bash
composer install
```

### 2. Salin File `.env`

```bash
cp .env.example .env
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Konfigurasi Environment

Edit file `.env` untuk menyesuaikan koneksi database dan konfigurasi lainnya:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nama-database
DB_USERNAME=username
DB_PASSWORD=password
```

### 5. Atur Izin Folder

Pastikan folder `storage` dan `bootstrap/cache` bisa ditulis oleh web server:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data .
```

### 6. Migrasi dan Seeder Database

Jika Anda menggunakan migration:

```bash
php artisan migrate
```

Jika perlu seed data awal:

```bash
php artisan db:seed
```

### 7. Konfigurasi Virtual Host (Opsional)

Jika menggunakan Nginx:

```nginx
server {
    listen 80;
    server_name domain-anda.com;
    root /var/www/nama-project/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.x-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Restart nginx:

```bash
sudo systemctl reload nginx
```

---

## 🐞 Troubleshooting

-   Jika `composer install` gagal, pastikan versi PHP kompatibel dan semua ekstensi Laravel terpasang.
-   Jika `php artisan migrate` error, pastikan database dan kredensial sudah benar.
-   Periksa log di `storage/logs/laravel.log` jika ada error.

---

## 📁 Struktur Project (Singkat)

```
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
└── .env
```

---

## ✍️ Author

By Andri Satriawan – [@andrisatriawan](https://github.com/andrisatriawan)
