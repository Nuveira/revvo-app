<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'History | REVVO';
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
require_once '../../includes/customer_history_helpers.php';
require_once '../../includes/customer_role.php';

$customer_id = null;
$motors = [];
$histories = [];
$total_rows = 0;
$selected_motor_id = null;
$current_page = normalize_history_page($_GET['page'] ?? null);
$sort = normalize_history_sort($_GET['sort'] ?? null);

if ($user_id) {
    $stmt = $conn->prepare("SELECT id FROM customers WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $customer_id = (int) $row['id'];
    }
    $stmt->close();
}

if ($customer_id) {
    $stmt = $conn->prepare("
        SELECT id, brand, model, plate_number
        FROM motors
        WHERE customer_id = ?
        ORDER BY brand ASC, model ASC, plate_number ASC
    ");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $motors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $owned_motor_ids = array_map('intval', array_column($motors, 'id'));
    $selected_motor_id = normalize_history_motor_filter($_GET['motor_id'] ?? null, $owned_motor_ids);

    if ($selected_motor_id !== null) {
        $stmt = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM bookings
            WHERE customer_id = ? AND motor_id = ?
        ");
        $stmt->bind_param("ii", $customer_id, $selected_motor_id);
    } else {
        $stmt = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM bookings
            WHERE customer_id = ?
        ");
        $stmt->bind_param("i", $customer_id);
    }

    $stmt->execute();
    $total_rows = (int) $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    $total_pages = history_total_pages($total_rows);
    if ($current_page > $total_pages) {
        $current_page = $total_pages;
    }

    $limit = CUSTOMER_HISTORY_PER_PAGE;
    $offset = history_offset_for_page($current_page);
    $order_by = match ($sort) {
        'motor_asc' => 'm.brand ASC, m.model ASC, m.plate_number ASC, b.booking_date DESC, b.created_at DESC, b.id DESC',
        'motor_desc' => 'm.brand DESC, m.model DESC, m.plate_number DESC, b.booking_date DESC, b.created_at DESC, b.id DESC',
        default => 'b.booking_date DESC, b.created_at DESC, b.id DESC',
    };

    if ($selected_motor_id !== null) {
        $stmt = $conn->prepare("
            SELECT b.id, b.booking_date, b.total_price, b.status, b.customer_complaint,
                b.mechanic_note, b.created_at,
                m.brand, m.model, m.plate_number,
                st.name AS service_name,
                ts.start_time, ts.end_time,
                mechanic_user.name AS mechanic_name,
                p.status AS payment_status, p.payment_method
            FROM bookings b
            JOIN motors m ON b.motor_id = m.id
            JOIN service_types st ON b.service_type_id = st.id
            JOIN time_slots ts ON b.time_slot_id = ts.id
            LEFT JOIN mechanics me ON b.mechanic_id = me.id
            LEFT JOIN users mechanic_user ON me.user_id = mechanic_user.id
            LEFT JOIN payments p ON p.booking_id = b.id
            WHERE b.customer_id = ? AND b.motor_id = ?
            ORDER BY {$order_by}
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("iiii", $customer_id, $selected_motor_id, $limit, $offset);
    } else {
        $stmt = $conn->prepare("
            SELECT b.id, b.booking_date, b.total_price, b.status, b.customer_complaint,
                b.mechanic_note, b.created_at,
                m.brand, m.model, m.plate_number,
                st.name AS service_name,
                ts.start_time, ts.end_time,
                mechanic_user.name AS mechanic_name,
                p.status AS payment_status, p.payment_method
            FROM bookings b
            JOIN motors m ON b.motor_id = m.id
            JOIN service_types st ON b.service_type_id = st.id
            JOIN time_slots ts ON b.time_slot_id = ts.id
            LEFT JOIN mechanics me ON b.mechanic_id = me.id
            LEFT JOIN users mechanic_user ON me.user_id = mechanic_user.id
            LEFT JOIN payments p ON p.booking_id = b.id
            WHERE b.customer_id = ?
            ORDER BY {$order_by}
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("iii", $customer_id, $limit, $offset);
    }

    $stmt->execute();
    $histories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $total_pages = 1;
}

function history_page_link($page, $motorId, $sort)
{
    $params = ['page' => $page];
    if ($motorId !== null) {
        $params['motor_id'] = $motorId;
    }
    if ($sort !== 'latest') {
        $params['sort'] = $sort;
    }

    return 'history.php?' . http_build_query($params);
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

    <div class="flex-1 bg-gray-100 overflow-y-auto overflow-x-hidden">
        <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex justify-between items-center w-full p-8">
            <div>
                <p class="text-white font-semibold text-xl uppercase">history booking</p>
                <p class="text-white/70 text-sm mt-1">Riwayat servis semua motor yang pernah kamu booking</p>
            </div>
        </div>

        <main class="p-4 md:p-6 space-y-4">
            <section class="bg-white rounded-lg border border-[#eadede] p-5 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase">Filter Motor</p>
                        <h1 class="mt-1 text-2xl font-semibold text-[#8E1616]">History Booking</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Menampilkan <?= count($histories) ?> dari <?= $total_rows ?> history booking
                        </p>
                    </div>

                    <form method="GET" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <input type="hidden" name="page" value="1">
                        <label for="motor_id" class="sr-only">Pilih motor</label>
                        <select
                            id="motor_id"
                            name="motor_id"
                            class="min-w-64 rounded border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 outline-none focus:border-[#8E1616] focus:ring-2 focus:ring-[#8E1616]/10"
                        >
                            <option value="">Semua motor</option>
                            <?php foreach ($motors as $motor): ?>
                                <option value="<?= (int) $motor['id'] ?>" <?= $selected_motor_id === (int) $motor['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($motor['brand'] . ' ' . $motor['model'] . ' - ' . $motor['plate_number']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="sort" class="sr-only">Urutkan history</label>
                        <select
                            id="sort"
                            name="sort"
                            class="min-w-44 rounded border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 outline-none focus:border-[#8E1616] focus:ring-2 focus:ring-[#8E1616]/10"
                        >
                            <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>Terbaru</option>
                            <option value="motor_asc" <?= $sort === 'motor_asc' ? 'selected' : '' ?>>Motor A-Z</option>
                            <option value="motor_desc" <?= $sort === 'motor_desc' ? 'selected' : '' ?>>Motor Z-A</option>
                        </select>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded bg-[#8E1616] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#6f1111]">
                            <span class="material-symbols-outlined text-base">filter_alt</span>
                            Terapkan
                        </button>
                        <?php if ($selected_motor_id !== null || $sort !== 'latest'): ?>
                            <a href="history.php" class="inline-flex items-center justify-center rounded border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                                Reset
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </section>

            <section class="bg-white rounded-lg border border-[#eadede] shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[980px] text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-[11px] tracking-[0.15em] text-gray-400 uppercase border-b border-gray-100">
                                <th class="text-left py-4 px-5">ID Booking</th>
                                <th class="text-left py-4 px-5">Tanggal</th>
                                <th class="text-left py-4 px-5">Motor</th>
                                <th class="text-left py-4 px-5">Layanan</th>
                                <th class="text-left py-4 px-5">Mekanik</th>
                                <th class="text-left py-4 px-5">Total</th>
                                <th class="text-left py-4 px-5">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($histories as $history): ?>
                                <tr class="border-b border-gray-50 align-top hover:bg-gray-50/60">
                                    <td class="py-4 px-5 font-semibold text-gray-500">
                                        BK-<?= str_pad((string) $history['id'], 4, '0', STR_PAD_LEFT) ?>
                                    </td>
                                    <td class="py-4 px-5">
                                        <p class="font-medium text-gray-900"><?= date('d M Y', strtotime($history['booking_date'])) ?></p>
                                        <p class="text-xs text-gray-400">
                                            <?= date('H:i', strtotime($history['start_time'])) ?> - <?= date('H:i', strtotime($history['end_time'])) ?>
                                        </p>
                                    </td>
                                    <td class="py-4 px-5">
                                        <p class="font-semibold text-gray-900"><?= htmlspecialchars($history['brand'] . ' ' . $history['model']) ?></p>
                                        <p class="text-xs text-gray-400"><?= htmlspecialchars($history['plate_number']) ?></p>
                                    </td>
                                    <td class="py-4 px-5">
                                        <p class="font-medium text-gray-900"><?= htmlspecialchars($history['service_name']) ?></p>
                                        <p class="max-w-64 truncate text-xs text-gray-400">
                                            <?= htmlspecialchars($history['customer_complaint'] ?: 'Tidak ada keluhan') ?>
                                        </p>
                                    </td>
                                    <td class="py-4 px-5 text-gray-600">
                                        <?= htmlspecialchars($history['mechanic_name'] ?: 'Belum di-assign') ?>
                                    </td>
                                    <td class="py-4 px-5 font-semibold text-gray-900">
                                        Rp<?= number_format((float) $history['total_price'], 0, ',', '.') ?>
                                    </td>
                                    <td class="py-4 px-5">
                                        <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold <?= history_status_class($history['status']) ?>">
                                            <?= history_status_label($history['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($histories)): ?>
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <span class="material-symbols-outlined text-4xl text-gray-300">history</span>
                                        <p class="mt-2 font-semibold text-gray-500">Belum ada history booking</p>
                                        <p class="text-sm text-gray-400">History akan muncul setelah kamu membuat booking servis.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-500">
                        Page <?= $current_page ?> dari <?= $total_pages ?>
                    </p>

                    <div class="flex items-center gap-2">
                        <?php if ($current_page > 1): ?>
                            <a href="<?= htmlspecialchars(history_page_link($current_page - 1, $selected_motor_id, $sort)) ?>" class="inline-flex h-10 min-w-10 items-center justify-center rounded border border-gray-200 px-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                                <span class="material-symbols-outlined text-lg">chevron_left</span>
                            </a>
                        <?php else: ?>
                            <span class="inline-flex h-10 min-w-10 items-center justify-center rounded border border-gray-100 px-3 text-sm font-semibold text-gray-300">
                                <span class="material-symbols-outlined text-lg">chevron_left</span>
                            </span>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        for ($page = $start_page; $page <= $end_page; $page++):
                        ?>
                            <a
                                href="<?= htmlspecialchars(history_page_link($page, $selected_motor_id, $sort)) ?>"
                                class="inline-flex h-10 min-w-10 items-center justify-center rounded px-3 text-sm font-semibold transition <?= $page === $current_page ? 'bg-[#8E1616] text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50' ?>"
                            >
                                <?= $page ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <a href="<?= htmlspecialchars(history_page_link($current_page + 1, $selected_motor_id, $sort)) ?>" class="inline-flex h-10 min-w-10 items-center justify-center rounded border border-gray-200 px-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                                <span class="material-symbols-outlined text-lg">chevron_right</span>
                            </a>
                        <?php else: ?>
                            <span class="inline-flex h-10 min-w-10 items-center justify-center rounded border border-gray-100 px-3 text-sm font-semibold text-gray-300">
                                <span class="material-symbols-outlined text-lg">chevron_right</span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
