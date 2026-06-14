# Class Diagram — Sistem REVVO

## Diagram

```mermaid
classDiagram
    class User {
        +int id
        +string name
        +string email
        +string password_hash
        +enum role
        +string phone
        +string profile_photo
        +enum status
        +timestamp created_at
        +timestamp updated_at
        +login()
        +logout()
        +register()
        +updateProfil()
        +getAll()
        +createByAdmin()
        +deactivate()
    }

    class Customer {
        +int id
        +int user_id
        +text address
        +enum gender
        +date birth_date
        +string no_ktp
        +timestamp created_at
        +timestamp updated_at
        +getProfil()
        +updateProfil()
    }

    class Mechanic {
        +int id
        +int user_id
        +string specialization
        +enum availability_status
        +text notes
        +timestamp created_at
        +timestamp updated_at
        +getStatus()
        +updateStatus()
        +create()
        +getAll()
        +update()
        +delete()
    }

    class Motor {
        +int id
        +int customer_id
        +string brand
        +string model
        +string plate_number
        +year production_year
        +string color
        +string image_path
        +timestamp created_at
        +timestamp updated_at
        +create()
        +read()
        +update()
        +delete()
    }

    class ServiceType {
        +int id
        +string name
        +text description
        +int estimated_duration_minutes
        +decimal base_price
        +enum status
        +timestamp created_at
        +timestamp updated_at
        +create()
        +read()
        +update()
        +delete()
    }

    class SparePart {
        +int id
        +string sku
        +string name
        +string unit
        +int stock
        +int minimum_stock
        +decimal price
        +enum status
        +timestamp created_at
        +timestamp updated_at
        +create()
        +read()
        +update()
        +delete()
        +kurangiStok()
        +cekStokMinimum()
        +exportExcel()
    }

    class TimeSlot {
        +int id
        +enum day
        +time start_time
        +time end_time
        +int capacity
        +enum status
        +create()
        +read()
        +update()
        +delete()
        +cekKetersediaan()
    }


    class Booking {
        +int id
        +int customer_id
        +int motor_id
        +int service_type_id
        +int mechanic_id
        +int time_slot_id
        +date booking_date
        +decimal service_price
        +decimal total_price
        +enum status
        +text customer_complaint
        +string condition_photo
        +text mechanic_note
        +timestamp created_at
        +timestamp updated_at
        +create()
        +cancel()
        +assignMekanik()
        +updateStatus()
        +hitungTotal()
        +getAll()
        +getByCustomer()
        +getByMechanic()
        +tambahCatatan()
        +getLaporan()
        +exportExcel()
    }

    class BookingPart {
        +int id
        +int booking_id
        +int spare_part_id
        +int qty
        +decimal price_at_time
        +decimal subtotal
        +timestamp created_at
        +tambahPart()
        +hapusPart()
        +getByBooking()
    }

    class Payment {
        +int id
        +int booking_id
        +enum payment_method
        +decimal amount
        +enum status
        +timestamp paid_at
        +int verified_by
        +timestamp created_at
        +timestamp updated_at
        +create()
        +verifikasi()
        +cancel()
        +generateInvoice()
        +exportPDF()
        +getLaporan()
    }

    class ServiceLog {
        +int id
        +int booking_id
        +int changed_by
        +string previous_status
        +string new_status
        +text note
        +timestamp created_at
        +create()
        +getByBooking()
    }

    %% === RELASI ===
    User "1" -- "0..1" Customer : memiliki
    User "1" -- "0..1" Mechanic : memiliki
    Customer "1" -- "*" Motor : memiliki
    Customer "1" -- "*" Booking : membuat
    Motor "1" -- "*" Booking : diservis
    ServiceType "1" -- "*" Booking : dikategorikan
    Mechanic "0..1" -- "*" Booking : ditugaskan
    TimeSlot "1" -- "*" Booking : dijadwalkan
    Booking "1" -- "*" BookingPart : berisi
    SparePart "1" -- "*" BookingPart : digunakan
    Booking "1" -- "0..1" Payment : dibayar
    Booking "1" -- "*" ServiceLog : dicatat
    User "1" -- "*" ServiceLog : mengubah
    User "1" -- "*" Payment : memverifikasi
```

## Catatan

- Karena proyek ini menggunakan **PHP Native prosedural** (tanpa OOP), class diagram ini merepresentasikan **model data/entitas domain**
- Method merepresentasikan operasi/fungsi yang tersedia pada entitas tersebut
- 11 class sesuai dengan 11 tabel di database
- Relasi `User → Customer` dan `User → Mechanic` adalah **1:0..1** (tidak semua user adalah customer/mekanik)

## Method yang Ditambahkan (Update)

| Class | Method Baru | Alasan |
|---|---|---|
| `User` | `getAll()`, `createByAdmin()`, `deactivate()` | Modul admin CRUD Users (Geral) |
| `Mechanic` | `create()`, `getAll()`, `update()`, `delete()` | Modul admin CRUD Mechanics (Raika) |
| `Booking` | `getAll()`, `getByCustomer()`, `getByMechanic()`, `tambahCatatan()`, `getLaporan()`, `exportExcel()` | Multi-role views + laporan (Dermawan) |
| `BookingPart` | `getByBooking()` | Dibutuhkan invoice & detail view |
| `SparePart` | `cekStokMinimum()`, `exportExcel()` | Dashboard alert + export (Dermawan) |
| `TimeSlot` | `delete()` | Fix inkonsistensi MD vs XML sebelumnya |
| `Payment` | `generateInvoice()`, `exportPDF()`, `getLaporan()` | Modul invoice (Nugi) + laporan keuangan |
