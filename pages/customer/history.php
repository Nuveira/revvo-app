<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'History | REVVO';
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
require_once '../../includes/customer_role.php';

const HISTORY_PER_PAGE = 6;

function normalize_history_page($page) {
    return is_numeric($page) && (int)$page > 0 ? (int)$page : 1;
}

function normalize_history_sort($sort) {
    return in_array($sort, ['latest', 'motor_asc', 'motor_desc'], true) ? $sort : 'latest';
}

function normalize_history_motor_filter($motorId, array $ownedMotorIds) {
    if ($motorId === null || $motorId === '' || !is_numeric($motorId)) {
        return null;
    }

    $motorId = (int)$motorId;
    return in_array($motorId, $ownedMotorIds, true) ? $motorId : null;
}

function history_total_pages($totalRows) {
    return max(1, (int)ceil(max(0, (int)$totalRows) / HISTORY_PER_PAGE));
}

function history_page_link($page, $motorId, $sort) {
    $params = ['page' => $page];

    if ($motorId !== null) {
        $params['motor_id'] = $motorId;
    }

    if ($sort !== 'latest') {
        $params['sort'] = $sort;
    }

    return 'history.php?' . http_build_query($params);
}

function history_status_label($status) {
    return match ($status) {
        'ready_for_pickup' => 'SIAP DIAMBIL',
        'completed' => 'SELESAI',
        'in_progress' => 'DIKERJAKAN',
        'queued' => 'ANTRI',
        'cancelled' => 'DIBATALKAN',
        default => strtoupper((string)$status),
    };
}

function history_status_color($status) {
    return match ($status) {
        'ready_for_pickup' => 'text-green-600',
        'completed' => 'text-blue-600',
        'in_progress' => 'text-yellow-600',
        'queued' => 'text-gray-600',
        'cancelled' => 'text-red-500',
        default => 'text-gray-400',
    };
}

$motors = [];
$histories = [];
$total_rows = 0;
$selected_motor_id = null;
$page_num = normalize_history_page($_GET['page'] ?? null);
$sort = normalize_history_sort($_GET['sort'] ?? null);

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

    if ($page_num > $total_pages) {
        $page_num = $total_pages;
    }

    $limit = HISTORY_PER_PAGE;
    $offset = ($page_num - 1) * HISTORY_PER_PAGE;
    $order_by = match ($sort) {
        'motor_asc' => 'm.brand ASC, m.model ASC, m.plate_number ASC, b.booking_date DESC, b.created_at DESC, b.id DESC',
        'motor_desc' => 'm.brand DESC, m.model DESC, m.plate_number DESC, b.booking_date DESC, b.created_at DESC, b.id DESC',
        default => 'b.booking_date DESC, b.created_at DESC, b.id DESC',
    };

    if ($selected_motor_id !== null) {
        $stmt = $conn->prepare("
            SELECT
                b.id,
                b.booking_date,
                b.total_price,
                b.status,
                b.customer_complaint,
                b.mechanic_note,
                b.created_at,
                m.brand,
                m.model,
                m.plate_number,
                st.name AS service_name,
                ts.start_time,
                ts.end_time,
                mechanic_user.name AS mechanic_name,
                p.status AS payment_status,
                p.payment_method
            FROM bookings b
            JOIN motors m
                ON b.motor_id = m.id
            JOIN service_types st
                ON b.service_type_id = st.id
            JOIN time_slots ts
                ON b.time_slot_id = ts.id
            LEFT JOIN mechanics me
                ON b.mechanic_id = me.id
            LEFT JOIN users mechanic_user
                ON me.user_id = mechanic_user.id
            LEFT JOIN payments p
                ON p.booking_id = b.id
            WHERE b.customer_id = ?
            AND b.motor_id = ?
            ORDER BY {$order_by}
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("iiii", $customer_id, $selected_motor_id, $limit, $offset);
    } else {
        $stmt = $conn->prepare("
            SELECT
                b.id,
                b.booking_date,
                b.total_price,
                b.status,
                b.customer_complaint,
                b.mechanic_note,
                b.created_at,
                m.brand,
                m.model,
                m.plate_number,
                st.name AS service_name,
                ts.start_time,
                ts.end_time,
                mechanic_user.name AS mechanic_name,
                p.status AS payment_status,
                p.payment_method
            FROM bookings b
            JOIN motors m
                ON b.motor_id = m.id
            JOIN service_types st
                ON b.service_type_id = st.id
            JOIN time_slots ts
                ON b.time_slot_id = ts.id
            LEFT JOIN mechanics me
                ON b.mechanic_id = me.id
            LEFT JOIN users mechanic_user
                ON me.user_id = mechanic_user.id
            LEFT JOIN payments p
                ON p.booking_id = b.id
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
        <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex flex-col gap-4 md:flex-row md:justify-between md:items-center w-full p-5">
            <div class="min-w-0">
                <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">history booking</p>
                <p class="mt-2 text-2xl sm:text-4xl text-white font-semibold break-words">Riwayat servis semua motor</p>
            </div>
        </div>

        <main class="p-4 md:p-6 space-y-4">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <section class="bg-white rounded-lg border border-[#eadede] p-5 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase">Filter Motor</p>
                        <h1 class="mt-1 text-2xl font-semibold text-[#8E1616]">History Booking</h1>
                    </div>

                    <form method="GET" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <input type="hidden" name="page" value="1">
                        <label for="motor_id" class="sr-only">Pilih motor</label>
                        <select
                            id="motor_id"
                            name="motor_id"
                            class="rounded border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 outline-none focus:border-[#8E1616] focus:ring-2 focus:ring-[#8E1616]/10"
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
                            class="rounded border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 outline-none focus:border-[#8E1616] focus:ring-2 focus:ring-[#8E1616]/10"
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
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-[11px] tracking-[0.15em] text-gray-400 uppercase border-b border-gray-100">
                                <th class="text-left py-3 px-3">ID Booking</th>
                                <th class="text-left py-3 px-3">Tanggal</th>
                                <th class="text-left py-3 px-3">Motor</th>
                                <th class="text-left py-3 px-3">Layanan</th>
                                <th class="text-left py-3 px-3">Mekanik</th>
                                <th class="text-left py-3 px-3">Total</th>
                                <th class="text-left py-3 px-3">Status</th>
                                <th class="text-left py-3 px-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($histories as $history): ?>
                                <tr class="border-b border-gray-50 align-top hover:bg-gray-50/60">
                                    <td class="py-2 px-3 font-semibold text-gray-500 text-xs">
                                        BK-<?= str_pad((string) $history['id'], 4, '0', STR_PAD_LEFT) ?>
                                    </td>
                                    <td class="py-2 px-3 text-xs">
                                        <?= date('d M Y', strtotime($history['booking_date'])) ?>
                                    </td>
                                    <td class="py-2 px-3 text-xs font-semibold text-gray-900">
                                        <?= htmlspecialchars($history['brand'] . ' ' . $history['model']) ?>
                                    </td>
                                    <td class="py-2 px-3 text-xs text-gray-900">
                                        <?= htmlspecialchars($history['service_name']) ?>
                                    </td>
                                    <td class="py-2 px-3 text-xs text-gray-600">
                                        <?= htmlspecialchars($history['mechanic_name'] ?: '-') ?>
                                    </td>
                                    <td class="py-2 px-3 text-xs font-semibold text-gray-900">
                                        Rp<?= number_format((float) $history['total_price'], 0, ',', '.') ?>
                                    </td>
                                    <td class="py-2 px-3">
                                        <span class="text-xs font-semibold <?= history_status_color($history['status']) ?>">
                                            <?= history_status_label($history['status']) ?>
                                        </span>
                                    </td>
                                    <td class="py-2 px-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <a href="detail_history.php?id=<?= $history['id'] ?>" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-[#8E1616] transition hover:underline">
                                                <span class="material-symbols-outlined text-[14px]">info</span>
                                                Detail
                                            </a>

                                            <?php if ($history['status'] === 'queued'): ?>
                                                <a href="edit_booking.php?id=<?= $history['id'] ?>" class="inline-flex items-center gap-1 rounded bg-yellow-500 px-2 py-1 text-xs font-semibold text-white transition hover:bg-yellow-600">
                                                    <span class="material-symbols-outlined text-[14px]">edit</span>
                                                    Edit
                                                </a>
                                                <a
                                                    href="hapus_booking.php?id=<?= $history['id'] ?>"
                                                    onclick="return confirm('Hapus booking?')"
                                                    class="inline-flex items-center gap-1 rounded bg-red-500 px-2 py-1 text-xs font-semibold text-white transition hover:bg-red-600"
                                                >
                                                    <span class="material-symbols-outlined text-[14px]">delete</span>
                                                    Hapus
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($histories)): ?>
                                <tr>
                                    <td colspan="8" class="py-12 text-center">
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
                        Halaman <span class="font-semibold text-gray-800"><?= $page_num ?></span> dari <span class="font-semibold text-gray-800"><?= $total_pages ?></span>
                        &nbsp;·&nbsp; Total <span class="font-semibold text-gray-800"><?= $total_rows ?></span> booking
                    </p>

                    <div class="flex items-center gap-2">
                        <?php if ($page_num > 1): ?>
                            <a href="<?= htmlspecialchars(history_page_link($page_num - 1, $selected_motor_id, $sort)) ?>" class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-200 px-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                                <span class="material-symbols-outlined text-lg">chevron_left</span>
                            </a>
                        <?php else: ?>
                            <span class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-100 px-3 text-sm font-semibold text-gray-300 cursor-not-allowed">
                                <span class="material-symbols-outlined text-lg">chevron_left</span>
                            </span>
                        <?php endif; ?>

                        <?php
                        $pg_start = max(1, $page_num - 2);
                        $pg_end = min($total_pages, $page_num + 2);
                        for ($pg = $pg_start; $pg <= $pg_end; $pg++):
                        ?>
                            <a
                                href="<?= htmlspecialchars(history_page_link($pg, $selected_motor_id, $sort)) ?>"
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded px-3 text-sm font-semibold transition <?= $pg === $page_num ? 'bg-[#8E1616] text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50' ?>"
                            >
                                <?= $pg ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page_num < $total_pages): ?>
                            <a href="<?= htmlspecialchars(history_page_link($page_num + 1, $selected_motor_id, $sort)) ?>" class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-200 px-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                                <span class="material-symbols-outlined text-lg">chevron_right</span>
                            </a>
                        <?php else: ?>
                            <span class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-100 px-3 text-sm font-semibold text-gray-300 cursor-not-allowed">
                                <span class="material-symbols-outlined text-lg">chevron_right</span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>

        <?php include 'footer.php'; ?>
    </div>
</body>
</html>
