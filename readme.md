<p align="center">
  <h1 align="center">🏍️ REVVO</h1>
  <p align="center"><strong>Repair and Vehicle Booking Operations</strong></p>
  <p align="center">
    Sistem informasi bengkel motor berbasis web untuk digitalisasi booking servis, operasional bengkel, sparepart, pembayaran, dan pelaporan.
  </p>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/TailwindCSS-UI-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/DomPDF-PDF_Export-4B5563?style=for-the-badge" alt="DomPDF">
  <img src="https://img.shields.io/badge/PhpSpreadsheet-Excel_Export-107C41?style=for-the-badge" alt="PhpSpreadsheet">
  <img src="https://github.com/Nuveira/revvo-app/actions/workflows/ci.yml/badge.svg?branch=develop" alt="CI Status">
</p>

---

## Deskripsi Singkat Sistem

REVVO adalah aplikasi web berbasis **PHP Native** dan **MySQL** yang mendukung tiga role utama: **Admin**, **Customer**, dan **Mekanik**. Sistem ini memungkinkan pelanggan melakukan booking servis motor secara online, admin mengatur operasional bengkel, dan mekanik memperbarui progres pengerjaan.

**Status proyek**: ✅ Selesai & Deployed

---

## Anggota Kelompok

| Nama | NIM | Modul |
|------|-----|-------|
| Geral Tritama Wahyuady | 2410631170070 | Auth + CRUD Users + Booking Form |
| Nugraha Adani | 2410631170098 | CRUD Motors + Customer Pages + Invoice PDF |
| Muhammad Rizky Dermawan | 2410631170038 | CRUD Spare Parts + Dashboard + Reports + Export |
| Raika Maulana Dwi Putra | 2410631170100 | CRUD Service Types + Mechanics + Time Slots |
| Ahmad Hidayat | 2410631170027 | CRUD Bookings (Admin) + Mekanik Flow + Payments |

---

## Demo Akun

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@bengkel.com` | `password` |
| Customer | `customer@example.com` | `password` |
| Mekanik | `andi.mek@bengkel.com` | `password` |

---

## Fitur Utama

### Fitur Wajib (7 CRUD)
- 🔐 **Auth** — Login, register, logout berbasis session PHP
- 🛡️ **Password hashing** — `password_hash()` / `password_verify()`
- 👤 **Multi-role** — Admin, Customer, Mekanik dengan guard per halaman
- 📋 **CRUD Users** — Admin kelola akun admin & mekanik
- 🏍️ **CRUD Motors** — Customer kelola motor pribadi + upload foto
- 🗂️ **CRUD Bookings** — Admin kelola seluruh booking + assign mekanik
- 🔧 **CRUD Mechanics** — Admin kelola data mekanik
- ⚙️ **CRUD Service Types** — Admin kelola jenis layanan & harga
- 🕐 **CRUD Time Slots** — Admin kelola slot waktu operasional
- 🪛 **CRUD Spare Parts** — Admin kelola stok & harga sparepart
- 📅 **Validasi double-booking** — cek kapasitas slot sebelum INSERT
- 🔄 **State machine booking** — queued → in_progress → completed → ready_for_pickup / cancelled
- 📝 **Audit log** — setiap perubahan status booking tercatat di `service_logs`
- 💳 **Verifikasi pembayaran** — admin konfirmasi pembayaran customer
- 📊 **Dashboard** — ringkasan data untuk setiap role

### Fitur Lainnya
- 🔎 Search & filter di semua list utama
- 📄 Pagination di halaman list
- 🖼️ Upload & validasi foto motor (MIME type + size limit)
- 🧾 **Invoice PDF** per transaksi (DomPDF)
- 📈 **Export Excel** laporan bulanan (PhpSpreadsheet + rumus SUM)
- 📄 **Export PDF** laporan bulanan (DomPDF)
- 🔒 CI pipeline 20 checks (syntax, security, business logic)

---

## Flow Sistem

### Customer
1. Register & login
2. Tambah data motor
3. Buat booking servis (pilih motor, layanan, tanggal, slot waktu)
4. Pantau status booking real-time
5. Lihat histori & detail booking
6. Download invoice PDF setelah servis selesai

### Admin
1. Login
2. Kelola data master (users, mechanics, service types, time slots, spare parts)
3. Lihat & kelola seluruh booking
4. Assign mekanik ke booking
5. Ubah status booking (state machine)
6. Verifikasi pembayaran customer
7. Lihat audit log perubahan status
8. Generate & export laporan bulanan (PDF / Excel)

### Mekanik
1. Login
2. Lihat daftar tugas yang di-assign
3. Update status pengerjaan (mulai / selesai)
4. Tambah sparepart yang dipakai (auto-kurang stok + recalculate total harga)
5. Tulis catatan pengerjaan
6. Lihat histori pekerjaan

---

## Tech Stack

### Backend
- **PHP 8.x Native** (no framework — sesuai aturan mata kuliah)
- **MySQL / MariaDB**
- **Composer** untuk manajemen dependency

### Frontend
- **HTML5 + CSS3 + JavaScript (vanilla)**
- **Tailwind CSS** via CDN
- **Lucide Icons** via CDN
- **Material Symbols** via CDN

### Library
- **DomPDF** — generate PDF (invoice & laporan)
- **PhpSpreadsheet** — generate Excel (.xlsx)

### Tools
- **VS Code** + PHP Intelephense
- **Laragon** / XAMPP (local server)
- **phpMyAdmin** / MySQL Workbench
- **Git + GitHub**
- **FileZilla** (FTP deploy ke InfinityFree)

---

## Struktur Folder

```text
revvo-app/
├── .github/
│   └── workflows/
│       └── ci.yml                    # Otomasi pengecekan kode setiap push ke GitHub
├── assets/
│   ├── css/
│   │   └── custom.css                # CSS tambahan di luar Tailwind
│   ├── js/
│   │   ├── main.js                   # JS global (dropdown, interaksi umum)
│   │   ├── landing.js                # Animasi & interaksi khusus landing page
│   │   ├── navbar.js                 # Toggle navbar mobile / hamburger menu
│   │   └── tailwind.config.js        # Konfigurasi warna custom Tailwind (tema merah REVVO)
│   └── images/
│       └── logo.png                  # Logo aplikasi REVVO
├── config/
│   ├── koneksi.php                   # Koneksi database MySQL (mysqli)
│   └── app.php                       # Konstanta global & helper konfigurasi
├── includes/
│   ├── auth.php                      # Cek session + fungsi checkRole() untuk guard halaman
│   ├── customer_role.php             # Guard khusus halaman customer (set $user_id, $customer_id)
│   ├── functions.php                 # Fungsi bantu bersama (format rupiah, validasi, dll)
│   ├── header.php                    # Bagian <head> HTML + load CSS & JS CDN
│   ├── footer.php                    # Penutup HTML + inisialisasi Lucide icons
│   └── navbar.php                    # Navbar atas (responsive, beda per role)
├── pages/
│   ├── 403.php                       # Halaman error akses ditolak (role tidak sesuai)
│   ├── auth/
│   │   ├── login.php                 # Halaman form login
│   │   ├── register.php              # Halaman form registrasi customer baru
│   │   ├── logout.php                # Halaman konfirmasi logout
│   │   ├── proses_login.php          # Handler POST: verifikasi email + password_verify()
│   │   ├── proses_logout.php         # Handler: destroy session + redirect ke login
│   │   └── proses_register.php       # Handler POST: validasi + password_hash() + INSERT user
│   ├── admin/
│   │   ├── dashboard.php             # Statistik ringkasan: booking, revenue, mekanik aktif
│   │   ├── users.php                 # CRUD akun: tambah/edit/hapus user admin & mekanik
│   │   ├── bookings.php              # Daftar semua booking + filter + assign mekanik
│   │   ├── booking_detail.php        # Detail booking + ubah status + lihat parts & log
│   │   ├── create_booking.php        # Admin buat booking manual (walk-in customer)
│   │   ├── mechanics.php             # CRUD mekanik: tambah/edit/hapus data mekanik
│   │   ├── service_types.php         # CRUD jenis layanan: nama, deskripsi, harga dasar
│   │   ├── spare_parts.php           # CRUD sparepart: nama, stok, harga
│   │   ├── time_slots.php            # CRUD slot waktu: jam buka, kapasitas per slot
│   │   ├── payments.php              # Daftar pembayaran + verifikasi / konfirmasi lunas
│   │   ├── reports.php               # Laporan bulanan + tombol export PDF & Excel
│   │   ├── audit_logs.php            # Riwayat semua perubahan status booking (siapa, kapan)
│   │   ├── nav.php                   # Sidebar / navbar khusus halaman admin
│   │   ├── proses_users.php          # Handler POST: create/update/delete user
│   │   ├── proses_mechanics.php      # Handler POST: create/update/delete mekanik
│   │   ├── proses_service_types.php  # Handler POST: create/update/delete jenis layanan
│   │   └── proses_time_slots.php     # Handler POST: create/update/delete slot waktu
│   ├── customer/
│   │   ├── dashboard.php             # Dashboard customer: ringkasan booking aktif & motor
│   │   ├── booking.php               # Daftar booking aktif milik customer
│   │   ├── booking_detail.php        # Detail booking + tombol cancel (jika masih queued)
│   │   ├── tambah_booking.php        # Form buat booking baru: pilih motor, layanan, slot
│   │   ├── edit_booking.php          # Form edit booking (hanya jika belum in_progress)
│   │   ├── update_booking.php        # Handler POST: update data booking
│   │   ├── hapus_booking.php         # Handler: hapus booking yang masih queued
│   │   ├── history.php               # Histori semua booking selesai & dibatalkan
│   │   ├── detail_history.php        # Detail booking dari histori + ringkasan biaya
│   │   ├── invoice.php               # Generate & download invoice PDF via DomPDF
│   │   ├── motor.php                 # Daftar semua motor milik customer
│   │   ├── tambah_motor.php          # Form tambah motor baru + upload foto
│   │   ├── edit_motor.php            # Form edit data motor + ganti foto
│   │   ├── detail_motor.php          # Detail motor + tombol hapus
│   │   ├── profile.php               # Halaman profil customer (edit nama, HP, alamat)
│   │   ├── proses_booking.php        # Handler POST: validasi slot + INSERT booking & payment
│   │   ├── proses_profile.php        # Handler POST: update profil + ganti foto profil
│   │   ├── nav.php                   # Navbar khusus halaman customer
│   │   └── footer.php                # Footer khusus halaman customer
│   └── mekanik/
│       ├── dashboard.php             # Dashboard mekanik: tugas hari ini & statistik
│       ├── my_tasks.php              # Daftar booking yang di-assign ke mekanik ini
│       ├── task_detail.php           # Detail tugas + mulai/selesai + tambah sparepart
│       ├── proses_task.php           # Handler POST: update status + INSERT service_logs
│       ├── history.php               # Histori pekerjaan yang sudah selesai
│       ├── nav.php                   # Navbar khusus halaman mekanik
│       └── footer.php                # Footer khusus halaman mekanik
├── uploads/
│   ├── motors/                       # Folder simpan foto motor (tidak di-commit ke git)
│   ├── profile/                      # Folder simpan foto profil (tidak di-commit ke git)
│   └── bukti_bayar/                  # Folder simpan bukti transfer (tidak di-commit ke git)
├── database/
│   └── revvo.sql                     # Schema lengkap 11 tabel + dummy data siap pakai
├── docs/
│   ├── diagram/                      # ERD, use case, activity diagram, class diagram
│   ├── plan/                         # Rencana pengerjaan & pembagian modul per anggota
│   ├── testing/                      # Checklist testing manual per fitur
│   └── gap/                          # Catatan bug yang ditemukan & status perbaikannya
├── composer.json                     # Daftar library yang dipakai (DomPDF, PhpSpreadsheet)
├── composer.lock                     # Versi exact library terkunci (wajib di-commit)
├── .gitignore                        # Daftar file/folder yang tidak masuk ke git (vendor, uploads, .env)
└── index.php                         # Landing page / halaman utama publik
```

---

## Struktur Database

11 tabel dengan relasi foreign key:

| # | Tabel | Fungsi |
|---|-------|--------|
| 1 | `users` | Akun semua role: admin, customer, mekanik |
| 2 | `customers` | Data tambahan customer (alamat, HP) |
| 3 | `mechanics` | Data tambahan mekanik (spesialisasi) |
| 4 | `motors` | Motor milik customer (brand, model, plat, foto) |
| 5 | `service_types` | Jenis layanan & harga dasar |
| 6 | `spare_parts` | Stok & harga sparepart |
| 7 | `time_slots` | Slot waktu operasional bengkel |
| 8 | `bookings` | Transaksi booking (snapshot harga saat booking) |
| 9 | `booking_parts` | Sparepart yang dipakai per booking (snapshot harga) |
| 10 | `payments` | Data & status pembayaran |
| 11 | `service_logs` | Riwayat perubahan status booking (audit trail) |

### Business Rules
- **Snapshot harga**: `bookings.service_price` dan `booking_parts.price_at_time` disimpan saat transaksi agar tidak terpengaruh perubahan harga master data
- **Double-booking prevention**: Cek `booked_count >= capacity` pada time_slot + tanggal sebelum INSERT
- **Stock decrement**: Stok sparepart otomatis berkurang saat mekanik tambah part ke booking
- **Total price**: `bookings.total_price = service_price + SUM(booking_parts.subtotal)`, di-recalculate setiap kali part ditambahkan

---

## CI Pipeline (GitHub Actions)

Proyek ini menggunakan **GitHub Actions** — setiap kali ada push ke GitHub, sistem otomatis menjalankan pengecekan kode tanpa perlu dilakukan manual. Tujuannya adalah memastikan tidak ada bug, celah keamanan, atau aturan bisnis yang terlewat sebelum kode masuk ke branch utama.

Total ada **20 pengecekan otomatis**, dibagi ke 5 kategori:

### 1. Syntax & Kualitas Kode
- Cek syntax PHP di semua file — pastikan tidak ada error fatal
- Deteksi `var_dump` / `print_r` — fungsi debug yang tidak boleh ada di kode final

### 2. Keamanan (Security)
- SQL Injection: variabel superglobal (`$_GET`, `$_POST`) tidak boleh langsung masuk ke query
- Tidak boleh ada `eval()` — fungsi berbahaya yang bisa dieksploitasi
- `md5()` / `sha1()` tidak boleh dipakai untuk password — harus `password_hash()`
- XSS: tidak boleh `echo $_GET/POST` langsung tanpa `htmlspecialchars()`
- File `.env` dan folder `vendor/` tidak boleh masuk ke repository git
- Koneksi database tidak boleh ditulis di luar `config/koneksi.php`

### 3. Auth Guard
- Setiap halaman admin wajib ada `checkRole(['admin'])`
- Setiap halaman customer wajib ada auth guard
- Setiap halaman mekanik wajib ada `checkRole(['mechanic'])`
→ Memastikan tidak ada halaman yang bisa diakses tanpa login atau role yang benar

### 4. Aturan Bisnis (Business Logic)
- Setiap perubahan status booking wajib mencatat ke tabel `service_logs` (audit trail)
- Setiap `INSERT bookings` wajib diikuti `INSERT payments` (tidak boleh ada booking tanpa record pembayaran)
- Setiap penambahan sparepart wajib mengurangi stok dan menghitung ulang total harga
- File upload wajib ada validasi tipe file (cegah upload file berbahaya)
- `proses_*.php` wajib redirect setelah POST (mencegah form tersubmit dua kali)

### 5. Kelengkapan Repository
- Folder `uploads/` tidak boleh masuk git (hanya `.gitkeep` yang boleh)
- `composer.lock` wajib ada di repository (pastikan semua anggota pakai versi library yang sama)

---

## Branch Workflow

### Branch yang digunakan

| Branch | Tujuan |
|--------|--------|
| `main` | Final / release |
| `develop` | Integrasi semua fitur |
| `feat-foundation-auth` | Auth + layout dasar |
| `feat-admin-users-crud` | CRUD users |
| `feat-admin-bookings-crud` | CRUD bookings admin + mekanik flow |
| `feat-admin-mechanics-crud` | CRUD mechanics |
| `feat-admin-service-types-crud` | CRUD service types |
| `feat-admin-spare-parts-crud` | CRUD spare parts + dashboard + reports |
| `feat-admin-time-slots-crud` | CRUD time slots |
| `feat-customer-motors-crud` | CRUD motors customer |
| `feat-customer-bookings-crud` | Booking form customer |
| `feat-customer` | Customer pages lanjutan |
| `feat-customer-temp` | Hotfix customer flow |
| `feat-customer-invoice-pdf` | Invoice PDF DomPDF |
| `feat-customer-profile` | Edit profil customer |
| `feat-landing-page` | Landing page |
| `chore-structure-cleanup` | Cleanup struktur |

### Alur
1. Branch fitur dibuat dari `develop`
2. Selesai → PR ke `develop`
3. Setelah stabil → merge `develop` ke `main`
4. Tidak ada push langsung ke `main`

---

## Status Pembagian Modul

| Anggota | Modul Utama | Status |
|---------|-------------|--------|
| Geral Tritama Wahyuady | Foundation + Auth + CRUD Users + Booking Form | ✅ Selesai |
| Nugraha Adani | CRUD Motors + Customer Pages + Invoice PDF | ✅ Selesai |
| Muhammad Rizky Dermawan | CRUD Spare Parts + Dashboard + Reports + Export | ✅ Selesai |
| Raika Maulana Dwi Putra | CRUD Service Types + Mechanics + Time Slots | ✅ Selesai |
| Ahmad Hidayat | CRUD Bookings (Admin) + Mekanik Flow + Payments | ✅ Selesai |

---

## Status Pengerjaan

| Phase | Deskripsi | Status |
|-------|-----------|--------|
| 0 | Persiapan & setup tools | ✅ Selesai |
| 1 | Design & setup database (11 tabel + dummy data) | ✅ Selesai |
| 2 | Authentication & layout dasar | ✅ Selesai |
| 3 | CRUD master data (7 entitas) | ✅ Selesai |
| 4 | Core booking flow (booking, status, parts, payment) | ✅ Selesai |
| 5 | Bonus features (search, filter, upload, PDF, Excel) | ✅ Selesai |
| 6 | Dashboard & audit log | ✅ Selesai |
| 7 | Testing & polish | ✅ Selesai |
| 8 | Deliverables | 🔄 In progress |
| 9 | Deployment | ✅ Deployed (InfinityFree) |
| 10| Responsive | Not Yet  |

---

## Cara Instalasi Lokal

### Requirements
- PHP 8.x
- MySQL / MariaDB
- Composer
- Laragon / XAMPP / MAMP

### Langkah

```bash
# 1. Clone repository
git clone https://github.com/Nuveira/revvo-app.git
cd revvo-app

# 2. Install dependencies
composer install

# 3. Import database
# Buka phpMyAdmin → buat database 'revvo' → import database/revvo.sql

# 4. Jalankan server (Laragon/XAMPP)
# Akses: http://localhost/revvo-app
```

### Login Demo
- **Admin**: `admin@bengkel.com` / `password`
- Semua akun dummy pakai password: `password`

---