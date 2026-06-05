<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'History | REVVO';

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
| History Task
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT
    b.id,
    b.booking_date,
    b.status,
    b.mechanic_note,
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

ORDER BY b.created_at DESC
");

$stmt->bind_param("i", $mechanicId);
$stmt->execute();

$histories = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<title><?= htmlspecialchars($pageTitle) ?></title>

</head>

<body class="font-['Plus_Jakarta_Sans']">

<div class="flex h-screen">

    <?php include 'nav.php'; ?>

    <div class="flex-1 bg-gray-100 overflow-auto">

        <!-- HEADER -->

        <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] p-5">

            <p class="text-[#FF0000] uppercase text-sm">
                History
            </p>

            <h1 class="text-4xl text-white py-2">
                Riwayat Pekerjaan
            </h1>

            <p class="text-white">
                Seluruh pekerjaan yang pernah ditangani mekanik.
            </p>

        </div>

        <!-- CONTENT -->

        <div class="p-6">

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm overflow-hidden">

                <div class="p-4 border-b">

                    <h2 class="font-semibold text-lg">
                        History Task
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
                                    Catatan
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php while($row = $histories->fetch_assoc()): ?>

                            <?php

                            $badge =
                                'bg-gray-100 text-gray-700';

                            if($row['status'] == 'queued'){
                                $badge =
                                'bg-yellow-100 text-yellow-700';
                            }

                            if($row['status'] == 'in_progress'){
                                $badge =
                                'bg-blue-100 text-blue-700';
                            }

                            if($row['status'] == 'completed'){
                                $badge =
                                'bg-green-100 text-green-700';
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

                                    <?= $row['mechanic_note']
                                        ? htmlspecialchars($row['mechanic_note'])
                                        : '-'; ?>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                        <?php if($histories->num_rows == 0): ?>

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center py-8 text-gray-500"
                                >
                                    Belum ada history pekerjaan.
                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>