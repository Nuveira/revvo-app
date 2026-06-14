<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Booking | REVVO';
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
require_once '../../includes/customer_role.php';

$bookings = [];
if ($customer_id) {
    $stmt = $conn->prepare("
        SELECT b.id, b.booking_date, b.total_price, b.status,
               m.brand, m.model, m.plate_number,
               st.name AS service_name,
               ts.start_time, ts.end_time,
               mechanic_user.name AS mechanic_name
        FROM bookings b
        JOIN motors m ON b.motor_id = m.id
        JOIN service_types st ON b.service_type_id = st.id
        JOIN time_slots ts ON b.time_slot_id = ts.id
        LEFT JOIN mechanics me ON b.mechanic_id = me.id
        LEFT JOIN users mechanic_user ON me.user_id = mechanic_user.id
        WHERE b.customer_id = ? AND b.status IN ('queued', 'in_progress')
        ORDER BY b.booking_date DESC, b.created_at DESC
    ");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

function booking_status_label($status) {
    return match ($status) {
        'in_progress' => 'DIKERJAKAN',
        'queued'      => 'ANTRI',
        default       => strtoupper($status),
    };
}

function booking_status_color($status) {
    return match ($status) {
        'in_progress' => 'text-yellow-600',
        'queued'      => 'text-gray-600',
        default       => 'text-gray-400',
    };
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
<body class="font-['Plus_Jakarta_Sans'] flex h-screen">
    <?php include 'nav.php'; ?>

    <div class="flex-1 bg-gray-100 overflow-y-auto overflow-x-hidden flex flex-col">
        <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex flex-col gap-4 md:flex-row md:justify-between md:items-center w-full p-5">
            <div class="min-w-0">
                <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">Booking</p>
                <p class="mt-2 text-2xl sm:text-4xl text-white font-semibold break-words">Booking Aktif</p>
            </div>
            <a href="tambah_booking.php" class="bg-[#FF0000] px-4 py-3 rounded text-white whitespace-nowrap hover:bg-[#6e1111] transition inline-flex items-center gap-2 shadow-[0_0_15px_rgba(142,22,22,0.3)] shadow-red-500/40">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                Booking Service Baru
            </a>
        </div>

        <main class="flex-1 p-4 md:p-6">
            <section class="bg-white rounded-lg border border-[#eadede] shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-[11px] tracking-[0.15em] text-gray-400 uppercase border-b border-gray-100">
                                <th class="text-left py-3 px-3">ID Booking</th>
                                <th class="text-left py-3 px-3">Tanggal</th>
                                <th class="text-left py-3 px-3">Waktu</th>
                                <th class="text-left py-3 px-3">Motor</th>
                                <th class="text-left py-3 px-3">Layanan</th>
                                <th class="text-left py-3 px-3">Mekanik</th>
                                <th class="text-left py-3 px-3">Total</th>
                                <th class="text-left py-3 px-3">Status</th>
                                <th class="text-left py-3 px-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $b): ?>
                                <tr class="border-b border-gray-50 align-top hover:bg-gray-50/60">
                                    <td class="py-2 px-3 font-semibold text-gray-500 text-xs">
                                        BK-<?= str_pad((string) $b['id'], 4, '0', STR_PAD_LEFT) ?>
                                    </td>
                                    <td class="py-2 px-3 text-xs">
                                        <?= date('d M Y', strtotime($b['booking_date'])) ?>
                                    </td>
                                    <td class="py-2 px-3 text-xs text-gray-600">
                                        <?= date('H:i', strtotime($b['start_time'])) ?> - <?= date('H:i', strtotime($b['end_time'])) ?>
                                    </td>
                                    <td class="py-2 px-3 text-xs font-semibold text-gray-900">
                                        <?= htmlspecialchars($b['brand'] . ' ' . $b['model']) ?>
                                    </td>
                                    <td class="py-2 px-3 text-xs text-gray-900">
                                        <?= htmlspecialchars($b['service_name']) ?>
                                    </td>
                                    <td class="py-2 px-3 text-xs text-gray-600">
                                        <?= htmlspecialchars($b['mechanic_name'] ?: '-') ?>
                                    </td>
                                    <td class="py-2 px-3 text-xs font-semibold text-gray-900">
                                        Rp<?= number_format((float) $b['total_price'], 0, ',', '.') ?>
                                    </td>
                                    <td class="py-2 px-3">
                                        <span class="text-xs font-semibold <?= booking_status_color($b['status']) ?>">
                                            <?= booking_status_label($b['status']) ?>
                                        </span>
                                    </td>
                                    <td class="py-2 px-3">
                                        <a href="booking_detail.php?id=<?= $b['id'] ?>" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-[#8E1616] transition hover:underline">
                                            <span class="material-symbols-outlined text-[14px]">info</span>
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($bookings)): ?>
                                <tr>
                                    <td colspan="9" class="py-12 text-center">
                                        <span class="material-symbols-outlined text-4xl text-gray-300">event_available</span>
                                        <p class="mt-2 font-semibold text-gray-500">Tidak ada booking aktif</p>
                                        <p class="text-sm text-gray-400">Semua servis sudah selesai atau belum ada booking.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-5 py-3">
                    <p class="text-sm text-gray-500">
                        Total <span class="font-semibold text-gray-800"><?= count($bookings) ?></span> booking aktif
                    </p>
                </div>
            </section>
        </main>

        <?php include 'footer.php'; ?>
    </div>
</body>
</html>
