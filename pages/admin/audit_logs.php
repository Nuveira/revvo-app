<?php
// Fungsi: Inisialisasi — memulai session, koneksi DB, dan cek role admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Audit Logs | REVVO Admin';
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
checkRole(['admin']);

// Fungsi: Ambil data user login — untuk sidebar nav
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

// Fungsi: Filter tanggal — default 30 hari terakhir
$date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
$date_to   = $_GET['date_to'] ?? date('Y-m-d');

// Fungsi: Pagination — konstanta dan helper
const AUDIT_LOGS_PER_PAGE = 20;

function audit_logs_total_pages($totalRows) {
    return max(1, (int)ceil(max(0, (int)$totalRows) / AUDIT_LOGS_PER_PAGE));
}

function audit_logs_page_link($page, $date_from, $date_to) {
    $params = ['page' => $page, 'date_from' => $date_from, 'date_to' => $date_to];
    return 'audit_logs.php?' . http_build_query($params);
}

$page_num = is_numeric($_GET['page'] ?? null) && (int)($_GET['page']) > 0 ? (int)$_GET['page'] : 1;

// Fungsi: Hitung total rows untuk pagination
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM service_logs sl
    JOIN bookings b ON sl.booking_id = b.id
    WHERE DATE(sl.created_at) >= ? AND DATE(sl.created_at) <= ?
");
$stmt->bind_param("ss", $date_from, $date_to);
$stmt->execute();
$total_logs = (int)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$total_pages = audit_logs_total_pages($total_logs);
if ($page_num > $total_pages) $page_num = $total_pages;

$limit  = AUDIT_LOGS_PER_PAGE;
$offset = ($page_num - 1) * AUDIT_LOGS_PER_PAGE;

// Fungsi: Query audit logs — JOIN service_logs dengan users dan bookings
$stmt = $conn->prepare("
    SELECT 
        sl.id,
        sl.booking_id,
        sl.previous_status,
        sl.new_status,
        sl.note,
        sl.created_at,
        u.name AS changed_by_name,
        u.role AS changed_by_role
    FROM service_logs sl
    LEFT JOIN users u ON sl.changed_by = u.id
    JOIN bookings b ON sl.booking_id = b.id
    WHERE DATE(sl.created_at) >= ? AND DATE(sl.created_at) <= ?
    ORDER BY sl.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("ssii", $date_from, $date_to, $limit, $offset);
$stmt->execute();
$logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fungsi: Helper badge status — 3 warna (merah, hijau, abu)
function getStatusBadgeClass($status) {
    if ($status === 'cancelled') {
        return 'bg-red-100 text-red-700 text-xs font-medium px-2.5 py-0.5 rounded-full';
    }
    if (in_array($status, ['completed', 'ready_for_pickup', 'paid'])) {
        return 'bg-green-100 text-green-700 text-xs font-medium px-2.5 py-0.5 rounded-full';
    }
    return 'bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-0.5 rounded-full';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="icon" type="image/png" href="<?php echo asset('assets/images/logo.png'); ?>">
</head>
<body class="font-['Plus_Jakarta_Sans']">
    <div class="flex h-screen">
        <?php include 'nav.php'; ?>

        <div class="flex-1 overflow-auto bg-gray-50">
            <!-- Fungsi: Header halaman — judul dan subjudul -->
            <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex justify-between items-center w-full p-5">
                <div class="mx-2">
                    <p class="text-[#8E1616] text-sm tracking-widest">ADMIN PANEL</p>
                    <p class="text-3xl text-white py-1">Audit Log Aktivitas</p>
                    <p class="text-white/70 text-sm">Riwayat perubahan status booking</p>
                </div>
            </div>

            <div class="p-6">
                <!-- Fungsi: Card filter tanggal — input date range -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-6">
                    <form method="GET" action="audit_logs.php" class="flex flex-wrap items-end gap-4">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
                            <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>"
                                   class="border border-gray-300 rounded-lg bg-white text-gray-700 px-3 py-2 text-sm focus:outline-none focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>"
                                   class="border border-gray-300 rounded-lg bg-white text-gray-700 px-3 py-2 text-sm focus:outline-none focus:border-red-500">
                        </div>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition cursor-pointer">
                            Filter
                        </button>
                        <a href="audit_logs.php" class="text-sm text-gray-500 hover:text-gray-700 py-2 transition">Reset</a>
                    </form>
                </div>

                <!-- Fungsi: Info total — jumlah log ditampilkan -->
                <p class="text-sm text-gray-500 mb-4">
                    Menampilkan <?php echo $total_logs; ?> log dari <?php echo date('d M Y', strtotime($date_from)); ?> s/d <?php echo date('d M Y', strtotime($date_to)); ?>
                </p>

                <!-- Fungsi: Tabel audit logs — riwayat perubahan status booking -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">No</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Waktu</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Booking ID</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Diubah Oleh</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Role</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Dari Status</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Ke Status</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($logs)): ?>
                                    <tr>
                                        <td colspan="8" class="px-4 py-16 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <i data-lucide="file-search" class="w-12 h-12 text-gray-300"></i>
                                                <p class="text-gray-400 text-sm">Tidak ada log ditemukan untuk periode ini</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $index => $log): ?>
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-gray-400"><?php echo $offset + $index + 1; ?></td>
                                        <td class="px-4 py-3 text-gray-700 text-xs whitespace-nowrap"><?php echo date('d M Y H:i', strtotime($log['created_at'])); ?></td>
                                        <td class="px-4 py-3">
                                            <span class="font-mono text-xs text-gray-700">#<?php echo $log['booking_id']; ?></span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-700"><?php echo htmlspecialchars($log['changed_by_name'] ?? 'Sistem'); ?></td>
                                        <td class="px-4 py-3">
                                            <?php if (!empty($log['changed_by_role'])): ?>
                                                <span class="text-gray-600 text-sm"><?php echo htmlspecialchars($log['changed_by_role']); ?></span>
                                            <?php else: ?>
                                                <span class="text-gray-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="<?php echo getStatusBadgeClass($log['previous_status']); ?>"><?php echo htmlspecialchars($log['previous_status']); ?></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="<?php echo getStatusBadgeClass($log['new_status']); ?>"><?php echo htmlspecialchars($log['new_status']); ?></span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate"><?php echo htmlspecialchars($log['note'] ?? '-'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Fungsi: Pagination -->
                    <div class="flex flex-col gap-3 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-gray-500">
                            Halaman <span class="font-semibold text-gray-800"><?php echo $page_num; ?></span> dari <span class="font-semibold text-gray-800"><?php echo $total_pages; ?></span>
                            &nbsp;·&nbsp; Total <span class="font-semibold text-gray-800"><?php echo $total_logs; ?></span> log
                        </p>

                        <div class="flex items-center gap-2">
                            <?php if ($page_num > 1): ?>
                                <a href="<?php echo htmlspecialchars(audit_logs_page_link($page_num - 1, $date_from, $date_to)); ?>" class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-200 px-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                                </a>
                            <?php else: ?>
                                <span class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-100 px-3 text-sm font-semibold text-gray-300 cursor-not-allowed">
                                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                                </span>
                            <?php endif; ?>

                            <?php
                            $pg_start = max(1, $page_num - 2);
                            $pg_end   = min($total_pages, $page_num + 2);
                            for ($pg = $pg_start; $pg <= $pg_end; $pg++):
                            ?>
                                <a href="<?php echo htmlspecialchars(audit_logs_page_link($pg, $date_from, $date_to)); ?>"
                                   class="inline-flex h-9 min-w-9 items-center justify-center rounded px-3 text-sm font-semibold transition <?php echo $pg === $page_num ? 'bg-[#8E1616] text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50'; ?>">
                                    <?php echo $pg; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page_num < $total_pages): ?>
                                <a href="<?php echo htmlspecialchars(audit_logs_page_link($page_num + 1, $date_from, $date_to)); ?>" class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-200 px-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                                </a>
                            <?php else: ?>
                                <span class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-100 px-3 text-sm font-semibold text-gray-300 cursor-not-allowed">
                                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fungsi: Load Lucide icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
