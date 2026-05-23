<p align="center">
  <h1 align="center">🏍️ REVVO</h1>
  <p align="center"><strong>Repair and Vehicle Booking Operations</strong></p>
  <p align="center">
    Sistem informasi bengkel motor berbasis web untuk digitalisasi booking servis, operasional bengkel, sparepart, pembayaran, dan pelaporan.
  </p>
</p>

---

## 🧾 Deskripsi Singkat Sistem

REVVO adalah aplikasi web berbasis **PHP Native** dan **MySQL** yang mendukung tiga role utama, yaitu **Admin**, **Customer**, dan **Mekanik**. Sistem ini memungkinkan pelanggan melakukan booking servis motor secara online, admin mengatur operasional bengkel, dan mekanik memperbarui progres pengerjaan servis.

### Fokus utama sistem:
- Digitalisasi proses booking servis motor
- Monitoring status servis secara transparan
- Pengelolaan data pelanggan, motor, mekanik, dan sparepart
- Pencatatan pembayaran dan histori servis
- Pembuatan laporan operasional bengkel

---

## 👥 Anggota Kelompok

- Geral Tritama Wahyuady
- Nugraha Adani
- Muhammad Rizky Dermawan
- Raika Maulana Dwi Putra
- Ahmad Hidayat

---

## ✨ Fitur Utama

### Fitur Wajib
- 🔐 Login, logout, dan autentikasi berbasis session
- 🛡️ Password disimpan dengan `password_hash()`
- 👤 Multi-role pengguna: **Admin**, **Customer**, dan **Mekanik**
- 🧩 CRUD lengkap untuk entitas utama:
  - Users
  - Motors
  - Bookings
  - Mechanics
  - Service Types
  - Time Slots
  - Spare Parts
- 📅 Validasi booking untuk mencegah double-booking
- 👨‍🔧 Penugasan mekanik ke booking
- 🔄 Update status servis:
  - Antri
  - Dikerjakan
  - Selesai
  - Siap Diambil
  - Dibatalkan
- 📝 Audit log perubahan status booking
- 📊 Dashboard ringkasan data
- ✅ Validasi input sisi server
- 🧱 Prepared Statement untuk mencegah SQL Injection

### Fitur Bonus
- 🔎 Search dan filter data booking
- 📄 Pagination pada halaman list
- 🖼️ Upload gambar kondisi motor
- 🧾 Export PDF
- 📈 Export Excel
- 🧑‍🤝‍🧑 Multi-role access control

---

## 🔄 Flow Sistem

### Customer
1. Register dan login
2. Mengelola data motor pribadi
3. Membuat booking servis
4. Melihat status booking
5. Melihat histori booking
6. Download invoice setelah servis selesai

### Admin
1. Login ke sistem
2. Mengelola data master
3. Melihat seluruh booking
4. Menugaskan mekanik
5. Verifikasi pembayaran
6. Melihat audit log
7. Generate laporan bulanan

### Mekanik
1. Login ke sistem
2. Melihat tugas yang di-assign
3. Mengubah status pengerjaan
4. Menambahkan sparepart yang dipakai
5. Menulis catatan pengerjaan
6. Melihat histori pekerjaan

---

## 🛠️ Tech Stack

<p>
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/HTML5-Markup-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5">
  <img src="https://img.shields.io/badge/CSS3-Styling-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3">
  <img src="https://img.shields.io/badge/JavaScript-Frontend-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
  <img src="https://img.shields.io/badge/TailwindCSS-UI-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Git-Version_Control-F05032?style=for-the-badge&logo=git&logoColor=white" alt="Git">
  <img src="https://img.shields.io/badge/GitHub-Repository-181717?style=for-the-badge&logo=github&logoColor=white" alt="GitHub">
  <img src="https://img.shields.io/badge/Laragon-Local_Server-0E83CD?style=for-the-badge" alt="Laragon">
  <img src="https://img.shields.io/badge/XAMPP-Alternative_Server-FB7A24?style=for-the-badge&logo=xampp&logoColor=white" alt="XAMPP">
  <img src="https://img.shields.io/badge/DomPDF-PDF_Export-4B5563?style=for-the-badge" alt="DomPDF">
  <img src="https://img.shields.io/badge/PhpSpreadsheet-Excel_Export-107C41?style=for-the-badge" alt="PhpSpreadsheet">
  <img src="https://img.shields.io/badge/VS_Code-Editor-007ACC?style=for-the-badge&logo=visualstudiocode&logoColor=white" alt="VS Code">
</p>

### Backend
- **PHP Native 8.x**

### Database
- **MySQL / MariaDB**

### Frontend
- **HTML5**
- **CSS3**
- **JavaScript**
- **Tailwind CSS**

### Library Pendukung
- **DomPDF** untuk export PDF
- **PhpSpreadsheet** untuk export Excel

### Tools Development
- **VS Code**
- **PHP Intelephense**
- **phpMyAdmin / MySQL Workbench**
- **Git & GitHub**
- **dbdiagram.io / draw.io**
- **Laragon / XAMPP / MAMP**

---

## 📁 Struktur Folder

`README` ini menjelaskan **target structure** proyek. Saat ini repo masih berada pada tahap fondasi awal, jadi belum semua folder di bawah sudah dibuat.

```text
revvo-app/
├── assets/
│   ├── css/
│   │   └── custom.css
│   ├── js/
│   │   └── main.js
│   └── images/
│       └── logo.png
├── config/
│   └── koneksi.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── navbar.php
│   ├── auth.php
│   └── functions.php
├── pages/
│   ├── auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   └── logout.php
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── users.php
│   │   ├── customers.php
│   │   ├── bookings.php
│   │   ├── mechanics.php
│   │   ├── service_types.php
│   │   ├── spare_parts.php
│   │   ├── time_slots.php
│   │   ├── payments.php
│   │   ├── reports.php
│   │   └── audit_logs.php
│   ├── customer/
│   │   ├── dashboard.php
│   │   ├── motors.php
│   │   ├── booking_new.php
│   │   ├── booking_history.php
│   │   ├── booking_detail.php
│   │   ├── invoice.php
│   │   └── profile.php
│   └── mekanik/
│       ├── dashboard.php
│       ├── my_tasks.php
│       └── history.php
├── uploads/
│   └── motors/
├── database/
│   └── revvo.sql
├── docs/
│   ├── brd/
│   ├── design/
│   ├── diagrams/
│   └── plan/
├── .gitignore
├── composer.json
└── index.php
```

---

## 🗃️ Struktur Database

Sistem menggunakan **11 tabel utama** dengan relasi foreign key:

1. **`users`** — menyimpan akun semua role: admin, customer, mekanik  
2. **`customers`** — menyimpan data tambahan customer  
3. **`mechanics`** — menyimpan data tambahan mekanik  
4. **`motors`** — menyimpan data motor milik customer  
5. **`service_types`** — menyimpan jenis layanan servis  
6. **`spare_parts`** — menyimpan data sparepart dan stok  
7. **`time_slots`** — menyimpan slot waktu operasional bengkel  
8. **`bookings`** — tabel utama transaksi booking servis  
9. **`booking_parts`** — menyimpan sparepart yang digunakan per booking  
10. **`payments`** — menyimpan data pembayaran booking  
11. **`service_logs`** — menyimpan riwayat perubahan status booking  

### Relasi Penting
- Satu user dapat menjadi customer atau mekanik
- Satu customer dapat memiliki banyak motor
- Satu customer dapat memiliki banyak booking
- Satu booking terkait dengan satu motor, satu layanan, satu slot waktu, dan satu mekanik
- Satu booking dapat memiliki banyak sparepart melalui `booking_parts`
- Satu booking memiliki satu pembayaran
- Satu booking memiliki banyak service log

---

## 🌿 Branch Workflow

### Branch yang digunakan
- `main` → branch utama/final
- `develop` → branch penggabungan progress
- `feat-setup-foundation` → setup awal project
- `feat-database` → pengerjaan database dan koneksi
- `feat-auth-flow` → pengerjaan autentikasi dan otorisasi

Gunakan format branch feature flat seperti `feat-admin-users-crud`, bukan `feat/...`, agar konsisten dengan repo ini.

Branch lain dapat ditambahkan sesuai kebutuhan fitur.

### Alur Branch
1. Branch fitur dibuat dari `develop`
2. Pengerjaan dilakukan di branch fitur masing-masing
3. Jika branch fitur sudah lama tertinggal atau `develop` berubah signifikan, update branch fitur dari `develop` sebelum lanjut kerja
4. Sebelum merge, sinkronkan branch fitur dengan `develop` agar conflict dan bug integrasi lebih kecil
5. Setelah selesai, branch fitur di-merge ke `develop`
6. Jika sudah stabil, `develop` di-merge ke `main`

### Aturan Update Branch Feature
- Branch feature **tidak harus** selalu up to date setiap saat, tetapi harus cukup dekat dengan `develop`
- Saat membuat branch baru, selalu mulai dari `develop` terbaru
- Jika branch didiamkan beberapa hari atau ada perubahan foundation penting di `develop`, lakukan update branch dulu
- Jika modul menyentuh area yang banyak dependensi, lebih baik lebih sering sync dengan `develop`

---

## 🤝 Cara Kerja Tim Singkat

- Setiap anggota bekerja pada modul yang telah disepakati
- Pengerjaan dilakukan melalui branch terpisah
- Tidak melakukan push langsung ke `main`
- Perubahan digabungkan melalui branch `develop`
- Setiap progress dicatat melalui commit yang deskriptif
- Struktur kode, naming, dan pembagian file dijaga tetap konsisten
- Setiap anggota wajib memahami bagian yang dikerjakan untuk presentasi

---

## 📌 Status Pembagian Modul

| Anggota | Modul Utama | Status |
|---------|-------------|--------|
| Geral Tritama Wahyuady | Foundation + CRUD Users | Planned |
| Nugraha Adani | CRUD Motors + Customer Pages | Planned |
| Muhammad Rizky Dermawan | CRUD Spare Parts + Dashboard + Reports | Planned |
| Raika Maulana Dwi Putra | CRUD Service Types + Mechanics + Time Slots | Planned |
| Ahmad Hidayat | CRUD Bookings + Mekanik Flow | Planned |

---

## 🗺️ Roadmap Pengerjaan

### Phase 0 — Persiapan
- Setup tools development
- Membuat repository
- Menyusun struktur project
- Menyepakati aturan tim

### Phase 1 — Database Design & Setup
- Finalisasi ERD
- Membuat file SQL
- Menambahkan dummy data
- Test import database
- Membuat file koneksi database

### Phase 2 — Authentication & Layout
- Membuat login
- Membuat register customer
- Membuat logout
- Membuat proteksi halaman per role
- Menyusun layout dasar aplikasi

### Phase 3 — CRUD Master Data
- CRUD users
- CRUD mechanics
- CRUD service types
- CRUD spare parts
- CRUD motors
- Pengaturan time slots
- View customers

### Phase 4 — Core Booking Flow
- Form booking customer
- Validasi double-booking
- Assign mekanik
- Update status booking
- Input sparepart pada booking
- Tracking status booking
- Pembayaran dan verifikasi

### Phase 5 — Bonus Features
- Search dan filter
- Pagination
- Upload gambar
- Export PDF
- Export Excel

### Phase 6 — Dashboard & Audit Log
- Dashboard admin
- Dashboard customer
- Dashboard mekanik
- Halaman audit log

### Phase 7 — Testing & Finalisasi
- Testing semua role
- Testing validasi dan edge case
- Fix bug
- Rapikan tampilan
- Rapikan kode

### Phase 8 — Deliverables
- Export SQL final
- Menyusun laporan PDF
- Menyiapkan screenshot aplikasi
- Menyiapkan presentasi dan demo

---

## 📎 Catatan

Project ini dikembangkan untuk memenuhi kebutuhan tugas akhir mata kuliah **Pemrograman Web Dasar (PHP & MySQL)** dengan pendekatan **PHP Native**, **MySQL**, dan pengelolaan project berbasis **GitHub Organization**.
