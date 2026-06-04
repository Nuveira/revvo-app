<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['customer']);

$userId = $_SESSION['user_id'] ?? 0;

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

$bookingId = (int) ($_GET['id'] ?? 0);

if (!$bookingId) {
    header('Location: history.php');
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
| Detail Booking
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT
    b.*,
    m.brand,
    m.model,
    m.plate_number,
    s.name AS service_name,
    ts.day,
    ts.start_time,
    ts.end_time,
    me.user_id AS mechanic_name
FROM bookings b
INNER JOIN motors m
    ON b.motor_id = m.id
INNER JOIN service_types s
    ON b.service_type_id = s.id
INNER JOIN time_slots ts
    ON b.time_slot_id = ts.id
LEFT JOIN mechanics me
    ON b.mechanic_id = me.id
WHERE b.id = ?
AND b.customer_id = ?
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
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Detail Booking</title>

<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet"href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
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
                Detail Booking
            </h1>

            <p class="text-white">
                Informasi lengkap booking service.
            </p>

        </div>

        <div class="p-6">

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6">

                <div class="grid md:grid-cols-2 gap-6">

                    <div>

                        <h3 class="font-semibold mb-3">
                            Data Motor
                        </h3>

                        <p>
                            <?= htmlspecialchars(
                                $booking['brand']
                                .' '.
                                $booking['model']
                            ) ?>
                        </p>

                        <p class="text-gray-500">
                            <?= htmlspecialchars(
                                $booking['plate_number']
                            ) ?>
                        </p>

                    </div>

                    <div>

                        <h3 class="font-semibold mb-3">
                            Service
                        </h3>

                        <p>
                            <?= htmlspecialchars(
                                $booking['service_name']
                            ) ?>
                        </p>

                    </div>

                    <div>

                        <h3 class="font-semibold mb-3">
                            Jadwal
                        </h3>

                        <p>
                            <?= date(
                                'd M Y',
                                strtotime(
                                    $booking['booking_date']
                                )
                            ) ?>
                        </p>

                        <p>
                            <?= ucfirst(
                                $booking['day']
                            ) ?>

                            |

                            <?= substr(
                                $booking['start_time'],
                                0,
                                5
                            ) ?>

                            -

                            <?= substr(
                                $booking['end_time'],
                                0,
                                5
                            ) ?>
                        </p>

                    </div>

                    <div>

                        <h3 class="font-semibold mb-3">
                            Status
                        </h3>

                        <p>
                            <?= ucfirst(
                                str_replace(
                                    '_',
                                    ' ',
                                    $booking['status']
                                )
                            ) ?>
                        </p>

                    </div>

                    <div>

                        <h3 class="font-semibold mb-3">
                            Harga Service
                        </h3>

                        <p>
                            Rp <?= number_format(
                                $booking['service_price']
                            ) ?>
                        </p>

                    </div>

                    <div>

                        <h3 class="font-semibold mb-3">
                            Total Harga
                        </h3>

                        <p>
                            Rp <?= number_format(
                                $booking['total_price']
                            ) ?>
                        </p>

                    </div>

                </div>

                <hr class="my-6">

                <div class="mb-6">

                    <h3 class="font-semibold mb-3">
                        Keluhan Customer
                    </h3>

                    <p class="text-gray-700">
                        <?= nl2br(
                            htmlspecialchars(
                                $booking['customer_complaint']
                            )
                        ) ?>
                    </p>

                </div>

                <div class="mb-6">

                    <h3 class="font-semibold mb-3">
                        Mekanik
                    </h3>

                    <p>
                        <?= $booking['mechanic_name']
                            ? htmlspecialchars($booking['mechanic_name'])
                            : 'Belum ditugaskan';
                        ?>
                    </p>

                </div>

                <div>

                    <h3 class="font-semibold mb-3">
                        Catatan Mekanik
                    </h3>

                    <p class="text-gray-700">
                        <?= $booking['mechanic_note']
                            ? nl2br(htmlspecialchars($booking['mechanic_note']))
                            : 'Belum ada catatan';
                        ?>
                    </p>

                </div>

                <div class="mt-6">

                    <a
                        href="history.php"
                        class="bg-[#8E1616] text-white px-5 py-2 rounded-lg"
                    >
                        Kembali
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>