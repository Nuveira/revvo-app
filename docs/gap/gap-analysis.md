# REVVO — Gap Analysis & Bug Audit

> Generated: 2026-06-08
> Branch: develop
> Auditor: Claude (full codebase read)

---

## 1. Missing Files (Unimplemented)

| File | Owner | Impact |
|------|-------|--------|
| `pages/admin/mechanics.php` | Raika | Nav link → **404**. CRUD mechanics belum ada |
| `pages/admin/time_slots.php` | Raika | Nav link → **404**. CRUD time slots belum ada |
| `pages/admin/customers.php` | Raika | List customer (read-only) belum ada |
| `pages/customer/invoice.php` | Nugi | Invoice PDF tidak bisa diakses |
| `includes/pagination.php` | Dermawan | Tidak ada pagination di seluruh app |

**Impact rubrik**: 2 dari 7 CRUD wajib belum ada → risiko nilai CRUD tidak full.

---

## 2. Critical Logic Bugs

### 2.1 `pages/customer/proses_booking.php`

**~~Bug A — Double-booking tidak divalidasi~~ ✅ FIXED**
- JOIN `time_slots + bookings` cek `booked >= capacity` sebelum INSERT
- File: `pages/customer/proses_booking.php`

**~~Bug B — service_logs tidak diinsert saat booking dibuat~~ ✅ FIXED**
- INSERT `service_logs` setelah booking sukses, `previous_status=''`, `new_status='queued'`
- File: `pages/customer/proses_booking.php`

**~~Bug C — Motor ownership tidak divalidasi (security)~~ ✅ FIXED**
- Query `motors WHERE id=? AND customer_id=?` sebelum lanjut proses
- File: `pages/customer/proses_booking.php`

---

### 2.2 `pages/mekanik/task_detail.php`

**~~Bug D — service_logs tidak diinsert saat status berubah~~ ✅ FIXED**
- INSERT service_logs di start_job dan finish_job dengan previous/new status
- File: `pages/mekanik/task_detail.php`

**~~Bug E — State machine tidak divalidasi~~ ✅ FIXED**
- start_job cek current status = queued sebelum update
- finish_job cek current status = in_progress sebelum update
- File: `pages/mekanik/task_detail.php`

**~~Bug F — total_price tidak direcalculate setelah part ditambah~~ ✅ FIXED**
- UPDATE bookings.total_price = service_price + SUM(booking_parts.subtotal) setelah insert
- File: `pages/mekanik/task_detail.php`

**~~Bug G — Stock bisa negatif~~ ✅ FIXED**
- Pre-check `$part['stock'] < $qty` sebelum insert booking_parts
- UPDATE spare_parts dengan WHERE AND stock >= ? sebagai safety net
- File: `pages/mekanik/task_detail.php`

**~~Bug H — Ownership booking tidak dicek sebelum add_part~~ ✅ FIXED**
- Query SELECT id FROM bookings WHERE id=? AND mechanic_id=? di awal handler add_part
- File: `pages/mekanik/task_detail.php`

---

### 2.3 `pages/mekanik/proses_task.php`

**~~Bug I — service_logs tidak diinsert~~ ✅ FIXED**
- INSERT service_logs setelah UPDATE dengan previous_status dan new_status
- File: `pages/mekanik/proses_task.php`

**~~Bug J — State machine tidak divalidasi~~ ✅ FIXED**
- validTransitions map: queued→[in_progress], in_progress→[completed]
- Tolak jika transisi tidak ada di map
- File: `pages/mekanik/proses_task.php`

---

### 2.4 `pages/admin/Bookings.php`

**~~Bug K — cancel_booking logs previous_status = empty string~~ ✅ FIXED**
- Query SELECT status WHERE id=? sebelum UPDATE cancel, pakai sebagai previous_status
- File: `pages/admin/bookings.php`

**~~Bug L — change_status tidak validasi state machine~~ ✅ FIXED**
- validTransitions map sesuai state machine dari CLAUDE.md
- Admin tidak bisa skip state (queued→ready_for_pickup akan ditolak)
- File: `pages/admin/bookings.php`

**~~Bug M — Filename case inconsistency~~ ✅ FIXED**
- git mv Bookings.php → bookings.php (via dua langkah untuk Windows)
- Semua redirect internal sudah pakai `bookings.php`

---

## 3. Feature Status

### PDF / Export

| Feature | Status |
|---------|--------|
| Excel reports (PhpSpreadsheet) | ✅ Implemented di `reports.php` |
| PDF reports (DomPDF) | ❌ Tidak diimplementasi — DomPDF tidak dipakai di mana pun |
| Invoice PDF per transaksi | ❌ `invoice.php` tidak ada |

Composer vendor terinstall, autoload tersedia. Tinggal implement.

### Bonus Rubrik Progress

| Bonus | Status |
|-------|--------|
| Pencarian & filter | ✅ Ada di bookings, service_types, spare_parts |
| Paginasi | ⚠️ Ada di service_types, spare_parts; belum di bookings/motors/customers |
| Export PDF | ❌ Belum |
| Export Excel | ✅ Reports |
| Upload gambar | ✅ Motors |
| Multi-role | ✅ 3 role jalan |

---

## 4. Apa yang Sudah Jalan

| Modul | Status |
|-------|--------|
| Auth (login/register/logout/role guard) | ✅ |
| CRUD users | ✅ |
| CRUD spare_parts | ✅ |
| CRUD service_types | ✅ |
| CRUD motors (customer) | ✅ |
| Admin bookings list + filter + assign mechanic | ✅ |
| Admin change status + cancel | ✅ (dengan bug K, L) |
| Mechanic tasks list + detail + add parts | ✅ (dengan bug D–H) |
| Booking form customer | ✅ (dengan bug A–C) |
| Admin dashboard | ✅ |
| Customer dashboard | ✅ |
| Mekanik dashboard | ✅ |
| Audit logs page | ✅ |
| Excel reports | ✅ |
| Payments verification | ✅ |

---

## 5. Priority Fix Order

| # | Fix | Owner | Urgency |
|---|-----|-------|---------|
| 1 | Buat `mechanics.php` + `time_slots.php` | Raika | 🔴 Tinggi — 404 di nav |
| 2 | ~~Double-booking validation (Bug A)~~ | Geral | ✅ Fixed |
| 3 | ~~Motor ownership check (Bug C)~~ | Geral | ✅ Fixed |
| 4 | ~~service_logs on booking creation (Bug B)~~ | Geral | ✅ Fixed |
| 5 | ~~service_logs pada mechanic status change (Bug D, I)~~ | Ahmad | ✅ Fixed |
| 6 | ~~total_price recalc setelah add_part (Bug F)~~ | Ahmad | ✅ Fixed |
| 7 | ~~Stock >= qty check (Bug G)~~ | Ahmad | ✅ Fixed |
| 8 | ~~State machine validation (Bug E, J, L)~~ | Geral/Ahmad | ✅ Fixed |
| 9 | Buat `invoice.php` + PDF DomPDF | Nugi | 🟡 Medium — bonus rubrik |
| 10 | PDF export di `reports.php` | Dermawan | 🟡 Medium — bonus rubrik |
| 11 | Pagination di semua list | Dermawan | 🟡 Medium — bonus rubrik |
| 12 | Buat `customers.php` | Raika | 🟢 Rendah |
| 13 | ~~Fix Bug K (empty previous_status)~~ | Ahmad | ✅ Fixed |
| 14 | ~~Rename `Bookings.php` → `bookings.php`~~ | Ahmad | ✅ Fixed |
