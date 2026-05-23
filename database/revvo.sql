-- ============================================================
-- Database: Sistem Booking & Tracking Servis Bengkel Motor
-- Mata Kuliah: Pemrograman Web Dasar (PHP & MySQL)
-- Kelompok: Geral, Nugi, Dermawan, Raika, Ahmad
-- ============================================================
-- IMPORTANT: Password untuk semua dummy users = 'password123'
-- Bcrypt hash menggunakan PHP password_hash() dengan PASSWORD_DEFAULT (cost 10)
-- Jika login gagal, regenerate hash dengan:
--   php -r "echo password_hash('password123', PASSWORD_DEFAULT);"
-- Lalu UPDATE users SET password_hash = 'hash_baru';
-- ============================================================

DROP DATABASE IF EXISTS bengkel_db;
CREATE DATABASE bengkel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bengkel_db;

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'mekanik', 'customer') NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    foto_profil VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: customers
-- ============================================================
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    alamat TEXT,
    no_hp VARCHAR(20) NOT NULL,
    no_ktp VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: mechanics
-- ============================================================
CREATE TABLE mechanics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    spesialisasi VARCHAR(100) DEFAULT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: service_types
-- ============================================================
CREATE TABLE service_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_layanan VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    estimasi_menit INT NOT NULL,
    harga DECIMAL(12, 2) NOT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: spare_parts
-- ============================================================
CREATE TABLE spare_parts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_part VARCHAR(100) NOT NULL,
    satuan VARCHAR(20) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    harga_satuan DECIMAL(12, 2) NOT NULL,
    stok_minimum INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: time_slots
-- ============================================================
CREATE TABLE time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    kapasitas INT DEFAULT 1,
    hari ENUM('senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu') NOT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    INDEX idx_hari_status (hari, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: motors
-- ============================================================
CREATE TABLE motors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    merk VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    plat_nomor VARCHAR(15) NOT NULL,
    tahun YEAR DEFAULT NULL,
    warna VARCHAR(30) DEFAULT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    INDEX idx_customer (customer_id),
    INDEX idx_plat (plat_nomor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: bookings
-- ============================================================
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    motor_id INT NOT NULL,
    service_type_id INT NOT NULL,
    mechanic_id INT DEFAULT NULL,
    time_slot_id INT NOT NULL,
    tanggal_booking DATE NOT NULL,
    harga_jasa DECIMAL(12, 2) NOT NULL,
    total_harga DECIMAL(12, 2) DEFAULT 0,
    status ENUM('antri', 'dikerjakan', 'selesai', 'siap_diambil', 'dibatalkan') DEFAULT 'antri',
    keluhan TEXT,
    foto_kondisi VARCHAR(255) DEFAULT NULL,
    catatan_mekanik TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    FOREIGN KEY (motor_id) REFERENCES motors(id) ON DELETE RESTRICT,
    FOREIGN KEY (service_type_id) REFERENCES service_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (mechanic_id) REFERENCES mechanics(id) ON DELETE SET NULL,
    FOREIGN KEY (time_slot_id) REFERENCES time_slots(id) ON DELETE RESTRICT,
    INDEX idx_tanggal_slot (tanggal_booking, time_slot_id),
    INDEX idx_status (status),
    INDEX idx_customer (customer_id),
    INDEX idx_mechanic (mechanic_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: booking_parts
-- ============================================================
CREATE TABLE booking_parts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    spare_part_id INT NOT NULL,
    qty INT NOT NULL,
    harga_saat_itu DECIMAL(12, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (spare_part_id) REFERENCES spare_parts(id) ON DELETE RESTRICT,
    INDEX idx_booking (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: payments
-- ============================================================
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNIQUE NOT NULL,
    jumlah_dibayar DECIMAL(12, 2) NOT NULL,
    metode ENUM('cash', 'transfer', 'ewallet') NOT NULL,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    bukti_bayar VARCHAR(255) DEFAULT NULL,
    tanggal_bayar TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: service_logs (audit trail)
-- ============================================================
CREATE TABLE service_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    status_dari VARCHAR(20) NOT NULL,
    status_ke VARCHAR(20) NOT NULL,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_booking (booking_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- DUMMY DATA
-- ============================================================

-- ------------------------------------------------------------
-- Dummy: users (15 rows: 2 admin, 5 mekanik, 8 customer)
-- Password semua: 'password123'
-- ------------------------------------------------------------
INSERT INTO users (username, email, password_hash, role, nama_lengkap) VALUES
('admin1', 'admin@bengkel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Pak Budi Pemilik Bengkel'),
('admin2', 'admin2@bengkel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Bu Sinta Manajer'),
('mek_andi', 'andi.mekanik@bengkel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mekanik', 'Andi Saputra'),
('mek_budi', 'budi.mekanik@bengkel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mekanik', 'Budi Hartono'),
('mek_cahyo', 'cahyo.mekanik@bengkel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mekanik', 'Cahyo Wijaya'),
('mek_dimas', 'dimas.mekanik@bengkel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mekanik', 'Dimas Pratama'),
('mek_eko', 'eko.mekanik@bengkel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mekanik', 'Eko Susanto'),
('geral', 'geral@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'Geral Maulana'),
('rizki', 'rizki@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'Rizki Ramadhan'),
('maya', 'maya@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'Maya Anggraini'),
('fajar', 'fajar@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'Fajar Nugroho'),
('siti', 'siti@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'Siti Aminah'),
('hendra', 'hendra@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'Hendra Kurniawan'),
('lisa', 'lisa@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'Lisa Permata'),
('agus', 'agus@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'Agus Setiawan');

-- ------------------------------------------------------------
-- Dummy: customers (8 rows, linked to users role=customer)
-- ------------------------------------------------------------
INSERT INTO customers (user_id, alamat, no_hp, no_ktp) VALUES
(8, 'Jl. Merdeka No. 12, Cikarang', '081234567801', '3216012345678901'),
(9, 'Jl. Sudirman No. 45, Karawang', '081234567802', '3216012345678902'),
(10, 'Jl. Pahlawan No. 23, Cikarang', '081234567803', '3216012345678903'),
(11, 'Jl. Diponegoro No. 67, Bekasi', '081234567804', '3216012345678904'),
(12, 'Jl. Kartini No. 89, Karawang', '081234567805', '3216012345678905'),
(13, 'Jl. Gatot Subroto No. 34, Cikarang', '081234567806', '3216012345678906'),
(14, 'Jl. Veteran No. 56, Bekasi', '081234567807', '3216012345678907'),
(15, 'Jl. Ahmad Yani No. 78, Karawang', '081234567808', '3216012345678908');

-- ------------------------------------------------------------
-- Dummy: mechanics (5 rows, linked to users role=mekanik)
-- ------------------------------------------------------------
INSERT INTO mechanics (user_id, spesialisasi, status, catatan) VALUES
(3, 'Mesin & Tune Up', 'aktif', 'Senior mekanik, sudah 10 tahun pengalaman'),
(4, 'Kelistrikan & Aki', 'aktif', 'Spesialis sistem kelistrikan motor'),
(5, 'CVT & Matic', 'aktif', 'Ahli motor matic Honda dan Yamaha'),
(6, 'Ban & Rem', 'aktif', 'Cepat dan teliti untuk servis ringan'),
(7, 'Mesin & Karburator', 'nonaktif', 'Sedang cuti panjang sampai akhir bulan');

-- ------------------------------------------------------------
-- Dummy: service_types (10 rows)
-- ------------------------------------------------------------
INSERT INTO service_types (nama_layanan, deskripsi, estimasi_menit, harga, status) VALUES
('Ganti Oli Mesin', 'Penggantian oli mesin standar (belum termasuk oli)', 30, 35000.00, 'aktif'),
('Tune Up Standar', 'Tune up lengkap: bersihkan karbu, setel klep, busi, filter udara', 90, 85000.00, 'aktif'),
('Service CVT Matic', 'Bongkar CVT, bersihkan roller, V-belt check, grease', 60, 75000.00, 'aktif'),
('Ganti Ban', 'Jasa ganti ban depan/belakang (belum termasuk ban)', 30, 25000.00, 'aktif'),
('Ganti Kampas Rem', 'Penggantian kampas rem depan/belakang (belum termasuk kampas)', 30, 30000.00, 'aktif'),
('Ganti Aki', 'Penggantian aki motor + setup kelistrikan (belum termasuk aki)', 20, 25000.00, 'aktif'),
('Service Karburator', 'Bongkar karbu, bersihkan, setel ulang', 45, 50000.00, 'aktif'),
('Servis Besar', 'Bongkar mesin total, overhaul ringan', 240, 350000.00, 'aktif'),
('Cuci Motor Premium', 'Cuci lengkap + wax + semir ban', 45, 40000.00, 'aktif'),
('Tambal Ban Tubeless', 'Tambal cepat dengan metode tip top', 15, 20000.00, 'aktif');

-- ------------------------------------------------------------
-- Dummy: spare_parts (15 rows)
-- ------------------------------------------------------------
INSERT INTO spare_parts (nama_part, satuan, stok, harga_satuan, stok_minimum) VALUES
('Oli Mesin Yamalube 1L', 'botol', 25, 55000.00, 5),
('Oli Mesin Shell Advance 1L', 'botol', 18, 65000.00, 5),
('Filter Oli', 'pcs', 30, 25000.00, 10),
('Ban IRC Depan 70/90-14', 'pcs', 8, 165000.00, 3),
('Ban IRC Belakang 80/90-14', 'pcs', 7, 185000.00, 3),
('Kampas Rem Depan', 'set', 15, 45000.00, 5),
('Kampas Rem Belakang', 'set', 12, 40000.00, 5),
('Aki GS Astra GTZ5S', 'pcs', 6, 285000.00, 2),
('Busi NGK CPR8EA-9', 'pcs', 40, 35000.00, 10),
('V-Belt CVT Honda Vario', 'pcs', 10, 95000.00, 3),
('Filter Udara', 'pcs', 22, 45000.00, 5),
('Kabel Gas Universal', 'pcs', 14, 35000.00, 5),
('Lampu LED Depan', 'pcs', 9, 75000.00, 3),
('Roller CVT', 'set', 8, 55000.00, 3),
('Grease CVT', 'tube', 25, 15000.00, 8);

-- ------------------------------------------------------------
-- Dummy: time_slots (16 rows, Senin-Selasa, 8 slot per hari)
-- ------------------------------------------------------------
INSERT INTO time_slots (jam_mulai, jam_selesai, kapasitas, hari, status) VALUES
('08:00:00', '09:00:00', 2, 'senin', 'aktif'),
('09:00:00', '10:00:00', 2, 'senin', 'aktif'),
('10:00:00', '11:00:00', 2, 'senin', 'aktif'),
('11:00:00', '12:00:00', 2, 'senin', 'aktif'),
('13:00:00', '14:00:00', 2, 'senin', 'aktif'),
('14:00:00', '15:00:00', 2, 'senin', 'aktif'),
('15:00:00', '16:00:00', 2, 'senin', 'aktif'),
('16:00:00', '17:00:00', 2, 'senin', 'aktif'),
('08:00:00', '09:00:00', 2, 'selasa', 'aktif'),
('09:00:00', '10:00:00', 2, 'selasa', 'aktif'),
('10:00:00', '11:00:00', 2, 'selasa', 'aktif'),
('11:00:00', '12:00:00', 2, 'selasa', 'aktif'),
('13:00:00', '14:00:00', 2, 'selasa', 'aktif'),
('14:00:00', '15:00:00', 2, 'selasa', 'aktif'),
('15:00:00', '16:00:00', 2, 'selasa', 'aktif'),
('16:00:00', '17:00:00', 2, 'selasa', 'aktif');

-- ------------------------------------------------------------
-- Dummy: motors (12 rows, milik 8 customer)
-- ------------------------------------------------------------
INSERT INTO motors (customer_id, merk, model, plat_nomor, tahun, warna) VALUES
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

-- ------------------------------------------------------------
-- Dummy: bookings (12 rows, mix status untuk demo)
-- Mei 2026
-- ------------------------------------------------------------
INSERT INTO bookings (customer_id, motor_id, service_type_id, mechanic_id, time_slot_id, tanggal_booking, harga_jasa, total_harga, status, keluhan, catatan_mekanik, created_at) VALUES
-- Booking selesai (untuk laporan)
(1, 1, 1, 1, 1, '2026-05-10', 35000.00, 90000.00, 'siap_diambil', 'Oli udah lama belum ganti', 'Diganti oli Yamalube + filter oli baru', '2026-05-09 14:30:00'),
(2, 3, 2, 1, 2, '2026-05-11', 85000.00, 120000.00, 'siap_diambil', 'Mesin sering brebet di gas tinggi', 'Tune up komplit, ganti busi baru', '2026-05-10 09:15:00'),
(3, 4, 3, 3, 3, '2026-05-12', 75000.00, 170000.00, 'siap_diambil', 'CVT bunyi kasar', 'Ganti V-belt dan grease ulang', '2026-05-11 11:00:00'),
-- Booking selesai (belum diambil customer)
(4, 6, 5, 4, 4, '2026-05-15', 30000.00, 115000.00, 'selesai', 'Rem depan bunyi cit-cit', 'Ganti kampas rem depan baru', '2026-05-14 13:45:00'),
(5, 8, 1, 1, 5, '2026-05-15', 35000.00, 90000.00, 'selesai', 'Mau servis rutin aja', 'Ganti oli rutin + filter', '2026-05-14 16:20:00'),
-- Booking dikerjakan
(6, 9, 4, 4, 6, '2026-05-18', 25000.00, 190000.00, 'dikerjakan', 'Ban belakang gundul', NULL, '2026-05-17 10:30:00'),
(7, 11, 7, 1, 7, '2026-05-18', 50000.00, 50000.00, 'dikerjakan', 'Motor susah idle, sering mati', NULL, '2026-05-17 14:00:00'),
-- Booking antri
(8, 12, 2, NULL, 9, '2026-05-19', 85000.00, 85000.00, 'antri', 'Service rutin pertama setelah beli', NULL, '2026-05-18 08:00:00'),
(1, 2, 3, NULL, 10, '2026-05-19', 75000.00, 75000.00, 'antri', 'Suara CVT kasar', NULL, '2026-05-18 09:30:00'),
(3, 5, 6, NULL, 11, '2026-05-20', 25000.00, 25000.00, 'antri', 'Aki tekor, susah starter', NULL, '2026-05-18 10:00:00'),
-- Booking dibatalkan
(4, 7, 9, NULL, 12, '2026-05-17', 40000.00, 40000.00, 'dibatalkan', 'Mau cuci aja', 'Customer batalkan via WA', '2026-05-16 19:00:00'),
-- Booking selesai lama (untuk data laporan bulan lalu - April)
(2, 3, 1, 2, 13, '2026-04-25', 35000.00, 90000.00, 'siap_diambil', 'Servis rutin', 'Ganti oli', '2026-04-24 10:00:00');

-- ------------------------------------------------------------
-- Dummy: booking_parts (15 rows, untuk booking yang udah dikerjakan)
-- ------------------------------------------------------------
INSERT INTO booking_parts (booking_id, spare_part_id, qty, harga_saat_itu, subtotal) VALUES
-- Booking 1: Ganti oli (oli + filter)
(1, 1, 1, 55000.00, 55000.00),  -- Oli Yamalube
-- Booking 2: Tune up (busi + filter udara)
(2, 9, 1, 35000.00, 35000.00),
-- Booking 3: Service CVT (V-belt + grease)
(3, 10, 1, 95000.00, 95000.00),
-- Booking 4: Kampas rem depan
(4, 6, 1, 45000.00, 45000.00),
(4, 15, 1, 15000.00, 15000.00),  -- grease tambahan
(4, 3, 1, 25000.00, 25000.00),   -- filter sekalian
-- Booking 5: Ganti oli (oli baru)
(5, 2, 1, 55000.00, 55000.00),
-- Booking 6: Ganti ban belakang (sedang dikerjakan)
(6, 5, 1, 185000.00, 185000.00),
-- Booking 12: April - ganti oli
(12, 1, 1, 55000.00, 55000.00);

-- ------------------------------------------------------------
-- Dummy: payments (10 rows, 1 per booking yang udah selesai/siap_diambil)
-- ------------------------------------------------------------
INSERT INTO payments (booking_id, jumlah_dibayar, metode, status, tanggal_bayar, created_at) VALUES
(1, 90000.00, 'cash', 'paid', '2026-05-10 11:30:00', '2026-05-10 11:30:00'),
(2, 120000.00, 'transfer', 'paid', '2026-05-11 14:00:00', '2026-05-11 14:00:00'),
(3, 170000.00, 'ewallet', 'paid', '2026-05-12 16:15:00', '2026-05-12 16:15:00'),
(4, 115000.00, 'cash', 'pending', NULL, '2026-05-15 15:00:00'),
(5, 90000.00, 'transfer', 'pending', NULL, '2026-05-15 17:00:00'),
(6, 190000.00, 'cash', 'pending', NULL, '2026-05-18 11:00:00'),
(7, 50000.00, 'cash', 'pending', NULL, '2026-05-18 15:00:00'),
(11, 0.00, 'cash', 'cancelled', NULL, '2026-05-16 19:00:00'),
(12, 90000.00, 'cash', 'paid', '2026-04-25 11:00:00', '2026-04-25 11:00:00'),
(8, 0.00, 'cash', 'pending', NULL, '2026-05-18 08:00:00');

-- ------------------------------------------------------------
-- Dummy: service_logs (audit trail, 18 rows)
-- ------------------------------------------------------------
INSERT INTO service_logs (booking_id, user_id, status_dari, status_ke, catatan, created_at) VALUES
-- Booking 1: full lifecycle
(1, 8, 'created', 'antri', 'Booking dibuat customer', '2026-05-09 14:30:00'),
(1, 1, 'antri', 'dikerjakan', 'Assign ke Andi, mulai pengerjaan', '2026-05-10 08:30:00'),
(1, 3, 'dikerjakan', 'selesai', 'Pengerjaan selesai', '2026-05-10 09:15:00'),
(1, 1, 'selesai', 'siap_diambil', 'Verifikasi OK, sudah bayar', '2026-05-10 11:30:00'),
-- Booking 2: full lifecycle
(2, 9, 'created', 'antri', 'Booking dibuat customer', '2026-05-10 09:15:00'),
(2, 1, 'antri', 'dikerjakan', 'Mulai tune up', '2026-05-11 10:00:00'),
(2, 3, 'dikerjakan', 'selesai', 'Tune up selesai, motor tes jalan oke', '2026-05-11 13:30:00'),
(2, 1, 'selesai', 'siap_diambil', 'Sudah bayar transfer', '2026-05-11 14:00:00'),
-- Booking 4: sampai selesai
(4, 11, 'created', 'antri', 'Booking dibuat customer', '2026-05-14 13:45:00'),
(4, 2, 'antri', 'dikerjakan', 'Assign ke Dimas', '2026-05-15 09:00:00'),
(4, 6, 'dikerjakan', 'selesai', 'Kampas rem sudah diganti', '2026-05-15 10:30:00'),
-- Booking 6: masih dikerjakan
(6, 13, 'created', 'antri', 'Booking dibuat customer', '2026-05-17 10:30:00'),
(6, 1, 'antri', 'dikerjakan', 'Mulai pengerjaan ban', '2026-05-18 10:30:00'),
-- Booking 7: masih dikerjakan
(7, 14, 'created', 'antri', 'Booking dibuat customer', '2026-05-17 14:00:00'),
(7, 1, 'antri', 'dikerjakan', 'Mulai bongkar karbu', '2026-05-18 14:00:00'),
-- Booking 11: dibatalkan
(11, 11, 'created', 'antri', 'Booking dibuat customer', '2026-05-16 19:00:00'),
(11, 11, 'antri', 'dibatalkan', 'Customer cancel via WhatsApp', '2026-05-17 07:30:00'),
-- Booking 12: April booking
(12, 9, 'created', 'antri', 'Booking dibuat customer', '2026-04-24 10:00:00');


-- ============================================================
-- VERIFIKASI DATA (Optional - jalankan untuk cek)
-- ============================================================
-- SELECT 'users' AS tabel, COUNT(*) AS jumlah FROM users
-- UNION SELECT 'customers', COUNT(*) FROM customers
-- UNION SELECT 'mechanics', COUNT(*) FROM mechanics
-- UNION SELECT 'service_types', COUNT(*) FROM service_types
-- UNION SELECT 'spare_parts', COUNT(*) FROM spare_parts
-- UNION SELECT 'time_slots', COUNT(*) FROM time_slots
-- UNION SELECT 'motors', COUNT(*) FROM motors
-- UNION SELECT 'bookings', COUNT(*) FROM bookings
-- UNION SELECT 'booking_parts', COUNT(*) FROM booking_parts
-- UNION SELECT 'payments', COUNT(*) FROM payments
-- UNION SELECT 'service_logs', COUNT(*) FROM service_logs;

-- ============================================================
-- END OF FILE
-- ============================================================
