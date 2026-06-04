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
| Booking Aktif
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT
    b.*,
    m.brand,
    m.model,
    m.plate_number,
    s.name AS service_name,
    mc.user_id AS mechanic_name
FROM bookings b

INNER JOIN motors m
    ON b.motor_id = m.id

INNER JOIN service_types s
    ON b.service_type_id = s.id

LEFT JOIN mechanics mc
    ON b.mechanic_id = mc.id

WHERE b.customer_id = ?
AND b.status != 'queued'

ORDER BY b.created_at DESC
");

$stmt->bind_param("i", $customerId);
$stmt->execute();

$bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Booking Saya</title>

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
                Booking Saya
            </h1>

            <p class="text-white">
                Pantau progres servis motor Anda.
            </p>

        </div>

        <div class="p-6">

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm overflow-hidden">

                <div class="p-4 border-b">

                    <h2 class="font-semibold text-lg">
                        Daftar Booking Aktif
                    </h2>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="p-4 text-left">ID</th>
                                <th class="p-4 text-left">Motor</th>
                                <th class="p-4 text-left">Service</th>
                                <th class="p-4 text-left">Mekanik</th>
                                <th class="p-4 text-left">Status</th>
                                <th class="p-4 text-left">Aksi</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php while($row = $bookings->fetch_assoc()): ?>

                            <?php

                            $badge = 'bg-gray-100 text-gray-700';

                            if($row['status'] == 'in_progress'){
                                $badge = 'bg-blue-100 text-blue-700';
                            }

                            if($row['status'] == 'completed'){
                                $badge = 'bg-green-100 text-green-700';
                            }

                            if($row['status'] == 'ready_for_pickup'){
                                $badge = 'bg-purple-100 text-purple-700';
                            }

                            ?>

                            <tr class="border-t">

                                <td class="p-4">
                                    #<?= $row['id']; ?>
                                </td>

                                <td class="p-4">

                                    <?= htmlspecialchars(
                                        $row['brand']
                                        .' '.
                                        $row['model']
                                    ); ?>

                                    <br>

                                    <span class="text-sm text-gray-500">

                                        <?= htmlspecialchars(
                                            $row['plate_number']
                                        ); ?>

                                    </span>

                                </td>

                                <td class="p-4">
                                    <?= htmlspecialchars($row['service_name']); ?>
                                </td>

                                <td class="p-4">

                                    <?= $row['mechanic_name']
                                        ? htmlspecialchars($row['mechanic_name'])
                                        : '-'; ?>

                                </td>

                                <td class="p-4">

                                    <span class="px-3 py-1 rounded-full text-sm <?= $badge; ?>">

                                        <?= ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $row['status']
                                            )
                                        ); ?>

                                    </span>

                                </td>

                                <td class="p-4">

                                    <a
                                        href="booking_detail.php?id=<?= $row['id']; ?>"
                                        class="bg-blue-500 text-white px-3 py-1 rounded"
                                    >
                                        Detail
                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>