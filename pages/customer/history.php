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
| Booking History
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT
    b.*,
    m.brand,
    m.model,
    m.plate_number,
    s.name AS service_name
FROM bookings b
INNER JOIN motors m
    ON b.motor_id = m.id
INNER JOIN service_types s
    ON b.service_type_id = s.id
WHERE b.customer_id = ?
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

<title>History Booking</title>

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
                History Booking
            </h1>

            <p class="text-white">
                Riwayat booking service motor Anda.
            </p>

        </div>

        <div class="p-6">

            <?php if(isset($_SESSION['success'])): ?>

                <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4">

                    <?= $_SESSION['success']; ?>

                </div>

                <?php unset($_SESSION['success']); ?>

            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>

                <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4">

                    <?= $_SESSION['error']; ?>

                </div>

                <?php unset($_SESSION['error']); ?>

            <?php endif; ?>

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm overflow-hidden">

                <div class="p-4 border-b">

                    <h2 class="font-semibold text-lg">
                        Daftar Booking
                    </h2>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-gray-50">

                        <tr>

                            <th class="p-4 text-left">
                                ID
                            </th>

                            <th class="p-4 text-left">
                                Motor
                            </th>

                            <th class="p-4 text-left">
                                Service
                            </th>

                            <th class="p-4 text-left">
                                Tanggal
                            </th>

                            <th class="p-4 text-left">
                                Status
                            </th>

                            <th class="p-4 text-left">
                                Aksi
                            </th>

                        </tr>

                        </thead>

                        <tbody>

                        <?php while($row = $bookings->fetch_assoc()): ?>

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

                                    <?= htmlspecialchars(
                                        $row['service_name']
                                    ); ?>

                                </td>

                                <td class="p-4">

                                    <?= date(
                                        'd M Y',
                                        strtotime($row['booking_date'])
                                    ); ?>

                                </td>

                                <td class="p-4">

                                    <?php
                                    $status = $row['status'];

                                    $badge = 'bg-gray-100 text-gray-700';

                                    if($status == 'queued'){
                                        $badge = 'bg-yellow-100 text-yellow-700';
                                    }

                                    if($status == 'in_progress'){
                                        $badge = 'bg-blue-100 text-blue-700';
                                    }

                                    if($status == 'completed'){
                                        $badge = 'bg-green-100 text-green-700';
                                    }

                                    if($status == 'cancelled'){
                                        $badge = 'bg-red-100 text-red-700';
                                    }
                                    ?>

                                    <span class="px-3 py-1 rounded-full text-sm <?= $badge ?>">

                                        <?= ucfirst(str_replace('_',' ',$status)); ?>

                                    </span>

                                </td>

                                <td class="p-4">

                                    <div class="flex gap-2">

                                        <a
                                            href="booking_detail.php?id=<?= $row['id']; ?>"
                                            class="bg-blue-500 text-white px-3 py-1 rounded"
                                        >
                                            Detail
                                        </a>

                                        <?php if($row['status'] == 'queued'): ?>

                                            <a
                                                href="tambah_booking.php?edit=<?= $row['id']; ?>"
                                                class="bg-yellow-500 text-white px-3 py-1 rounded"
                                            >
                                                Edit
                                            </a>

                                            <a
                                                href="hapus_booking.php?id=<?= $row['id']; ?>"
                                                onclick="return confirm('Hapus booking?')"
                                                class="bg-red-500 text-white px-3 py-1 rounded"
                                            >
                                                Hapus
                                            </a>

                                        <?php endif; ?>

                                    </div>

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
