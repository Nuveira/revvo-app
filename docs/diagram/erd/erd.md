# Entity Relationship Diagram — Sistem REVVO

## Diagram (Mermaid ERD)

```mermaid
erDiagram
    users ||--o| customers : "memiliki"
    users ||--o| mechanics : "memiliki"
    customers ||--o{ motors : "memiliki"
    customers ||--o{ bookings : "membuat"
    motors ||--o{ bookings : "diservis di"
    service_types ||--o{ bookings : "dikategorikan"
    mechanics |o--o{ bookings : "ditugaskan ke"
    time_slots ||--o{ bookings : "dijadwalkan di"
    bookings ||--o{ booking_parts : "berisi"
    spare_parts ||--o{ booking_parts : "digunakan di"
    bookings ||--o| payments : "dibayar via"
    bookings ||--o{ service_logs : "dicatat di"
    users ||--o{ service_logs : "diubah oleh"
    users ||--o{ payments : "diverifikasi oleh"

    users {
        int id PK
        varchar name "NOT NULL"
        varchar email "UNIQUE NOT NULL"
        varchar password_hash "NOT NULL"
        enum role "admin-mechanic-customer"
        varchar phone "NULL"
        varchar profile_photo "NULL"
        enum status "active-inactive DEFAULT active"
        timestamp created_at
        timestamp updated_at
    }

    customers {
        int id PK
        int user_id FK "UNIQUE NOT NULL"
        text address "NULL"
        enum gender "male-female NULL"
        date birth_date "NULL"
        varchar no_ktp "NULL"
        timestamp created_at
        timestamp updated_at
    }

    mechanics {
        int id PK
        int user_id FK "UNIQUE NOT NULL"
        varchar specialization "NULL"
        enum availability_status "available-busy-inactive"
        text notes "NULL"
        timestamp created_at
        timestamp updated_at
    }

    motors {
        int id PK
        int customer_id FK "NOT NULL CASCADE"
        varchar brand "NOT NULL"
        varchar model "NOT NULL"
        varchar plate_number "NOT NULL"
        year production_year "NULL"
        varchar color "NULL"
        varchar image_path "NULL"
        timestamp created_at
        timestamp updated_at
    }

    service_types {
        int id PK
        varchar name "NOT NULL"
        text description "NULL"
        int estimated_duration_minutes "NOT NULL"
        decimal base_price "NOT NULL"
        enum status "active-inactive"
        timestamp created_at
        timestamp updated_at
    }

    spare_parts {
        int id PK
        varchar sku "UNIQUE NOT NULL"
        varchar name "NOT NULL"
        varchar unit "NOT NULL"
        int stock "DEFAULT 0"
        int minimum_stock "DEFAULT 5"
        decimal price "NOT NULL"
        enum status "active-inactive"
        timestamp created_at
        timestamp updated_at
    }

    time_slots {
        int id PK
        enum day "monday-sunday NOT NULL"
        time start_time "NOT NULL"
        time end_time "NOT NULL"
        int capacity "DEFAULT 1"
        enum status "active-inactive"
    }

    bookings {
        int id PK
        int customer_id FK "NOT NULL RESTRICT"
        int motor_id FK "NOT NULL RESTRICT"
        int service_type_id FK "NOT NULL RESTRICT"
        int mechanic_id FK "NULL SET-NULL"
        int time_slot_id FK "NOT NULL RESTRICT"
        date booking_date "NOT NULL"
        decimal service_price "NOT NULL snapshot"
        decimal total_price "DEFAULT 0"
        enum status "queued-in_progress-completed-ready_for_pickup-cancelled"
        text customer_complaint "NULL"
        varchar condition_photo "NULL"
        text mechanic_note "NULL"
        timestamp created_at
        timestamp updated_at
    }

    booking_parts {
        int id PK
        int booking_id FK "NOT NULL CASCADE"
        int spare_part_id FK "NOT NULL RESTRICT"
        int qty "NOT NULL"
        decimal price_at_time "NOT NULL snapshot"
        decimal subtotal "NOT NULL"
        timestamp created_at
    }

    payments {
        int id PK
        int booking_id FK "UNIQUE NOT NULL CASCADE"
        enum payment_method "cash-transfer-ewallet"
        decimal amount "NOT NULL"
        enum status "pending-paid-cancelled"
        timestamp paid_at "NULL"
        int verified_by FK "NULL SET-NULL"
        timestamp created_at
        timestamp updated_at
    }

    service_logs {
        int id PK
        int booking_id FK "NOT NULL CASCADE"
        int changed_by FK "NULL SET-NULL"
        varchar previous_status "NOT NULL"
        varchar new_status "NOT NULL"
        text note "NULL"
        timestamp created_at
    }
```

## Ringkasan: 11 Tabel, 14 Relasi

| Relasi | Kardinalitas | ON DELETE |
|--------|-------------|----------|
| users → customers | 1 : 0..1 | CASCADE |
| users → mechanics | 1 : 0..1 | CASCADE |
| customers → motors | 1 : N | CASCADE |
| customers → bookings | 1 : N | RESTRICT |
| motors → bookings | 1 : N | RESTRICT |
| service_types → bookings | 1 : N | RESTRICT |
| mechanics → bookings | 0..1 : N | SET NULL |
| time_slots → bookings | 1 : N | RESTRICT |
| bookings → booking_parts | 1 : N | CASCADE |
| spare_parts → booking_parts | 1 : N | RESTRICT |
| bookings → payments | 1 : 0..1 | CASCADE |
| bookings → service_logs | 1 : N | CASCADE |
| users → service_logs | 1 : N | SET NULL |
| users → payments | 1 : N | SET NULL |

## Koreksi dari ERD Lama (docs/diagrams/erd.xml)

1. **booking_parts** — `created_at` tidak ditampilkan di ERD lama (padahal ada di SQL)
2. **users → customers** — ERD lama label "1:1" seharusnya "1:0..1" (tidak semua user = customer)
3. **users → mechanics** — ERD lama label "1:1" seharusnya "1:0..1" (tidak semua user = mekanik)
4. **Arrow style** — ERD lama pakai `ERmany` pada sisi customer/mechanic, kontradiksi dengan label "1:1"

## Koreksi dari Physical DB Lama (docs/diagrams/physical-database.xml)

1. **spare_parts** — `created_at, updated_at` tidak ditampilkan di diagram (padahal ada di SQL)
2. Masalah kardinalitas sama dengan ERD di atas
