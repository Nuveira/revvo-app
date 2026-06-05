<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Tugas Saya | REVVO';

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['mechanic']);

$userId = $_SESSION['user_id'] ?? 0;

/*
|--------------------------------------------------------------------------
| Data Mekanik
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        m.id,
        u.name,
        u.role,
        u.profile_photo
    FROM mechanics m
    JOIN users u
        ON m.user_id = u.id
    WHERE u.id = ?
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$mechanic = $stmt->get_result()->fetch_assoc();

if (!$mechanic) {
    die('Data mekanik tidak ditemukan');
}

$mechanicId = $mechanic['id'];

$nama = $mechanic['name'];
$role = $mechanic['role'];
$profile_photo = $mechanic['profile_photo'];

/*
|--------------------------------------------------------------------------
| Task Mekanik
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        b.id,
        b.booking_date,
        b.status,
        b.created_at,

        mo.brand,
        mo.model,
        mo.plate_number,

        st.name AS service_name

    FROM bookings b

    JOIN motors mo
        ON b.motor_id = mo.id

    JOIN service_types st
        ON b.service_type_id = st.id

    WHERE b.mechanic_id = ?
    AND b.status IN ('queued','in_progress')
    ORDER BY b.created_at DESC
");

$stmt->bind_param("i", $mechanicId);
$stmt->execute();

$tasks = $stmt->get_result();

$totalTask = $tasks->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

<title><?= htmlspecialchars($pageTitle) ?></title>

</head>

<body class="font-['Plus_Jakarta_Sans'] overflow-hidden">

<div class="flex h-screen overflow-hidden">

    <?php include 'nav.php'; ?>

    <div class="flex-1 flex-col min-w-0 bg-gray-100 overflow-y-auto overflow-x-hidden">

        <!-- Header -->

        <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex flex-col gap-4 md:flex-row md:justify-between md:items-center w-full p-5">

            <div class="min-w-0">

                <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">
                    TUGAS MEKANIK
                </p>

                <p class="mt-2 text-2xl sm:text-4xl text-white font-semibold break-words">
                    Halo, <?= htmlspecialchars($nama) ?>
                </p>

                <p class="text-white">
                    Anda memiliki
                    <span class="text-[#FF0000]">
                        <?= $totalTask ?>
                    </span>
                    tugas yang ditugaskan.
                </p>

            </div>

        </div>

        <!-- Content -->

        <div class="p-4 md:p-6">

            <?php if(isset($_SESSION['success'])): ?>

                <div class="bg-green-100 border border-green-200 text-green-700 p-3 rounded-lg mb-4">
                    <?= $_SESSION['success']; ?>
                </div>

                <?php unset($_SESSION['success']); ?>

            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>

                <div class="bg-red-100 border border-red-200 text-red-700 p-3 rounded-lg mb-4">
                    <?= $_SESSION['error']; ?>
                </div>

                <?php unset($_SESSION['error']); ?>

            <?php endif; ?>

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm overflow-hidden">

                <div class="p-5 border-b border-gray-100">

                    <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase">
                        Tugas Servis
                    </p>

                    <h2 class="mt-1 text-2xl font-semibold text-[#8E1616]">
                        Tugas Saya
                    </h2>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[900px] text-sm">

                        <thead>

                            <tr class="text-[11px] tracking-[0.15em] text-gray-400 uppercase border-b border-gray-100 bg-gray-50">

                                <th class="text-left py-3 px-4">
                                    ID Booking
                                </th>

                                <th class="text-left py-3 px-4">
                                    Motor
                                </th>

                                <th class="text-left py-3 px-4">
                                    Layanan
                                </th>

                                <th class="text-left py-3 px-4">
                                    Tanggal
                                </th>

                                <th class="text-left py-3 px-4">
                                    Status
                                </th>

                                <th class="text-left py-3 px-4">
                                    Aksi
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php while($row = $tasks->fetch_assoc()): ?>

                            <?php

                            $statusColor = 'text-gray-500';

                            if($row['status'] == 'queued'){
                                $statusColor = 'text-yellow-500';
                            }

                            if($row['status'] == 'in_progress'){
                                $statusColor = 'text-blue-500';
                            }

                            if($row['status'] == 'completed'){
                                $statusColor = 'text-green-500';
                            }

                            if($row['status'] == 'cancelled'){
                                $statusColor = 'text-red-500';
                            }

                            ?>

                            <tr class="border-b border-gray-50 hover:bg-gray-50">

                                <td class="py-3 px-4 font-semibold text-gray-500">

                                    BK-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?>

                                </td>

                                <td class="py-3 px-4">

                                    <div class="font-semibold text-gray-900">

                                        <?= htmlspecialchars(
                                            $row['brand']
                                            .' '.
                                            $row['model']
                                        ); ?>

                                    </div>

                                    <div class="text-xs text-gray-500">

                                        <?= htmlspecialchars(
                                            $row['plate_number']
                                        ); ?>

                                    </div>

                                </td>

                                <td class="py-3 px-4">

                                    <?= htmlspecialchars(
                                        $row['service_name']
                                    ); ?>

                                </td>

                                <td class="py-3 px-4">

                                    <?= date(
                                        'd M Y',
                                        strtotime(
                                            $row['booking_date']
                                        )
                                    ); ?>

                                </td>

                                <td class="py-3 px-4">

                                    <span class="font-semibold text-xs <?= $statusColor; ?>">

                                        <?= strtoupper(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $row['status']
                                            )
                                        ); ?>

                                    </span>

                                </td>

                                <td class="py-3 px-4">

                                    <a
                                        href="task_detail.php?id=<?= $row['id']; ?>"
                                        class="bg-[#8E1616] px-4 py-2 rounded text-white text-sm font-semibold hover:bg-[#6f1111] transition"
                                    >
                                        Detail
                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                        <?php if($totalTask == 0): ?>

                            <tr>

                                <td colspan="6" class="text-center py-12">

                                    <span class="material-symbols-outlined text-5xl text-gray-300">
                                        build
                                    </span>

                                    <p class="mt-3 text-gray-500 font-semibold">
                                        Belum ada tugas
                                    </p>

                                    <p class="text-sm text-gray-400">
                                        Tugas servis yang diberikan admin akan muncul di sini.
                                    </p>

                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <?php include 'footer.php'; ?>

    </div>

</div>

</body>
</html>