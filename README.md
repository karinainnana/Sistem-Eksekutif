# Sistem Informasi Eksekutif BPBD DIY

Sistem informasi untuk mengelola data SPAB (Satuan Pendidikan Aman Bencana) dan DESTANA (Desa Tangguh Bencana) di wilayah DIY.

## Struktur Folder

```
project-eksekutif/
├── admin/                  # Halaman khusus admin
│   ├── index.php          # Dashboard admin
│   └── users.php          # Kelola pengguna
├── assets/                # Asset statis
│   ├── css/              # File CSS
│   │   └── styles.css
│   ├── js/               # File JavaScript
│   │   ├── scripts.js
│   │   └── datatables-simple-demo.js
│   └── img/              # Gambar
├── auth/                  # Autentikasi
│   ├── login.php         # Halaman login
│   ├── logout.php        # Proses logout
│   ├── register.php      # Halaman registrasi
│   └── forgot-password.php # Lupa password
├── config/               # Konfigurasi
│   ├── config.php        # Konfigurasi aplikasi
│   └── database.php      # Konfigurasi database
├── errors/               # Halaman error
│   ├── 401.php
│   ├── 404.php
│   └── 500.php
├── includes/             # File include/komponen
│   ├── header.php        # Header template
│   ├── sidebar.php       # Sidebar template
│   ├── footer.php        # Footer template
│   ├── auth_functions.php    # Fungsi autentikasi
│   ├── spab_functions.php    # Fungsi SPAB
│   └── destana_functions.php # Fungsi DESTANA
├── pages/                # Halaman utama
│   ├── spab.php          # Data SPAB
│   ├── destana.php       # Data DESTANA
│   ├── tabel-spab.php    # Tabel SPAB
│   └── tabel-destana.php # Tabel DESTANA
├── eksekutif/            # Redirect eksekutif
│   └── index.php
├── _old/                 # File lama (backup)
└── index.php             # Halaman utama/dashboard
```

## Fitur

- **Login System**: Sistem login dengan role-based access (Admin/Eksekutif)
- **Dashboard**: Tampilan statistik dengan FusionCharts
- **SPAB Management**: CRUD data Satuan Pendidikan Aman Bencana
- **DESTANA Management**: CRUD data Desa Tangguh Bencana
- **User Management**: Kelola pengguna (khusus admin)
- **DataTables**: Tabel data dengan sorting dan pencarian

## Teknologi

- PHP 7.4+
- MySQL/MariaDB
- Bootstrap 5.2
- FusionCharts
- Simple DataTables

## Instalasi

1. Clone/copy project ke folder htdocs
2. Import database `si_eksekutif.sql` ke MySQL
3. Konfigurasi database di `config/database.php`
4. Akses via browser: `http://localhost/project-eksekutif`

## Default Login

- **Admin**: admin@bpbd.go.id / admin123
- **Eksekutif**: eksekutif@bpbd.go.id / eksekutif123

## Chart Library

Project ini menggunakan **FusionCharts** untuk visualisasi data:

- Line Chart: Trend data per tahun
- Bar/Column Chart: Distribusi per kabupaten
- Pie/Doughnut Chart: Proporsi data

## Changelog

### v2.0.0 (2024)

- Reorganisasi struktur folder
- Konversi HTML ke PHP
- Integrasi FusionCharts
- Sistem login yang diperbaiki
- Template reusable (header, sidebar, footer)
- Fungsi terpisah untuk SPAB dan DESTANA

## Author

BPBD DIY - Bidang PKRR
