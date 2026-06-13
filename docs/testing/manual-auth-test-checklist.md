# Manual Auth Test Checklist

Checklist ini dipakai untuk bukti uji manual flow auth 3 role di REVVO.

## Scope

- login admin
- login mechanic
- login customer
- role guard ke halaman terproteksi
- logout

## Test Data

Password dummy semua user seed:

`password123`

Contoh akun:

- Admin: `admin@bengkel.com`
- Mechanic: `andi.mek@bengkel.com`
- Customer: `geral@gmail.com`

Referensi seed: `database/revvo.sql`

## Checklist

| ID | Skenario | Langkah Singkat | Expected Result | Status | Catatan |
|---|---|---|---|---|---|
| AUTH-01 | Login admin valid | Login pakai akun admin aktif | Redirect ke `pages/admin/dashboard.php` | `[x]` | |
| AUTH-02 | Login mechanic valid | Login pakai akun mechanic aktif | Redirect ke `pages/mekanik/dashboard.php` | `[x]` | |
| AUTH-03 | Login customer valid | Login pakai akun customer aktif | Redirect ke `pages/customer/dashboard.php` | `[x]` | |
| AUTH-04 | Login gagal password salah | Input email valid + password salah | Tetap di login, muncul error | `[x]` | |
| AUTH-05 | Login gagal user inactive | Login pakai user inactive | Ditolak, tidak bisa masuk | `[x]` | Contoh: `eko.mek@bengkel.com` |
| AUTH-06 | Guard admin page | Login customer/mechanic lalu buka `pages/admin/users.php` | Redirect ke `pages/403.php` | `[x]` | |
| AUTH-07 | Guard mechanic page | Login customer/admin lalu buka `pages/mekanik/dashboard.php` | Redirect ke `pages/403.php` | `[x]` | |
| AUTH-08 | Guard customer page | Login admin/mechanic lalu buka `pages/customer/dashboard.php` | Redirect ke `pages/403.php` | `[x]` | |
| AUTH-09 | Guest buka protected page | Logout lalu buka page protected langsung | Redirect ke `pages/auth/login.php` | `[x]` | |
| AUTH-10 | Logout flow | Login lalu logout dari page logout | Session habis, redirect ke login | `[x]` | |


