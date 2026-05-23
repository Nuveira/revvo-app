# Team Workflow — REVVO

> Dokumen kerja tim sementara untuk dipakai saat ngoding.
> Source of truth untuk: pembagian modul, branch, dependency, dan apa yang aman dikerjakan sekarang.
> Referensi utama tetap: `docs/plan/plan.md` dan `docs/plan/foundation.md`.

---

## 1. Tujuan Dokumen

Dokumen ini dibuat supaya setiap anggota tim bisa langsung tahu:

- dia pegang modul apa
- branch apa yang dipakai
- file target apa yang kemungkinan disentuh
- apa yang aman dikerjakan sekarang
- apa yang harus menunggu ERD / diagram / SQL final
- kapan harus sync dari `develop`

Dokumen ini sengaja dibuat sebagai **dokumen tim tunggal**, bukan file terpisah per orang, supaya tidak cepat drift saat schema masih berubah.

---

## 2. Kondisi Project Saat Ini

Status project saat dokumen ini ditulis:

- Landing page dan fondasi folder awal sudah ada
- Workflow branch sudah dibersihkan dan distandardkan
- `develop` adalah branch integrasi utama
- ERD, diagram lain, dan SQL masih mungkin berubah
- Karena schema masih bergerak, tim **belum disarankan** masuk terlalu jauh ke query database final

Kesimpulan praktis:

- **Aman dikerjakan sekarang**: layout, shell halaman, struktur file, helper non-query, komponen UI, flow dokumen, validasi statis, route/guard dasar
- **Tunggu schema freeze**: query CRUD final, relasi tabel, laporan berbasis aggregate, validasi bisnis yang tergantung field final

---

## 3. Workflow Git

### Branch Utama

- `main` = branch final / rilis
- `develop` = branch integrasi semua feature

### Aturan Branch Feature

- Branch feature selalu dibuat dari `develop`
- Gunakan format branch flat:
  - `feat-admin-users-crud`
  - `feat-admin-service-types-crud`
  - `feat-auth-flow`
- Jangan pakai format `feat/...`

### Alur Kerja

1. Tarik perubahan terbaru dari `develop`
2. Buat branch feature baru dari `develop`
3. Kerja hanya pada scope branch itu
4. Jika `develop` berubah signifikan, sync branch feature
5. Setelah selesai, merge branch feature ke `develop`
6. `develop` di-merge ke `main` hanya saat integrasi stabil

### Kapan Wajib Sync Dari `develop`

- saat baru membuat branch
- saat branch sudah tertinggal beberapa hari
- saat ada perubahan foundation penting
- sebelum merge kembali ke `develop`

---

## 4. Status Branch Saat Ini

Branch yang masih relevan:

- `develop`
- `main`
- `feat-landing-page`
- branch feature baru sesuai modul yang sedang dikerjakan

Branch lama seperti `feat/auth`, `feat/database`, dan `feat/setup-foundation` sudah dianggap obsolete dan tidak dipakai lagi.

---

## 5. Pembagian Modul Tim

| Anggota | Modul Utama | Branch yang Disarankan | Status Sekarang |
|---------|-------------|------------------------|-----------------|
| Geral | Foundation + CRUD Users | `feat-admin-users-crud` | Mulai dari shell + auth/foundation, jangan finalkan query dulu sebelum schema cukup stabil |
| Raika | CRUD Service Types + Mechanics + Time Slots | `feat-admin-service-types-crud`, `feat-admin-mechanics-crud`, `feat-admin-time-slots-crud` | Siapkan file dan UI shell dulu |
| Nugi | CRUD Motors + Customer Pages | `feat-customer-motors-crud` | Aman siapkan UI customer dan upload flow dummy |
| Ahmad | CRUD Bookings + Mekanik Flow | `feat-admin-bookings-crud` | Sebaiknya tunggu lebih banyak schema fix |
| Dermawan | CRUD Spare Parts + Dashboard + Reports | `feat-admin-spare-parts-crud` | CRUD spare part bisa disiapkan shell, reports tunggu schema final |

Catatan:

- Kalau satu modul terlalu besar, pecah branch per fitur kecil
- Jangan campur beberapa modul besar dalam satu branch

---

## 6. Dependency Antar Modul

Urutan dependency utama:

1. Foundation layout + auth
2. Master data dasar
3. Booking flow
4. Dashboard, laporan, bonus

Dependency praktis:

- `users` mempengaruhi `mechanics` dan auth
- `service_types`, `mechanics`, `time_slots`, `motors` menjadi fondasi booking
- `bookings` mempengaruhi `payments`, `service_logs`, `booking_parts`
- `spare_parts` mempengaruhi stok dan booking parts
- dashboard dan reports bergantung pada data transaksi yang lebih stabil

---

## 7. Apa Yang Aman Dikerjakan Sekarang

Karena schema masih mungkin berubah, ini yang aman:

### Geral

- rapikan struktur file foundation
- siapkan `pages/admin/users.php` shell
- siapkan `pages/auth/register.php`
- siapkan `pages/auth/logout.php`
- siapkan `pages/403.php`
- siapkan `includes/functions.php` untuk helper umum yang tidak tergantung query final
- siapkan role guard dasar di `includes/auth.php`

### Raika

- siapkan shell halaman CRUD admin:
  - `pages/admin/service_types.php`
  - `pages/admin/mechanics.php`
  - `pages/admin/time_slots.php`
- siapkan layout tabel, form, dan placeholder action

### Nugi

- siapkan shell halaman customer:
  - `pages/customer/dashboard.php`
  - `pages/customer/motors.php`
  - `pages/customer/booking_history.php`
  - `pages/customer/profile.php`
- siapkan struktur folder upload dan validasi file level UI

### Ahmad

- siapkan shell:
  - `pages/admin/bookings.php`
  - `pages/admin/payments.php`
  - `pages/mekanik/dashboard.php`
  - `pages/mekanik/my_tasks.php`
  - `pages/mekanik/history.php`
- siapkan flow status di level dokumen / komentar / pseudo-flow, bukan query final

### Dermawan

- siapkan shell:
  - `pages/admin/spare_parts.php`
  - `pages/admin/dashboard.php`
  - `pages/admin/reports.php`
  - `pages/admin/audit_logs.php`
- siapkan komponen pagination placeholder

---

## 8. Apa Yang Sebaiknya Menunggu ERD / SQL Final

Tahan dulu pekerjaan berikut sampai schema cukup stabil:

- query CRUD final
- relasi foreign key yang dipakai di code
- laporan dengan `COUNT`, `SUM`, `GROUP BY`
- validasi state machine yang bergantung tabel final
- insert / update stok otomatis
- booking conflict check final
- payment logic final
- audit log yang sudah mengikat nama field tabel

Kalau terpaksa membuat sementara, beri penanda jelas bahwa itu masih asumsi dan siap diubah setelah schema freeze.

---

## 9. Definition of Done Per Branch

Sebuah branch feature dianggap siap merge ke `develop` jika:

- scope branch jelas dan tidak melebar
- file target sesuai modul
- tidak merusak layout / foundation yang sudah ada
- sudah sync dengan `develop` jika perlu
- tidak ada debug liar seperti `var_dump` atau `echo` sementara
- kalau belum pakai query final, beri catatan placeholder yang jelas
- commit message cukup deskriptif

---

## 10. Checklist Sebelum Merge

- pastikan branch dibuat dari `develop`
- pull / merge perubahan terbaru dari `develop` bila perlu
- cek ulang apakah ada file di luar scope branch
- pastikan perubahan tidak mengganggu branch orang lain
- review manual halaman yang disentuh
- merge ke `develop`, bukan ke `main`

---

## 11. Cara Pakai Dokumen Ini

Setiap anggota disarankan membaca urutan ini sebelum mulai ngoding:

1. `docs/plan/foundation.md`
2. `docs/plan/plan.md`
3. `docs/plan/team-workflow.md`

Kalau ada conflict antara kerja aktual dan dokumen ini:

- update dokumen ini lebih dulu
- baru lanjut implementasi

Tujuannya supaya tim tetap sinkron dan tidak coding berdasarkan asumsi masing-masing.

---

## 12. Next Step Tim

Prioritas dekat:

1. Finalisasi ERD / diagram / SQL
2. Sambil menunggu, tiap orang siapkan shell file modulnya
3. Setelah schema cukup stabil, mulai isi query dan logic per modul
4. Merge bertahap ke `develop`

Dokumen ini boleh dipakai sementara sebagai panduan kerja harian sampai schema final sudah lebih stabil.
