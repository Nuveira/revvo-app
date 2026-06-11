<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Detail Booking | REVVO';
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
require_once '../../includes/customer_role.php';

$booking_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$booking_id) {
    header('Location: booking.php');
    exit;
}

// Ambil detail booking — pastikan milik customer ini
$stmt = $conn->prepare("
    SELECT b.id, b.booking_date, b.total_price, b.service_price, b.status,
           b.customer_complaint, b.mechanic_note, b.created_at,
           m.brand, m.model, m.plate_number, m.color, m.production_year,
           st.name AS service_name, st.base_price, st.estimated_duration_minutes,
           ts.day, ts.start_time, ts.end_time,
           mechanic_user.name AS mechanic_name,
           p.status AS payment_status, p.payment_method, p.amount, p.paid_at
    FROM bookings b
    JOIN motors m ON b.motor_id = m.id
    JOIN service_types st ON b.service_type_id = st.id
    JOIN time_slots ts ON b.time_slot_id = ts.id
    LEFT JOIN mechanics me ON b.mechanic_id = me.id
    LEFT JOIN users mechanic_user ON me.user_id = mechanic_user.id
    LEFT JOIN payments p ON p.booking_id = b.id
    WHERE b.id = ? AND b.customer_id = ?
");
$stmt->bind_param("ii", $booking_id, $customer_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    header('Location: booking.php');
    exit;
}

// Ambil spare parts
$parts = [];
$stmt = $conn->prepare("
    SELECT bp.qty, bp.price_at_time, bp.subtotal,
           sp.name AS part_name, sp.unit
    FROM booking_parts bp
    JOIN spare_parts sp ON bp.spare_part_id = sp.id
    WHERE bp.booking_id = ?
");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$parts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle cancel booking
$cancelError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    if ($booking['status'] === 'queued') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND customer_id = ?");
        $stmt->bind_param("ii", $booking_id, $customer_id);
        $stmt->execute();
        $stmt->close();
        header('Location: booking.php');
        exit;
    } else {
        $cancelError = 'Booking hanya bisa dibatalkan saat status masih Antri.';
    }
}

$status_color = match($booking['status']) {
    'ready_for_pickup' => 'bg-green-50 text-green-700 border-green-200',
    'completed'        => 'bg-blue-50 text-blue-700 border-blue-200',
    'in_progress'      => 'bg-yellow-50 text-yellow-700 border-yellow-200',
    'queued'           => 'bg-gray-50 text-gray-700 border-gray-200',
    'cancelled'        => 'bg-red-50 text-red-700 border-red-200',
    default            => 'bg-gray-50 text-gray-500 border-gray-200',
};
$status_label = match($booking['status']) {
    'ready_for_pickup' => 'SIAP DIAMBIL',
    'completed'        => 'SELESAI',
    'in_progress'      => 'DIKERJAKAN',
    'queued'           => 'ANTRI',
    'cancelled'        => 'DIBATALKAN',
    default            => strtoupper($booking['status']),
};
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
<body class="font-['Plus_Jakarta_Sans'] flex h-screen overflow-hidden">
    <?php include 'nav.php'; ?>

    <div class="flex-1 bg-gray-100 overflow-y-auto overflow-x-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between">
            <div class="min-w-0">
                <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">Booking Aktif</p>
                <h1 class="mt-2 text-2xl sm:text-4xl text-white font-semibold">
                    BK-<?= str_pad((string) $booking['id'], 4, '0', STR_PAD_LEFT) ?>
                </h1>
            </div>
            <a href="booking.php" class="bg-[#FF0000] px-4 py-3 rounded text-white whitespace-nowrap hover:bg-[#6e1111] transition inline-flex items-center gap-2 shadow-[0_0_15px_rgba(142,22,22,0.3)] shadow-red-500/40">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                Kembali
            </a>
        </div>

        <div class="p-4 max-w-3xl mx-auto space-y-4">

            <?php if ($cancelError): ?>
                <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    <?= htmlspecialchars($cancelError) ?>
                </div>
            <?php endif; ?>

            <!-- Info Booking -->
            <div class="rounded-lg border border-[#eadede] bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase">Detail Booking</p>
                        <h2 class="mt-1 text-xl font-semibold text-gray-900"><?= htmlspecialchars($booking['service_name']) ?></h2>
                    </div>
                    <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold <?= $status_color ?>">
                        <?= $status_label ?>
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Tanggal</p>
                        <p class="mt-1 font-medium text-gray-800"><?= date('d M Y', strtotime($booking['booking_date'])) ?></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Waktu</p>
                        <p class="mt-1 font-medium text-gray-800">
                            <?= date('H:i', strtotime($booking['start_time'])) ?> - <?= date('H:i', strtotime($booking['end_time'])) ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Mekanik</p>
                        <p class="mt-1 font-medium text-gray-800"><?= htmlspecialchars($booking['mechanic_name'] ?: 'Belum di-assign') ?></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Estimasi Durasi</p>
                        <p class="mt-1 font-medium text-gray-800"><?= $booking['estimated_duration_minutes'] ?> menit</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Dibuat</p>
                        <p class="mt-1 font-medium text-gray-800"><?= date('d M Y H:i', strtotime($booking['created_at'])) ?></p>
                    </div>
                </div>

                <?php if ($booking['customer_complaint']): ?>
                <div class="mt-4 border-t border-gray-100 pt-4">
                    <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Keluhan</p>
                    <p class="mt-1 text-sm text-gray-700"><?= htmlspecialchars($booking['customer_complaint']) ?></p>
                </div>
                <?php endif; ?>

                <?php if ($booking['mechanic_note']): ?>
                <div class="mt-3">
                    <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Catatan Mekanik</p>
                    <p class="mt-1 text-sm text-gray-700"><?= htmlspecialchars($booking['mechanic_note']) ?></p>
                </div>
                <?php endif; ?>

                <!-- Tombol Download Invoice (hanya setelah selesai) -->
                <?php if (in_array($booking['status'], ['completed', 'ready_for_pickup'])): ?>
                <div class="mt-5 border-t border-gray-100 pt-4">
                    <a href="invoice.php?id=<?= $booking['id'] ?>"
                        class="inline-flex items-center gap-2 rounded bg-[#8E1616] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#6f1111]">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        Download Invoice PDF
                    </a>
                </div>
                <?php endif; ?>

                <!-- Tombol Cancel (hanya kalau masih queued) -->
                <?php if ($booking['status'] === 'queued'): ?>
                <div class="mt-5 border-t border-gray-100 pt-4 flex gap-3">
                    <a href="edit_booking.php?id=<?= $booking['id'] ?>"
                        class="inline-flex items-center gap-2 rounded bg-[#8E1616] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#6f1111]">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                        Edit Booking
                    </a>
                    <button type="button" onclick="showCancelModal()"
                        class="inline-flex items-center gap-2 rounded border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-100">
                        <span class="material-symbols-outlined text-[18px]">cancel</span>
                        Batalkan Booking
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Info Motor -->
            <div class="rounded-lg border border-[#eadede] bg-white p-5 shadow-sm">
                <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase mb-3">Motor</p>
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-[#f8eeee] text-[#8E1616]">
                        <span class="material-symbols-outlined text-[24px]">sports_motorsports</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900"><?= htmlspecialchars($booking['brand'] . ' ' . $booking['model']) ?></p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($booking['plate_number']) ?> · <?= htmlspecialchars($booking['color'] ?? '-') ?> · <?= $booking['production_year'] ?? '-' ?></p>
                    </div>
                </div>
            </div>

            <!-- Spare Parts -->
            <?php if (!empty($parts)): ?>
            <div class="rounded-lg border border-[#eadede] bg-white p-5 shadow-sm">
                <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase mb-3">Spare Parts</p>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[11px] tracking-[0.15em] text-gray-400 uppercase border-b border-gray-100">
                            <th class="text-left py-2">Part</th>
                            <th class="text-left py-2">Qty</th>
                            <th class="text-left py-2">Harga</th>
                            <th class="text-left py-2">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($parts as $p): ?>
                        <tr class="border-b border-gray-50">
                            <td class="py-2"><?= htmlspecialchars($p['part_name']) ?></td>
                            <td class="py-2"><?= $p['qty'] ?> <?= htmlspecialchars($p['unit']) ?></td>
                            <td class="py-2">Rp<?= number_format($p['price_at_time'], 0, ',', '.') ?></td>
                            <td class="py-2 font-semibold">Rp<?= number_format($p['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Info Pembayaran -->
            <div class="rounded-lg border border-[#eadede] bg-white p-5 shadow-sm">
                <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase mb-3">Pembayaran</p>
                <div class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Metode</p>
                        <p class="mt-1 font-medium text-gray-800"><?= $booking['payment_method'] ? strtoupper($booking['payment_method']) : '-' ?></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Status</p>
                        <p class="mt-1 font-medium text-gray-800"><?= $booking['payment_status'] ? strtoupper($booking['payment_status']) : '-' ?></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-gray-400">Dibayar</p>
                        <p class="mt-1 font-medium text-gray-800"><?= $booking['paid_at'] ? date('d M Y H:i', strtotime($booking['paid_at'])) : '-' ?></p>
                    </div>
                </div>
                <div class="mt-4 border-t border-gray-100 pt-4 flex justify-between items-center">
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-xl font-bold text-gray-900">Rp<?= number_format((float) $booking['total_price'], 0, ',', '.') ?></p>
                </div>
            </div>

        </div>

        <?php include 'footer.php'; ?>
    </div>

    <!-- Modal Konfirmasi Cancel -->
    <div id="cancelModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4 shadow-xl">
            <h3 class="text-lg font-semibold text-gray-900">Batalkan Booking?</h3>
            <p class="mt-2 text-sm text-gray-500">Booking <strong>BK-<?= str_pad((string) $booking['id'], 4, '0', STR_PAD_LEFT) ?></strong> akan dibatalkan. Tindakan ini tidak bisa diurungkan.</p>
            <div class="mt-5 flex gap-3">
                <button onclick="closeCancelModal()"
                    class="flex-1 rounded border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                    Kembali
                </button>
                <form method="POST" action="booking_detail.php?id=<?= $booking['id'] ?>" class="flex-1">
                    <input type="hidden" name="action" value="cancel">
                    <button type="submit"
                        class="w-full rounded bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                        Ya, Batalkan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function showCancelModal() {
        document.getElementById('cancelModal').classList.remove('hidden');
    }
    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }
    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) closeCancelModal();
    });
    </script>
</body>
</html>
