# 📅 Plan: Sistem Booking & Tracking Servis Bengkel Motor (REVVO)

> Roadmap pengerjaan UAS Pemrograman Web Dasar
> **Kelompok**: Geral, Nugi, Dermawan, Raika, Ahmad
> **Referensi spec**: lihat `foundation.md`

---

## A. Roadmap Pengerjaan

> Timeline asumsi ~4 minggu. Sesuaikan dengan deadline aktual dari dosen.

### Phase 0: Persiapan & Persetujuan (Hari 1-2)

- [ ] Submit proposal usulan domain ke dosen
- [ ] Tunggu persetujuan dosen
- [ ] Setup tools: Laragon/XAMPP, VS Code, MySQL Workbench
- [ ] Buat repository GitHub (private dulu, ubah jadi public di akhir)
- [ ] Setup struktur folder project awal (lihat section D: current vs target)
- [ ] Sepakati aturan tim (lihat section G)

### Phase 1: Design & Setup Database (Hari 3-5)

- [ ] Finalisasi ERD (11 tabel) — tools: dbdiagram.io / draw.io
- [ ] Buat file `.sql` lengkap dengan relasi & constraint
- [ ] Insert data dummy minimal 10 baris per tabel
- [ ] Test import-export `.sql` dari scratch (wajib bisa di-import ulang tanpa error)
- [ ] Buat file `config/koneksi.php`
- [ ] Test koneksi di file dummy

### Phase 2: Authentication & Layout (Hari 6-8)

- [ ] Halaman login + logic (cek role, set session)
- [ ] Halaman register (untuk customer saja)
- [ ] Halaman logout
- [ ] Middleware proteksi halaman per role (`includes/auth.php`)
- [ ] Layout dasar (header, navbar, footer) dengan Tailwind CSS
- [ ] Halaman 403 forbidden
- [ ] Test login-logout untuk 3 role

### Phase 3: CRUD Master Data (Hari 9-12)

> **6 entitas utama dengan full CRUD** — target utama untuk rubrik "Kelengkapan Fitur CRUD" (25%).

- [ ] **CRUD users** (admin manage akun admin & mekanik)
- [ ] **CRUD service_types** (admin manage master layanan)
- [ ] **CRUD mechanics** (admin manage data mekanik)
- [ ] **CRUD spare_parts** (admin manage master sparepart + stok)
- [ ] **CRUD motors** (customer-side, dengan upload foto)
- [ ] **CRUD time_slots** (admin manage slot operasional bengkel)
- [ ] List page untuk `customers` (admin view, read-only)

### Phase 4: Core Booking Flow (Hari 13-17)

> **Entitas utama ke-6 (Bookings)** dikerjakan di phase ini karena flow-nya kompleks dan menyentuh multi-role.

- [ ] Form booking customer (motor, layanan, tanggal, slot, foto, keluhan)
- [ ] Validasi double-booking
- [ ] Snapshot harga saat booking dibuat
- [ ] List booking di sisi admin (semua booking)
- [ ] Fitur assign mekanik ke booking
- [ ] List booking di sisi mekanik (yang di-assign ke dirinya)
- [ ] Update status booking (state machine, validate transisi)
- [ ] Insert ke `service_logs` setiap perubahan status
- [ ] Form input sparepart oleh mekanik (auto-kurang stok + snapshot harga)
- [ ] Verifikasi & konfirmasi pembayaran (admin)
- [ ] Tracking status real-time di customer dashboard

### Phase 5: Bonus Features (Hari 18-21)

- [ ] Search & filter di list booking (by tanggal, status, mekanik)
- [ ] Paginasi di semua list (booking, customer, motor, sparepart)
- [ ] Upload gambar motor (validation extension + rename + size limit)
- [ ] Install DomPDF via Composer
- [ ] Generate invoice PDF per transaksi
- [ ] Generate laporan bulanan PDF
- [ ] Install PhpSpreadsheet via Composer
- [ ] Generate laporan bulanan Excel

### Phase 6: Dashboard & Audit Log (Hari 22-23)

- [ ] Dashboard admin (ringkasan harian, alert stok hampir habis)
- [ ] Dashboard customer (booking aktif, histori)
- [ ] Dashboard mekanik (tugas hari ini, histori personal)
- [ ] Halaman audit log (read-only, dengan filter)

### Phase 7: Testing & Polish (Hari 24-26)

- [ ] Test semua flow end-to-end per role
- [ ] Test edge cases (empty state, validasi gagal, slot bentrok, stok habis)
- [ ] Test responsive di 1024px
- [ ] Fix bug-bug yang muncul
- [ ] Cleanup code, hapus `var_dump`/`echo` debug
- [ ] Final styling polish & konsistensi
- [ ] Test SQL injection (coba input nakal)

### Phase 8: Deliverables (Hari 27-28)

- [ ] Export `.sql` final + test import dari scratch
- [ ] Tulis laporan PDF (5-10 halaman):
  - Cover & identitas kelompok
  - Deskripsi kasus & latar belakang
  - ERD & struktur database
  - Pembagian tugas anggota (dengan kontribusi tiap orang)
  - Screenshot tampilan aplikasi (semua halaman utama)
  - Kesimpulan
- [ ] Zip source code atau push ke GitHub (final commit)
- [ ] Latihan presentasi: semua anggota wajib paham kodenya
- [ ] Siapkan demo skenario (login per role, booking lengkap, generate laporan)

### Phase 9: Deployment ke Production (Hari 29-30)

> Hosting + domain biar bisa diakses online dan ditampilkan saat presentasi.

**A. Persiapan Production**

- [ ] Disable `display_errors` di `.htaccess` (production mode)
- [ ] Buat file `koneksi.production.php` dengan kredensial hosting (jangan commit ke Git)
- [ ] Update `BASE_URL` di koneksi.php sesuai domain production
- [ ] Generate ulang password admin (jangan pakai password dummy)
- [ ] Test seluruh flow di localhost sekali lagi (regression)
- [ ] Backup database final ke folder `database/`

**B. Pilih & Beli Hosting + Domain**

Rekomendasi untuk student project:

| Tier | Provider | Catatan |
|------|----------|---------|
| **Free** | InfinityFree, 000webhost | PHP + MySQL gratis, tapi iklan & limit |
| **Murah Indo** | Niagahoster, IDCloudHost, RumahWeb | ~Rp 10-30K/bulan paket pelajar |
| **Cloud** | Railway, Render | Free tier ada, modern UI |

Domain:

| Tier | Pilihan |
|------|---------|
| **Free** | `.my.id` dari PANDI (khusus pelajar Indonesia, butuh KTP/KTM) |
| **Murah** | `.com` (~Rp 150K/tahun), `.id` (~Rp 250K/tahun) |

- [ ] Beli/daftar hosting (rekomendasi: Niagahoster paket pelajar atau InfinityFree)
- [ ] Beli/daftar domain
- [ ] Tunggu propagasi DNS (1-24 jam)

**C. Upload ke Hosting**

- [ ] Login ke cPanel hosting
- [ ] Buat database MySQL baru di cPanel → catat nama DB, user, password
- [ ] Import `database/bengkel.sql` via phpMyAdmin di cPanel
- [ ] Update `config/koneksi.php` dengan kredensial database hosting
- [ ] Upload file project via **FTP** (FileZilla) atau **File Manager** cPanel
   - Folder tujuan: `public_html/` (atau subfolder kalau mau)
- [ ] Set permission folder `uploads/` ke `755` agar bisa di-write

**D. Konfigurasi SSL & Domain**

- [ ] Aktifkan SSL (Let's Encrypt gratis di cPanel)
- [ ] Setup force HTTPS di `.htaccess`
- [ ] Test akses via `https://[domain]`

**E. Testing Production**

- [ ] Test login semua role
- [ ] Test booking end-to-end
- [ ] Test upload foto
- [ ] Test generate PDF & Excel
- [ ] Test responsive di mobile
- [ ] Cek loading speed

**F. Dokumentasi Akhir**

- [ ] Tulis URL production di README.md
- [ ] Tulis kredensial admin (untuk dosen test) di laporan
- [ ] Screenshot tampilan live untuk laporan
- [ ] Backup final source code + .sql ke Drive/email

---

## B. Pembagian Modul

> **Prinsip**: Setiap anggota memegang minimal **1 modul CRUD penuh** yang bisa dipresentasikan sebagai kontribusi utama. Total ada **7 modul CRUD** yang dibagi rata.

### 📊 Ringkasan Pembagian CRUD

| Anggota | CRUD Module | Halaman Pendukung |
|---------|------------|-------------------|
| **Geral** | 🟧 **CRUD USERS** | Foundation, Auth, Booking Form Customer |
| **Raika** | 🟧 **CRUD SERVICE_TYPES**<br>🟧 **CRUD MECHANICS**<br>🟧 **CRUD TIME_SLOTS** | List customers (read-only) |
| **Nugi** | 🟧 **CRUD MOTORS** | Customer dashboard, tracking, invoice, profile |
| **Ahmad** | 🟧 **CRUD BOOKINGS** (admin-side) | Payment confirmation, mekanik flow |
| **Dermawan** | 🟧 **CRUD SPARE_PARTS** | Dashboard admin, reports, audit log, bonus features |

**Total: 7 modul CRUD penuh** — jauh melebihi requirement minimal "2 entitas" dalam rubrik.

---

### 1. Geral — Foundation + 🟧 CRUD USERS

> **Modul CRUD Utama**: `users` (admin kelola akun staff)
> **Plus**: Setup fondasi project + form booking customer
> Semua anggota bergantung pada modul Geral di awal, jadi dikerjakan duluan.

#### 🟧 CRUD Module: USERS

| Operasi | File | Deskripsi |
|---------|------|-----------|
| Create | `pages/admin/users.php` (form add) | Admin tambah akun admin/mekanik baru |
| Read | `pages/admin/users.php` (list) | List semua akun staff dengan filter role |
| Update | `pages/admin/users.php` (form edit) | Edit data + reset password |
| Delete | `pages/admin/users.php` (handler) | Hapus akun dengan konfirmasi |

#### Pendukung (Foundation)

| Checklist | Halaman/File |
|-----------|-------------|
| Setup project + `koneksi.php` + `.htaccess` | `config/koneksi.php` |
| Layout komponen Tailwind | `includes/header.php`, `footer.php`, `navbar.php` |
| Login, register, logout | `pages/auth/*.php` |
| Middleware role protection | `includes/auth.php` |
| Helper functions: state machine, service_logs, validasi | `includes/functions.php` |
| Halaman 403 forbidden | `pages/403.php` |
| Form booking customer (multi-step) | `pages/customer/booking_new.php` |
| Validasi double-booking + snapshot harga | Logic di `booking_new.php` |

**Estimasi effort**: Phase 0-2 (hari 1-8) + Phase 4 sebagian (hari 13-14)
**Yang harus bisa dijelaskan saat presentasi**: Auth flow, session, role-based access, CRUD users end-to-end, validasi double-booking, snapshot harga

---

### 2. Raika — 🟧 CRUD SERVICE_TYPES + 🟧 CRUD MECHANICS + 🟧 CRUD TIME_SLOTS

> **3 Modul CRUD**: Master data konfigurasi bengkel (layanan, mekanik, jadwal operasional)
> Bisa mulai setelah Geral selesai layout + auth (hari ke-6).

#### 🟧 CRUD Module 1: SERVICE_TYPES

| Operasi | Detail |
|---------|--------|
| Create | Tambah jenis layanan (nama, deskripsi, durasi, harga) |
| Read | List semua layanan dengan filter status aktif/nonaktif |
| Update | Edit layanan + toggle status aktif |
| Delete | Hapus dengan konfirmasi (cek tidak dipakai di booking) |

**File**: `pages/admin/service_types.php`

#### 🟧 CRUD Module 2: MECHANICS

| Operasi | Detail |
|---------|--------|
| Create | Tambah mekanik baru (link ke `users` tabel) |
| Read | List mekanik + spesialisasi + status aktif |
| Update | Edit data mekanik + ubah status |
| Delete | Soft delete (set status `nonaktif`) untuk preserve history |

**File**: `pages/admin/mechanics.php`

#### 🟧 CRUD Module 3: TIME_SLOTS

| Operasi | Detail |
|---------|--------|
| Create | Tambah slot baru (jam, hari, kapasitas) |
| Read | List slot dengan filter per hari |
| Update | Edit kapasitas atau status slot |
| Delete | Hapus slot yang tidak terpakai |

**File**: `pages/admin/time_slots.php`

#### Pendukung

| Checklist | Halaman/File |
|-----------|-------------|
| List customers (read-only, admin view) | `pages/admin/customers.php` |
| Validasi server-side + Prepared Statement di semua form | Setiap file CRUD |
| Delete dengan konfirmasi modal | Setiap file CRUD |

**Estimasi effort**: Phase 3 (hari 9-12)
**Yang harus bisa dijelaskan saat presentasi**: CRUD flow 3 entitas, Prepared Statement, foreign key constraint, soft vs hard delete

---

### 3. Nugi — 🟧 CRUD MOTORS + Customer Pages

> **Modul CRUD Utama**: `motors` (customer kelola motor pribadi + upload foto)
> **Plus**: Semua halaman lain yang dilihat customer
> Bisa mulai setelah Geral selesai auth + layout (hari ke-6).

#### 🟧 CRUD Module: MOTORS

| Operasi | Detail |
|---------|--------|
| Create | Customer tambah motor baru + **upload foto** |
| Read | List motor pribadi customer |
| Update | Edit data motor + ganti foto |
| Delete | Hapus motor (cek tidak ada booking aktif) |

**File**: `pages/customer/motors.php`
**Fitur khusus**: Upload foto dengan validasi ext/size, rename hash, simpan ke `uploads/motors/`

#### Pendukung (Customer Pages)

| Checklist | Halaman/File |
|-----------|-------------|
| Customer dashboard (booking aktif + statistik) | `pages/customer/dashboard.php` |
| Tracking status booking real-time | `pages/customer/booking_history.php` |
| Histori booking + filter status | `pages/customer/booking_history.php` |
| Detail booking (lihat parts, total) | `pages/customer/booking_detail.php` |
| Generate invoice PDF per transaksi (DomPDF) | `pages/customer/invoice.php` |
| Edit profil customer (alamat, no HP) | `pages/customer/profile.php` |

**Estimasi effort**: Phase 3 sebagian (hari 10-12) + Phase 4 (hari 15-17)
**Yang harus bisa dijelaskan saat presentasi**: CRUD motors + upload file handling, query tracking status, DomPDF invoice generation, customer flow end-to-end

---

### 4. Ahmad — 🟧 CRUD BOOKINGS (Admin) + Mekanik Flow

> **Modul CRUD Utama**: `bookings` dari sisi admin (kelola booking + state machine)
> **Plus**: Semua halaman mekanik + payment confirmation
> Bisa mulai setelah Geral selesai booking creation + helper (hari ke-15).

#### 🟧 CRUD Module: BOOKINGS (Admin-side)

| Operasi | Detail |
|---------|--------|
| Create | Booking manual oleh admin (untuk walk-in customer) |
| Read | List semua booking + filter status/tanggal/mekanik |
| Update | Assign mekanik, ubah status (state machine), edit detail |
| Delete | Soft delete via status `dibatalkan` + hard delete admin |

**File**: `pages/admin/bookings.php`
**Fitur khusus**: State machine validation, auto-insert `service_logs`, integrasi dengan `mechanics` & `payments`

#### Pendukung (Operations)

| Checklist | Halaman/File |
|-----------|-------------|
| Admin: form pembayaran + konfirmasi | `pages/admin/payments.php` |
| Admin: verifikasi selesai → "Siap Diambil" | Logic di `bookings.php` |
| Mekanik: dashboard (tugas hari ini) | `pages/mekanik/dashboard.php` |
| Mekanik: list tugas yang di-assign | `pages/mekanik/my_tasks.php` |
| Mekanik: update status (pakai helper Geral) | `pages/mekanik/my_tasks.php` |
| Mekanik: input sparepart (booking_parts logic) | `pages/mekanik/my_tasks.php` |
| Mekanik: histori pengerjaan personal | `pages/mekanik/history.php` |

**Estimasi effort**: Phase 4 (hari 15-17) + Phase 5 sebagian (hari 18)
**Yang harus bisa dijelaskan saat presentasi**: CRUD bookings + state machine end-to-end, booking_parts logic + auto-kurang stok, payment confirmation flow

---

### 5. Dermawan — 🟧 CRUD SPARE_PARTS + Dashboard + Reports

> **Modul CRUD Utama**: `spare_parts` (kelola stok + alert minimum)
> **Plus**: Dashboard analytics + laporan + bonus features
> Sparepart cocok jadi domain Dermawan karena tightly coupled dengan analytics (alert stok di dashboard, sparepart consumption di laporan).

#### 🟧 CRUD Module: SPARE_PARTS

| Operasi | Detail |
|---------|--------|
| Create | Tambah sparepart baru (nama, satuan, harga, stok awal, stok minimum) |
| Read | List sparepart + filter stok rendah + sort by konsumsi |
| Update | Edit data + adjust stok manual (untuk stok opname) |
| Delete | Hapus sparepart (cek tidak dipakai di booking) |

**File**: `pages/admin/spare_parts.php`
**Fitur khusus**: Auto-alert stok di bawah `stok_minimum`, tracking history konsumsi via `booking_parts`

#### Pendukung (Dashboard + Reports + Bonus)

| Checklist | Halaman/File |
|-----------|-------------|
| Dashboard admin (total booking, revenue, alert stok) | `pages/admin/dashboard.php` |
| Audit log page (read-only, dengan filter) | `pages/admin/audit_logs.php` |
| Laporan bulanan: preview + tabel detail | `pages/admin/reports.php` |
| Laporan bulanan: export PDF (DomPDF) | `pages/admin/reports.php` |
| Laporan bulanan: export Excel (PhpSpreadsheet) | `pages/admin/reports.php` |
| Komponen paginasi reusable | `includes/pagination.php` |
| Pasang paginasi di semua list | Kolaborasi dengan Raika, Nugi, Ahmad |
| Search & filter di list booking | Kolaborasi dengan Ahmad |
| Styling polish & responsive check | Semua halaman |

**Estimasi effort**: Phase 3 sebagian (hari 11-12) + Phase 5-6 (hari 19-23)
**Yang harus bisa dijelaskan saat presentasi**: CRUD spare_parts + stok logic, dashboard query aggregation (COUNT, SUM, GROUP BY), DomPDF & PhpSpreadsheet usage, paginasi component

---

### Dependency Flow

```
Geral (Foundation + Auth + CRUD Users)
  ↓ layout & auth ready (hari ke-6)
  │
  ├── Raika (3 CRUD: services, mechanics, time_slots) → mulai hari 6
  ├── Nugi (CRUD Motors + customer pages)             → mulai hari 6
  ├── Dermawan (CRUD Spare Parts)                     → mulai hari 11
  │
  ↓ master data ready (hari ke-12)
  ↓ booking creation ready (hari ke-15)
  │
  ├── Ahmad (CRUD Bookings + mekanik flow)            → mulai hari 15
  ├── Nugi (tracking + invoice)                       → lanjut hari 15
  │
  ↓ semua flow operasional ready (hari ke-19)
  └── Dermawan (Dashboard + Reports + Bonus)          → lanjut hari 19
```

### Pair Programming (Wajib untuk Bagian Sulit)

| Pair | Topik | Alasan |
|------|-------|--------|
| **Geral + Ahmad** | Helper state machine + service_logs | Geral bikin helper, Ahmad konsumsi — harus sinkron interface |
| **Ahmad + Nugi** | Sinkronisasi status booking | Ahmad ubah status, Nugi tampilkan ke customer — kontrak data harus jelas |
| **Raika + Dermawan** | Paginasi & search | Dermawan bikin komponen, Raika pasang di CRUD-nya |
| **Nugi + Dermawan** | DomPDF | Nugi invoice, Dermawan laporan — share library knowledge |

### Shared Responsibilities (Semua Anggota)

| Task | Siapa |
|------|-------|
| Code review mingguan | Semua |
| Test flow end-to-end per role | Semua |
| Data dummy (10 baris per tabel) | Raika (master data) + Ahmad (booking + parts) |
| Laporan tertulis PDF | Geral (compile) + semua (screenshot masing-masing) |
| Latihan presentasi | Semua — setiap orang wajib paham kode bagiannya |

**Catatan**: Setiap anggota harus punya kontribusi yang bisa diidentifikasi & mampu menjelaskan kodenya saat presentasi (syarat spec dosen).

---

## C. Tech Stack

### Wajib

- **Backend**: PHP 8.x Native (no framework — sesuai aturan)
- **Database**: MySQL 8.x / MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (vanilla)
- **CSS Framework**: Tailwind CSS (via CDN Play)
- **Server**: Laragon (recommended) / XAMPP / MAMP

### Library Pendukung (via Composer)

```bash
composer require dompdf/dompdf
composer require phpoffice/phpspreadsheet
```

- **DomPDF**: Generate invoice & laporan PDF
- **PhpSpreadsheet**: Generate laporan Excel

### Tools Development

- **IDE**: VS Code dengan extension PHP Intelephense
- **Database Client**: phpMyAdmin (built-in Laragon) atau MySQL Workbench
- **Version Control**: Git + GitHub
- **ERD Tool**: dbdiagram.io (online, gratis) atau draw.io
- **Mockup** (opsional): Figma

---

## D. Struktur Folder Project

### Current Repo Snapshot

Struktur yang **sudah ada saat dokumen ini ditulis** sudah cukup untuk landing page dan fondasi awal modul, tetapi belum lengkap untuk seluruh flow aplikasi:

```text
revvo-app/
├── assets/
│   ├── css/
│   │   └── custom.css
│   ├── js/
│   │   └── main.js
│   └── images/
│       └── logo.png
├── config/btw
│   └── koneksi.php
├── includes/
│   ├── auth.php
│   ├── footer.php
│   ├── header.php
│   └── navbar.php
├── pages/
│   ├── admin/
│   └── auth/
│       └── login.php
│   ├── customer/
│   └── mekanik/
├── uploads/
│   └── motors/
├── database/
│   └── revvo.sql
├── docs/
│   ├── brd/
│   ├── design/
│   ├── diagrams/
│   └── plan/
├── composer.json
└── index.php
```

### Target Structure Setelah Implementasi

Ini adalah struktur folder **target** yang akan dibangun bertahap selama pengerjaan fitur.

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
│   ├── auth.php          # Check session & role
│   └── functions.php     # Helper functions
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
├── .htaccess
├── .gitignore
├── composer.json
└── index.php             # Entry point / landing page
```

---

## E. Deliverables Checklist

Berdasarkan ketentuan dosen:

- [ ] **Source Code**: Folder project lengkap dalam format `.zip` atau link repository GitHub
- [ ] **File SQL Database**: Script `.sql` lengkap dengan data dummy minimal 10 baris per tabel, siap di-import ulang
- [ ] **Laporan Tertulis**: Dokumen PDF (5-10 halaman) berisi:
  - Deskripsi kasus
  - ERD / struktur database
  - Pembagian tugas anggota
  - Screenshot tampilan aplikasi
- [ ] **Presentasi & Demo**: Setiap anggota mampu menjelaskan bagian yang dikerjakan & menjawab pertanyaan terkait kode

---

## F. Risk & Mitigation

| Risk | Mitigation |
|------|------------|
| Library Composer ribet di environment lokal | Composer dependency sudah dideklarasikan dari awal; test install dan autoload sebelum masuk fitur PDF/Excel |
| Anggota nggak paham kode bagiannya | Wajib code review per minggu, pair programming kalau perlu |
| Bug double-booking pas demo | Test stress double-booking di Phase 7 dengan multiple browser session |
| File `.sql` error pas import | Test import dari scratch setiap akhir phase, bukan cuma di akhir |
| Presentasi kena pertanyaan random | Latihan Q&A — setiap anggota minimal paham seluruh kode di area dia |
| Anggota tiba-tiba sibuk/sakit | Pair coding (2 orang per modul), jangan satu modul satu orang |
| Konflik merge Git | Branch per fitur, daily sync, jangan kerja di branch yang sama |

---

## G. Aturan Tim

1. **Branch per modul** di Git dengan format `feat-nama-modul`, merge ke `develop` setelah review; `develop` di-merge ke `main` hanya saat integration sudah stabil
2. **Daily check-in singkat** (chat group, 10 menit, bahas progress & blocker)
3. **Tidak ada yang nyalin kode tanpa paham** — setiap anggota wajib bisa jelasin kodenya pas presentasi
4. **Commit message deskriptif** (contoh: `feat: add booking validation`, bukan `update` atau `fix bug`)
5. **Format kode konsisten** — sepakati naming convention (snake_case untuk DB, camelCase atau snake_case untuk PHP, pilih satu)
6. **Comment kode yang kompleks** — terutama bagian state machine & validasi
7. **Tidak commit `vendor/` & `uploads/`** — pakai `.gitignore`
8. **Backup database** secara berkala di repo (folder `database/`)
9. **Sync branch fitur dari `develop` bila perlu** — tidak wajib setiap saat, tetapi wajib dilakukan saat branch sudah tertinggal lama, ada perubahan foundation penting, atau sebelum merge kembali ke `develop`

---

## H. Library & Resource Reference

### Tailwind CSS (CDN Play — cocok untuk project tanpa build step)
```html
<script src="https://cdn.tailwindcss.com"></script>
```

> **Catatan**: Tailwind CDN Play cocok untuk project PHP native karena tidak perlu Node.js / build process. Untuk production, idealnya pakai Tailwind CLI, tapi untuk scope UAS ini CDN sudah cukup.

### Lucide Icons (CDN — lightweight, cocok dengan Tailwind)
```html
<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
```
Pemakaian: `<i data-lucide="home"></i>`

### DomPDF Docs
https://github.com/dompdf/dompdf

### PhpSpreadsheet Docs
https://phpspreadsheet.readthedocs.io

---

**Next Step**: Buat ERD lengkap (field detail + relasi + cardinality) → bisa dilihat di file `erd.md` setelah selesai.
