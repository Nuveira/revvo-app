<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Dashboard | REVVO';
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
require_once '../../includes/customer_role.php';

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

// Ambil motor customer
$motors = [];
if ($user_id) {
    $stmt = $conn->prepare("
        SELECT m.brand, m.model, m.plate_number
        FROM motors m
        JOIN customers c ON m.customer_id = c.id
        WHERE c.user_id = ?
        ORDER BY m.id DESC
        LIMIT 1
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $motors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Ambil booking aktif
$histori = [];
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
        WHERE c.user_id = ? AND b.status NOT IN ('cancelled')
        ORDER BY b.booking_date DESC
        LIMIT 1
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();
}

if ($user_id) {
    $stmt = $conn->prepare("
        SELECT b.id, b.booking_date, b.total_price, b.status,
               st.name AS service_name
        FROM bookings b
        JOIN customers c ON b.customer_id = c.id
        JOIN service_types st ON b.service_type_id = st.id
        WHERE c.user_id = ?
        ORDER BY b.booking_date DESC
        LIMIT 5
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $histori = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$steps = ['queued' => 'Antri', 'in_progress' => 'Dikerjakan', 'completed' => 'Selesai', 'ready_for_pickup' => 'Siap Diambil'];
$step_keys = array_keys($steps);
$current_step = $booking ? array_search($booking['status'], $step_keys) : -1;
?>

<!--- HTML --->
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
<body class="font-['Plus_Jakarta_Sans'] overflow-hidden">
    <div class="flex h-screen overflow-hidden">
        <?php include 'nav.php'; ?>

        <div class="flex-1 flex-col min-w-0 bg-gray-100 overflow-y-auto overflow-x-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex flex-col gap-4 md:flex-row md:justify-between md:items-center w-full p-5">
                <div class="min-w-0">
                    <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">SELAMAT DATANG KEMBALI</p>
                    <p class="mt-2 text-2xl sm:text-4xl text-white font-semibold break-words">Halo, <?= htmlspecialchars($nama) ?></p>
                    <p class="text-white">Kamu punya <span class="text-[#FF0000]"><?= $jumlah_booking ?> booking aktif</span> dan <span class="text-[#FF0000]"><?= $jumlah_motor ?> motor</span> terdaftar</p>

                </div>
                <div class="py-3 rounded inline-block items-center md:ml-3 md:px-3">
                    <a href="tambah_booking.php" class="bg-[#FF0000] px-4 py-3 rounded text-white whitespace-nowrap hover:bg-[#6e1111] transition inline-flex items-center gap-2 shadow-[0_0_15px_rgba(142,22,22,0.3)] shadow-red-500/40">
                        <span class="material-symbols-outlined">
                            add_circle
                        </span>
                        Booking Service Baru
                    </a>
                </div>
            </div>

            <!-- Main Dashboard -->
            <div class="flex flex-col gap-4 my-2 mx-4 lg:flex-row">
                <div class="flex-[2] bg-white rounded-lg border border-[#eadede] p-6 w-full shadow-sm min-w-0">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase">Booking Aktif</p>
                            <h3 class="mt-2 text-2xl sm:text-[32px] leading-none font-medium text-[#8E1616] break-words">
                                <?= $booking ? htmlspecialchars($booking['service_name']) : 'Tidak ada booking' ?>
                            </h3>
                        </div>

                        <span class="w-fit rounded-full bg-[#f8eeee] px-4 py-1 text-sm font-medium text-[#8E1616]">
                            <?= $booking ? htmlspecialchars(ucfirst($booking['status'])) : '-' ?>
                        </span>
                    </div>
                    
                    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div class="min-w-0">
                            <p class="text-sm text-gray-400">Motor</p>
                            <p class="break-words"><?= $booking ? htmlspecialchars($booking['model']) : '-' ?></p>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm text-gray-400">Tanggal</p>
                            <p><?= $booking ? htmlspecialchars($booking['booking_date']) : '-' ?></p>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm text-gray-400">Waktu</p>
                            <p><?= $booking ? date('H:i', strtotime($booking['start_time'])) . ' - ' . date('H:i', strtotime($booking['end_time'])) : '-' ?></p>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col gap-4 sm:flex-row">
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


            <div class="flex-1 bg-[#8E1616] rounded-lg border border-[#eadede] p-6 w-full shadow-sm min-w-0">
                <p class="items-start text-[11px] tracking-[0.2em] text-gray-400 uppercase">
                    Motor terdaftar | <?= $jumlah_motor ?> motor tersedia
                </p>
                
                <div class="mt-6 space-y-2">
                    <?php if (!empty($motors)): ?>
                    <?php foreach ($motors as $m): ?>
                    <div class="bg-[#a32828]/100 px-4 py-4">
                        <p class="text-lg font-semibold text-white break-words"><?= htmlspecialchars($m['brand'] . ' ' . $m['model']) ?></p>
                        <p class="text-sm text-[#f1caca]"><?= htmlspecialchars($m['plate_number']) ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-white/70 text-center text-sm p-8">Belum ada motor terdaftar</p>
                    <?php endif; ?>
                </div>
                
                <a
                href="motor.php"
                class="mt-8 block w-full bg-white py-4 rounded-lg text-center text-base font-medium text-[#8E1616] transition hover:bg-gray-300"
                >
                Lihat Selengkapnya →
            </a>
        </div> 
    </div>
    
    <!--- Track Progress --->
    <?php if ($booking): ?>
    <div class="mt-4">
    <span class="rounded-full bg-[#FF0000] mx-4 md:mx-12 px-4 py-1 text-sm font-medium text-white shadow-[0_0_20px_rgba(255,0,0,0.6)]">
        Track Progress
    </span>
    </div>
    <div class="w-full mt-6 overflow-x-auto px-4 md:px-16">
    <div class="flex min-w-[520px] items-center pb-2">
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
            <div class="flex-1 h-0.5 mx-2 -mt-5 <?= $index < $current_step ? 'bg-orange-400' : 'bg-gray-200' ?>"></div>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>
    </div>
    <?php else: ?>
        <p class="text-gray-400 text-sm px-12 mt-4">Tidak ada booking aktif</p>
    <?php endif; ?>
    
    <!--- History booking --->
    <div class="bg-white rounded-lg border border-[#eadede] p-6 mx-4 mt-4 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-4">
            <h3 class="font-semibold text-lg">
                History Booking
            </h3>
            <a href='history.php' class="bg-[#8E1616] px-4 py-2 rounded-sm text-center text-[12px] font-semibold text-white transition hover:bg-[#6f1111]">
                Lihat Selengkapnya →
            </a>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full min-w-[720px] text-sm">
            <thead>
                <tr class="text-[11px] tracking-[0.15em] text-gray-400 uppercase border-b border-gray-100">
                    <th class="text-left py-3">ID BOOKING</th>
                    <th class="text-left py-3">Tanggal</th>
                    <th class="text-left py-3">Layanan</th>
                    <th class="text-left py-3">Total</th>
                    <th class="text-left py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($histori as $h): ?>
                <tr class="border-b border-gray-50">
                    <td class="py-3 text-gray-500">BK-<?= str_pad($h['id'], 4, '0', STR_PAD_LEFT) ?></td>
                    <td class="py-3"><?= date('d M Y', strtotime($h['booking_date'])) ?></td>
                    <td class="py-3"><?= htmlspecialchars($h['service_name']) ?></td>
                    <td class="py-3 font-semibold">Rp<?= number_format($h['total_price'], 0, ',', '.') ?></td>
                    <td class="py-3">
                    <?php
                        $status_color = match($h['status']) {
                            'ready_for_pickup' => 'text-green-500',
                            'completed' => 'text-blue-500',
                            'in_progress' => 'text-yellow-500',
                            'queued' => 'text-gray-500',
                            'cancelled' => 'text-red-400',
                            default => 'text-gray-400'
                        };
                        $status_label = match($h['status']) {
                            'ready_for_pickup' => 'SIAP DIAMBIL',
                            'completed' => 'SELESAI',
                            'in_progress' => 'DIKERJAKAN',
                            'queued' => 'ANTRI',
                            'cancelled' => 'DIBATALKAN',
                            default => strtoupper($h['status'])
                        };
                    ?>
                    <span class="font-semibold text-xs <?= $status_color ?>"><?= $status_label ?></span>
                    </td>
                </tr>
                    <?php endforeach; ?>
                    <?php if (empty($histori)): ?>
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-400">
                            Belum ada histori booking
                        </td>
                    </tr>
                    <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
    
    <?php include 'footer.php';?>
    </div>
</body>
</html>
