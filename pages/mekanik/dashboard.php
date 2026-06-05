<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Dashboard Mekanik | REVVO';

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
| Jumlah Task Aktif
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT COUNT(*) total
    FROM bookings
    WHERE mechanic_id = ?
    AND status IN ('queued','in_progress')
");

$stmt->bind_param("i", $mechanicId);
$stmt->execute();

$jumlahTaskAktif =
    $stmt->get_result()->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| Jumlah Task Selesai
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT COUNT(*) total
    FROM bookings
    WHERE mechanic_id = ?
    AND status = 'completed'
");

$stmt->bind_param("i", $mechanicId);
$stmt->execute();

$jumlahTaskSelesai =
    $stmt->get_result()->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| Task Terbaru
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT
    b.*,
    m.brand,
    m.model,
    s.name AS service_name

FROM bookings b

JOIN motors m
    ON b.motor_id = m.id

JOIN service_types s
    ON b.service_type_id = s.id

WHERE b.mechanic_id = ?

ORDER BY b.created_at DESC
LIMIT 1
");

$stmt->bind_param("i", $mechanicId);
$stmt->execute();

$lastTask =
    $stmt->get_result()->fetch_assoc();
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

    <div class="flex-1 bg-gray-100">

        <!-- Header -->

        <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] p-5">

            <p class="text-[#FF0000]">
                SELAMAT DATANG
            </p>

            <h1 class="text-4xl text-white py-2">
                Halo, <?= htmlspecialchars($nama); ?>
            </h1>

            <p class="text-white">
                Anda memiliki
                <?= $jumlahTaskAktif; ?>
                task aktif.
            </p>

        </div>

        <!-- Content -->

        <div class="p-6">

            <div class="grid md:grid-cols-2 gap-5">

                <!-- Task Aktif -->

                <div class="bg-white rounded-lg p-6 border border-[#eadede] shadow-sm">

                    <p class="text-gray-500 text-sm">
                        TASK AKTIF
                    </p>

                    <h2 class="text-4xl font-bold text-[#8E1616] mt-2">
                        <?= $jumlahTaskAktif; ?>
                    </h2>

                </div>

                <!-- Task Selesai -->

                <div class="bg-white rounded-lg p-6 border border-[#eadede] shadow-sm">

                    <p class="text-gray-500 text-sm">
                        TASK SELESAI
                    </p>

                    <h2 class="text-4xl font-bold text-green-600 mt-2">
                        <?= $jumlahTaskSelesai; ?>
                    </h2>

                </div>

            </div>

            <!-- Task Terakhir -->

            <div class="bg-white rounded-lg p-6 border border-[#eadede] shadow-sm mt-5">

                <div class="flex justify-between items-center">

                    <div>

                        <p class="text-gray-500 text-sm">
                            TASK TERBARU
                        </p>

                        <?php if($lastTask): ?>

                            <h3 class="text-2xl font-semibold mt-2">

                                <?= htmlspecialchars(
                                    $lastTask['brand']
                                    .' '.
                                    $lastTask['model']
                                ); ?>

                            </h3>

                            <p class="text-gray-600 mt-1">

                                <?= htmlspecialchars(
                                    $lastTask['service_name']
                                ); ?>

                            </p>

                        <?php else: ?>

                            <h3 class="text-xl">
                                Belum ada task
                            </h3>

                        <?php endif; ?>

                    </div>

                    <a
                        href="my_tasks.php"
                        class="bg-[#8E1616] text-white px-5 py-3 rounded-lg hover:bg-[#6f1111]"
                    >
                        Lihat Task
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>