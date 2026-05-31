<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Dashboard | REVVO';
require_once '../../config/koneksi.php';

// Ambil data user dari session
$user_id = $_SESSION['user_id'] ?? null;
$nama = 'Guest';
$role = '';
$profile_photo = null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT name, role, profile_photo FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $nama = $row['name'];
        $role = $row['role'];
        $profile_photo = $row['profile_photo'];
    }
    $stmt->close();
}

// Hitung booking aktif
$jumlah_booking = 0;
$jumlah_motor = 0;
if ($user_id) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total FROM bookings b
        JOIN customers c ON b.customer_id = c.id
        WHERE c.user_id = ? AND b.status IN ('queued', 'in_progress')
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $jumlah_booking = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    $stmt = $conn->prepare("
        SELECT COUNT(*) as total FROM motors m
        JOIN customers c ON m.customer_id = c.id
        WHERE c.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $jumlah_motor = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
}

// Ambil booking aktif
$booking = null;
if ($user_id) {
    $stmt = $conn->prepare("
        SELECT b.status, b.customer_complaint, b.booking_date,
               m.brand, m.model, m.plate_number,
               st.name AS service_name,
               ts.start_time, ts.end_time
        FROM bookings b
        JOIN customers c ON b.customer_id = c.id
        JOIN motors m ON b.motor_id = m.id
        JOIN service_types st ON b.service_type_id = st.id
        JOIN time_slots ts ON b.time_slot_id = ts.id
        WHERE c.user_id = ? AND b.status IN ('queued', 'in_progress')
        ORDER BY b.booking_date DESC
        LIMIT 1
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();
}

$steps = ['queued' => 'Antri', 'in_progress' => 'Dikerjakan', 'completed' => 'Selesai', 'ready_for_pickup' => 'Siap Diambil'];
$step_keys = array_keys($steps);
$current_step = $booking ? array_search($booking['status'], $step_keys) : -1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/logo.png') ?>">
</head>
<body class="font-['Plus_Jakarta_Sans']">
    <div class="flex h-screen">
        <?php include 'nav.php'; ?>

        <div class="flex-1 bg-gray-100">
            <!-- Header -->
            <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex justify-between items-center w-full p-5">
                <div class="mx-2">
                    <p class="text-[#8E1616]">SELAMAT DATANG KEMBALI</p>
                    <p class="text-4xl text-white py-2">Halo, <?= htmlspecialchars($nama) ?></p>
                    <p class="text-white">Kamu punya <span class="text-[#FF0000]"><?= $jumlah_booking ?> booking aktif</span> dan <span class="text-[#FF0000]"><?= $jumlah_motor ?> motor</span> terdaftar</p>

                </div>
                <div class="ml-3 px-3 py-3 rounded inline-block items-center">
                    <a href=tambah_booking.php class="bg-[#FF0000] ml-3 px-4 py-3 rounded text-white whitespace-nowrap hover:bg-[#6e1111] transition flex items-center gap-2 shadow-[0_0_15px_rgba(142,22,22,0.3)] shadow-red-500/40">
                        <span class="material-symbols-outlined">
                            add_circle
                        </span>
                        Booking Service Baru
                    </a>
                </div>
            </div>

            <!-- Main Dashboard -->
            <div class="flex gap-4 my-2 mx-4">
                <div class="flex-[2] bg-white rounded-lg border border-[#eadede] p-6 w-full shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase">Booking Aktif</p>
                            <h3 class="mt-2 text-[32px] leading-none font-medium text-[#8E1616]">
                                <?= $booking ? htmlspecialchars($booking['service_name']) : 'Tidak ada booking' ?>
                            </h3>
                        </div>

                        <span class="rounded-full bg-[#f8eeee] px-4 py-1 text-sm font-medium text-[#8E1616]">
                            <?= $booking ? htmlspecialchars(ucfirst($booking['status'])) : '-' ?>
                        </span>
                    </div>
                    
                    <div class="mt-8 grid grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-400">Motor</p>
                            <?= $booking ? htmlspecialchars($booking['model']) : '-' ?>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Tanggal</p>
                            <?= $booking ? htmlspecialchars($booking['booking_date']) : '-' ?>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Waktu</p>
                            <p><?= $booking ? date('H:i', strtotime($booking['start_time'])) . ' - ' . date('H:i', strtotime($booking['end_time'])) : '-' ?></p>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-4">
                        <a 
                            href="booking.php"
                            class="flex-[2] bg-[#2f2f2f] px-6 py-4 rounded-lg text-center text-base font-semibold text-white transition hover:bg-black"
                        >
                            lihat detail
                        </a>
                    
                        <a 
                            href="booking_edit.php"
                            class="flex-1 bg-[#8E1616] px-6 py-4 rounded-lg text-center text-base font-semibold text-white transition hover:bg-[#6f1111]"
                        >
                            ubah jadwal
                        </a>
                    </div>
                </div>


            <div class="flex-1 bg-[#8E1616] rounded-lg border border-[#eadede] p-6 w-full shadow-sm">
                <p class="items-start text-[11px] tracking-[0.2em] text-gray-400 uppercase">
                    Motor terdaftar
                </p>
                
                <div class="mt-6 space-y-6">
                    <div class="items-center gap-4 bg-[#a32828]/40 px-4 py-4">
                        <p class="text-lg font-semibold text-white">
                            <?= $booking ? htmlspecialchars($booking['model']) : 'Tidak ada motor terdaftar'; ?>
                        </p>
                        <p class="text-sm text-[#f1caca]">
                            <?= $booking ? htmlspecialchars($booking['plate_number']) : '-'; ?>
                        </p>
                    </div>
                </div>
                
                <a
                    href="motor.php"
                    class="mt-8 block w-full bg-white py-4 rounded-lg text-center text-base font-medium text-[#8E1616] transition hover:bg-gray-300"
                >
                    Kelola Motor
                </a>
            </div> 
        </div>
            
        <!--- Track Progress --->
        <span class="rounded-full bg-[#FF0000] mx-12 px-4 py-1 text-sm font-medium text-white shadow-[0_0_20px_rgba(255,0,0,0.6)]">Track Progress</span>
        <div class="flex items-center w-full mt-6 px-15">
            <?php foreach ($steps as $key => $label): 
                $index = array_search($key, $step_keys);
                $is_done = $index <= $current_step;
            ?>

                <!-- Step -->
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center <?= $is_done ? 'bg-orange-400 text-white' : 'bg-gray-200 text-gray-400' ?>">
                        <?php if ($is_done): ?>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs mt-1 <?= $is_done ? 'text-black font-medium' : 'text-gray-400' ?>"><?= $label ?></p>
                </div>

            <!-- Line (kecuali setelah step terakhir) -->
            <?php if ($index < count($steps) - 1): ?>
                <div class="flex-1 h-0.5 mx-2 <?= $index < $current_step ? 'bg-orange-400' : 'bg-gray-200' ?>"></div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
