# 📊 ERD: Sistem Booking & Tracking Servis Bengkel Motor (REVVO)

> **Kelompok**: Geral, Nugi, Dermawan, Raika, Ahmad
> **Referensi**: Lihat `foundation.md` untuk spec & flow, `plan.md` untuk roadmap
> **Total Tabel**: 11 tabel dengan relasi foreign key | **7 entitas utama dengan CRUD penuh**

---

## A. Diagram Relasi

```
                    ┌──────────┐
                    │  USERS   │
                    └────┬─────┘
              ┌──────────┼──────────┐
              │          │          │
         1:1  ▼     1:1  ▼          │
    ┌──────────┐  ┌──────────┐      │
    │CUSTOMERS │  │MECHANICS │      │
    └────┬─────┘  └────┬─────┘      │
         │              │           │
    1:N  ▼              │           │
    ┌──────────┐        │           │
    │  MOTORS  │        │           │
    └────┬─────┘        │           │
         │              │           │
         ▼ 1:N          ▼ 1:N       ▼ 1:N
    ┌─────────────────────────────────────┐
    │              BOOKINGS               │◄── SERVICE_TYPES (1:N)
    │                                     │◄── TIME_SLOTS (1:N)
    └───┬──────────┬──────────────────────┘
        │          │                  │
   1:N  ▼     1:1  ▼             1:N  ▼
  ┌──────────┐ ┌──────────┐  ┌──────────────┐
  │ SERVICE  │ │ PAYMENTS │  │ BOOKING      │
  │ LOGS     │ │          │  │ PARTS        │
  └──────────┘ └──────────┘  └──────┬───────┘
                                    │
                              N:1   ▼
                             ┌──────────┐
                             │  SPARE   │
                             │  PARTS   │
                             └──────────┘
```

---

## B. Detail Tabel & Field

### 1. `users`

> Akun semua role. Satu tabel untuk admin, mekanik, dan customer.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `username` | VARCHAR(50) | UNIQUE, NOT NULL | |
| `email` | VARCHAR(100) | UNIQUE, NOT NULL | |
| `password_hash` | VARCHAR(255) | NOT NULL | Pakai `password_hash()` |
| `role` | ENUM('admin','mekanik','customer') | NOT NULL | Tentukan hak akses |
| `nama_lengkap` | VARCHAR(100) | NOT NULL | |
| `foto_profil` | VARCHAR(255) | NULL | Path ke file foto |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | |

---

### 2. `customers`

> Extension data untuk user dengan role customer.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `user_id` | INT | FK → users.id, UNIQUE | 1:1 ke users |
| `alamat` | TEXT | NULL | |
| `no_hp` | VARCHAR(20) | NOT NULL | |
| `no_ktp` | VARCHAR(20) | NULL | Opsional |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |

---

### 3. `mechanics`

> Extension data untuk user dengan role mekanik.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `user_id` | INT | FK → users.id, UNIQUE | 1:1 ke users |
| `spesialisasi` | VARCHAR(100) | NULL | Misal: "Mesin, Kelistrikan" |
| `status` | ENUM('aktif','nonaktif') | DEFAULT 'aktif' | |
| `catatan` | TEXT | NULL | Notes internal admin |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |

---

### 4. `motors`

> Motor milik customer. Satu customer bisa punya banyak motor.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `customer_id` | INT | FK → customers.id | |
| `merk` | VARCHAR(50) | NOT NULL | Honda, Yamaha, dll |
| `model` | VARCHAR(50) | NOT NULL | Vario 125, NMAX, dll |
| `plat_nomor` | VARCHAR(15) | NOT NULL | Contoh: B 1234 XYZ |
| `tahun` | YEAR | NULL | |
| `warna` | VARCHAR(30) | NULL | |
| `foto` | VARCHAR(255) | NULL | Path ke file foto |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | |

---

### 5. `service_types`

> Master jenis layanan bengkel.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `nama_layanan` | VARCHAR(100) | NOT NULL | "Ganti Oli", "Tune Up", dll |
| `deskripsi` | TEXT | NULL | Detail apa yang dikerjakan |
| `estimasi_menit` | INT | NOT NULL | Perkiraan durasi pengerjaan |
| `harga` | DECIMAL(12,2) | NOT NULL | Harga jasa (tanpa sparepart) |
| `status` | ENUM('aktif','nonaktif') | DEFAULT 'aktif' | Bisa nonaktifkan tanpa hapus |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |

---

### 6. `spare_parts`

> Master sparepart bengkel. Stok dikelola admin, dikurangi otomatis saat mekanik pakai.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `nama_part` | VARCHAR(100) | NOT NULL | "Oli Yamalube 1L", "Filter Oli", dll |
| `satuan` | VARCHAR(20) | NOT NULL | "pcs", "liter", "set" |
| `stok` | INT | NOT NULL, DEFAULT 0 | Jumlah saat ini |
| `harga_satuan` | DECIMAL(12,2) | NOT NULL | Harga per satuan |
| `stok_minimum` | INT | DEFAULT 5 | Alert kalau stok di bawah ini |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | |

---

### 7. `time_slots`

> Jam operasional bengkel. Admin manage via full CRUD (tambah/edit/hapus slot).

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `jam_mulai` | TIME | NOT NULL | Contoh: 08:00:00 |
| `jam_selesai` | TIME | NOT NULL | Contoh: 09:00:00 |
| `kapasitas` | INT | DEFAULT 1 | Jumlah booking per slot |
| `hari` | ENUM('senin','selasa','rabu','kamis','jumat','sabtu','minggu') | NOT NULL | |
| `status` | ENUM('aktif','nonaktif') | DEFAULT 'aktif' | |

---

### 8. `bookings`

> Tabel transaksi utama. Jantung sistem.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `customer_id` | INT | FK → customers.id | Yang booking |
| `motor_id` | INT | FK → motors.id | Motor yang diservis |
| `service_type_id` | INT | FK → service_types.id | Jenis layanan |
| `mechanic_id` | INT | FK → mechanics.id, **NULL** | Nullable — belum di-assign saat booking |
| `time_slot_id` | INT | FK → time_slots.id | Slot waktu yang dipilih |
| `tanggal_booking` | DATE | NOT NULL | Tanggal servis |
| `harga_jasa` | DECIMAL(12,2) | NOT NULL | **Snapshot** dari service_types.harga |
| `total_harga` | DECIMAL(12,2) | DEFAULT 0 | harga_jasa + SUM(booking_parts) |
| `status` | ENUM('antri','dikerjakan','selesai','siap_diambil','dibatalkan') | DEFAULT 'antri' | State machine |
| `keluhan` | TEXT | NULL | Customer tulis keluhan |
| `foto_kondisi` | VARCHAR(255) | NULL | Path ke foto motor |
| `catatan_mekanik` | TEXT | NULL | Catatan pengerjaan dari mekanik |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | |

**Validasi penting:**
- `mechanic_id` nullable → di-assign oleh admin setelah booking masuk
- `harga_jasa` = snapshot saat booking dibuat, bukan referensi langsung
- `total_harga` = `harga_jasa` + `SUM(booking_parts.subtotal)`, di-recalculate setiap mekanik tambah/hapus part
- Double-booking prevention: UNIQUE constraint atau query check pada kombinasi `time_slot_id` + `tanggal_booking` + slot belum penuh (cek `kapasitas`)

---

### 9. `booking_parts`

> Junction table: sparepart yang dipakai dalam satu booking.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `booking_id` | INT | FK → bookings.id | |
| `spare_part_id` | INT | FK → spare_parts.id | |
| `qty` | INT | NOT NULL | Jumlah yang dipakai |
| `harga_saat_itu` | DECIMAL(12,2) | NOT NULL | **Snapshot** dari spare_parts.harga_satuan |
| `subtotal` | DECIMAL(12,2) | NOT NULL | qty × harga_saat_itu |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |

**Trigger saat insert:**
- Kurangi `spare_parts.stok` sebanyak `qty`
- Recalculate `bookings.total_harga`

---

### 10. `payments`

> Catatan pembayaran per booking. 1 booking = 1 payment record.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `booking_id` | INT | FK → bookings.id, UNIQUE | 1:1 ke bookings |
| `jumlah_dibayar` | DECIMAL(12,2) | NOT NULL | Nominal aktual |
| `metode` | ENUM('cash','transfer','ewallet') | NOT NULL | |
| `status` | ENUM('pending','paid','cancelled') | DEFAULT 'pending' | |
| `bukti_bayar` | VARCHAR(255) | NULL | Path ke foto bukti transfer |
| `tanggal_bayar` | TIMESTAMP | NULL | Diisi saat status = paid |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |

---

### 11. `service_logs`

> Audit trail perubahan status booking. Immutable (read-only by design).

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `booking_id` | INT | FK → bookings.id | |
| `user_id` | INT | FK → users.id | Siapa yang ubah status |
| `status_dari` | VARCHAR(20) | NOT NULL | Status sebelumnya |
| `status_ke` | VARCHAR(20) | NOT NULL | Status sesudahnya |
| `catatan` | TEXT | NULL | Notes opsional |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Kapan diubah |

---

## C. Relasi Antar Tabel

### One-to-One

| Parent | Child | Keterangan |
|--------|-------|------------|
| `users` | `customers` | 1 user role customer = 1 profil customer |
| `users` | `mechanics` | 1 user role mekanik = 1 profil mekanik |
| `bookings` | `payments` | 1 booking = 1 record pembayaran |

### One-to-Many

| Parent (1) | Child (N) | Keterangan |
|------------|-----------|------------|
| `customers` | `motors` | 1 customer punya banyak motor |
| `customers` | `bookings` | 1 customer bisa banyak booking |
| `motors` | `bookings` | 1 motor bisa diservis berkali-kali |
| `service_types` | `bookings` | 1 jenis layanan dipake banyak booking |
| `mechanics` | `bookings` | 1 mekanik handle banyak booking |
| `time_slots` | `bookings` | 1 slot bisa dipake di tanggal berbeda |
| `bookings` | `booking_parts` | 1 booking pakai banyak part |
| `spare_parts` | `booking_parts` | 1 part dipake di banyak booking |
| `bookings` | `service_logs` | 1 booking punya banyak log perubahan |
| `users` | `service_logs` | 1 user bisa bikin banyak log |

### Many-to-Many (via Junction)

| Tabel A | Junction | Tabel B | Keterangan |
|---------|----------|---------|------------|
| `bookings` | `booking_parts` | `spare_parts` | Booking ↔ sparepart yang dipakai |

---

## D. Catatan Teknis

### Snapshot Fields (Jangan Referensi Langsung ke Master)

| Tabel | Field Snapshot | Sumber Master |
|-------|---------------|---------------|
| `bookings` | `harga_jasa` | `service_types.harga` |
| `booking_parts` | `harga_saat_itu` | `spare_parts.harga_satuan` |

**Alasan**: Kalau harga master berubah, histori transaksi tetap akurat.

### Nullable Foreign Keys

| Tabel | Field | Alasan |
|-------|-------|--------|
| `bookings` | `mechanic_id` | Belum di-assign saat customer baru booking |

### Soft Delete vs Hard Delete

| Tabel | Strategi | Alasan |
|-------|----------|--------|
| `bookings` | Status `dibatalkan` (soft) + hard delete oleh admin | Preserve audit trail |
| `service_types` | Status `nonaktif` (soft) | Masih dipakai di booking lama |
| `mechanics` | Status `nonaktif` (soft) | Masih dipakai di booking lama |
| `spare_parts` | Hard delete OK (kalau stok 0 & tidak ada di booking) | Tapi hati-hati FK constraint |

### Index yang Disarankan

| Tabel | Kolom | Alasan |
|-------|-------|--------|
| `bookings` | `tanggal_booking, time_slot_id` | Validasi double-booking cepat |
| `bookings` | `status` | Filter booking by status |
| `bookings` | `customer_id` | Query booking per customer |
| `bookings` | `mechanic_id` | Query booking per mekanik |
| `service_logs` | `booking_id` | Query log per booking |
| `motors` | `customer_id` | Query motor per customer |

---

## E. Validasi Double-Booking (Logic)

```sql
-- Cek apakah slot masih tersedia pada tanggal tertentu
SELECT COUNT(*) as jumlah_booking
FROM bookings
WHERE time_slot_id = ?
  AND tanggal_booking = ?
  AND status != 'dibatalkan';

-- Bandingkan dengan time_slots.kapasitas
-- Jika jumlah_booking >= kapasitas → TOLAK booking
-- Jika jumlah_booking < kapasitas → IZINKAN booking
```

---

**Next**: Lihat pembagian modul di `plan.md` (section B).
