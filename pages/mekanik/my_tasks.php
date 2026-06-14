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
| Tugas Aktif Mekanik
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

ORDER BY b.booking_date ASC
");

$stmt->bind_param("i", $mechanicId);
$stmt->execute();

$tasks = $stmt->get_result();
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

        <div>

            <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">
                TUGAS MEKANIK
            </p>

            <p class="mt-2 text-2xl sm:text-4xl text-white font-semibold">
                Tugas Saya
            </p>

            <p class="text-white">
                Daftar tugas yang sedang menunggu pengerjaan atau sedang dikerjakan.
            </p>

        </div>

        <div>

            <span class="bg-[#FF0000] px-4 py-3 rounded text-white inline-flex items-center gap-2 shadow-[0_0_15px_rgba(142,22,22,0.3)] shadow-red-500/40">

                <span class="material-symbols-outlined">
                    engineering
                </span>

                <?= $tasks->num_rows ?> Tugas Aktif

            </span>

        </div>

    </div>

    <!-- Content -->

    <div class="p-4">

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

            <div class="p-4 border-b border-[#eadede]">

                <h2 class="font-semibold text-lg">
                    Daftar Tugas Aktif
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
                                Layanan
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

                    <?php while($row = $tasks->fetch_assoc()): ?>

                        <?php

                        $badge = 'bg-gray-100 text-gray-700';

                        if($row['status'] == 'queued'){
                            $badge = 'bg-yellow-100 text-yellow-700';
                        }

                        if($row['status'] == 'in_progress'){
                            $badge = 'bg-blue-100 text-blue-700';
                        }

                        ?>

                        <tr class="border-t border-[#f2f2f2]">

                            <td class="p-4 font-medium">
                                #<?= $row['id']; ?>
                            </td>

                            <td class="p-4">

                                <div class="font-medium">

                                    <?= htmlspecialchars(
                                        $row['brand']
                                        .' '.
                                        $row['model']
                                    ); ?>

                                </div>

                                <div class="text-sm text-gray-500">

                                    <?= htmlspecialchars(
                                        $row['plate_number']
                                    ); ?>

                                </div>

                            </td>

                            <td class="p-4">

                                <?= htmlspecialchars(
                                    $row['service_name']
                                ); ?>

                            </td>

                            <td class="p-4">

                                <?= date(
                                    'd M Y',
                                    strtotime(
                                        $row['booking_date']
                                    )
                                ); ?>

                            </td>

                            <td class="p-4">

                                <span class="px-3 py-1 rounded-full text-sm <?= $badge ?>">

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
                                    href="task_detail.php?id=<?= $row['id']; ?>"
                                    class="bg-[#8E1616] text-white px-4 py-2 rounded-lg hover:bg-[#6f1111] inline-flex items-center gap-2"
                                >

                                    <span class="material-symbols-outlined text-[18px]">
                                        visibility
                                    </span>

                                    Detail

                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    <?php if($tasks->num_rows == 0): ?>

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-10 text-gray-500"
                            >

                                Tidak ada tugas aktif.

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
