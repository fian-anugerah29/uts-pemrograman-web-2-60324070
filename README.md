# uts-pemrograman-web-2-60324070

## Nama dan NIM
- Nama : Fian Anugerah
- NIM  : 60324070

## Deskripsi Singkat Aplikasi
Aplikasi web sederhana untuk mengelola Kategori Buku di perpustakaan menggunakan PHP Native dan MySQL. Aplikasi memiliki fitur CRUD lengkap (Create, Read, Update, Delete) dengan antarmuka Bootstrap 5 dan sistem notifikasi flash message berbasis PHP Session.

## Cara Instalasi dan Menjalankan Aplikasi
1. Clone repository ini ke folder `htdocs` XAMPP:
   ```
   git clone https://github.com/fian-anugerah29/uts-pemrograman-web-2-60324070.git uts_60324070
   ```
2. Import database melalui phpMyAdmin atau MySQL CLI:
   ```
   mysql -u root -p < database/database_backup.sql
   ```
3. Sesuaikan konfigurasi di `config/database.php` jika diperlukan.
4. Jalankan XAMPP (Apache + MySQL), lalu buka browser:
   ```
   http://localhost/uts_60324070/
   ```

## Struktur Folder
```
uts_60324070/
├── config/
│   └── database.php
├── database/
│   └── database_backup.sql
├── index.php
├── create.php
├── edit.php
├── delete.php
└── README.md
```

## Link Repository GitHub
https://github.com/fian-anugerah29/uts-pemrograman-web-2-60324070
