<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Motor | REVVO';
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
require_once '../../includes/customer_role.php';

$successMessage = $_SESSION['motor_success'] ?? null;
unset($_SESSION['motor_success']);

$motors = [];
if ($customer_id) {
    $stmt = $conn->prepare("
        SELECT m.id, m.brand, m.model, m.plate_number, m.production_year, m.color, m.image_path, m.created_at,
               COALESCE(stats.booking_count, 0) AS booking_count,
               stats.last_booking_date
        FROM motors m
        LEFT JOIN (
            SELECT motor_id, COUNT(*) AS booking_count, MAX(booking_date) AS last_booking_date
            FROM bookings
            GROUP BY motor_id
        ) stats ON stats.motor_id = m.id
        WHERE m.customer_id = ?
        ORDER BY m.id DESC
    ");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $motors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/logo.png') ?>">
</head>
<body class="font-['Plus_Jakarta_Sans'] overflow-hidden">
    <div class="flex h-screen overflow-hidden">
        <?php include 'nav.php'; ?>

        <main class="flex-1 min-w-0 bg-gray-100 overflow-y-auto overflow-x-hidden">
            <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between">
                <div class="min-w-0">
                    <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">Motor Saya</p>
                    <h1 class="mt-2 text-2xl sm:text-4xl text-white font-semibold break-words">Kelola motor pribadi</h1>
                    <p class="mt-2 text-sm text-white/70">Total <?= count($motors) ?> motor terdaftar</p>
                </div>
                <a href="tambah_motor.php" class="inline-flex w-fit items-center gap-2 rounded bg-[#FF0000] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#6e1111]">
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                    Tambah Motor
                </a>
            </div>

            <div class="grid grid-cols-1 gap-5 p-4 w-full">
                <section class="min-w-0">
                    <?php if ($successMessage): ?>
                        <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                            <?= htmlspecialchars($successMessage) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($motors)): ?>
                        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                            <?php foreach ($motors as $motor): ?>
                                <article class="rounded-lg border border-[#eadede] bg-white p-5 shadow-sm">
                                    <div class="flex gap-4">
                                        <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-[#f8eeee] text-[#8E1616]">
                                            <?php if (!empty($motor['image_path'])): ?>
                                                <img src="<?= htmlspecialchars(asset($motor['image_path'])) ?>" alt="<?= htmlspecialchars($motor['brand'] . ' ' . $motor['model']) ?>" class="h-full w-full object-cover">
                                            <?php else: ?>
                                                <span class="material-symbols-outlined text-[34px]">sports_motorsports</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xl font-semibold text-gray-900 break-words"><?= htmlspecialchars($motor['brand'] . ' ' . $motor['model']) ?></p>
                                            <p class="mt-1 inline-flex rounded bg-black px-3 py-1 text-sm font-semibold tracking-wide text-white">
                                                <?= htmlspecialchars($motor['plate_number']) ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-5 grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Tahun</p>
                                            <p class="mt-1 font-medium text-gray-800"><?= $motor['production_year'] ? htmlspecialchars((string) $motor['production_year']) : '-' ?></p>
                                        </div>
                                        <div>
                                            <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Warna</p>
                                            <p class="mt-1 font-medium text-gray-800"><?= $motor['color'] ? htmlspecialchars($motor['color']) : '-' ?></p>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <a href="detail_motor.php?id=<?= $motor['id'] ?>" class="inline-flex w-full items-center justify-center gap-2 rounded bg-[#f8eeee] px-4 py-2.5 text-sm font-semibold text-[#8E1616] transition hover:bg-[#eadede]">
                                            <span class="material-symbols-outlined text-[18px]">info</span>
                                            Lihat Detail
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="rounded-lg border border-dashed border-[#d8b7b7] bg-white px-6 py-14 text-center shadow-sm">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#f8eeee] text-[#8E1616]">
                                <span class="material-symbols-outlined text-[30px]">sports_motorsports</span>
                            </div>
                            <p class="mt-4 text-lg font-semibold text-gray-900">Belum ada motor terdaftar</p>
                            <p class="mt-2 text-sm text-gray-500">Tambahkan motor pertama agar bisa dipilih saat booking servis.</p>
                            <a href="#form-tambah-motor" class="mt-5 inline-flex items-center gap-2 rounded bg-[#8E1616] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#6f1111]">
                                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                                Tambah Motor
                            </a>
                        </div>
                    <?php endif; ?>
                </section>
            </div>

            <?php include 'footer.php'; ?>
        </main>
    </div>
</body>
</html>
