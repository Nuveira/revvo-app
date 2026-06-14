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
                    SELAMAT DATANG KEMBALI
                </p>

                <p class="mt-2 text-2xl sm:text-4xl text-white font-semibold">
                    Halo, <?= htmlspecialchars($nama) ?>
                </p>

                <p class="text-white">
                    Anda memiliki
                    <span class="text-[#FF0000]">
                        <?= $jumlahTaskAktif ?>
                    </span>
                    tugas aktif.
                </p>

            </div>

            <div>

                <a
                    href="my_tasks.php"
                    class="bg-[#FF0000] px-4 py-3 rounded text-white hover:bg-[#6e1111] transition inline-flex items-center gap-2 shadow-[0_0_15px_rgba(142,22,22,0.3)] shadow-red-500/40"
                >

                    <span class="material-symbols-outlined">
                        engineering
                    </span>

                    Tugas Saya

                </a>

            </div>

        </div>

        <!-- Content -->

        <div class="p-4">

            <!-- Statistik -->

            <div class="grid md:grid-cols-2 gap-4">

                <!-- Tugas Aktif -->

                <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6">

                    <div class="flex justify-between items-center">

                        <div>

                            <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase">
                                Tugas Aktif
                            </p>

                            <h2 class="mt-3 text-4xl font-bold text-[#8E1616]">
                                <?= $jumlahTaskAktif ?>
                            </h2>

                        </div>

                        <span class="material-symbols-outlined text-5xl text-[#8E1616]">
                            pending_actions
                        </span>

                    </div>

                </div>

                <!-- Tugas Selesai -->

                <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6">

                    <div class="flex justify-between items-center">

                        <div>

                            <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase">
                                Tugas Selesai
                            </p>

                            <h2 class="mt-3 text-4xl font-bold text-green-600">
                                <?= $jumlahTaskSelesai ?>
                            </h2>

                        </div>

                        <span class="material-symbols-outlined text-5xl text-green-600">
                            task_alt
                        </span>

                    </div>

                </div>

            </div>

            <!-- Task Terbaru -->

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6 mt-4">

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">

                    <div>

                        <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase">
                            Tugas Terbaru
                        </p>

                        <?php if($lastTask): ?>

                            <h3 class="mt-2 text-2xl font-semibold text-[#8E1616]">

                                <?= htmlspecialchars(
                                    $lastTask['brand']
                                    .' '.
                                    $lastTask['model']
                                ); ?>

                            </h3>

                            <p class="text-gray-600">

                                <?= htmlspecialchars(
                                    $lastTask['service_name']
                                ); ?>

                            </p>

                            <p class="text-sm text-gray-400 mt-2">

                                <?= date(
                                    'd M Y',
                                    strtotime(
                                        $lastTask['booking_date']
                                    )
                                ); ?>

                            </p>

                        <?php else: ?>

                            <h3 class="mt-2 text-lg text-gray-500">
                                Belum ada tugas
                            </h3>

                        <?php endif; ?>

                    </div>

                    <div>

                        <a
                            href="my_tasks.php"
                            class="bg-[#8E1616] text-white px-5 py-3 rounded-lg hover:bg-[#6f1111] inline-flex items-center gap-2"
                        >

                            <span class="material-symbols-outlined">
                                visibility
                            </span>

                            Lihat Tugas

                        </a>

                    </div>

                </div>

            </div>

        </div>

        <?php include 'footer.php'; ?>
        
    </div>

</div>

</body>
</html>