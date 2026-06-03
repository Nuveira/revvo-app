<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Detail Motor | REVVO';
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
require_once '../../includes/customer_role.php';

$motor_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$motor_id) {
    header('Location: motor.php');
    exit;
}

// Ambil data motor — pastikan milik customer ini
$motor = null;
$stmt = $conn->prepare("
    SELECT m.*
    FROM motors m
    WHERE m.id = ? AND m.customer_id = ?
");
$stmt->bind_param("ii", $motor_id, $customer_id);
$stmt->execute();
$motor = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$motor) {
    header('Location: motor.php');
    exit;
}

// Ambil histori booking motor ini
$bookings = [];
$stmt = $conn->prepare("
    SELECT b.id, b.booking_date, b.status, b.total_price,
           st.name AS service_name,
           ts.start_time, ts.end_time
    FROM bookings b
    JOIN service_types st ON b.service_type_id = st.id
    JOIN time_slots ts ON b.time_slot_id = ts.id
    WHERE b.motor_id = ?
    ORDER BY b.booking_date DESC
");
$stmt->bind_param("i", $motor_id);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle hapus motor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    // Cek apakah motor masih punya booking aktif
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM bookings WHERE motor_id = ? AND status IN ('queued', 'in_progress')");
    $stmt->bind_param("i", $motor_id);
    $stmt->execute();
    $activeBookings = (int) $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    if ($activeBookings > 0) {
        $deleteError = 'Motor tidak bisa dihapus karena masih ada booking aktif.';
    } else {
        // Hapus gambar jika ada
        if (!empty($motor['image_path'])) {
            $fullPath = ROOT_PATH . '/' . $motor['image_path'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        $stmt = $conn->prepare("DELETE FROM motors WHERE id = ? AND customer_id = ?");
        $stmt->bind_param("ii", $motor_id, $customer_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['motor_success'] = 'Motor berhasil dihapus.';
        header('Location: motor.php');
        exit;
    }
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
            <!-- Header -->
            <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between">
                <div class="min-w-0">
                    <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">Motor Saya</p>
                    <h1 class="mt-2 text-2xl sm:text-4xl text-white font-semibold break-words">
                        <?= htmlspecialchars($motor['brand'] . ' ' . $motor['model']) ?>
                    </h1>
                    <p class="mt-1 text-white/70 text-sm"><?= htmlspecialchars($motor['plate_number']) ?></p>
                </div>
                <a href="motor.php" class="inline-flex w-fit items-center gap-2 rounded bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    Kembali
                </a>
            </div>

            <div class="p-4 max-w-3xl mx-auto">

                <?php if (!empty($deleteError)): ?>
                    <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                        <?= htmlspecialchars($deleteError) ?>
                    </div>
                <?php endif; ?>

                <!-- Detail Motor -->
                <div class="rounded-lg border border-[#eadede] bg-white p-5 shadow-sm">
                    <div class="flex gap-5">
                        <!-- Foto Motor -->
                        <div class="flex h-32 w-32 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-[#f8eeee] text-[#8E1616]">
                            <?php if (!empty($motor['image_path'])): ?>
                                <img src="<?= htmlspecialchars(asset($motor['image_path'])) ?>"
                                     alt="<?= htmlspecialchars($motor['brand'] . ' ' . $motor['model']) ?>"
                                     class="h-full w-full object-cover">
                            <?php else: ?>
                                <span class="material-symbols-outlined text-[56px]">sports_motorsports</span>
                            <?php endif; ?>
                        </div>

                        <!-- Info -->
                        <div class="min-w-0 flex-1">
                            <p class="text-2xl font-semibold text-gray-900"><?= htmlspecialchars($motor['brand'] . ' ' . $motor['model']) ?></p>
                            <span class="mt-2 inline-flex rounded bg-black px-3 py-1 text-sm font-semibold tracking-wide text-white">
                                <?= htmlspecialchars($motor['plate_number']) ?>
                            </span>

                            <div class="mt-4 grid grid-cols-2 gap-4 text-sm sm:grid-cols-4">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Tahun</p>
                                    <p class="mt-1 font-medium text-gray-800"><?= $motor['production_year'] ?? '-' ?></p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Warna</p>
                                    <p class="mt-1 font-medium text-gray-800"><?= $motor['color'] ? htmlspecialchars($motor['color']) : '-' ?></p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Total Booking</p>
                                    <p class="mt-1 font-medium text-gray-800"><?= count($bookings) ?> kali</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Terdaftar</p>
                                    <p class="mt-1 font-medium text-gray-800"><?= date('d M Y', strtotime($motor['created_at'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="mt-5 flex gap-3 border-t border-gray-100 pt-4">
                        <a href="edit_motor.php?id=<?= $motor['id'] ?>"
                           class="inline-flex items-center gap-2 rounded bg-[#8E1616] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#6f1111]">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                            Edit Motor
                        </a>

                        <form method="POST" action="detail_motor.php?id=<?= $motor['id'] ?>"
                              onsubmit="return confirm('Yakin hapus motor ini? Data tidak bisa dikembalikan.')">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-100">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                Hapus Motor
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Histori Booking -->
                <div class="mt-4 rounded-lg border border-[#eadede] bg-white p-5 shadow-sm">
                    <h3 class="font-semibold text-lg mb-4">Histori Booking Motor Ini</h3>
                    <?php if (!empty($bookings)): ?>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-[11px] tracking-[0.15em] text-gray-400 uppercase border-b border-gray-100">
                                    <th class="text-left py-3">ID</th>
                                    <th class="text-left py-3">Tanggal</th>
                                    <th class="text-left py-3">Layanan</th>
                                    <th class="text-left py-3">Total</th>
                                    <th class="text-left py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $b):
                                    $status_color = match($b['status']) {
                                        'ready_for_pickup' => 'text-green-500',
                                        'completed' => 'text-blue-500',
                                        'in_progress' => 'text-yellow-500',
                                        'queued' => 'text-gray-500',
                                        'cancelled' => 'text-red-400',
                                        default => 'text-gray-400'
                                    };
                                    $status_label = match($b['status']) {
                                        'ready_for_pickup' => 'SIAP DIAMBIL',
                                        'completed' => 'SELESAI',
                                        'in_progress' => 'DIKERJAKAN',
                                        'queued' => 'ANTRI',
                                        'cancelled' => 'DIBATALKAN',
                                        default => strtoupper($b['status'])
                                    };
                                ?>
                                <tr class="border-b border-gray-50">
                                    <td class="py-3 text-gray-500">#BK-<?= str_pad($b['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                    <td class="py-3"><?= date('d M Y', strtotime($b['booking_date'])) ?></td>
                                    <td class="py-3"><?= htmlspecialchars($b['service_name']) ?></td>
                                    <td class="py-3 font-semibold">Rp<?= number_format($b['total_price'], 0, ',', '.') ?></td>
                                    <td class="py-3"><span class="font-semibold text-xs <?= $status_color ?>"><?= $status_label ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-gray-400 text-sm text-center py-6">Belum ada booking untuk motor ini.</p>
                    <?php endif; ?>
                </div>

            </div>

            <?php include 'footer.php'; ?>
        </main>
    </div>
</body>
</html>
