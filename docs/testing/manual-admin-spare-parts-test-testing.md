# Manual Test Checklist — Admin: Spare Parts, Audit Logs, Reports, Dashboard

Checklist uji manual fitur yang dikerjakan oleh **Dermawan** di REVVO Admin Panel.

## Scope

- CRUD Spare Parts + validasi input
- Alert badge stok rendah di list & dashboard
- Pagination di spare_parts.php dan audit_logs.php
- Audit Logs (read-only, filter tanggal, pagination)
- Reports (filter, summary, export Excel, export PDF)
- Dashboard (revenue card, alert stok rendah, link ke reports)

## Test Data

- Akun admin: `admin@bengkel.com` / `password123`
- Referensi seed: `database/revvo.sql`

---

## 1. Spare Parts — CRUD & Validasi

| ID | Skenario | Langkah Singkat | Expected Result | Status |
|---|---|---|---|---|
| ADMIN-SP-01 | Tambah spare part valid | Isi semua field lengkap, submit form tambah | Data muncul di tabel, alert sukses | `[x]` |
| ADMIN-SP-02 | Tambah — field kosong | Submit form tanpa mengisi field wajib | Muncul pesan error field wajib diisi | `[x]` |
| ADMIN-SP-03 | Tambah — SKU duplikat | Submit dengan SKU yang sudah ada | Muncul pesan error SKU sudah digunakan | `[x]` |
| ADMIN-SP-04 | Tambah — stok negatif | Input stok `-1`, submit | Muncul pesan error stok tidak boleh negatif | `[x]` |
| ADMIN-SP-05 | Tambah — harga negatif | Input harga `-1000`, submit | Muncul pesan error harga tidak boleh negatif | `[x]` |
| ADMIN-SP-06 | Edit spare part valid | Klik Edit, ubah nama/harga, submit | Data terupdate di tabel, alert sukses | `[x]` |
| ADMIN-SP-07 | Edit — SKU duplikat milik lain | Ganti SKU dengan SKU spare part lain | Muncul pesan error SKU sudah digunakan | `[x]` |
| ADMIN-SP-08 | Edit — SKU milik sendiri | Update data tanpa ganti SKU | Berhasil update tanpa error duplikat | `[x]` |
| ADMIN-SP-09 | Hapus spare part tidak terpakai | Klik Hapus pada part yang belum dipakai di booking | Part terhapus, alert sukses | `[x]` |
| ADMIN-SP-10 | Hapus spare part terpakai | Klik Hapus pada part yang sudah dipakai di booking | Ditolak, muncul pesan error tidak bisa dihapus | `[x]` |
| ADMIN-SP-11 | Alert badge stok rendah | Buka list spare parts, cek part dengan stok ≤ minimum_stock | Badge merah tampil di kolom Stok | `[x]` |

---

## 2. Pagination Spare Parts

| ID | Skenario | Langkah Singkat | Expected Result | Status |
|---|---|---|---|---|
| ADMIN-SP-12 | Navigasi halaman berikutnya | Klik tombol next / nomor halaman 2 | Halaman 2 tampil, data berbeda dari halaman 1 | `[x]` |
| ADMIN-SP-13 | Navigasi halaman sebelumnya | Dari halaman 2, klik prev / halaman 1 | Kembali ke halaman 1 | `[x]` |
| ADMIN-SP-14 | Tombol prev di halaman 1 disabled | Buka halaman 1, cek tombol prev | Tombol prev tidak bisa diklik (disabled) | `[x]` |
| ADMIN-SP-15 | Tombol next di halaman terakhir disabled | Navigasi ke halaman terakhir | Tombol next tidak bisa diklik (disabled) | `[x]` |
| ADMIN-SP-16 | Jumlah data per halaman benar | Hitung baris di halaman tengah | Menampilkan 10 data per halaman | `[x]` |
| ADMIN-SP-17 | Info halaman akurat | Cek teks "Halaman X dari Y · Total Z" | Angka sesuai dengan data di DB | `[x]` |

---

## 3. Audit Logs

| ID | Skenario | Langkah Singkat | Expected Result | Status |
|---|---|---|---|---|
| ADMIN-SP-18 | List audit logs tampil | Buka `audit_logs.php` default (30 hari terakhir) | Tabel terisi data log perubahan status booking | `[x]` |
| ADMIN-SP-19 | Filter tanggal valid | Set date_from dan date_to, klik Filter | Hanya log dalam rentang tanggal yang ditampilkan | `[x]` |
| ADMIN-SP-20 | Filter reset | Klik link Reset | Filter kembali ke default 30 hari, data reload | `[x]` |
| ADMIN-SP-21 | Badge status warna benar | Cek badge kolom Dari/Ke Status | cancelled = merah, completed/paid = hijau, lainnya = abu | `[x]` |
| ADMIN-SP-22 | Kolom role tampil | Cek kolom Role di tabel | Menampilkan role user yang mengubah (admin/mekanik/sistem) | `[x]` |
| ADMIN-SP-23 | Read-only — tidak ada tombol edit/hapus | Periksa seluruh tabel audit logs | Tidak ada tombol Edit, Hapus, atau form modifikasi | `[x]` |
| ADMIN-SP-24 | Empty state filter tidak ada log | Set rentang tanggal di mana tidak ada log | Muncul pesan "Tidak ada log ditemukan untuk periode ini" | `[x]` |
| ADMIN-SP-25 | Pagination audit logs — navigasi | Klik halaman 2 jika log > 20 | Halaman 2 tampil, filter tanggal tetap terbawa di URL | `[x]` |
| ADMIN-SP-26 | Pagination audit logs — filter terbawa | Filter tanggal lalu pindah halaman | Parameter date_from & date_to tetap ada di URL halaman 2 | `[x]` |

---

## 4. Reports

| ID | Skenario | Langkah Singkat | Expected Result | Status |
|---|---|---|---|---|
| ADMIN-SP-27 | Filter bulan/tahun | Pilih bulan dan tahun, klik Tampilkan | Tabel dan summary card hanya menampilkan data periode tersebut | `[x]` |
| ADMIN-SP-28 | Summary card Total Booking | Cek card vs jumlah baris tabel | Angka card = jumlah baris di tabel | `[x]` |
| ADMIN-SP-29 | Summary card Total Revenue | Cek card vs jumlah manual dari booking status paid | Hanya menjumlahkan payment status = paid | `[x]` |
| ADMIN-SP-30 | Summary card Booking Selesai | Cek card Booking Selesai | Menghitung status completed + ready_for_pickup | `[x]` |
| ADMIN-SP-31 | Summary card Booking Dibatalkan | Cek card Booking Dibatalkan | Menghitung status cancelled | `[x]` |
| ADMIN-SP-32 | Export Excel — file terdownload | Klik Export Excel | File `.xlsx` terdownload dengan nama `laporan-revvo-{bulan}-{tahun}.xlsx` | `[x]` |
| ADMIN-SP-33 | Export Excel — isi data benar | Buka file xlsx, cek isi | Kolom lengkap, data sesuai periode, TOTAL REVENUE = nilai paid saja | `[x]` |
| ADMIN-SP-34 | Export PDF — file terdownload | Klik Export PDF | File `.pdf` terdownload dengan nama `laporan-revvo-{bulan}-{tahun}.pdf` | `[x]` |
| ADMIN-SP-35 | Export PDF — isi data benar | Buka file pdf, cek isi | Summary 4 angka benar, tabel lengkap, total revenue = paid saja | `[x]` |
| ADMIN-SP-36 | Empty state tidak ada transaksi | Pilih bulan yang tidak ada booking | Tabel menampilkan pesan kosong, summary card semua 0 | `[x]` |

---

## 5. Dashboard

| ID | Skenario | Langkah Singkat | Expected Result | Status |
|---|---|---|---|---|
| ADMIN-SP-37 | Total revenue card tampil | Buka dashboard admin | Card Total Revenue menampilkan angka dari payment status paid | `[x]` |
| ADMIN-SP-38 | Alert stok rendah tampil | Pastikan ada spare part dengan stok ≤ minimum_stock | Alert / daftar part stok rendah tampil di dashboard | `[x]` |
| ADMIN-SP-39 | Alert stok rendah tidak tampil | Set semua stok di atas minimum | Alert stok rendah tidak muncul | `[x]` |
| ADMIN-SP-40 | Link ke Reports berfungsi | Klik link/tombol menuju Reports dari dashboard | Redirect ke `pages/admin/reports.php` | `[x]` |

---

## Ringkasan Status

| Kategori | Total | Lulus | Gagal | Belum Diuji |
|---|---|---|---|---|
| Spare Parts CRUD & Validasi | 11 | 11 | 0 | 0 |
| Pagination Spare Parts | 6 | 6 | 0 | 0 |
| Audit Logs | 9 | 9 | 0 | 0 |
| Reports | 10 | 10 | 0 | 0 |
| Dashboard | 4 | 4 | 0 | 0 |
| **Total** | **40** | **40** | **0** | **0** |
