# 📊 ERD: Sistem Booking & Tracking Servis Bengkel Motor (REVVO)

> **Kelompok**: Geral, Nugi, Dermawan, Raika, Ahmad
> **Total Tabel**: 11 tabel dengan relasi foreign key | **7 entitas utama dengan CRUD penuh**
> **Naming Convention**: English column names, Indonesian enum values for domain terms

---

## A. Diagram Relasi

```
                    ┌──────────┐
                    │  USERS   │
                    └────┬─────┘
              ┌──────────┼──────────┐
              │          │          │
         1:1  ▼     1:1  ▼     1:N  ▼
    ┌──────────┐  ┌──────────┐  ┌──────────────┐
    │CUSTOMERS │  │MECHANICS │  │ SERVICE_LOGS │
    └────┬─────┘  └────┬─────┘  └──────────────┘
         │              │               ▲
    1:N  ▼              │          1:N  │
    ┌──────────┐        │               │
    │  MOTORS  │        │               │
    └────┬─────┘        │               │
         │              │               │
         ▼ 1:N          ▼ 1:N           │
    ┌─────────────────────────────────────┐
    │              BOOKINGS               │◄── SERVICE_TYPES (1:N)
    │            (central)                │◄── TIME_SLOTS (1:N)
    └───┬──────────┬──────────────────────┘
        │          │
   1:N  ▼     1:1  ▼
  ┌──────────┐ ┌──────────┐
  │ BOOKING  │ │ PAYMENTS │
  │ PARTS    │ │          │
  └────┬─────┘ └──────────┘
       │
  N:1  ▼
  ┌──────────┐
  │  SPARE   │
  │  PARTS   │
  └──────────┘
```

---

## B. Detail Tabel & Field

### 1. `users`

> Akun semua role. Login via email + password.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `name` | VARCHAR(100) | NOT NULL | Nama lengkap |
| `email` | VARCHAR(100) | UNIQUE, NOT NULL | Untuk login |
| `password_hash` | VARCHAR(255) | NOT NULL | Pakai `password_hash()` |
| `role` | ENUM('admin','mechanic','customer') | NOT NULL | Tentukan hak akses |
| `phone` | VARCHAR(20) | NULL | Nomor HP (semua role) |
| `profile_photo` | VARCHAR(255) | NULL | Path ke file foto |
| `status` | ENUM('active','inactive') | DEFAULT 'active' | Soft-disable akun |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | |

---

### 2. `customers`

> Extension data untuk user dengan role customer.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `user_id` | INT | FK → users.id, UNIQUE | 1:1 ke users |
| `address` | TEXT | NULL | Alamat lengkap |
| `gender` | ENUM('male','female') | NULL | Jenis kelamin |
| `birth_date` | DATE | NULL | Tanggal lahir |
| `no_ktp` | VARCHAR(20) | NULL | NIK KTP (konteks Indonesia) |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | |

---

### 3. `mechanics`

> Extension data untuk user dengan role mechanic.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `user_id` | INT | FK → users.id, UNIQUE | 1:1 ke users |
| `specialization` | VARCHAR(100) | NULL | Misal: "Mesin, Kelistrikan" |
| `availability_status` | ENUM('available','busy','inactive') | DEFAULT 'available' | Status ketersediaan real-time |
| `notes` | TEXT | NULL | Catatan internal admin |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | |

---

### 4. `motors`

> Motor milik customer. Satu customer bisa punya banyak motor.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `customer_id` | INT | FK → customers.id | |
| `brand` | VARCHAR(50) | NOT NULL | Honda, Yamaha, dll |
| `model` | VARCHAR(50) | NOT NULL | Vario 125, NMAX, dll |
| `plate_number` | VARCHAR(15) | NOT NULL | Contoh: B 1234 XYZ |
| `production_year` | YEAR | NULL | |
| `color` | VARCHAR(30) | NULL | |
| `image_path` | VARCHAR(255) | NULL | Path ke file foto |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | |

---

### 5. `service_types`

> Master jenis layanan bengkel.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `name` | VARCHAR(100) | NOT NULL | "Ganti Oli", "Tune Up", dll |
| `description` | TEXT | NULL | Detail apa yang dikerjakan |
| `estimated_duration_minutes` | INT | NOT NULL | Perkiraan durasi pengerjaan |
| `base_price` | DECIMAL(12,2) | NOT NULL | Harga jasa (tanpa sparepart) |
| `status` | ENUM('active','inactive') | DEFAULT 'active' | |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | |

---

### 6. `spare_parts`

> Master sparepart bengkel. Stok dikelola admin, dikurangi otomatis saat mekanik pakai.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `sku` | VARCHAR(50) | UNIQUE, NOT NULL | Stock Keeping Unit (kode unik) |
| `name` | VARCHAR(100) | NOT NULL | "Oli Yamalube 1L", "Filter Oli", dll |
| `unit` | VARCHAR(20) | NOT NULL | "pcs", "liter", "set" (dari XML satuan) |
| `stock` | INT | NOT NULL, DEFAULT 0 | Jumlah stok saat ini |
| `minimum_stock` | INT | DEFAULT 5 | Alert kalau stok di bawah ini |
| `price` | DECIMAL(12,2) | NOT NULL | Harga per satuan |
| `status` | ENUM('active','inactive') | DEFAULT 'active' | |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | |

---

### 7. `time_slots`

> Jam operasional bengkel. Admin manage via full CRUD.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `day` | ENUM('monday','tuesday','wednesday','thursday','friday','saturday','sunday') | NOT NULL | Hari operasional |
| `start_time` | TIME | NOT NULL | Contoh: 08:00:00 |
| `end_time` | TIME | NOT NULL | Contoh: 09:00:00 |
| `capacity` | INT | DEFAULT 1 | Jumlah booking per slot |
| `status` | ENUM('active','inactive') | DEFAULT 'active' | |

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
| `booking_date` | DATE | NOT NULL | Tanggal servis |
| `service_price` | DECIMAL(12,2) | NOT NULL | **Snapshot** dari service_types.base_price |
| `total_price` | DECIMAL(12,2) | DEFAULT 0 | service_price + SUM(booking_parts) |
| `status` | ENUM('queued','in_progress','completed','ready_for_pickup','cancelled') | DEFAULT 'queued' | State machine |
| `customer_complaint` | TEXT | NULL | Keluhan customer |
| `condition_photo` | VARCHAR(255) | NULL | Path ke foto kondisi motor |
| `mechanic_note` | TEXT | NULL | Catatan pengerjaan dari mekanik |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | |

**State Machine:**
```
queued → in_progress → completed → ready_for_pickup
  ↓          ↓
cancelled  cancelled
```

**Validasi penting:**
- `mechanic_id` nullable → di-assign oleh admin setelah booking masuk
- `service_price` = snapshot saat booking dibuat, bukan referensi langsung
- `total_price` = `service_price` + `SUM(booking_parts.subtotal)`, di-recalculate setiap mekanik tambah/hapus part
- Double-booking prevention: query check pada kombinasi `time_slot_id` + `booking_date` + slot belum penuh

---

### 9. `booking_parts`

> Junction table: sparepart yang dipakai dalam satu booking.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `booking_id` | INT | FK → bookings.id | |
| `spare_part_id` | INT | FK → spare_parts.id | |
| `qty` | INT | NOT NULL | Jumlah yang dipakai |
| `price_at_time` | DECIMAL(12,2) | NOT NULL | **Snapshot** dari spare_parts.price |
| `subtotal` | DECIMAL(12,2) | NOT NULL | qty × price_at_time |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |

---

### 10. `payments`

> Catatan pembayaran per booking. 1 booking = 1 payment record.

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `booking_id` | INT | FK → bookings.id, UNIQUE | 1:1 ke bookings |
| `payment_method` | ENUM('cash','transfer','ewallet') | NOT NULL | |
| `amount` | DECIMAL(12,2) | NOT NULL | Nominal aktual yang dibayar |
| `status` | ENUM('pending','paid','cancelled') | DEFAULT 'pending' | |
| `paid_at` | TIMESTAMP | NULL | Diisi saat status = paid |
| `verified_by` | INT | FK → users.id, NULL | Admin yang verifikasi |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | |

---

### 11. `service_logs`

> Audit trail perubahan status booking. Immutable (read-only by design).

| Field | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| `id` | INT | PK, AUTO_INCREMENT | |
| `booking_id` | INT | FK → bookings.id | |
| `changed_by` | INT | FK → users.id, NULL | Siapa yang ubah status |
| `previous_status` | VARCHAR(20) | NOT NULL | Status sebelumnya |
| `new_status` | VARCHAR(20) | NOT NULL | Status sesudahnya |
| `note` | TEXT | NULL | Notes opsional |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Kapan diubah |

---

## C. Relasi Antar Tabel

### One-to-One

| Parent | Child | Keterangan |
|--------|-------|------------|
| `users` | `customers` | 1 user role customer = 1 profil customer |
| `users` | `mechanics` | 1 user role mechanic = 1 profil mekanik |
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

### Snapshot Fields

| Tabel | Field Snapshot | Sumber Master |
|-------|---------------|---------------|
| `bookings` | `service_price` | `service_types.base_price` |
| `booking_parts` | `price_at_time` | `spare_parts.price` |

### Nullable Foreign Keys

| Tabel | Field | Alasan |
|-------|-------|--------|
| `bookings` | `mechanic_id` | Belum di-assign saat customer baru booking |
| `payments` | `verified_by` | NULL kalau belum diverifikasi |

### ON DELETE Rules

| Parent → Child | Rule | Alasan |
|---------------|------|--------|
| users → customers | CASCADE | Hapus user = hapus profil |
| users → mechanics | CASCADE | Hapus user = hapus profil |
| customers → motors | CASCADE | Hapus customer = hapus motornya |
| customers → bookings | RESTRICT | Jangan hapus customer yang punya booking |
| motors → bookings | RESTRICT | Jangan hapus motor yang punya booking |
| service_types → bookings | RESTRICT | Jangan hapus layanan yang dipake |
| mechanics → bookings | SET NULL | Mekanik dihapus = booking tetap, mechanic_id jadi NULL |
| time_slots → bookings | RESTRICT | Jangan hapus slot yang dipake |
| bookings → booking_parts | CASCADE | Hapus booking = hapus parts-nya |
| spare_parts → booking_parts | RESTRICT | Jangan hapus part yang dipake |
| bookings → payments | CASCADE | Hapus booking = hapus payment |
| bookings → service_logs | CASCADE | Hapus booking = hapus log |
| users → service_logs | SET NULL | User dihapus = log tetap, changed_by jadi NULL |

### Index yang Disarankan

| Tabel | Kolom | Alasan |
|-------|-------|--------|
| `bookings` | `booking_date, time_slot_id` | Validasi double-booking cepat |
| `bookings` | `status` | Filter booking by status |
| `bookings` | `customer_id` | Query booking per customer |
| `bookings` | `mechanic_id` | Query booking per mekanik |
| `service_logs` | `booking_id` | Query log per booking |
| `motors` | `customer_id` | Query motor per customer |
| `spare_parts` | `sku` | Lookup by SKU |

---

## E. Validasi Double-Booking (Logic)

```sql
-- Cek apakah slot masih tersedia pada tanggal tertentu
SELECT COUNT(*) as total_booked
FROM bookings
WHERE time_slot_id = ?
  AND booking_date = ?
  AND status != 'cancelled';

-- Bandingkan dengan time_slots.capacity
-- Jika total_booked >= capacity → TOLAK booking
-- Jika total_booked < capacity → IZINKAN booking
```

---

## F. Mapping Perubahan dari Versi Sebelumnya

| Tabel | Field Lama (XML) | Field Baru (Merged) | Keterangan |
|-------|-----------------|---------------------|------------|
| users | username | *(dropped)* | Login via email |
| users | nama_lengkap | name | |
| users | foto_profil | profile_photo | |
| users | *(none)* | phone, status | Added |
| customers | no_hp | *(moved to users.phone)* | |
| customers | alamat | address | |
| customers | *(none)* | gender, birth_date | Added |
| mechanics | spesialisasi | specialization | |
| mechanics | status | availability_status | Enum: available/busy/inactive |
| mechanics | catatan | notes | |
| motors | merk | brand | |
| motors | plat_nomor | plate_number | |
| motors | tahun | production_year | |
| motors | foto | image_path | |
| service_types | nama_layanan | name | |
| service_types | harga | base_price | |
| service_types | estimasi_menit | estimated_duration_minutes | |
| spare_parts | *(none)* | sku | Added (UNIQUE) |
| spare_parts | nama_part | name | |
| spare_parts | satuan | unit | |
| spare_parts | stok | stock | |
| spare_parts | harga_satuan | price | |
| spare_parts | stok_minimum | minimum_stock | |
| time_slots | jam_mulai | start_time | |
| time_slots | jam_selesai | end_time | |
| time_slots | kapasitas | capacity | |
| time_slots | hari | day | English enum |
| bookings | tanggal_booking | booking_date | |
| bookings | harga_jasa | service_price | |
| bookings | total_harga | total_price | |
| bookings | keluhan | customer_complaint | |
| bookings | foto_kondisi | condition_photo | |
| bookings | catatan_mekanik | mechanic_note | |
| booking_parts | harga_saat_itu | price_at_time | |
| payments | jumlah_dibayar | amount | |
| payments | metode | payment_method | |
| payments | tanggal_bayar | paid_at | |
| payments | *(none)* | verified_by | Added (FK → users) |
| payments | bukti_bayar | *(dropped)* | Simplified |
| service_logs | user_id | changed_by | |
| service_logs | status_dari | previous_status | |
| service_logs | status_ke | new_status | |
| service_logs | catatan | note | |

---

**Next**: Lihat `bengkel.sql` untuk CREATE TABLE implementasi, `plan.md` untuk roadmap pengerjaan.
