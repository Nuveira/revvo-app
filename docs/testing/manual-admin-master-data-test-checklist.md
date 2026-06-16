# Manual Admin Master Data Test Checklist

Checklist ini digunakan sebagai bukti pengujian manual fitur Master Data yang dikembangkan pada REVVO Admin Panel.

## Scope

* Mechanics Management
* Service Types Management
* Time Slots Management
* Search, Filter, dan Sorting
* Custom Pagination
* Delete Confirmation Modal

## Test Data

Akun Admin:

* Email: `admin@bengkel.com`
* Password: `password123`

Referensi data: `database/revvo.sql`

---

# 1. Mechanics Management

| ID           | Skenario                                   | Langkah Singkat                  | Expected Result                    | Status |
| ------------ | ------------------------------------------ | -------------------------------- | ---------------------------------- | ------ |
| ADMIN-MEC-01 | List mechanic tampil                       | Buka halaman mechanics           | Data mechanic tampil pada tabel    | `[x]`  |
| ADMIN-MEC-02 | Tambah mechanic valid                      | Tambah mechanic baru lalu simpan | Data berhasil ditambahkan          | `[x]`  |
| ADMIN-MEC-03 | Edit specialization mechanic               | Ubah specialization lalu simpan  | Data berhasil diperbarui           | `[x]`  |
| ADMIN-MEC-04 | Edit availability status                   | Ubah status availability         | Status berhasil diperbarui         | `[x]`  |
| ADMIN-MEC-05 | Edit notes mechanic                        | Ubah notes lalu simpan           | Notes berhasil diperbarui          | `[x]`  |
| ADMIN-MEC-06 | Hapus mechanic                             | Klik hapus dan konfirmasi        | Data mechanic terhapus             | `[x]`  |
| ADMIN-MEC-07 | Search mechanic berdasarkan nama           | Cari nama mechanic               | Data sesuai pencarian tampil       | `[x]`  |
| ADMIN-MEC-08 | Search mechanic berdasarkan specialization | Cari specialization              | Data sesuai pencarian tampil       | `[x]`  |
| ADMIN-MEC-09 | Filter availability status                 | Pilih filter status              | Data terfilter sesuai status       | `[x]`  |
| ADMIN-MEC-10 | Sorting ID                                 | Klik header ID                   | Data terurut sesuai ID             | `[x]`  |
| ADMIN-MEC-11 | Sorting nama mechanic                      | Klik header nama                 | Data terurut sesuai nama           | `[x]`  |
| ADMIN-MEC-12 | Sorting specialization                     | Klik header specialization       | Data terurut sesuai specialization | `[x]`  |

---

# 2. Service Types Management

| ID           | Skenario                 | Langkah Singkat            | Expected Result                | Status |
| ------------ | ------------------------ | -------------------------- | ------------------------------ | ------ |
| ADMIN-SRV-01 | List service type tampil | Buka halaman service types | Data layanan tampil pada tabel | `[x]`  |
| ADMIN-SRV-02 | Tambah service type      | Tambah layanan baru        | Data berhasil ditambahkan      | `[x]`  |
| ADMIN-SRV-03 | Edit service type        | Ubah data layanan          | Data berhasil diperbarui       | `[x]`  |
| ADMIN-SRV-04 | Hapus service type       | Klik hapus dan konfirmasi  | Data berhasil dihapus          | `[x]`  |
| ADMIN-SRV-05 | Search service type      | Cari nama layanan          | Data sesuai pencarian tampil   | `[x]`  |
| ADMIN-SRV-06 | Sorting nama layanan     | Klik header nama layanan   | Data terurut sesuai nama       | `[x]`  |
| ADMIN-SRV-07 | Sorting harga            | Klik header harga          | Data terurut sesuai harga      | `[x]`  |
| ADMIN-SRV-08 | Sorting durasi           | Klik header durasi         | Data terurut sesuai durasi     | `[x]`  |

---

# 3. Time Slots Management

| ID          | Skenario                | Langkah Singkat           | Expected Result                 | Status |
| ----------- | ----------------------- | ------------------------- | ------------------------------- | ------ |
| ADMIN-TS-01 | List time slot tampil   | Buka halaman time slots   | Data slot tampil pada tabel     | `[x]`  |
| ADMIN-TS-02 | Tambah time slot        | Tambah slot baru          | Data berhasil ditambahkan       | `[x]`  |
| ADMIN-TS-03 | Edit time slot          | Ubah data slot            | Data berhasil diperbarui        | `[x]`  |
| ADMIN-TS-04 | Hapus time slot         | Klik hapus dan konfirmasi | Data berhasil dihapus           | `[x]`  |
| ADMIN-TS-05 | Filter berdasarkan hari | Pilih filter hari         | Data sesuai filter tampil       | `[x]`  |
| ADMIN-TS-06 | Sorting hari            | Klik header hari          | Data terurut sesuai hari        | `[x]`  |
| ADMIN-TS-07 | Sorting jam mulai       | Klik header start time    | Data terurut sesuai jam mulai   | `[x]`  |
| ADMIN-TS-08 | Sorting jam selesai     | Klik header end time      | Data terurut sesuai jam selesai | `[x]`  |

---

# 4. Custom Pagination

| ID           | Skenario                               | Langkah Singkat             | Expected Result                 | Status |
| ------------ | -------------------------------------- | --------------------------- | ------------------------------- | ------ |
| ADMIN-PAG-01 | Navigasi halaman berikutnya            | Klik tombol Next            | Berpindah ke halaman berikutnya | `[x]`  |
| ADMIN-PAG-02 | Navigasi halaman sebelumnya            | Klik tombol Previous        | Berpindah ke halaman sebelumnya | `[x]`  |
| ADMIN-PAG-03 | Navigasi nomor halaman                 | Klik nomor halaman tertentu | Halaman sesuai tampil           | `[x]`  |
| ADMIN-PAG-04 | Previous disabled pada halaman pertama | Buka halaman pertama        | Tombol Previous tidak aktif     | `[x]`  |
| ADMIN-PAG-05 | Next disabled pada halaman terakhir    | Buka halaman terakhir       | Tombol Next tidak aktif         | `[x]`  |
| ADMIN-PAG-06 | Informasi halaman tampil benar         | Cek total halaman dan data  | Informasi sesuai data sistem    | `[x]`  |

---

# 5. Delete Confirmation Modal

| ID           | Skenario           | Langkah Singkat              | Expected Result                     | Status |
| ------------ | ------------------ | ---------------------------- | ----------------------------------- | ------ |
| ADMIN-MOD-01 | Modal hapus tampil | Klik tombol Hapus            | Modal konfirmasi muncul             | `[x]`  |
| ADMIN-MOD-02 | Tombol batal       | Klik tombol Batal            | Modal tertutup tanpa menghapus data | `[x]`  |
| ADMIN-MOD-03 | Klik area overlay  | Klik area luar modal         | Modal tertutup                      | `[x]`  |
| ADMIN-MOD-04 | Konfirmasi hapus   | Klik tombol Hapus pada modal | Data berhasil dihapus               | `[x]`  |

---

# Ringkasan Status

| Kategori                  | Total  | Lulus  | Gagal | Belum Diuji |
| ------------------------- | ------ | ------ | ----- | ----------- |
| Mechanics Management      | 12     | 12     | 0     | 0           |
| Service Types Management  | 8      | 8      | 0     | 0           |
| Time Slots Management     | 8      | 8      | 0     | 0           |
| Custom Pagination         | 6      | 6      | 0     | 0           |
| Delete Confirmation Modal | 4      | 4      | 0     | 0           |
| **Total**                 | **38** | **38** | **0** | **0**       |
