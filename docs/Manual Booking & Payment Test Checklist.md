Manual Booking & Payment Test Checklist

Checklist ini dipakai untuk bukti uji manual fitur Booking Management dan Payment Management pada REVVO.

Scope

- create booking admin
- booking list admin
- assign mechanic
- update booking status
- payment management
- booking detail
- delete booking
- time slot booking

Test Data

Gunakan data seed yang tersedia pada database.

Contoh:

- Customer aktif
- Motor aktif milik customer
- Service Type aktif
- Time Slot aktif
- Mechanic aktif

Referensi seed: "database/revvo.sql"

Checklist

ID| Skenario| Langkah Singkat| Expected Result| Status| Catatan
BOOK-01| Create booking valid| Isi seluruh field booking lalu simpan| Booking berhasil dibuat| "[ ]"| 
BOOK-02| Create booking tanpa keluhan| Kosongkan field keluhan lalu simpan| Booking tetap berhasil dibuat| "[ ]"| 
BOOK-03| Pilih customer| Pilih customer pada form booking| Data motor customer tersedia| "[ ]"| 
BOOK-04| Filter motor customer| Pilih customer berbeda| Hanya motor milik customer tersebut yang tampil| "[ ]"| 
BOOK-05| Pilih service type| Pilih service| Service berhasil dipilih| "[ ]"| 
BOOK-06| Tampilkan time slot| Buka dropdown time slot| Seluruh slot aktif tampil| "[ ]"| 
BOOK-07| Booking slot tersedia| Pilih slot kosong| Booking berhasil dibuat| "[ ]"| 
BOOK-08| Booking slot terpakai| Gunakan slot yang sudah penuh| Booking ditolak| "[ ]"| 
BOOK-09| Urutan booking| Buka halaman booking| Data tampil urut ASC berdasarkan ID| "[ ]"| 
BOOK-10| Nomor urut booking| Buka halaman booking| Kolom menampilkan nomor urut, bukan ID database| "[ ]"| 
BOOK-11| Detail booking| Klik tombol Detail| Detail booking tampil lengkap| "[ ]"| 
BOOK-12| Edit booking| Ubah data booking| Perubahan berhasil disimpan| "[ ]"| 

ID| Skenario| Langkah Singkat| Expected Result| Status| Catatan
ASSIGN-01| Assign mechanic queued| Booking status queued| Tombol Assign tampil| "[ ]"| 
ASSIGN-02| Assign mechanic success| Assign mechanic ke booking| Mechanic berhasil tersimpan| "[ ]"| 
ASSIGN-03| Assign mechanic in progress| Booking status in_progress| Tombol Assign tidak tampil| "[ ]"| 
ASSIGN-04| Assign mechanic completed| Booking status completed| Tombol Assign tidak tampil| "[ ]"| 
ASSIGN-05| Assign mechanic ready for pickup| Booking status ready_for_pickup| Tombol Assign tidak tampil| "[ ]"| 
ASSIGN-06| Assign mechanic cancelled| Booking status cancelled| Tombol Assign tidak tampil| "[ ]"| 

ID| Skenario| Langkah Singkat| Expected Result| Status| Catatan
STATUS-01| Update queued → in progress| Ubah status booking| Status berhasil berubah| "[ ]"| 
STATUS-02| Update in progress → ready for pickup| Ubah status booking| Status berhasil berubah| "[ ]"| 
STATUS-03| Update ready for pickup → completed| Ubah status booking| Status berhasil berubah| "[ ]"| 
STATUS-04| Status cancelled| Booking cancelled| Tombol Status tidak tampil| "[ ]"| 
STATUS-05| Status completed| Booking completed| Tombol Status tidak tampil| "[ ]"| 

ID| Skenario| Langkah Singkat| Expected Result| Status| Catatan
PAY-01| Payment pending queued| Booking queued + payment pending| Tombol Payment tampil| "[ ]"| 
PAY-02| Payment paid queued| Booking queued + payment paid| Tombol Payment tidak tampil| "[ ]"| 
PAY-03| Payment pending in progress| Booking in_progress + payment pending| Tombol Payment tampil| "[ ]"| 
PAY-04| Payment paid in progress| Booking in_progress + payment paid| Tombol Payment tidak tampil| "[ ]"| 
PAY-05| Payment pending ready for pickup| Booking ready_for_pickup + payment pending| Tombol Payment tampil| "[ ]"| 
PAY-06| Payment paid ready for pickup| Booking ready_for_pickup + payment paid| Tombol Payment tidak tampil| "[ ]"| 
PAY-07| Payment pending completed| Booking completed + payment pending| Tombol Payment tampil| "[ ]"| 
PAY-08| Payment paid completed| Booking completed + payment paid| Tombol Payment tidak tampil| "[ ]"| 
PAY-09| Payment pending cancelled| Booking cancelled + payment pending| Tombol Payment tidak tampil| "[ ]"| 
PAY-10| Payment paid cancelled| Booking cancelled + payment paid| Tombol Payment tidak tampil| "[ ]"| 

ID| Skenario| Langkah Singkat| Expected Result| Status| Catatan
DELETE-01| Hapus booking queued| Klik tombol Hapus| Booking terhapus| "[ ]"| 
DELETE-02| Hapus booking in progress| Klik tombol Hapus| Booking terhapus| "[ ]"| 
DELETE-03| Hapus booking completed| Klik tombol Hapus| Booking terhapus| "[ ]"| 
DELETE-04| Hapus booking ready for pickup| Klik tombol Hapus| Booking terhapus| "[ ]"| 
DELETE-05| Hapus booking cancelled| Klik tombol Hapus| Booking terhapus| "[ ]"|
