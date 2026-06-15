# Penjelasan Kode Bagian Geral — REVVO

> Dokumen ini menjelaskan **semua file yang Geral kerjakan** dalam bahasa Indonesia yang mudah dipahami.
> Cocok untuk persiapan presentasi ke dosen.

---

## Daftar File yang Geral Kerjakan

```
config/
  app.php                          ← helper URL & asset path
  koneksi.php                      ← koneksi database

includes/
  auth.php                         ← fungsi proteksi halaman per role
  functions.php                    ← fungsi-fungsi pembantu global
  header.php                       ← template HTML head (Tailwind, font, icon)
  customer_role.php                ← guard + set $user_id, $customer_id, $nama

pages/
  403.php                          ← halaman "akses ditolak"
  auth/
    login.php                      ← tampilan form login
    proses_login.php               ← logic login
    register.php                   ← tampilan form register
    proses_register.php            ← logic register
    logout.php                     ← halaman konfirmasi logout
    proses_logout.php              ← logic logout (hapus session)
  admin/
    users.php                      ← CRUD users (list + form create/edit)
    proses_users.php               ← backend create/edit/delete user
  customer/
    invoice.php                    ← generate invoice PDF via DomPDF
    profile.php                    ← form edit profil customer
    proses_profile.php             ← backend update profil + upload foto profil
```

---

## BAGIAN 1 — FOUNDATION (Fondasi)

> File-file ini dipakai oleh SEMUA anggota tim. Geral yang bikin.

---

### `config/app.php`

**Apa fungsinya?**
File ini menghitung "alamat dasar" aplikasi secara otomatis, lalu membuat dua fungsi pembantu: `url()` dan `asset()`.

**Kenapa perlu?**
Karena aplikasi ini bisa diakses dari berbagai kedalaman folder (misalnya `/pages/admin/` vs `/pages/customer/`), link dan path gambar harus selalu benar. Tanpa ini, link bisa rusak tergantung dari folder mana kita akses.

```php
// Cara pakai:
url('pages/auth/login.php')    // → /revvo-app/pages/auth/login.php
asset('assets/images/logo.png') // → /revvo-app/assets/images/logo.png
```

**Yang perlu dijelasin ke dosen:**
`BASE_URL` dihitung dari `$_SERVER['DOCUMENT_ROOT']` dibandingkan dengan lokasi folder project. Ini membuat aplikasi bisa jalan di subfolder manapun tanpa hardcode URL.

---

### `config/koneksi.php`

**Apa fungsinya?**
Membuat koneksi ke database MySQL dan menyimpannya di variabel `$conn`.

```php
$conn = new mysqli('localhost', 'root', '', 'revvo');
$conn->set_charset('utf8mb4');
```

**Kenapa `utf8mb4`?**
`utf8mb4` mendukung semua karakter Unicode termasuk emoji. `utf8` biasa di MySQL hanya support 3 byte per karakter, sehingga karakter tertentu bisa error saat disimpan.

**Yang perlu dijelasin ke dosen:**
Setiap file PHP yang butuh database cukup `require_once '../../config/koneksi.php'` untuk mendapatkan `$conn`. Koneksi dibuat sekali, dipakai di seluruh halaman.

---

### `includes/auth.php`

**Apa fungsinya?**
Menyediakan fungsi `checkRole()` yang melindungi setiap halaman dari akses tidak sah.

```php
function checkRole(array $allowed_roles) {
    // Kalau belum login → redirect ke login
    if (empty($_SESSION['user_id'])) {
        header('Location: .../login.php');
        exit;
    }
    // Kalau role tidak sesuai → redirect ke 403
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header('Location: .../403.php');
        exit;
    }
}
```

**Cara pakainya:**
Di setiap halaman, baris pertama setelah `require` adalah:
```php
checkRole(['admin']);     // hanya admin
checkRole(['customer']);  // hanya customer
checkRole(['mechanic']);  // hanya mekanik
```

**Yang perlu dijelasin ke dosen:**
Ini adalah "middleware" — kode yang berjalan sebelum konten halaman ditampilkan. Kalau tidak lolos pengecekan, user langsung diarahkan dan kode di bawahnya tidak dijalankan (`exit`).

---

### `includes/functions.php`

**Apa fungsinya?**
Kumpulan fungsi-fungsi pembantu kecil yang dipakai di banyak halaman.

| Fungsi | Kegunaan | Contoh |
|--------|----------|--------|
| `format_tanggal($tgl)` | Konversi tanggal ke format Indonesia | `2026-06-14` → `14 Juni 2026` |
| `format_rupiah($angka)` | Format angka jadi Rupiah | `150000` → `Rp 150.000` |
| `potong_teks($teks, $batas)` | Potong teks panjang + tambah `...` | `"Ini teks panjang..."` → `"Ini teks..."` |
| `redirect($url)` | Redirect ke halaman lain | — |
| `sudah_login()` | Cek apakah user sudah login | returns `true`/`false` |
| `get_role()` | Ambil role user yang sedang login | `"admin"`, `"customer"`, dll |
| `get_nama()` | Ambil nama user yang login | — |

---

### `includes/header.php`

**Apa fungsinya?**
Template HTML bagian `<head>` yang di-include di semua halaman. Berisi:
- Load Tailwind CSS dari CDN
- Load Google Fonts (Plus Jakarta Sans, Bricolage Grotesque, JetBrains Mono)
- Load Lucide Icons
- Link ke custom CSS
- Set `$pageTitle` dinamis

**Yang perlu dijelasin ke dosen:**
Daripada menulis `<head>` berulang di setiap halaman, cukup `require_once 'includes/header.php'`. Ini menjaga konsistensi tampilan dan memudahkan perubahan global.

---

### `includes/customer_role.php`

**Apa fungsinya?**
Guard khusus halaman customer yang menggabungkan dua tugas sekaligus: proteksi role dan pengambilan data user dari database.

```php
checkRole(['customer']);           // tolak kalau bukan customer

$user_id = $_SESSION['user_id'];
$nama = '...';                     // ambil dari DB
$profile_photo = '...';           // ambil dari DB
$customer_id = ...;               // ambil dari tabel customers
```

**Kenapa bukan langsung pakai `checkRole()`?**
Halaman customer butuh tiga variabel: `$user_id`, `$customer_id`, dan `$nama`. Kalau tiap halaman query sendiri, kode duplikat. `customer_role.php` memusatkan ini dalam satu file — include sekali, semua variabel sudah siap.

**Yang perlu dijelasin ke dosen:**
Ini adalah versi customer-specific dari auth guard. Bedanya dengan `auth.php`: selain cek role, juga sekalian query `customers.id` dari DB karena customer pages butuh `$customer_id`, bukan hanya `$user_id` dari session.

---

## BAGIAN 2 — AUTH (Autentikasi)

> Semua file di dalam `pages/auth/` — Geral yang buat semuanya.

---

### `pages/auth/login.php` — Tampilan Form Login

**Apa ini?**
Halaman yang menampilkan form login saja. Tidak ada logic di sini.

**Cara kerjanya:**
1. Ambil pesan error dari session kalau ada (misalnya "Email atau password salah")
2. Hapus pesan dari session agar tidak muncul lagi setelah refresh
3. Tampilkan form dengan input email dan password
4. Form dikirim ke `proses_login.php` via `method="POST"`

**Yang perlu dijelasin ke dosen:**
Pemisahan antara tampilan (`login.php`) dan logic (`proses_login.php`) adalah pola PRG (Post-Redirect-Get). Ini mencegah data form terkirim ulang jika user me-refresh halaman.

---

### `pages/auth/proses_login.php` — Logic Login

**Apa ini?**
Backend yang memproses form login. Tidak menampilkan HTML apapun — hanya memproses data lalu redirect.

**Alur kode langkah per langkah:**

```
1. Tolak request selain POST (kalau URL diakses langsung via browser)
   ↓
2. Ambil email dan password dari form
   ↓
3. Validasi: email dan password tidak boleh kosong
   ↓
4. Query database:
   SELECT * FROM users WHERE email = ? AND status = 'active'
   → Pakai Prepared Statement (PENTING: cegah SQL injection)
   ↓
5. Kalau user ditemukan:
   → password_verify($password, $user['password_hash'])
   → Bandingkan password yang diketik dengan hash yang tersimpan di DB
   ↓
6. Kalau cocok: simpan ke session
   $_SESSION['user_id'] = $user['id']
   $_SESSION['role']    = $user['role']
   $_SESSION['name']    = $user['name']
   ↓
7. Redirect sesuai role:
   admin    → /pages/admin/dashboard.php
   mechanic → /pages/mekanik/dashboard.php
   customer → /pages/customer/dashboard.php
   ↓
8. Kalau tidak cocok: simpan error ke session → redirect kembali ke login.php
```

**Kenapa `password_verify()` bukan cek langsung?**
Password di database disimpan dalam bentuk *hash* (hasil enkripsi satu arah pakai bcrypt). Tidak bisa dibandingkan langsung dengan `===`. Fungsi `password_verify()` mengolah password yang diketik user dan membandingkannya secara aman dengan hash.

**Kenapa Prepared Statement?**
Kalau tanpa Prepared Statement:
```php
// BERBAHAYA — SQL Injection!
$query = "SELECT * FROM users WHERE email = '$email'";
```
Kalau user mengisi email: `admin@test.com' OR '1'='1`, query jadi:
```sql
SELECT * FROM users WHERE email = 'admin@test.com' OR '1'='1'
```
Ini akan return semua user! Dengan Prepared Statement, input user diperlakukan sebagai data biasa, bukan bagian dari query SQL.

---

### `pages/auth/register.php` — Tampilan Form Register

**Apa ini?**
Halaman form pendaftaran untuk customer baru. Ada 5 input: Nama Lengkap, Email, No. HP, Password, Konfirmasi Password.

**Yang perlu dijelasin ke dosen:**
Register hanya untuk customer. Admin dan mechanic tidak bisa daftar sendiri — harus dibuat oleh admin lewat halaman Users.

---

### `pages/auth/proses_register.php` — Logic Register

**Alur kode langkah per langkah:**

```
1. Tolak request selain POST
   ↓
2. Ambil: name, email, phone, password, confirm_password dari form
   ↓
3. Validasi bertahap:
   a. Semua field tidak boleh kosong
   b. password === confirm_password (harus sama persis)
   c. Password minimal 6 karakter
   ↓
4. Cek email duplikat:
   SELECT id FROM users WHERE email = ?
   → Kalau sudah ada → redirect dengan pesan "Email sudah terdaftar"
   ↓
5. Hash password:
   $hashed = password_hash($password, PASSWORD_DEFAULT)
   → Hasilnya sesuatu seperti: $2y$10$abc123...
   ↓
6. INSERT ke tabel users:
   INSERT INTO users (name, email, password_hash, role, phone, status)
   VALUES (?, ?, ?, 'customer', ?, 'active')
   → role selalu 'customer', status selalu 'active'
   ↓
7. INSERT ke tabel customers (WAJIB):
   INSERT INTO customers (user_id) VALUES (?)
   → Karena ada tabel customers terpisah yang berelasi dengan users
   ↓
8. Redirect ke register.php dengan pesan sukses
```

**Kenapa insert ke 2 tabel?**
Desain database REVVO memisahkan data akun (tabel `users`) dari data profil customer (tabel `customers`). Relasi ini one-to-one: satu user punya satu customer. Kalau hanya insert ke `users`, data customer tidak ada dan booking tidak bisa dilakukan.

---

### `pages/auth/logout.php` — Halaman Konfirmasi Logout

**Apa ini?**
Halaman yang menampilkan konfirmasi "Yakin mau keluar?" sebelum benar-benar logout. Jadi user tidak sengaja logout.

**Yang perlu dijelasin ke dosen:**
Logout tidak langsung terjadi — ada halaman konfirmasi dulu. Tombol "Ya, Keluar" mengirim POST ke `proses_logout.php`. Link "Batal" menggunakan `javascript:history.back()` untuk kembali ke halaman sebelumnya.

---

### `pages/auth/proses_logout.php` — Logic Logout

**Alur kode:**

```
1. Tolak request selain POST
   ↓
2. Kosongkan semua data session: $_SESSION = []
   ↓
3. Hapus session cookie dari browser:
   setcookie(session_name(), '', time() - 42000, ...)
   (waktu -42000 = sudah lewat = browser akan hapus cookie)
   ↓
4. Destroy session di server: session_destroy()
   ↓
5. Redirect ke login.php
```

**Kenapa 3 langkah (kosongkan + hapus cookie + destroy)?**
- `$_SESSION = []` → hapus data dari memori PHP saat ini
- `setcookie(... time()-42000)` → paksa browser hapus session cookie
- `session_destroy()` → hapus file session dari server

Kalau hanya `session_destroy()` tanpa dua langkah lainnya, session cookie masih ada di browser dan data session mungkin masih bisa diakses sebentar.

---

## BAGIAN 3 — HALAMAN 403

### `pages/403.php` — Akses Ditolak

**Apa ini?**
Halaman yang ditampilkan kalau user mencoba akses halaman yang bukan haknya.

```php
http_response_code(403); // set HTTP status code 403 Forbidden
```

**Yang perlu dijelasin ke dosen:**
`checkRole()` di `includes/auth.php` yang memanggil redirect ke halaman ini. HTTP status code 403 (bukan 200) memberitahu browser dan search engine bahwa halaman ini memang diblokir, bukan halaman biasa.

---

## BAGIAN 4 — CRUD USERS (Admin)

---

### `pages/admin/users.php` — Halaman Manajemen Users

**Apa fungsinya?**
Halaman utama CRUD users. Satu halaman ini menghandle: list users, form tambah, form edit, filter, sort, dan pagination.

**Bagian-bagian penting:**

#### A. Filter dan Search

```php
$filter_role   = $_GET['role'] ?? '';    // filter by role
$filter_status = $_GET['status'] ?? '';  // filter by status
$search        = $_GET['search'] ?? '';  // cari nama atau email
```

Query menggunakan kondisi dinamis:
```sql
WHERE (? = '' OR role = ?)
  AND (? = '' OR status = ?)
  AND (? = '' OR name LIKE ? OR email LIKE ?)
```
Trik ini: kalau filter kosong, kondisinya selalu true (tidak memfilter).

#### B. Sort dengan Whitelist

```php
$allowed_sort = ['id', 'name', 'email', 'role', 'status', 'created_at'];
$sort = in_array($_GET['sort'] ?? '', $allowed_sort) ? $_GET['sort'] : 'id';
$order = ($_GET['order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';
```

**Kenapa whitelist?**
Nama kolom tidak bisa dimasukkan sebagai parameter di Prepared Statement. Kalau langsung pakai `$_GET['sort']`, bisa SQL injection. Whitelist memastikan hanya kolom yang kita ijinkan yang bisa di-sort.

#### C. Pagination

```php
$per_page = 10;
$offset   = ($page - 1) * $per_page;
// ...
LIMIT ? OFFSET ?
```

Cara kerjanya: halaman 1 → offset 0 (mulai dari baris 0), halaman 2 → offset 10 (mulai dari baris 10), dst.

#### D. Form Create dan Edit (satu halaman)

Halaman yang sama menampilkan form create (`?show=create`) atau form edit (`?show=edit&id=X`) berdasarkan parameter GET. Ini membuat satu URL meng-handle tiga state: list, create form, edit form.

---

### `pages/admin/proses_users.php` — Backend CRUD Users

**Apa fungsinya?**
Memproses semua aksi POST dari users.php: create, edit, delete.

#### Fungsi `create_user()`

```
Ambil data form → validasi tidak kosong
→ cek email belum dipakai user lain
→ hash password
→ INSERT INTO users
→ redirect dengan msg=created
```

#### Fungsi `edit_user()`

```
Ambil data form → validasi tidak kosong
→ cek email tidak dipakai USER LAIN (WHERE email=? AND id!=?)
→ kalau password diisi: hash dan update sekalian
  kalau password kosong: update data lain, jangan sentuh password_hash
→ UPDATE users SET ... WHERE id=?
→ redirect dengan msg=updated
```

**Trik edit password opsional:**
```php
if ($pass !== '') {
    // pakai query dengan password_hash baru
    UPDATE users SET name=?, email=?, password_hash=?, ...
} else {
    // pakai query tanpa menyentuh kolom password_hash
    UPDATE users SET name=?, email=?, ...
}
```

#### Fungsi `delete_user()`

```
Cek id valid
→ Cek bukan id diri sendiri sendiri ($id !== $current_user_id)
→ DELETE FROM users WHERE id=?
→ redirect dengan msg=deleted
```

**Kenapa tidak bisa hapus diri sendiri?**
Kalau admin menghapus akunnya sendiri, session masih aktif tapi akunnya sudah tidak ada. Ini akan menyebabkan error karena session berisi `user_id` yang sudah tidak eksis di database. Pengecekan ini mencegah skenario tersebut.

---

## BAGIAN 5 — INVOICE PDF CUSTOMER

---

### `pages/customer/invoice.php` — Generate Invoice PDF

**Apa fungsinya?**
Generate dan download invoice PDF untuk booking yang sudah selesai. Menggunakan library DomPDF.

**Alur kode:**

```
1. Ambil booking_id dari GET parameter
   ↓
2. Query booking JOIN 7 tabel:
   bookings → customers → users (nama, email, phone)
             → motors (brand, model, plat)
             → service_types (nama layanan)
             → time_slots (jam servis)
             → mechanics → users (nama mekanik)
             → payments (status, metode, tanggal bayar)
   WHERE b.id = ? AND b.customer_id = ?   ← security!
   ↓
3. Validasi status: hanya 'completed' atau 'ready_for_pickup' boleh download invoice
   → Kalau masih queued/in_progress = redirect ke booking_detail.php
   ↓
4. Query spare parts yang dipakai (booking_parts JOIN spare_parts)
   ↓
5. Bangun HTML invoice dengan ob_start() / ob_get_clean()
   ↓
6. Pass HTML ke DomPDF → render → stream sebagai file download
```

**Teknik penting: Output Buffering**

```php
ob_start();         // mulai "rekam" output — tidak langsung kirim ke browser
?>
<!DOCTYPE html>     // seluruh HTML invoice ditulis di sini
<?php
$html = ob_get_clean();  // ambil semua output yang direkam sebagai string
// sekarang $html berisi seluruh HTML invoice
```

Kenapa perlu? DomPDF butuh HTML sebagai string, bukan dikirim langsung ke browser. `ob_start()` menangkap output PHP sementara.

**Kenapa hanya completed/ready_for_pickup?**
Invoice = bukti pembayaran. Booking yang masih `queued` atau `in_progress` belum ada kepastian harga final (spare parts bisa ditambah mekanik). Jadi invoice hanya boleh digenerate setelah servis benar-benar selesai.

**Yang perlu dijelasin ke dosen:**
- DomPDF convert HTML+CSS → PDF di server-side, tanpa browser.
- `$dompdf->stream($filename, ['Attachment' => true])` → trigger download di browser customer.
- Security: `AND b.customer_id = ?` memastikan customer tidak bisa download invoice milik orang lain.

---

## BAGIAN 6 — PROFILE CUSTOMER

---

### `pages/customer/profile.php` — Halaman Edit Profil

**Apa fungsinya?**
Halaman untuk customer mengubah data profil: nama, no HP, alamat, jenis kelamin, tanggal lahir, no KTP, dan foto profil.

**Data yang di-query:**
```sql
SELECT u.name, u.email, u.phone, u.profile_photo,
       c.address, c.gender, c.birth_date, c.no_ktp
FROM users u
LEFT JOIN customers c ON c.user_id = u.id
WHERE u.id = ?
```

Data tersebar di 2 tabel: `users` (data akun) dan `customers` (data profil). Satu query JOIN keduanya.

**Trik: Old Input setelah error**

```php
$oldInput = $_SESSION['profile_old'] ?? [];
// ...
if (!empty($oldInput)) {
    foreach ($oldInput as $key => $value) {
        $profileData[$key] = $value;  // overwrite dengan input sebelumnya
    }
}
```

Kalau validasi gagal di `proses_profile.php`, input user disimpan ke session lalu redirect kembali ke sini. Halaman ini membaca session tersebut dan mengisi ulang form — jadi user tidak perlu ketik ulang dari nol.

---

### `pages/customer/proses_profile.php` — Backend Update Profil

**Alur kode:**

```
1. Tolak request selain POST
   ↓
2. Simpan semua input ke $_SESSION['profile_old'] (untuk repopulate form jika error)
   ↓
3. Validasi bertahap:
   a. name dan phone tidak boleh kosong
   b. name maksimal 100 karakter
   c. phone maksimal 20 karakter
   d. gender harus 'male', 'female', atau kosong
   e. birth_date harus format Y-m-d yang valid
   f. no_ktp maksimal 20 karakter
   ↓
4. Kalau ada upload foto profil:
   → Cek MIME type (bukan extension): harus image/jpeg, image/png, atau image/webp
   → Cek ukuran: maksimal 2MB
   → Simpan ke uploads/profile/ dengan nama: profile_{user_id}_{timestamp}.ext
   → Hapus foto lama dari server
   ↓
5. UPDATE 2 tabel dalam satu transaction:
   UPDATE users SET name=?, phone=?, profile_photo=? WHERE id=?
   UPDATE customers SET address=?, gender=?, birth_date=?, no_ktp=? WHERE id=?
   ↓
6. Update $_SESSION['name'] supaya navbar langsung tampil nama baru
   ↓
7. Redirect ke profile.php dengan pesan sukses
```

**2 hal kritis yang harus bisa dijelasin:**

**MIME Type Validation (bukan cek extension):**
```php
$fileType = mime_content_type($_FILES['profile_photo']['tmp_name']);
if (!in_array($fileType, ['image/jpeg', 'image/png', 'image/webp'])) { ... }
```
Cek extension (`.jpg`) mudah di-bypass — user bisa rename `hack.php` jadi `hack.jpg`. MIME type membaca isi file sungguhan, bukan nama file. Jauh lebih aman.

**Database Transaction:**
```php
$conn->begin_transaction();
try {
    // UPDATE users ...
    // UPDATE customers ...
    $conn->commit();    // kalau semua sukses
} catch (Throwable $e) {
    $conn->rollback();  // kalau salah satu gagal, batalkan semuanya
}
```
Kenapa perlu? Profil tersebar di 2 tabel. Kalau UPDATE `users` sukses tapi UPDATE `customers` gagal (misal koneksi putus), data jadi setengah-setengah. Transaction menjamin atomicity: semua berhasil, atau semua dibatalkan.

---

## RINGKASAN KONSEP SECURITY YANG GERAL TERAPKAN

| Ancaman | Cara Geral Handle |
|---------|-------------------|
| **SQL Injection** | Semua query pakai Prepared Statement (`prepare` + `bind_param`) |
| **Plaintext Password** | `password_hash()` saat simpan, `password_verify()` saat cek |
| **Akses Halaman Tanpa Login** | `checkRole()` di setiap halaman, redirect ke login kalau session kosong |
| **Akses Halaman Salah Role** | `checkRole(['admin'])` — kalau bukan admin, kena 403 |
| **Invoice Orang Lain** | `WHERE b.id = ? AND b.customer_id = ?` di invoice.php |
| **Session Hijacking** | Logout bersih: kosongkan session + hapus cookie + destroy |
| **SQL Injection via Sort** | Whitelist kolom sort yang diizinkan |
| **Upload File Berbahaya** | Validasi MIME type (bukan extension) + max size di proses_profile.php |
| **Admin Hapus Diri Sendiri** | Cek `$id !== $current_user_id` sebelum delete |

---

## PERTANYAAN DOSEN YANG MUNGKIN DITANYA

**Q: Kenapa pakai Prepared Statement?**
A: Mencegah SQL injection. User input diperlakukan sebagai data, bukan bagian dari query SQL.

**Q: Kenapa `password_hash()` bukan MD5/SHA1?**
A: MD5 dan SHA1 sudah tidak aman (bisa di-crack pakai rainbow table). `password_hash()` pakai bcrypt yang otomatis menambahkan salt random setiap kali hash — meskipun dua user pakai password sama, hashnya akan berbeda.

**Q: Kenapa register insert ke 2 tabel?**
A: Desain database memisahkan data akun (users) dari data profil customer (customers). Foreign key `customers.user_id` menghubungkan keduanya.

**Q: Kenapa ada halaman konfirmasi logout?**
A: UX — mencegah user tidak sengaja logout. Juga lebih aman karena logout menggunakan POST method, bukan GET, sehingga tidak bisa di-trigger hanya dengan mengunjungi URL.

**Q: Apa fungsi `session_status() === PHP_SESSION_NONE` sebelum `session_start()`?**
A: Mencegah error "headers already sent" jika session sudah dibuka oleh file lain yang di-include sebelumnya. Ini defensive programming.

**Q: Bagaimana invoice PDF di-generate?**
A: Pakai library DomPDF. HTML invoice dibangun dulu sebagai string menggunakan `ob_start()`/`ob_get_clean()` (output buffering), lalu di-pass ke DomPDF yang mengkonversi HTML+CSS menjadi file PDF dan langsung di-stream ke browser sebagai download.

**Q: Kenapa validasi upload foto pakai MIME type, bukan cek extension?**
A: Extension bisa dipalsukan — user bisa rename `hack.php` jadi `hack.jpg`. MIME type (`mime_content_type()`) membaca isi file yang sebenarnya, bukan nama file. Jadi lebih aman dari serangan file upload berbahaya.

**Q: Kenapa update profil pakai database transaction?**
A: Data profil tersebar di 2 tabel: `users` dan `customers`. Kalau UPDATE pertama sukses tapi UPDATE kedua gagal, data jadi tidak konsisten. Transaction (`begin_transaction`, `commit`, `rollback`) menjamin atomicity — semua berhasil atau semua dibatalkan.

---

## PEMBAGIAN MODUL TIM LENGKAP

> Referensi: `docs/plan/plan.md` — 5 anggota, 7 CRUD module

### Geral — Foundation + CRUD USERS + Invoice PDF + Profile Customer

| File | Keterangan |
|------|------------|
| `config/koneksi.php`, `config/app.php` | Koneksi DB + helper URL |
| `includes/auth.php`, `functions.php`, `header.php`, `customer_role.php` | Foundation shared |
| `pages/auth/login.php`, `proses_login.php` | Auth |
| `pages/auth/register.php`, `proses_register.php` | Auth |
| `pages/auth/logout.php`, `proses_logout.php` | Auth |
| `pages/403.php` | Forbidden page |
| `pages/admin/users.php`, `proses_users.php` | CRUD Users |
| `pages/customer/invoice.php` | Invoice PDF (DomPDF) |
| `pages/customer/profile.php`, `proses_profile.php` | Profile management |

### Raika — CRUD Service Types + CRUD Mechanics + CRUD Time Slots

| File | Keterangan |
|------|------------|
| `pages/admin/service_types.php`, `proses_service_types.php` | CRUD Service Types |
| `pages/admin/mechanics.php`, `proses_mechanics.php` | CRUD Mechanics |
| `pages/admin/time_slots.php`, `proses_time_slots.php` | CRUD Time Slots |
| `pages/admin/customers.php` | List customers (read-only) |

### Nugi — CRUD Motors + Customer Pages

| File | Keterangan |
|------|------------|
| `pages/customer/motor.php`, `tambah_motor.php`, `edit_motor.php`, `detail_motor.php` | CRUD Motors |
| `pages/customer/dashboard.php` | Customer dashboard |
| `pages/customer/history.php`, `detail_history.php` | Histori booking |
| `pages/customer/booking.php` | Daftar booking aktif |
| `pages/customer/nav.php`, `footer.php` | Layout customer |

### Ahmad — CRUD Bookings (Admin + Customer) + Mekanik Flow + Payment

| File | Keterangan |
|------|------------|
| `pages/customer/tambah_booking.php`, `proses_booking.php` | Form booking customer |
| `pages/customer/booking_detail.php`, `edit_booking.php`, `update_booking.php` | Booking management customer |
| `pages/admin/bookings.php`, `booking_detail.php`, `create_booking.php` | CRUD Bookings admin |
| `pages/admin/payments.php` | Konfirmasi pembayaran |
| `pages/mekanik/dashboard.php`, `nav.php`, `footer.php` | Layout mekanik |
| `pages/mekanik/my_tasks.php`, `task_detail.php`, `proses_task.php` | Mekanik flow |
| `pages/mekanik/history.php` | Histori mekanik |

### Dermawan — CRUD Spare Parts + Dashboard Admin + Reports + Bonus

| File | Keterangan |
|------|------------|
| `pages/admin/spare_parts.php` | CRUD Spare Parts |
| `pages/admin/dashboard.php` | Dashboard admin |
| `pages/admin/reports.php` | Laporan PDF + Excel |
| `pages/admin/audit_logs.php` | Audit log |
| `pages/admin/nav.php` | Layout admin |
| `includes/footer.php`, `includes/navbar.php` | Layout global (landing page) |
