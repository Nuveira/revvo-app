<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['customer']);

$userId = $_SESSION['user_id'] ?? 0;

/*
|--------------------------------------------------------------------------
| Customer Login
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
| Motor Customer
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

<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Tambah Booking</title>

<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet"href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>

<body class="font-['Plus_Jakarta_Sans']">

<div class="flex h-screen">

    <?php include 'nav.php'; ?>

    <div class="flex-1 bg-gray-100">

        <!-- Header -->
        <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex justify-between items-center w-full p-5">

            <div class="mx-2">
                <p class="text-[#8E1616]">
                    BOOKING SERVICE
                </p>

                <p class="text-4xl text-white py-2">
                    Tambah Booking
                </p>

                <p class="text-white">
                    Buat jadwal service motor Anda.
                </p>
            </div>

        </div>

        <!-- Form -->
        <div class="mx-4 mt-4">

            <div class="bg-white rounded-lg border border-[#eadede] p-6 shadow-sm">

                <form action="proses_booking.php" method="POST" class="space-y-5">

                    <!-- Motor -->
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Pilih Motor
                        </label>

                        <select
                            name="motor_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#8E1616]"
                            required
                        >

                            <?php while($motor = $motors->fetch_assoc()): ?>

                                <option value="<?= $motor['id']; ?>">

                                    <?= htmlspecialchars(
                                        $motor['brand']
                                        .' '.
                                        $motor['model']
                                        .' - '.
                                        $motor['plate_number']
                                    ); ?>

                                </option>

                            <?php endwhile; ?>

                        </select>
                    </div>

                    <!-- Service -->
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Jenis Service
                        </label>

                        <select
                            name="service_type_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3"
                            required
                        >

                            <?php while($service = $serviceTypes->fetch_assoc()): ?>

                                <option value="<?= $service['id']; ?>">

                                    <?= htmlspecialchars($service['name']); ?>

                                </option>

                            <?php endwhile; ?>

                        </select>
                    </div>

                    <!-- Jadwal -->
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Tanggal Booking
                        </label>

                        <input
                            type="date"
                            name="booking_date"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3"
                            required
                        >
                    </div>

                    <!-- Time Slot -->
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Time Slot
                        </label>

                        <select
                            name="time_slot_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3"
                            required
                        >

                            <?php while($slot = $timeSlots->fetch_assoc()): ?>

                                <option value="<?= $slot['id']; ?>">

                                    <?= htmlspecialchars(
                                        $slot['day']
                                        .' | '.
                                        substr($slot['start_time'],0,5)
                                        .' - '.
                                        substr($slot['end_time'],0,5)
                                    ); ?>

                                </option>

                            <?php endwhile; ?>

                        </select>
                    </div>

                    <!-- Keluhan -->
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Keluhan
                        </label>

                        <textarea
                            name="customer_complaint"
                            rows="4"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3"
                            placeholder="Jelaskan keluhan motor..."
                        ></textarea>
                    </div>

                    <!-- Button -->
                    <div class="flex gap-3">

                        <button
                            type="submit"
                            class="bg-[#8E1616] px-6 py-3 rounded-lg text-white hover:bg-[#6f1111] transition"
                        >
                            Simpan Booking
                        </button>

                        <a
                            href="dashboard.php"
                            class="bg-gray-200 px-6 py-3 rounded-lg hover:bg-gray-300 transition"
                        >
                            Batal
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

</body>
</html>