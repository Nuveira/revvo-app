<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Dashboard | REVVO Admin';
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
checkRole(['admin']);

$user_id = $_SESSION['user_id'] ?? null;
$nama    = $_SESSION['name'] ?? 'Admin';
$role    = $_SESSION['role'] ?? '';
$profile_photo = null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT profile_photo FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $profile_photo = $row['profile_photo'] ?? null;
    $stmt->close();
}

// Jumlah user per role
$stmt = $conn->prepare("SELECT role, COUNT(*) as total FROM users GROUP BY role");
$stmt->execute();
$result = $stmt->get_result();
$role_counts = ['admin' => 0, 'mechanic' => 0, 'customer' => 0];
while ($row = $result->fetch_assoc()) {
    $role_counts[$row['role']] = $row['total'];
}
$stmt->close();
$total_users = array_sum($role_counts);

// Booking hari ini
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE booking_date = CURDATE()");
$stmt->execute();
$bookings_today = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Booking menunggu (queued)
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE status = 'queued'");
$stmt->execute();
$bookings_queued = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Mekanik aktif
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'mechanic' AND status = 'active'");
$stmt->execute();
$mekanik_aktif = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// 5 booking terbaru
$stmt = $conn->prepare("
    SELECT b.id, b.status, b.booking_date,
            u.name AS customer_name,
            m.brand, m.model,
            st.name AS service_name
    FROM bookings b
    JOIN customers c ON b.customer_id = c.id
    JOIN users u ON c.user_id = u.id
    JOIN motors m ON b.motor_id = m.id
    JOIN service_types st ON b.service_type_id = st.id
    ORDER BY b.created_at DESC
    LIMIT 5
");
$stmt->execute();
$recent_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
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
<body class="font-['Plus_Jakarta_Sans']">
    <div class="flex h-screen">
        <?php include 'nav.php'; ?>

        <div class="flex-1 overflow-auto bg-gray-100">
            <!-- Header -->
            <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex justify-between items-center w-full p-5">
                <div class="mx-2">
                    <p class="text-[#8E1616] text-sm tracking-widest">ADMIN PANEL</p>
                    <p class="text-4xl text-white py-2">Halo, <?= htmlspecialchars($nama) ?></p>
                    <p class="text-white/70">Total <?= $total_users ?> user terdaftar &mdash; <?= $bookings_queued ?> booking menunggu</p>
                </div>
            </div>

            <div class="p-6">
                <!-- 4 Stats Cards -->
                <div class="grid grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                        <p class="text-xs tracking-widest text-gray-400 uppercase">Total Users</p>
                        <p class="text-4xl font-semibold text-[#8E1616] mt-2"><?= $total_users ?></p>
                        <p class="text-xs text-gray-400 mt-2">
                            <?= $role_counts['customer'] ?> customer &bull;
                            <?= $role_counts['mechanic'] ?> mekanik &bull;
                            <?= $role_counts['admin'] ?> admin
                        </p>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                        <p class="text-xs tracking-widest text-gray-400 uppercase">Booking Hari Ini</p>
                        <p class="text-4xl font-semibold text-[#8E1616] mt-2"><?= $bookings_today ?></p>
                        <p class="text-xs text-gray-400 mt-2">total booking masuk hari ini</p>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                        <p class="text-xs tracking-widest text-gray-400 uppercase">Menunggu</p>
                        <p class="text-4xl font-semibold text-[#8E1616] mt-2"><?= $bookings_queued ?></p>
                        <p class="text-xs text-gray-400 mt-2">booking status queued</p>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                        <p class="text-xs tracking-widest text-gray-400 uppercase">Mekanik Aktif</p>
                        <p class="text-4xl font-semibold text-[#8E1616] mt-2"><?= $mekanik_aktif ?></p>
                        <p class="text-xs text-gray-400 mt-2">dari <?= $role_counts['mechanic'] ?> mekanik terdaftar</p>
                    </div>
                </div>

                <!-- Tabel Booking Terbaru -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-700">Booking Terbaru</h3>
                        <a href="bookings.php" class="text-sm text-[#8E1616] hover:underline">Lihat semua</a>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="text-left px-6 py-3 text-gray-500 font-medium">Customer</th>
                                <th class="text-left px-6 py-3 text-gray-500 font-medium">Motor</th>
                                <th class="text-left px-6 py-3 text-gray-500 font-medium">Servis</th>
                                <th class="text-left px-6 py-3 text-gray-500 font-medium">Tanggal</th>
                                <th class="text-left px-6 py-3 text-gray-500 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (empty($recent_bookings)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada booking</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_bookings as $b): ?>
                                <?php
                                $status_class = match($b['status']) {
                                    'queued'           => 'bg-yellow-100 text-yellow-700',
                                    'in_progress'      => 'bg-blue-100 text-blue-700',
                                    'completed'        => 'bg-green-100 text-green-700',
                                    'ready_for_pickup' => 'bg-purple-100 text-purple-700',
                                    'cancelled'        => 'bg-red-100 text-red-700',
                                    default            => 'bg-gray-100 text-gray-700',
                                };
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium"><?= htmlspecialchars($b['customer_name']) ?></td>
                                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($b['brand'] . ' ' . $b['model']) ?></td>
                                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($b['service_name']) ?></td>
                                    <td class="px-6 py-4 text-gray-500"><?= htmlspecialchars($b['booking_date']) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium <?= $status_class ?>">
                                            <?= htmlspecialchars($b['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
