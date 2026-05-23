# 📋 Foundation: Sistem Booking & Tracking Servis Bengkel Motor (REVVO)

> **Mata Kuliah**: Pemrograman Web Dasar (PHP & MySQL)
> **Domain**: Sistem Penjadwalan/Reservasi Sederhana
> **Kelompok**: Geral, Nugi, Dermawan, Raika, Ahmad

---

## A. Spesifikasi Final

| Aspek | Detail |
|-------|--------|
| **Total Tabel** | 11 tabel dengan relasi (foreign key) |
| **Role** | 3 (Customer, Admin, Mekanik) |
| **Stack** | PHP Native + MySQL + Tailwind CSS |
| **Bonus Rubrik** | Semua 5 fitur bonus (+40 poin) |

### Fitur Wajib

- Multi-role pengguna (Customer, Admin Bengkel, Mekanik)
- **CRUD lengkap pada 7 entitas utama**: Users, Service Types, Mechanics, Time Slots, Motors, Bookings, Spare Parts
- State machine status servis: **Queued → In Progress → Completed → Ready for Pickup**
- Validasi double-booking time slot
- Autentikasi dengan `password_hash()` + session PHP
- Prepared Statement untuk mencegah SQL Injection
- Audit log perubahan status booking
- Dashboard ringkasan harian

### Fitur Bonus (+40 poin)

- ✅ **Pencarian & filter** (booking by tanggal/status/mekanik)
- ✅ **Paginasi** (list booking, customer, motor, sparepart)
- ✅ **Export PDF & Excel** (laporan bulanan + invoice individual)
- ✅ **Upload gambar** (dokumentasi kondisi motor)
- ✅ **Multi-role** (3 role dengan hak akses berbeda)

---

## B. Daftar Tabel (11 Tabel)

| # | Tabel | Fungsi |
|---|-------|--------|
| 1 | `users` | Akun semua role — punya field `role` (admin/mekanik/customer) |
| 2 | `customers` | Data tambahan customer (alamat, no HP) |
| 3 | `mechanics` | Data tambahan mekanik (spesialisasi, status aktif) |
| 4 | `motors` | Motor milik customer (merk, plat, tahun, foto) |
| 5 | `service_types` | Master jenis layanan (nama, durasi, harga jasa) |
| 6 | `spare_parts` | Master sparepart (nama, stok, harga, satuan) |
| 7 | `time_slots` | Jam operasional bengkel (slot waktu booking) |
| 8 | `bookings` | Transaksi utama (motor, layanan, mekanik, status, harga snapshot) |
| 9 | `booking_parts` | Junction: sparepart yang dipakai per booking + harga snapshot |
| 10 | `payments` | Catatan pembayaran (metode, jumlah, status lunas) |
| 11 | `service_logs` | Audit trail perubahan status booking |

---

## C. Flow Sistem

### 1. Booking Lifecycle (Flow Utama)

```
[Customer] Booking → Queued
    ↓
[Admin] Assign mekanik
    ↓
[Mekanik] Mulai kerja → In Progress
    ↓
[Mekanik] Tambah sparepart (auto-kurang stok)
    ↓
[Mekanik] Selesai kerja → Completed (invoice auto-generate)
    ↓
[Admin] Verifikasi + konfirmasi bayar → Ready for Pickup
    ↓
[Customer] Datang ambil + download invoice
```

### 2. Flow Per Role

**Customer:**

1. Register → Login
2. CRUD motor pribadi (merk, plat, tahun, foto)
3. Buat booking baru (pilih motor, layanan, tanggal, slot)
4. Tracking status booking real-time
5. Lihat histori booking
6. Download invoice PDF per transaksi yang selesai

**Admin Bengkel:**

1. Login (admin di-seed di database, no self-register)
2. Dashboard (total booking, revenue, status distribution)
3. CRUD master data:
   - Jenis layanan
   - Mekanik
   - Sparepart (+ monitor stok)
   - Time slots
4. Assign mekanik ke booking
5. Verifikasi pengerjaan selesai → ubah status ke "Ready for Pickup"
6. Konfirmasi pembayaran
7. Lihat audit log
8. Generate laporan bulanan (PDF/Excel)

**Mekanik:**

1. Login
2. Lihat daftar tugas yang di-assign
3. Update status pengerjaan (Queued → In Progress → Completed)
4. Input sparepart yang dipakai (otomatis kurangi stok)
5. Tulis catatan pengerjaan
6. Lihat histori pengerjaan personal

### 3. Flow Otomatis Sistem

| Trigger | Aksi |
|---------|------|
| Register / ubah password | Hash password (`password_hash()`) |
| Submit booking | Validasi double-booking + snapshot harga jasa + log "created" |
| Mekanik tambah part | Kurangi `spare_parts.stock` + snapshot ke `booking_parts` + recalc total |
| Status berubah | Insert `service_logs` (from, to, by, at) |
| Status → "Completed" | Auto-generate invoice PDF |
| Akses halaman | Cek session + role authorization |

### 4. Flow Harga (Layered Snapshot)

```
service_types.base_price (master)
        ↓ saat booking dibuat
bookings.service_price (snapshot)
        +
spare_parts.price (master)
        ↓ saat mekanik input part
booking_parts.price_at_time × qty (snapshot)
        ↓ accumulated
bookings.total_price (calculated)
        ↓ saat bayar
payments.amount (actual)
```

**Kenapa snapshot?**
Harga master bisa berubah di kemudian hari, tapi booking lama harus tetap pakai harga saat booking dibuat. Snapshot menjaga akurasi histori transaksi.

### 5. Flow Laporan Bulanan

```
Admin pilih periode + filter (mekanik, layanan, status)
    ↓
System query → preview di web (ringkasan + tabel detail)
    ↓
Admin pilih export → PDF (DomPDF) atau Excel (PhpSpreadsheet)
    ↓
Browser auto-download file
```

**Isi laporan:**

- **Header**: Logo bengkel, periode, tanggal cetak
- **Ringkasan**: Total booking, revenue, top mekanik, top layanan, customer baru
- **Detail Transaksi**: Tabel (tanggal, customer, motor, layanan, mekanik, parts, total, status)
- **Footer**: Generated timestamp + space tanda tangan

### 6. Flow Keamanan

- **Authentication**: Login → cek `password_hash` → set session (`user_id`, `role`)
- **Authorization**: Setiap halaman cek role di awal → redirect kalau salah role
- **SQL Injection prevention**: Prepared Statement (mysqli/PDO) di semua query
- **File Upload**: Validasi ekstensi (jpg/png), max size, rename dengan hash
- **Protect sensitive files**: `.htaccess` block akses langsung ke `/includes/`

---

## D. Distribusi Bonus Rubrik

| Bonus | Implementasi | Poin |
|-------|--------------|------|
| Pencarian/Filter | Filter booking by tanggal, status, mekanik | +5 |
| Paginasi | List booking, customer, motor, sparepart | +5 |
| Export PDF/Excel | Laporan bulanan + invoice transaksi | +10 |
| Upload Gambar | Foto kondisi motor saat booking | +10 |
| Multi-Role | Customer / Admin / Mekanik | +10 |
| | **TOTAL BONUS** | **+40** |

---

## E. Glossary Status Booking

| Status | Arti | Yang Bisa Ubah |
|--------|------|----------------|
| **Queued** | Booking diterima, menunggu assign mekanik | System (saat customer book) |
| **In Progress** | Mekanik sedang mengerjakan | Mekanik (yang di-assign) |
| **Completed** | Pengerjaan selesai, menunggu verifikasi admin | Mekanik (yang di-assign) |
| **Ready for Pickup** | Verifikasi + pembayaran OK, customer bisa ambil | Admin |
| **Cancelled** | Booking dibatalkan (opsional) | Admin / Customer |

---

## F. Catatan CRUD Coverage

### 7 Entitas Utama dengan Full CRUD

| # | Entitas | Owner | Halaman |
|---|---------|-------|---------|
| 1 | **Users** | Geral | `pages/admin/users.php` |
| 2 | **Service Types** | Raika | `pages/admin/service_types.php` |
| 3 | **Mechanics** | Raika | `pages/admin/mechanics.php` |
| 4 | **Time Slots** | Raika | `pages/admin/time_slots.php` |
| 5 | **Motors** | Nugi | `pages/customer/motors.php` |
| 6 | **Bookings** | Ahmad | `pages/admin/bookings.php` |
| 7 | **Spare Parts** | Dermawan | `pages/admin/spare_parts.php` |

### Tabel Lain (Bukan CRUD Eksplisit, Tapi Tetap Berfungsi)

| Tabel | Operasi yang Tersedia | Alasan |
|-------|----------------------|--------|
| `customers` | Read-only (admin view list) | Customer self-manage via profil. Admin tidak perlu hapus data customer |
| `payments` | Auto-create via booking flow + Update | Tidak boleh dihapus (audit trail keuangan) |
| `booking_parts` | CRUD via parent booking | Junction table, operasi mengikuti flow booking |
| `service_logs` | Auto-create + Read (immutable) | Audit trail by design — kalau bisa di-edit/delete bukan audit log |

### Argumentasi untuk Dosen

> Sistem memiliki **7 entitas utama dengan operasi CRUD penuh**, jauh melebihi requirement minimal 2 entitas dalam rubrik. Setiap anggota kelompok memegang minimal 1 modul CRUD sebagai kontribusi utamanya. Tabel pendukung seperti `service_logs` (audit trail) dan `booking_parts` (junction table) dirancang sesuai prinsip database design — operasinya terintegrasi dengan flow operasional yang relevan, bukan dengan UI CRUD terpisah.

---

**Next**: Lihat `plan.md` untuk roadmap pengerjaan & timeline.
