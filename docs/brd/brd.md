# 📑 Business Requirements Document (BRD)

## Sistem Booking & Tracking Servis Bengkel Motor — REVVO

| Item | Detail |
|------|--------|
| **Versi Dokumen** | 1.0 |
| **Tanggal** | [TBD - tanggal pembuatan] |
| **Status** | Draft (akan di-update setelah wawancara) |
| **Penyusun** | Kelompok: Geral, Nugi, Dermawan, Raika, Ahmad |
| **Stakeholder Narasumber** | [Nama pemilik bengkel] - [Nama bengkel] |
| **Mata Kuliah** | Pemrograman Web Dasar |

---

## 1. Executive Summary

> **Catatan**: Bagian ini diisi setelah seluruh BRD selesai. Maksimal 1 paragraf.

Sistem Booking & Tracking Servis Bengkel Motor adalah aplikasi berbasis web yang bertujuan mendigitalisasi proses booking servis motor pada bengkel skala UMKM di Indonesia. Sistem ini memungkinkan customer melakukan booking servis secara online, admin mengelola operasional bengkel, dan mekanik meng-update status pengerjaan secara real-time. Dengan adanya sistem ini, diharapkan dapat mengurangi double-booking, meningkatkan transparansi proses servis, dan menyederhanakan pelaporan keuangan bulanan.

---

## 2. Latar Belakang

### 2.1 Konteks Industri

Industri bengkel motor di Indonesia mayoritas dijalankan oleh UMKM dengan proses pencatatan manual. Berdasarkan pengamatan dan wawancara dengan pemilik bengkel, ditemukan beberapa kelemahan dalam workflow saat ini:

- *[Pain point 1 dari wawancara]*
- *[Pain point 2 dari wawancara]*
- *[Pain point 3 dari wawancara]*

### 2.2 Latar Belakang Akademik

Proyek ini dikerjakan sebagai tugas Ujian Akhir Semester (UAS) mata kuliah Pemrograman Web Dasar. Kelompok memilih domain ini karena:

1. Memiliki workflow yang kompleks dengan state machine yang jelas
2. Relevan dengan kebutuhan UMKM lokal yang ingin go-digital
3. Memungkinkan implementasi seluruh requirement teknis (multi-role, upload, reporting)

---

## 3. Tujuan Bisnis (Business Objectives)

| # | Tujuan | KPI / Indikator Keberhasilan |
|---|--------|-------------------------------|
| BO-1 | Mengurangi waktu yang dihabiskan untuk pencatatan manual | Penghematan waktu admin minimal 30% |
| BO-2 | Mencegah double-booking pada slot waktu yang sama | Zero double-booking incident |
| BO-3 | Meningkatkan transparansi status pengerjaan ke customer | Customer dapat tracking status tanpa telepon |
| BO-4 | Mempermudah pelaporan keuangan bulanan | Laporan dapat di-generate dalam ≤ 30 detik |
| BO-5 | Mempermudah monitoring stok sparepart | Alert otomatis saat stok di bawah minimum |

---

## 4. Scope Proyek

### 4.1 In Scope ✅

Fitur yang **termasuk** dalam proyek ini:

- Multi-role authentication (Admin, Mekanik, Customer)
- CRUD data master (users, mechanics, service types, spare parts, time slots)
- Booking online oleh customer dengan validasi double-booking
- State machine status booking (Antri → Dikerjakan → Selesai → Siap Diambil)
- Audit log perubahan status
- Upload foto kondisi motor
- Manajemen sparepart dengan auto-kurang stok
- Konfirmasi pembayaran (manual, tidak terintegrasi payment gateway)
- Generate invoice PDF per transaksi
- Generate laporan bulanan PDF/Excel
- Dashboard ringkasan per role

### 4.2 Out of Scope ❌

Fitur yang **tidak termasuk** dan menjadi future improvement:

- Integrasi payment gateway (Midtrans, Xendit, dll)
- Notifikasi via WhatsApp / Email otomatis
- Aplikasi mobile (mobile responsive web only)
- Integrasi dengan sistem akuntansi
- Multi-cabang bengkel
- E-commerce sparepart untuk customer
- Rating dan review customer
- Membership / loyalty program

---

## 5. Stakeholder

| Role | Deskripsi | Kebutuhan Utama |
|------|-----------|------------------|
| **Pemilik Bengkel** | Owner yang memantau performa bisnis | Laporan bulanan, dashboard analytics |
| **Admin Bengkel** | Operator harian, mengelola booking & customer | Interface cepat untuk assign mekanik |
| **Mekanik** | Pengerjaan fisik servis motor | UI sederhana untuk update status & input parts |
| **Customer** | Pelanggan yang melakukan servis | Booking mudah, transparansi status |
| **Dosen Pengampu** | Evaluator proyek akademik | Sesuai rubrik penilaian |

---

## 6. Current Process (As-Is)

> Diisi berdasarkan hasil wawancara.

### 6.1 Proses Booking Saat Ini

*[Deskripsikan alur booking manual di bengkel narasumber]*

Contoh placeholder:
1. Customer datang/telepon ke bengkel
2. Admin mencatat di buku tulis
3. ...

### 6.2 Proses Pengerjaan Saat Ini

*[Deskripsikan alur dari motor diterima sampai diambil customer]*

### 6.3 Pencatatan Saat Ini

*[Format pencatatan: buku, Excel, atau lainnya]*

---

## 7. Proposed Process (To-Be)

### 7.1 Booking Lifecycle (Sistem Baru)

```
[Customer] Booking online → status: Antri
    ↓
[Admin] Assign mekanik
    ↓
[Mekanik] Mulai kerja → status: Dikerjakan
    ↓
[Mekanik] Selesai + input sparepart → status: Selesai
    ↓
[Admin] Verifikasi + konfirmasi bayar → status: Siap Diambil
    ↓
[Customer] Datang ambil motor + download invoice
```

### 7.2 Improvement vs As-Is

| Aspek | As-Is | To-Be | Manfaat |
|-------|-------|-------|---------|
| Booking | Telepon/datang langsung | Online via web | Customer tidak perlu telepon |
| Pencatatan | Buku tulis | Database digital | Pencarian cepat, tidak hilang |
| Status Servis | Customer harus telepon tanya | Real-time tracking di web | Mengurangi telepon ke admin |
| Stok Sparepart | Cek manual | Auto-kurang + alert | Tidak kelupaan stok habis |
| Laporan Bulanan | Hitung manual | Generate otomatis | Hemat waktu, akurat |

---

## 8. Functional Requirements (FR)

### 8.1 Authentication & Authorization

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-AUTH-01 | Sistem harus menyediakan halaman login untuk seluruh role | MUST |
| FR-AUTH-02 | Sistem harus menyimpan password dalam bentuk hash (bcrypt) | MUST |
| FR-AUTH-03 | Customer harus bisa register akun sendiri | MUST |
| FR-AUTH-04 | Admin dan Mekanik di-create oleh Admin (tidak self-register) | MUST |
| FR-AUTH-05 | Setiap halaman protected harus mengecek role user | MUST |
| FR-AUTH-06 | Sistem harus menyediakan fungsi logout yang menghapus session | MUST |

### 8.2 Master Data Management (Admin)

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-MD-01 | Admin dapat CRUD jenis layanan (nama, durasi, harga) | MUST |
| FR-MD-02 | Admin dapat CRUD data mekanik (nama, spesialisasi, status) | MUST |
| FR-MD-03 | Admin dapat CRUD sparepart (nama, stok, harga, stok minimum) | MUST |
| FR-MD-04 | Admin dapat set time slots operasional bengkel | MUST |
| FR-MD-05 | Admin dapat melihat list customer terdaftar | MUST |

### 8.3 Booking Management

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-BK-01 | Customer dapat membuat booking baru dengan memilih motor, layanan, tanggal, slot | MUST |
| FR-BK-02 | Sistem harus mencegah double-booking pada slot yang sudah penuh | MUST |
| FR-BK-03 | Customer dapat upload foto kondisi motor saat booking | MUST |
| FR-BK-04 | Sistem harus menyimpan snapshot harga saat booking dibuat | MUST |
| FR-BK-05 | Customer dapat melihat status booking secara real-time | MUST |
| FR-BK-06 | Customer dapat membatalkan booking yang masih status "Antri" | SHOULD |
| FR-BK-07 | Admin dapat assign mekanik ke booking yang masuk | MUST |
| FR-BK-08 | Admin dapat melihat semua booking dengan filter & search | MUST |

### 8.4 Operations (Mekanik)

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-OP-01 | Mekanik dapat melihat daftar tugas yang di-assign | MUST |
| FR-OP-02 | Mekanik dapat update status pengerjaan (Antri → Dikerjakan → Selesai) | MUST |
| FR-OP-03 | Mekanik dapat menambahkan sparepart yang dipakai (auto-kurang stok) | MUST |
| FR-OP-04 | Mekanik dapat menulis catatan pengerjaan | MUST |
| FR-OP-05 | Sistem harus mencatat audit log setiap perubahan status | MUST |

### 8.5 Payment & Invoice

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-PAY-01 | Admin dapat konfirmasi pembayaran (cash/transfer/ewallet) | MUST |
| FR-PAY-02 | Sistem menghitung total harga = harga jasa + sparepart yang dipakai | MUST |
| FR-PAY-03 | Customer dapat download invoice PDF per transaksi yang selesai | MUST |
| FR-PAY-04 | Setelah pembayaran dikonfirmasi, status booking jadi "Siap Diambil" | MUST |

### 8.6 Dashboard & Reporting

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-RPT-01 | Admin dashboard menampilkan ringkasan total booking, revenue, mekanik aktif | MUST |
| FR-RPT-02 | Admin dashboard menampilkan alert sparepart yang stoknya di bawah minimum | MUST |
| FR-RPT-03 | Admin dapat generate laporan bulanan dalam format PDF | MUST |
| FR-RPT-04 | Admin dapat generate laporan bulanan dalam format Excel | MUST |
| FR-RPT-05 | Laporan menampilkan ringkasan + tabel detail transaksi | MUST |
| FR-RPT-06 | Admin dapat melihat audit log perubahan status | SHOULD |

---

## 9. Non-Functional Requirements (NFR)

| ID | Kategori | Requirement |
|----|----------|-------------|
| NFR-01 | **Performance** | Halaman list booking harus load dalam ≤ 2 detik untuk 100 record |
| NFR-02 | **Performance** | Generate laporan PDF/Excel maksimal 30 detik untuk data 1 bulan |
| NFR-03 | **Security** | Semua password disimpan dalam hash bcrypt (cost ≥ 10) |
| NFR-04 | **Security** | Semua query database menggunakan Prepared Statement |
| NFR-05 | **Security** | Output yang menampilkan data user harus di-sanitize (XSS prevention) |
| NFR-06 | **Usability** | UI harus responsive minimal di resolusi desktop 1024px |
| NFR-07 | **Usability** | Pesan error harus informatif tapi tidak membocorkan info teknis |
| NFR-08 | **Reliability** | Audit log tidak dapat diubah atau dihapus (immutable) |
| NFR-09 | **Maintainability** | Folder dan file ter-organisasi sesuai struktur standar |
| NFR-10 | **Compatibility** | Sistem dapat dijalankan di Laragon, XAMPP, dan MAMP |

---

## 10. Asumsi & Batasan

### 10.1 Asumsi

- Pengguna sistem memiliki akses internet stabil
- Admin dan mekanik memiliki akses ke komputer/laptop di bengkel
- Customer memiliki smartphone dengan browser modern (Chrome, Safari, Firefox)
- Pembayaran dilakukan secara manual (offline), sistem hanya mencatat status

### 10.2 Batasan

- Sistem tidak menangani transaksi pembayaran online (no payment gateway)
- Sistem tidak mengirim notifikasi otomatis (WA, email)
- Bengkel hanya 1 cabang (tidak multi-tenant)
- Bahasa interface hanya Bahasa Indonesia
- Sistem berbasis web, bukan aplikasi mobile native

---

## 11. Kriteria Keberhasilan (Success Criteria)

Proyek dianggap **berhasil** jika:

1. ✅ Seluruh fitur dalam scope berjalan tanpa error pada minimal 2 entitas CRUD
2. ✅ Sistem dapat di-akses online melalui domain & hosting yang aktif
3. ✅ Demo skenario booking end-to-end berjalan lancar
4. ✅ Laporan PDF/Excel dapat di-generate dengan data yang akurat
5. ✅ Setiap anggota kelompok mampu menjelaskan kontribusi codenya
6. ✅ Mendapat nilai akhir minimal 85 dari dosen

---

## 12. Glossary

| Istilah | Definisi |
|---------|----------|
| **Antri** | Status awal booking setelah customer submit, menunggu assign mekanik |
| **Dikerjakan** | Status saat mekanik sedang mengerjakan servis |
| **Selesai** | Status saat mekanik telah menyelesaikan pengerjaan, menunggu verifikasi admin |
| **Siap Diambil** | Status final saat pembayaran sudah dikonfirmasi, customer bisa ambil motor |
| **Dibatalkan** | Status saat booking dibatalkan (oleh customer atau admin) |
| **Snapshot Harga** | Harga yang di-copy ke transaksi saat dibuat, agar histori tetap akurat meski harga master berubah |
| **Audit Log** | Catatan immutable setiap perubahan status booking (siapa, kapan, dari status apa) |
| **Double-Booking** | Kondisi dimana 2+ customer book slot waktu yang sama melebihi kapasitas |

---

## 13. Approval & Sign-Off

| Role | Nama | Tanda Tangan | Tanggal |
|------|------|--------------|---------|
| Pemilik Bengkel (Stakeholder) | [Nama] | | |
| Project Lead | Geral | | |
| Dosen Pengampu | [Nama Dosen] | | |

---

## 📎 Lampiran

- `wawancara.md` — Pertanyaan & hasil wawancara stakeholder
- `foundation.md` — Spesifikasi teknis & flow
- `erd.md` — Entity Relationship Diagram
- `plan.md` — Roadmap pengerjaan & pembagian modul
- `mockup.html` — Wireframe / mockup UI

---

> **Catatan**: Bagian yang ditandai `[TBD]` atau placeholder harus diisi setelah wawancara dengan stakeholder dilakukan. BRD ini bersifat **living document** — di-update seiring berjalannya proyek jika ada perubahan requirement.