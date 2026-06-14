<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['customer']);

$userId = $_SESSION['user_id'] ?? 0;
$bookingId = (int) ($_GET['id'] ?? 0);

if (!$bookingId) {
    header('Location: booking.php');
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;

$nama = 'Guest';
$role = '';
$profile_photo = null;

if ($user_id) {

    $stmtUser = $conn->prepare("
        SELECT name, role, profile_photo
        FROM users
        WHERE id = ?
    ");

    $stmtUser->bind_param("i", $user_id);
    $stmtUser->execute();

    $userData = $stmtUser->get_result()->fetch_assoc();

    if ($userData) {

        $nama = $userData['name'];
        $role = $userData['role'];
        $profile_photo = $userData['profile_photo'];

    }

    $stmtUser->close();
}

/*
|--------------------------------------------------------------------------
| Customer
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT id
    FROM customers
    WHERE user_id = ?
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$customer = $stmt->get_result()->fetch_assoc();

if (!$customer) {
    die('Customer tidak ditemukan');
}

$customerId = $customer['id'];

/*
|--------------------------------------------------------------------------
| Booking
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT *
FROM bookings
WHERE id = ?
AND customer_id = ?
");

$stmt->bind_param(
    "ii",
    $bookingId,
    $customerId
);

$stmt->execute();

$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    die('Booking tidak ditemukan');
}

if ($booking['status'] !== 'queued') {
    $_SESSION['error'] =
        'Booking yang sudah diproses tidak dapat diedit';

    header('Location: booking.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Motor
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT *
FROM motors
WHERE customer_id = ?
ORDER BY id DESC
");

$stmt->bind_param("i", $customerId);
$stmt->execute();

$motors = $stmt->get_result();

/*
|--------------------------------------------------------------------------
| Service Type
|--------------------------------------------------------------------------
*/
$serviceTypes = $conn->query("
SELECT *
FROM service_types
WHERE status='active'
ORDER BY name
");

/*
|--------------------------------------------------------------------------
| Time Slot
|--------------------------------------------------------------------------
*/
$timeSlots = $conn->query("
SELECT *
FROM time_slots
WHERE status='active'
ORDER BY day,start_time
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <title>Edit Booking | REVVO</title>
</head>
<body class="font-['Plus_Jakarta_Sans'] overflow-hidden">
<div class="flex h-screen overflow-hidden">

    <?php include 'nav.php'; ?>

    <main class="flex-1 min-w-0 bg-gray-100 overflow-y-auto overflow-x-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between">
            <div class="min-w-0">
                <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">Booking</p>
                <h1 class="mt-2 text-2xl sm:text-4xl text-white font-semibold break-words">Edit Booking</h1>
            </div>
            <a href="booking.php" class="bg-[#FF0000] px-4 py-3 rounded text-sm font-semibold text-white whitespace-nowrap hover:bg-[#6e1111] transition inline-flex items-center gap-2 shadow-[0_0_15px_rgba(142,22,22,0.3)] shadow-red-500/40">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                Kembali
            </a>
        </div>

        <div class="p-4 mx-auto">
            <div class="rounded-lg border border-[#eadede] bg-white p-5 shadow-sm">
                <div class="mb-5">
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Form Booking</p>
                    <h2 class="mt-2 text-xl font-semibold text-gray-900">Ubah detail booking</h2>
                </div>

                <form action="update_booking.php" method="POST" class="space-y-4">
                    <input type="hidden" name="booking_id" value="<?= $booking['id']; ?>">

                    <!-- Motor -->
                    <div>
                        <label for="motor_id" class="text-sm font-medium text-gray-700">Motor</label>
                        <select id="motor_id" name="motor_id" required
                            class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                            <?php while($motor = $motors->fetch_assoc()): ?>
                                <option value="<?= $motor['id']; ?>" <?= $motor['id'] == $booking['motor_id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($motor['brand'] . ' ' . $motor['model'] . ' (' . $motor['plate_number'] . ')') ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Jenis Service -->
                    <div>
                        <label for="service_type_id" class="text-sm font-medium text-gray-700">Jenis Service</label>
                        <select id="service_type_id" name="service_type_id" required
                            class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                            <?php while($service = $serviceTypes->fetch_assoc()): ?>
                                <option value="<?= $service['id']; ?>" <?= $service['id'] == $booking['service_type_id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($service['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Tanggal Booking -->
                    <div>
                        <label for="booking_date" class="text-sm font-medium text-gray-700">Tanggal Booking</label>
                        <input id="booking_date" name="booking_date" type="date" required
                            value="<?= htmlspecialchars($booking['booking_date']); ?>"
                            class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                    </div>

                    <!-- Pilih Waktu -->
                    <div>
                        <label for="time_slot_id" class="text-sm font-medium text-gray-700">Pilih Waktu</label>
                        <select id="time_slot_id" name="time_slot_id" required
                            class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                            <?php while($slot = $timeSlots->fetch_assoc()): ?>
                                <option value="<?= $slot['id']; ?>" <?= $slot['id'] == $booking['time_slot_id'] ? 'selected' : ''; ?>>
                                    <?= ucfirst(htmlspecialchars($slot['day'])) ?> | <?= substr($slot['start_time'], 0, 5) ?> - <?= substr($slot['end_time'], 0, 5) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Keluhan -->
                    <div>
                        <label for="customer_complaint" class="text-sm font-medium text-gray-700">
                            Keluhan <span class="text-gray-400 font-normal">(opsional)</span>
                        </label>
                        <textarea id="customer_complaint" name="customer_complaint" rows="4"
                            placeholder="Jelaskan keluhan motor kamu..."
                            class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616] resize-none"><?= htmlspecialchars($booking['customer_complaint']); ?></textarea>
                    </div>

                    <!-- Tombol -->
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded bg-[#8E1616] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#6f1111]">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <?php include 'footer.php'; ?>
    </main>

</div>
</body>
</html>