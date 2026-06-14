# Customer Test Checklist

Checklist ini dipakai untuk bukti uji manual fitur role Customer di REVVO.

## Scope

- Dashboard customer
- Manajemen motor (CRUD)
- Booking service (tambah, edit, batal)
- Tracking progress booking
- History booking & detail
- Profil customer

## Test Data

Password semua user seed: `password123`

Akun customer contoh:
- `geral@gmail.com` — Geral Maulana
- `rizki@gmail.com` — Rizki Ramadhan
- `maya@gmail.com` — Maya Anggraini

Referensi seed: `database/revvo.sql`

---

## Dashboard

| ID | Skenario | Langkah Singkat | Expected Result | Status | Catatan |
|---|---|---|---|---|---|
| CUST-01 | Dashboard tampil data benar | Login customer lalu buka dashboard | Nama, jumlah booking aktif, dan jumlah motor tampil sesuai data DB | `[x]` | |
| CUST-02 | Booking aktif tampil di dashboard | Ada booking status `queued`/`in_progress` | Card booking aktif tampil nama service, motor, tanggal, waktu | `[x]` | |
| CUST-03 | Dashboard kosong saat tidak ada booking | Login customer tanpa booking | Tampil teks "Tidak ada booking" | `[x]` | |
| CUST-04 | Tombol lihat detail booking | Klik "lihat detail" di card booking aktif | Redirect ke `booking_detail.php?id=...` dengan id yang benar | `[x]` | |
| CUST-05 | Track progress tampil | Ada booking aktif | Stepper track progress tampil dengan step yang sesuai status | `[x]` | |
| CUST-06 | History 5 terbaru di dashboard | Ada riwayat booking | Tabel history menampilkan max 5 booking terbaru | `[x]` | |

---

## Motor

| ID | Skenario | Langkah Singkat | Expected Result | Status | Catatan |
|---|---|---|---|---|---|
| CUST-07 | Lihat list motor | Buka `motor.php` | Semua motor milik customer tampil | `[x]` | |
| CUST-08 | Tambah motor valid | Isi form tambah motor lengkap lalu submit | Motor tersimpan, redirect ke `motor.php` dengan pesan sukses | `[x]` | |
| CUST-09 | Tambah motor — validasi kosong | Submit form tanpa mengisi field wajib | Form tidak tersubmit, muncul pesan error per field | `[x]` | |
| CUST-10 | Tambah motor — plat duplikat | Submit plat nomor yang sudah terdaftar | Muncul error "Nomor plat sudah terdaftar" | `[x]` | |
| CUST-11 | Tambah motor — upload foto | Upload foto JPG/PNG/WEBP maks 2MB | Foto tersimpan, tampil di detail motor | `[x]` | |
| CUST-12 | Detail motor tampil | Klik "Lihat Detail" di card motor | Halaman detail motor tampil info lengkap + histori booking | `[x]` | |
| CUST-13 | Edit motor valid | Ubah data motor lalu simpan | Data terupdate, redirect ke detail motor | `[x]` | |
| CUST-14 | Hapus motor tanpa booking aktif | Motor tidak punya booking antri/dikerjakan | Motor terhapus, redirect ke `motor.php` | `[x]` | |
| CUST-15 | Hapus motor dengan booking aktif | Motor masih punya booking `queued`/`in_progress` | Muncul error, motor tidak terhapus | `[x]` | |
| CUST-16 | Motor kosong | Customer belum punya motor | Halaman motor tampil empty state dengan tombol tambah | `[x]` | |

---

## Booking

| ID | Skenario | Langkah Singkat | Expected Result | Status | Catatan |
|---|---|---|---|---|---|
| CUST-17 | List booking aktif | Buka `booking.php` | Hanya booking status `queued`/`in_progress` yang tampil | `[x]` | |
| CUST-18 | Tambah booking valid | Isi semua field form tambah booking lalu submit | Booking tersimpan, redirect ke `booking.php` | `[x]` | |
| CUST-19 | Tambah booking — filter waktu by hari | Pilih tanggal di form booking | Select waktu otomatis filter slot sesuai hari yang dipilih | `[x]` | |
| CUST-20 | Tambah booking — slot sudah lewat hari ini | Pilih tanggal hari ini | Slot waktu yang end_time-nya sudah lewat tidak tampil di select | `[x]` | |
| CUST-21 | Tambah booking — tanpa motor | Customer belum punya motor | Form booking tidak tampil, muncul pesan tambah motor dulu | `[x]` | |
| CUST-22 | Detail booking | Klik "Detail" di list booking | Halaman detail tampil info lengkap (service, motor, waktu, mekanik) | `[x]` | |
| CUST-23 | Edit booking status queued | Booking masih antri, klik "Edit Booking" | Form edit muncul dengan data pre-filled | `[x]` | |
| CUST-24 | Edit booking — simpan perubahan | Ubah data booking (motor/service/tanggal/waktu) lalu simpan | Data booking terupdate | `[x]` | |
| CUST-25 | Edit booking status bukan queued | Coba akses edit booking yang sudah `in_progress` | Redirect balik dengan pesan error | `[x]` | |
| CUST-26 | Batalkan booking status queued | Klik "Batalkan Booking" lalu konfirmasi | Status booking jadi `cancelled`, redirect ke `booking.php` | `[x]` | |
| CUST-27 | Batalkan booking status bukan queued | Coba batalkan booking yang sudah `in_progress` | Muncul error, booking tidak dibatalkan | `[x]` | |
| CUST-28 | Booking kosong | Tidak ada booking aktif | Tampil empty state di tabel booking | `[x]` | |

---

## History

| ID | Skenario | Langkah Singkat | Expected Result | Status | Catatan |
|---|---|---|---|---|---|
| CUST-29 | List history tampil | Buka `history.php` | Semua booking milik customer tampil (semua status) | `[x]` | |
| CUST-30 | Filter history by motor | Pilih motor tertentu lalu klik Terapkan | Hanya booking motor itu yang tampil | `[x]` | |
| CUST-31 | Filter history urutan Motor A-Z | Pilih urutan Motor A-Z | Booking diurutkan berdasarkan nama motor ascending | `[x]` | |
| CUST-32 | Reset filter | Klik tombol Reset | Filter hilang, semua history tampil kembali | `[x]` | |
| CUST-33 | Paginasi history | Ada lebih dari 6 booking | Tombol prev/next berfungsi, halaman berpindah | `[x]` | |
| CUST-34 | Detail history | Klik "Detail" di list history | Halaman detail tampil info booking + spare parts + pembayaran | `[x]` | |
| CUST-35 | History kosong | Customer belum punya booking apapun | Tampil empty state di tabel history | `[x]` | |

---

## Profil

| ID | Skenario | Langkah Singkat | Expected Result | Status | Catatan |
|---|---|---|---|---|---|
| CUST-36 | Tampil data profil | Buka `profile.php` | Data nama, email, HP, gender, dll tampil sesuai DB | `[x]` | |
| CUST-37 | Edit profil valid | Ubah nama/HP/alamat lalu simpan | Data terupdate, muncul pesan sukses | `[x]` | |
| CUST-38 | Upload foto profil | Upload foto maks 2MB format JPG/PNG/WEBP | Foto profil berubah | `[x]` | |

---

## Ringkasan Status

| Total | Lulus | Gagal | Belum Diuji |
|---|---|---|---|
| 38 | 38 | 0 | 0 |
