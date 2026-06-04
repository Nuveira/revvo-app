<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['customer']);

$userId = $_SESSION['user_id'] ?? 0;
$bookingId = (int) ($_GET['id'] ?? 0);

if (!$bookingId) {
    header('Location: history.php');
    exit;
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

    header('Location: history.php');
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
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Booking</title>

<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>

<body class="font-['Plus_Jakarta_Sans'] bg-gray-100">

<div class="flex h-screen">

    <?php include 'nav.php'; ?>

    <div class="flex-1 overflow-auto">

        <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] p-5">

            <p class="text-[#8E1616] uppercase text-sm">
                Booking
            </p>

            <h1 class="text-4xl text-white py-2">
                Edit Booking
            </h1>

            <p class="text-white">
                Ubah data booking Anda.
            </p>

        </div>

        <div class="p-6">

            <div class="bg-white border border-[#eadede] rounded-lg shadow-sm p-6">

                <form
                    action="update_booking.php"
                    method="POST"
                    class="space-y-5"
                >

                    <input
                        type="hidden"
                        name="booking_id"
                        value="<?= $booking['id']; ?>"
                    >

                    <div>

                        <label class="block mb-2 font-medium">
                            Motor
                        </label>

                        <select
                            name="motor_id"
                            class="w-full border rounded-lg p-3"
                            required
                        >

                            <?php while($motor = $motors->fetch_assoc()): ?>

                                <option
                                    value="<?= $motor['id']; ?>"
                                    <?= $motor['id'] == $booking['motor_id'] ? 'selected' : ''; ?>
                                >

                                    <?= htmlspecialchars(
                                        $motor['brand']
                                        .' '.
                                        $motor['model']
                                        .' ('.
                                        $motor['plate_number']
                                        .')'
                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div>

                        <label class="block mb-2 font-medium">
                            Jenis Service
                        </label>

                        <select
                            name="service_type_id"
                            class="w-full border rounded-lg p-3"
                            required
                        >

                            <?php while($service = $serviceTypes->fetch_assoc()): ?>

                                <option
                                    value="<?= $service['id']; ?>"
                                    <?= $service['id'] == $booking['service_type_id'] ? 'selected' : ''; ?>
                                >

                                    <?= htmlspecialchars($service['name']); ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div>

                        <label class="block mb-2 font-medium">
                            Tanggal Booking
                        </label>

                        <input
                            type="date"
                            name="booking_date"
                            value="<?= $booking['booking_date']; ?>"
                            class="w-full border rounded-lg p-3"
                            required
                        >

                    </div>

                    <div>

                        <label class="block mb-2 font-medium">
                            Keluhan
                        </label>

                        <textarea
                            name="customer_complaint"
                            rows="4"
                            class="w-full border rounded-lg p-3"
                        ><?= htmlspecialchars($booking['customer_complaint']); ?></textarea>

                    </div>

                    <button
                        type="submit"
                        class="bg-[#8E1616] text-white px-6 py-3 rounded-lg hover:bg-[#6d1111]"
                    >
                        Simpan Perubahan
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

</body>
</html>