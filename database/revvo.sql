-- ============================================================
-- Database: REVVO — Sistem Booking & Tracking Servis Bengkel Motor
-- Password semua dummy users: 'password123'

DROP DATABASE IF EXISTS revvo;
CREATE DATABASE revvo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE revvo;

-- ============================================================
-- 1. USERS
-- ============================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'mechanic', 'customer') NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    profile_photo VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 2. CUSTOMERS
-- ============================================================
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    address TEXT DEFAULT NULL,
    gender ENUM('male', 'female') DEFAULT NULL,
    birth_date DATE DEFAULT NULL,
    no_ktp VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 3. MECHANICS
-- ============================================================
CREATE TABLE mechanics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    specialization VARCHAR(100) DEFAULT NULL,
    availability_status ENUM('available', 'busy', 'inactive') DEFAULT 'available',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_availability (availability_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 4. SERVICE_TYPES
-- ============================================================
CREATE TABLE service_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    estimated_duration_minutes INT NOT NULL,
    base_price DECIMAL(12, 2) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 5. SPARE_PARTS
-- ============================================================
CREATE TABLE spare_parts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    minimum_stock INT DEFAULT 5,
    price DECIMAL(12, 2) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 6. TIME_SLOTS
-- ============================================================
CREATE TABLE time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    capacity INT DEFAULT 1,
    status ENUM('active', 'inactive') DEFAULT 'active',
    INDEX idx_day_status (day, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 7. MOTORS
-- ============================================================
CREATE TABLE motors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    plate_number VARCHAR(15) NOT NULL,
    production_year YEAR DEFAULT NULL,
    color VARCHAR(30) DEFAULT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    INDEX idx_customer (customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 8. BOOKINGS
-- ============================================================
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    motor_id INT DEFAULT NULL,
    service_type_id INT NOT NULL,
    mechanic_id INT DEFAULT NULL,
    time_slot_id INT NOT NULL,
    booking_date DATE NOT NULL,
    service_price DECIMAL(12, 2) NOT NULL,
    total_price DECIMAL(12, 2) DEFAULT 0,
    status ENUM('queued', 'in_progress', 'completed', 'ready_for_pickup', 'cancelled') DEFAULT 'queued',
    customer_complaint TEXT DEFAULT NULL,
    condition_photo VARCHAR(255) DEFAULT NULL,
    mechanic_note TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    FOREIGN KEY (motor_id) REFERENCES motors(id) ON DELETE RESTRICT,
    FOREIGN KEY (service_type_id) REFERENCES service_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (mechanic_id) REFERENCES mechanics(id) ON DELETE SET NULL,
    FOREIGN KEY (time_slot_id) REFERENCES time_slots(id) ON DELETE RESTRICT,
    INDEX idx_date_slot (booking_date, time_slot_id),
    INDEX idx_status (status),
    INDEX idx_customer (customer_id),
    INDEX idx_mechanic (mechanic_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 9. BOOKING_PARTS
-- ============================================================
CREATE TABLE booking_parts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    spare_part_id INT NOT NULL,
    qty INT NOT NULL,
    price_at_time DECIMAL(12, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (spare_part_id) REFERENCES spare_parts(id) ON DELETE RESTRICT,
    INDEX idx_booking (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 10. PAYMENTS
-- ============================================================
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNIQUE NOT NULL,
    payment_method ENUM('cash', 'transfer', 'ewallet') NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    paid_at TIMESTAMP NULL DEFAULT NULL,
    verified_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 11. SERVICE_LOGS (audit trail)
-- ============================================================
CREATE TABLE service_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    changed_by INT DEFAULT NULL,
    previous_status VARCHAR(20) NOT NULL,
    new_status VARCHAR(20) NOT NULL,
    note TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_booking (booking_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- DUMMY DATA
-- ============================================================

-- users (15 rows: 2 admin, 5 mechanic, 8 customer)
INSERT INTO users (name, email, password_hash, role, phone, status) VALUES
('Budi Pemilik Bengkel', 'admin@bengkel.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'admin', '081200000001', 'active'),
('Sinta Manajer', 'admin2@bengkel.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'admin', '081200000002', 'active'),
('Andi Saputra', 'andi.mek@bengkel.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'mechanic', '081200000003', 'active'),
('Budi Hartono', 'budi.mek@bengkel.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'mechanic', '081200000004', 'active'),
('Cahyo Wijaya', 'cahyo.mek@bengkel.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'mechanic', '081200000005', 'active'),
('Dimas Pratama', 'dimas.mek@bengkel.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'mechanic', '081200000006', 'active'),
('Eko Susanto', 'eko.mek@bengkel.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'mechanic', '081200000007', 'inactive'),
('Geral Maulana', 'geral@gmail.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'customer', '081234567801', 'active'),
('Rizki Ramadhan', 'rizki@gmail.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'customer', '081234567802', 'active'),
('Maya Anggraini', 'maya@gmail.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'customer', '081234567803', 'active'),
('Fajar Nugroho', 'fajar@gmail.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'customer', '081234567804', 'active'),
('Siti Aminah', 'siti@gmail.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'customer', '081234567805', 'active'),
('Hendra Kurniawan', 'hendra@gmail.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'customer', '081234567806', 'active'),
('Lisa Permata', 'lisa@gmail.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'customer', '081234567807', 'active'),
('Agus Setiawan', 'agus@gmail.com', '$2y$12$lNiTtajoC/AxHO1w9C0XMOsOKKpyWYqXu9inuDGfX3GLS7QpObdIS', 'customer', '081234567808', 'active');

-- customers (8 rows)
INSERT INTO customers (user_id, address, gender, birth_date, no_ktp) VALUES
(8, 'Jl. Merdeka No. 12, Cikarang', 'male', '2003-05-15', '3216012345678901'),
(9, 'Jl. Sudirman No. 45, Karawang', 'male', '2002-11-20', '3216012345678902'),
(10, 'Jl. Pahlawan No. 23, Cikarang', 'female', '2004-03-08', '3216012345678903'),
(11, 'Jl. Diponegoro No. 67, Bekasi', 'male', '2001-07-25', '3216012345678904'),
(12, 'Jl. Kartini No. 89, Karawang', 'female', '2003-12-02', '3216012345678905'),
(13, 'Jl. Gatot Subroto No. 34, Cikarang', 'male', '2000-09-14', '3216012345678906'),
(14, 'Jl. Veteran No. 56, Bekasi', 'female', '2002-01-30', '3216012345678907'),
(15, 'Jl. Ahmad Yani No. 78, Karawang', 'male', '2001-06-11', '3216012345678908');

-- mechanics (5 rows)
INSERT INTO mechanics (user_id, specialization, availability_status, notes) VALUES
(3, 'Mesin & Tune Up', 'available', 'Senior mekanik, 10 tahun pengalaman'),
(4, 'Kelistrikan & Aki', 'available', 'Spesialis sistem kelistrikan motor'),
(5, 'CVT & Matic', 'busy', 'Ahli motor matic Honda dan Yamaha'),
(6, 'Ban & Rem', 'available', 'Cepat dan teliti untuk servis ringan'),
(7, 'Mesin & Karburator', 'inactive', 'Sedang cuti panjang');

-- service_types (10 rows)
INSERT INTO service_types (name, description, estimated_duration_minutes, base_price, status) VALUES
('Ganti Oli Mesin', 'Penggantian oli mesin standar (belum termasuk oli)', 30, 35000.00, 'active'),
('Tune Up Standar', 'Tune up lengkap: bersihkan karbu, setel klep, busi, filter udara', 90, 85000.00, 'active'),
('Service CVT Matic', 'Bongkar CVT, bersihkan roller, V-belt check, grease', 60, 75000.00, 'active'),
('Ganti Ban', 'Jasa ganti ban depan/belakang (belum termasuk ban)', 30, 25000.00, 'active'),
('Ganti Kampas Rem', 'Penggantian kampas rem depan/belakang (belum termasuk kampas)', 30, 30000.00, 'active'),
('Ganti Aki', 'Penggantian aki motor + setup kelistrikan (belum termasuk aki)', 20, 25000.00, 'active'),
('Service Karburator', 'Bongkar karbu, bersihkan, setel ulang', 45, 50000.00, 'active'),
('Servis Besar', 'Bongkar mesin total, overhaul ringan', 240, 350000.00, 'active'),
('Cuci Motor Premium', 'Cuci lengkap + wax + semir ban', 45, 40000.00, 'active'),
('Tambal Ban Tubeless', 'Tambal cepat dengan metode tip top', 15, 20000.00, 'active');

-- spare_parts (15 rows)
INSERT INTO spare_parts (sku, name, unit, stock, minimum_stock, price, status) VALUES
('OLI-YAM-1L', 'Oli Mesin Yamalube 1L', 'botol', 25, 5, 55000.00, 'active'),
('OLI-SHL-1L', 'Oli Mesin Shell Advance 1L', 'botol', 18, 5, 65000.00, 'active'),
('FLT-OLI-01', 'Filter Oli Universal', 'pcs', 30, 10, 25000.00, 'active'),
('BAN-IRC-D70', 'Ban IRC Depan 70/90-14', 'pcs', 8, 3, 165000.00, 'active'),
('BAN-IRC-B80', 'Ban IRC Belakang 80/90-14', 'pcs', 7, 3, 185000.00, 'active'),
('KMP-REM-D', 'Kampas Rem Depan', 'set', 15, 5, 45000.00, 'active'),
('KMP-REM-B', 'Kampas Rem Belakang', 'set', 12, 5, 40000.00, 'active'),
('AKI-GS-5S', 'Aki GS Astra GTZ5S', 'pcs', 6, 2, 285000.00, 'active'),
('BSI-NGK-01', 'Busi NGK CPR8EA-9', 'pcs', 40, 10, 35000.00, 'active'),
('VBT-HND-01', 'V-Belt CVT Honda Vario', 'pcs', 10, 3, 95000.00, 'active'),
('FLT-UDR-01', 'Filter Udara Universal', 'pcs', 22, 5, 45000.00, 'active'),
('KBL-GAS-01', 'Kabel Gas Universal', 'pcs', 14, 5, 35000.00, 'active'),
('LMP-LED-01', 'Lampu LED Depan', 'pcs', 9, 3, 75000.00, 'active'),
('ROL-CVT-01', 'Roller CVT Set', 'set', 8, 3, 55000.00, 'active'),
('GRS-CVT-01', 'Grease CVT', 'tube', 25, 8, 15000.00, 'active');

-- time_slots (16 rows — Monday & Tuesday)
INSERT INTO time_slots (day, start_time, end_time, capacity, status) VALUES
('monday', '08:00:00', '09:00:00', 2, 'active'),
('monday', '09:00:00', '10:00:00', 2, 'active'),
('monday', '10:00:00', '11:00:00', 2, 'active'),
('monday', '11:00:00', '12:00:00', 2, 'active'),
('monday', '13:00:00', '14:00:00', 2, 'active'),
('monday', '14:00:00', '15:00:00', 2, 'active'),
('monday', '15:00:00', '16:00:00', 2, 'active'),
('monday', '16:00:00', '17:00:00', 2, 'active'),
('tuesday', '08:00:00', '09:00:00', 2, 'active'),
('tuesday', '09:00:00', '10:00:00', 2, 'active'),
('tuesday', '10:00:00', '11:00:00', 2, 'active'),
('tuesday', '11:00:00', '12:00:00', 2, 'active'),
('tuesday', '13:00:00', '14:00:00', 2, 'active'),
('tuesday', '14:00:00', '15:00:00', 2, 'active'),
('tuesday', '15:00:00', '16:00:00', 2, 'active'),
('tuesday', '16:00:00', '17:00:00', 2, 'active');

-- motors (12 rows)
INSERT INTO motors (customer_id, brand, model, plate_number, production_year, color) VALUES
(1, 'Honda', 'Vario 125', 'B 1234 GER', 2022, 'Hitam'),
(1, 'Yamaha', 'NMAX 155', 'B 5678 GER', 2023, 'Putih'),
(2, 'Honda', 'Beat Street', 'B 2345 RZK', 2021, 'Merah'),
(3, 'Yamaha', 'Mio M3', 'B 3456 MYA', 2020, 'Biru'),
(3, 'Honda', 'Scoopy', 'B 7890 MYA', 2022, 'Pink'),
(4, 'Yamaha', 'Aerox 155', 'B 4567 FJR', 2023, 'Hitam Matte'),
(4, 'Honda', 'PCX 160', 'B 8901 FJR', 2024, 'Silver'),
(5, 'Honda', 'Vario 160', 'B 5678 STI', 2023, 'Hitam'),
(6, 'Yamaha', 'Lexi 125', 'B 6789 HND', 2022, 'Putih'),
(6, 'Suzuki', 'Address', 'B 9012 HND', 2021, 'Hitam'),
(7, 'Honda', 'Genio', 'B 7890 LSA', 2023, 'Merah Maroon'),
(8, 'Yamaha', 'Fazzio', 'B 8901 AGS', 2024, 'Hijau');

-- bookings (12 rows — mix status for demo)
INSERT INTO bookings (customer_id, motor_id, service_type_id, mechanic_id, time_slot_id, booking_date, service_price, total_price, status, customer_complaint, mechanic_note, created_at) VALUES
(1, 1, 1, 1, 1, '2026-05-10', 35000.00, 90000.00, 'ready_for_pickup', 'Oli udah lama belum ganti', 'Diganti oli Yamalube + filter baru', '2026-05-09 14:30:00'),
(2, 3, 2, 1, 2, '2026-05-11', 85000.00, 120000.00, 'ready_for_pickup', 'Mesin sering brebet', 'Tune up komplit, ganti busi', '2026-05-10 09:15:00'),
(3, 4, 3, 3, 3, '2026-05-12', 75000.00, 170000.00, 'ready_for_pickup', 'CVT bunyi kasar', 'Ganti V-belt dan grease ulang', '2026-05-11 11:00:00'),
(4, 6, 5, 4, 4, '2026-05-15', 30000.00, 115000.00, 'completed', 'Rem depan bunyi cit-cit', 'Ganti kampas rem depan', '2026-05-14 13:45:00'),
(5, 8, 1, 1, 5, '2026-05-15', 35000.00, 90000.00, 'completed', 'Servis rutin', 'Ganti oli + filter', '2026-05-14 16:20:00'),
(6, 9, 4, 4, 6, '2026-05-18', 25000.00, 210000.00, 'in_progress', 'Ban belakang gundul', NULL, '2026-05-17 10:30:00'),
(7, 11, 7, 1, 7, '2026-05-18', 50000.00, 50000.00, 'in_progress', 'Motor susah idle', NULL, '2026-05-17 14:00:00'),
(8, 12, 2, NULL, 9, '2026-05-19', 85000.00, 85000.00, 'queued', 'Service rutin pertama', NULL, '2026-05-18 08:00:00'),
(1, 2, 3, NULL, 10, '2026-05-19', 75000.00, 75000.00, 'queued', 'Suara CVT kasar', NULL, '2026-05-18 09:30:00'),
(3, 5, 6, NULL, 11, '2026-05-20', 25000.00, 25000.00, 'queued', 'Aki tekor', NULL, '2026-05-18 10:00:00'),
(4, 7, 9, NULL, 12, '2026-05-17', 40000.00, 40000.00, 'cancelled', 'Mau cuci aja', 'Customer cancel via WA', '2026-05-16 19:00:00'),
(2, 3, 1, 2, 13, '2026-04-25', 35000.00, 90000.00, 'ready_for_pickup', 'Servis rutin', 'Ganti oli', '2026-04-24 10:00:00');

-- booking_parts (9 rows)
INSERT INTO booking_parts (booking_id, spare_part_id, qty, price_at_time, subtotal) VALUES
(1, 1, 1, 55000.00, 55000.00),
(2, 9, 1, 35000.00, 35000.00),
(3, 10, 1, 95000.00, 95000.00),
(4, 6, 1, 45000.00, 45000.00),
(4, 15, 1, 15000.00, 15000.00),
(4, 3, 1, 25000.00, 25000.00),
(5, 2, 1, 55000.00, 55000.00),
(6, 5, 1, 185000.00, 185000.00),
(12, 1, 1, 55000.00, 55000.00);

-- payments (10 rows)
INSERT INTO payments (booking_id, payment_method, amount, status, paid_at, verified_by) VALUES
(1, 'cash', 90000.00, 'paid', '2026-05-10 11:30:00', 1),
(2, 'transfer', 120000.00, 'paid', '2026-05-11 14:00:00', 1),
(3, 'ewallet', 170000.00, 'paid', '2026-05-12 16:15:00', 2),
(4, 'cash', 115000.00, 'pending', NULL, NULL),
(5, 'transfer', 90000.00, 'pending', NULL, NULL),
(6, 'cash', 210000.00, 'pending', NULL, NULL),
(7, 'cash', 50000.00, 'pending', NULL, NULL),
(11, 'cash', 0.00, 'cancelled', NULL, NULL),
(12, 'cash', 90000.00, 'paid', '2026-04-25 11:00:00', 1),
(8, 'cash', 0.00, 'pending', NULL, NULL);

-- service_logs (18 rows — audit trail)
INSERT INTO service_logs (booking_id, changed_by, previous_status, new_status, note, created_at) VALUES
(1, 8, 'created', 'queued', 'Booking dibuat customer', '2026-05-09 14:30:00'),
(1, 1, 'queued', 'in_progress', 'Assign ke Andi', '2026-05-10 08:30:00'),
(1, 3, 'in_progress', 'completed', 'Pengerjaan selesai', '2026-05-10 09:15:00'),
(1, 1, 'completed', 'ready_for_pickup', 'Verified + sudah bayar', '2026-05-10 11:30:00'),
(2, 9, 'created', 'queued', 'Booking dibuat customer', '2026-05-10 09:15:00'),
(2, 1, 'queued', 'in_progress', 'Mulai tune up', '2026-05-11 10:00:00'),
(2, 3, 'in_progress', 'completed', 'Tune up selesai', '2026-05-11 13:30:00'),
(2, 1, 'completed', 'ready_for_pickup', 'Sudah bayar transfer', '2026-05-11 14:00:00'),
(4, 11, 'created', 'queued', 'Booking dibuat customer', '2026-05-14 13:45:00'),
(4, 2, 'queued', 'in_progress', 'Assign ke Dimas', '2026-05-15 09:00:00'),
(4, 6, 'in_progress', 'completed', 'Kampas rem diganti', '2026-05-15 10:30:00'),
(6, 13, 'created', 'queued', 'Booking dibuat customer', '2026-05-17 10:30:00'),
(6, 1, 'queued', 'in_progress', 'Mulai ganti ban', '2026-05-18 10:30:00'),
(7, 14, 'created', 'queued', 'Booking dibuat customer', '2026-05-17 14:00:00'),
(7, 1, 'queued', 'in_progress', 'Mulai bongkar karbu', '2026-05-18 14:00:00'),
(11, 11, 'created', 'queued', 'Booking dibuat customer', '2026-05-16 19:00:00'),
(11, 11, 'queued', 'cancelled', 'Customer cancel via WA', '2026-05-17 07:30:00'),
(12, 9, 'created', 'queued', 'Booking dibuat customer', '2026-04-24 10:00:00');

-- ============================================================
-- END OF FILE
-- ============================================================
