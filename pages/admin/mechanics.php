<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Mechanics | REVVO Admin';
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

function getStatus($status) {
    return match($status) {
        'available' => 'Available',
        'busy'      => 'Busy',
        'inactive'  => 'Inactive',
        default     => ucfirst($status)
    };
}

function updateStatus($conn, $id, $status) {
    $stmt = $conn->prepare("
        UPDATE mechanics
        SET availability_status = ?
        WHERE id = ?
    ");
    $stmt->bind_param("si", $status, $id);
    return $stmt->execute();
}


// GET params
$filter_status = $_GET['status'] ?? '';
$search        = trim($_GET['search'] ?? '');
$search_like   = "%{$search}%";
$show          = $_GET['show'] ?? '';
$edit_id       = (int)($_GET['id'] ?? 0);
$page          = max(1, (int)($_GET['page'] ?? 1));
$per_page      = 10;
$offset        = ($page - 1) * $per_page;

// Sort — whitelist wajib karena nama kolom tidak bisa di-parameterize
$allowed_sort  = ['id', 'name', 'specialization', 'availability_status'];
$sort          = in_array($_GET['sort'] ?? '', $allowed_sort) ? $_GET['sort'] : 'id';
$order         = ($_GET['order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';
$sort_map = [
    'id'                  => 'm.id',
    'name'                => 'u.name',
    'specialization'      => 'm.specialization',
    'availability_status' => 'm.availability_status'
];
$sort_sql = $sort_map[$sort];

function getAllMechanics($conn, $filter_status, $search, $search_like, $sort_sql, $order, $per_page, $offset) {
    $stmt = $conn->prepare("
        SELECT
            m.id, m.user_id, m.specialization, m.availability_status, m.notes, m.created_at,
            u.name, u.email, u.phone, u.profile_photo
        FROM mechanics m JOIN users u ON u.id = m.user_id
        WHERE (? = '' OR m.availability_status = ?) AND
        (? = '' OR u.name LIKE ? OR m.specialization LIKE ?)
        ORDER BY {$sort_sql} {$order}
        LIMIT ? OFFSET ?");
    $stmt->bind_param("sssssii", $filter_status, $filter_status, $search, $search_like, $search_like, $per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $result;
}

// Ambil data user yang akan di-edit
$edit_mechanic = null;
if ($show === 'edit' && $edit_id > 0) {
    $stmt = $conn->prepare("SELECT m.*, u.id AS user_id, u.name, u.email, u.phone, u.profile_photo FROM mechanics m JOIN users u ON u.id = m.user_id WHERE m.id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_mechanic = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Hitung total untuk pagination
$stmt = $conn->prepare("SELECT COUNT(*) AS total
    FROM mechanics m
    JOIN users u ON u.id = m.user_id
    WHERE
    (? = '' OR m.availability_status = ?)
    AND
    (
        ? = ''
        OR u.name LIKE ?
        OR m.specialization LIKE ?
    )");
$stmt->bind_param("sssss", $filter_status,$filter_status,$search,$search_like,$search_like);
$stmt->execute();
$total_rows = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();
$total_pages = (int)ceil($total_rows / $per_page);

// Ambil list mechanics
$mechanics = getAllMechanics($conn, $filter_status, $search, $search_like, $sort, $order, $per_page, $offset);

// Helper untuk build query string — gabungkan filter + sort + pagination
function filter_query($extra = []) {
    global $filter_status, $search, $sort, $order;
    $params = [];
    if ($filter_status !== '') $params['status'] = $filter_status;
    if ($search !== '')        $params['search'] = $search;
    if ($sort !== 'id')        $params['sort']   = $sort;
    if ($order !== 'ASC')      $params['order']  = $order;
    foreach ($extra as $k => $v) {
        $params[$k] = $v;
    }
    return $params ? '?' . http_build_query($params) : '';
}

// Helper untuk link header kolom — toggle arah sort
function sort_link($col, $label) {
    global $sort, $order;
    $next_order = ($sort === $col && $order === 'ASC') ? 'DESC' : 'ASC';
    $url        = filter_query(['sort' => $col, 'order' => $next_order, 'page' => 1]);

    if ($sort === $col) {
        $icon  = $order === 'ASC' ? '↑' : '↓';
        $class = 'inline-flex items-center gap-1 font-semibold text-[#8E1616] whitespace-nowrap';
        $icon_html = '<span class="text-[#8E1616] text-xs">' . $icon . '</span>';
    } else {
        $class = 'inline-flex items-center gap-1 text-gray-500 hover:text-[#8E1616] transition-colors whitespace-nowrap';
        $icon_html = '<span class="text-gray-300 text-xs">⇅</span>';
    }

    return '<a href="mechanics.php' . $url . '" class="' . $class . '">'
        . htmlspecialchars($label) . $icon_html . '</a>';
}

$available_users = [];
$stmt = $conn->prepare("
    SELECT
        u.id, u.name, u.email
    FROM users u LEFT JOIN mechanics m ON m.user_id = u.id
    WHERE u.role = 'mechanic' AND m.id IS NULL AND u.status = 'active'
    ORDER BY u.name ASC
");

$stmt->execute();
$available_users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
                    <p class="text-3xl text-white py-1">Manajemen Mekanik</p>
                    <p class="text-white/70 text-sm">Total <?= $total_rows ?> mekanik<?= $filter_status || $search ? ' (difilter)' : '' ?></p>
                </div>
                <div class="px-3">
                    <a href="mechanics.php<?= filter_query(['show' => 'create']) ?>"
                       class="bg-[#FF0000] px-4 py-3 rounded text-white whitespace-nowrap hover:bg-[#6e1111] transition flex items-center gap-2 shadow-red-500/40">
                        + Tambah Mechanic
                    </a>
                </div>
            </div>

            <div class="p-6">
                <!-- Pesan feedback -->
                <?php
                $msg_map = [
                    'created' => [
                        'text' => 'Mechanic berhasil ditambahkan.',
                        'class' => 'bg-green-100 text-green-800 border-green-300'
                    ],

                    'updated' => [
                        'text' => 'Data mechanic berhasil diperbarui.',
                        'class' => 'bg-blue-100 text-blue-800 border-blue-300'
                    ],

                    'deleted' => [
                        'text' => 'Mechanic berhasil dihapus.',
                        'class' => 'bg-yellow-100 text-yellow-800 border-yellow-300'
                    ],

                    'mechanic_exists' => [
                        'text' => 'User tersebut sudah terdaftar sebagai mechanic.',
                        'class' => 'bg-orange-100 text-orange-800 border-orange-300'
                    ],

                    'invalid_user' => [
                        'text' => 'User yang dipilih bukan user mechanic.',
                        'class' => 'bg-orange-100 text-orange-800 border-orange-300'
                    ],

                    'error' => [
                        'text' => 'Terjadi kesalahan, coba lagi.',
                        'class' => 'bg-red-100 text-red-800 border-red-300'
                    ]
                ];
                $msg_key = $_GET['msg'] ?? '';
                if (isset($msg_map[$msg_key])):
                ?>
                <div id="alert-message"
                    class="mb-4 px-4 py-3 rounded border <?= $msg_map[$msg_key]['class'] ?>">
                    <div class="flex items-center justify-between">
                        <span>
                            <?= htmlspecialchars($msg_map[$msg_key]['text']) ?>
                        </span>

                        <button type="button"
                                onclick="document.getElementById('alert-message').remove()"
                                class="ml-4 text-lg leading-none font-bold opacity-60 hover:opacity-100 transition">
                            &times;
                        </button>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Form Tambah Mekanik -->
                <?php if ($show === 'create'): ?>
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6 shadow-sm">
                    <h2 class="text-lg font-semibold mb-4">Tambah Mekanik Baru</h2>
                    <form method="POST" action="proses_mechanics.php">
                        <input type="hidden" name="action" value="create">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">User Mekanik <span class="text-red-500">*</span></label>
                                <select name="user_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                                    <?php if (empty($available_users)): ?>
                                        <option value="">Semua user mekanik sudah terdaftar
                                        </option>
                                    <?php else: ?>
                                        <option value="">
                                            Pilih User Mekanik
                                        </option>
                                        <?php foreach ($available_users as $user): ?>
                                            <option value="<?= $user['id'] ?>">
                                                <?= htmlspecialchars($user['name']) ?>
                                                (<?= htmlspecialchars($user['email']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Spesialisasi <span class="text-red-500">*</span></label>
                                <input type="text" name="specialization" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Status Ketersediaan <span class="text-red-500">*</span></label>
                                <select name="availability_status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                                    <option value="available">Available</option>
                                    <option value="busy">Busy</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Catatan</label>
                                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616] resize-y"></textarea>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-5">
                            <button type="submit" class="bg-[#8E1616] text-white px-6 py-2 rounded hover:bg-[#6f1111] transition text-sm">Simpan</button>
                            <a href="mechanics.php<?= filter_query() ?>" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300 transition text-sm">Batal</a>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Form Edit Mekanik -->
                <?php if ($show === 'edit' && $edit_mechanic): ?>
                <div class="bg-white rounded-lg border border-blue-200 p-6 mb-6 shadow-sm">
                    <h2 class="text-lg font-semibold mb-4">Edit Mekanik: <?= htmlspecialchars($edit_mechanic['name']) ?></h2>
                    <form method="POST" action="proses_mechanics.php">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= $edit_mechanic['id'] ?>">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Nama Meknaik</label>
                                <input type="text" value="<?= htmlspecialchars($edit_mechanic['name']) ?>" disabled class="w-full bg-gray-100 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Email</label>
                                <input type="text" value="<?= htmlspecialchars($edit_mechanic['email']) ?>" disabled class="w-full bg-gray-100 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Spesialisasi <span class="text-red-500">*</span></label>
                                <input type="text" name="specialization" value="<?= htmlspecialchars($edit_mechanic['specialization']) ?>" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Status Ketersediaan</label>
                                <select name="availability_status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                                    <option value="available"
                                        <?= $edit_mechanic['availability_status'] === 'available' ? 'selected' : '' ?>>
                                        Available
                                    </option>
                                    <option value="busy"
                                        <?= $edit_mechanic['availability_status'] === 'busy' ? 'selected' : '' ?>>
                                        Busy
                                    </option>
                                    <option value="inactive"
                                        <?= $edit_mechanic['availability_status'] === 'inactive' ? 'selected' : '' ?>>
                                        Inactive
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">No. HP</label>
                                <input type="text" value="<?= htmlspecialchars($edit_mechanic['phone'] ?? '-') ?>" disabled class="w-full bg-gray-100 border border-gray-300 rounded px-3 py-2 text-sm cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Catatan</label>
                                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616] resize-none"><?= htmlspecialchars($edit_mechanic['notes'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-5">
                            <button type="submit" class="bg-[#8E1616] text-white px-6 py-2 rounded hover:bg-[#6f1111] transition text-sm">Update</button>
                            <a href="mechanics.php<?= filter_query() ?>" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300 transition text-sm">Batal</a>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Filter Bar -->
                <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4 shadow-sm">
                    <form method="GET" action="mechanics.php" class="flex gap-4 items-end flex-wrap">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Filter Status</label>
                            <select name="status" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                                <option value="">Semua Status</option>
                                <option value="available" <?= $filter_status === 'available' ? 'selected' : '' ?>>
                                    Available
                                </option>
                                <option value="busy" <?= $filter_status === 'busy' ? 'selected' : '' ?>>
                                    Busy
                                </option>
                                <option value="inactive" <?= $filter_status === 'inactive' ? 'selected' : '' ?>>
                                    Inactive
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Cari Mekanik / Spesialisasi</label>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari Nama atau Spesialisasi..." class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                        </div>
                        <button type="submit" class="bg-[#8E1616] text-white px-4 py-2 rounded text-sm hover:bg-[#6f1111] transition">Filter</button>
                        <?php if ($filter_status || $search): ?>
                            <a href="mechanics.php" class="text-sm text-gray-500 hover:text-gray-700 py-2">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Tabel Mekanik -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-[1200px] w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <?php
                                // Fungsi kecil untuk class <th> — highlight kolom aktif
                                $th = fn($col) => 'text-left px-4 py-3 font-medium ' . ($sort === $col ? 'bg-[#8E1616]/5' : '');
                                ?>
                                <tr>
                                    <th class="<?= $th('id') ?>"><?= sort_link('id', 'ID') ?></th>
                                    <th class="<?= $th('name') ?>"><?= sort_link('name', 'Mekanik') ?></th>
                                    <th class="<?= $th('specialization') ?>"><?= sort_link('specialization', 'Spesialisasi') ?></th>
                                    <th class="<?= $th('availability_status') ?>"><?= sort_link('availability_status', 'Status') ?></th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-500">No. HP</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-500">Catatan</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if (empty($mechanics)): ?>
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada mekanik ditemukan</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($mechanics as $m): ?>
                                    <?php
                                    $status_class = match($m['availability_status']) {
                                        'available' => 'bg-green-100 text-green-700',
                                        'busy'      => 'bg-yellow-100 text-yellow-700',
                                        'inactive'  => 'bg-red-100 text-red-700',
                                        default     => 'bg-gray-100 text-gray-700',
                                    };
                                    ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-400">#<?= $m['id'] ?></td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <?php if (!empty($m['profile_photo'])): ?>
                                                    <img src="../../uploads/profile/<?= htmlspecialchars($m['profile_photo']) ?>" alt="Profile" class="w-10 h-10 rounded-full object-cover border">
                                                <?php else: ?>
                                                    <div class="w-10 h-10 rounded-full bg-[#8E1616]/20 flex items-center justify-center text-sm font-bold text-[#8E1616]">
                                                        <?= strtoupper(substr($m['name'], 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <p class="font-medium"><?= htmlspecialchars($m['name']) ?></p>
                                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($m['email']) ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 font-medium"><?= htmlspecialchars($m['specialization']) ?></td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium <?= $status_class ?>"><?= getStatus($m['availability_status']) ?></span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($m['phone'] ?? '-') ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($m['notes'] ?? '-') ?></td>
                                        <td class="px-4 py-3">
                                            <div class="flex gap-2 items-center">
                                                <a href="mechanics.php<?= filter_query(['show' => 'edit', 'id' => $m['id']]) ?>"
                                                    class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 transition">Edit</a>
                                                <button type="button" onclick="openDeleteModal(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['name']), ENT_QUOTES) ?>')" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition cursor-pointer">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="flex flex-col gap-3 border-t border-gray-200 pt-4 mt-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-500">
                        Menampilkan
                        <span class="font-semibold text-gray-800"><?= count($mechanics) ?></span>
                        dari
                        <span class="font-semibold text-gray-800"><?= $total_rows ?></span>
                        mekanik
                    </p>

                    <div class="flex items-center gap-2">
                        <?php if ($page > 1): ?>
                            <a href="mechanics.php<?= filter_query(['page' => $page - 1]) ?>"
                            class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-200 px-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                                <span class="material-symbols-outlined text-lg">chevron_left</span>
                            </a>
                        <?php else: ?>
                            <span class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-100 px-3 text-sm font-semibold text-gray-300 cursor-not-allowed">
                                <span class="material-symbols-outlined text-lg">chevron_left</span>
                            </span>
                        <?php endif; ?>

                        <?php
                        $pg_start = max(1, $page - 2);
                        $pg_end   = min($total_pages, $page + 2);

                        for ($pg = $pg_start; $pg <= $pg_end; $pg++):
                        ?>
                            <a href="mechanics.php<?= filter_query(['page' => $pg]) ?>"
                            class="inline-flex h-9 min-w-9 items-center justify-center rounded px-3 text-sm font-semibold transition
                            <?= $pg === $page
                                    ? 'bg-[#8E1616] text-white'
                                    : 'border border-gray-200 text-gray-600 hover:bg-gray-50' ?>">
                                <?= $pg ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="mechanics.php<?= filter_query(['page' => $page + 1]) ?>"
                            class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-200 px-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                                <span class="material-symbols-outlined text-lg">chevron_right</span>
                            </a>
                        <?php else: ?>
                            <span class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-100 px-3 text-sm font-semibold text-gray-300 cursor-not-allowed">
                                <span class="material-symbols-outlined text-lg">chevron_right</span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Fungsi: Modal Konfirmasi Hapus — custom modal dark theme pengganti confirm() -->
    <div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- Overlay -->
        <div id="delete-modal-overlay" onclick="closeDeleteModal()" class="absolute inset-0 bg-black/50 transition-opacity duration-200 opacity-0 pointer-events-none"></div>
        <!-- Card Modal -->
        <div id="delete-modal-card" class="relative bg-stone-800 border border-white/10 rounded-xl p-6 w-full max-w-sm mx-4 transition-all duration-200 opacity-0 scale-95">
            <div class="flex flex-col items-center text-center">
                <!-- Icon Warning -->
                <div class="mb-4 text-yellow-400">
                    <i data-lucide="triangle-alert" class="w-12 h-12"></i>
                </div>
                <!-- Judul -->
                <h3 class="text-lg font-semibold text-white mb-1">Hapus Mekanik?</h3>
                <!-- Nama mekanik dinamis -->
                <p id="delete-modal-name" class="text-sm text-stone-300 mb-2"></p>
                <!-- Peringatan -->
                <p class="text-xs text-stone-400 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
                <!-- Form POST hapus -->
                <form id="delete-modal-form" method="POST" action="proses_mechanics.php" class="w-full">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete-modal-id" value="">
                    <div class="flex gap-3 justify-center">
                        <button type="button" onclick="closeDeleteModal()" class="px-5 py-2 rounded-lg text-sm font-medium text-white bg-stone-700 hover:bg-stone-600 transition cursor-pointer">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition cursor-pointer">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Fungsi: JavaScript — toggle form, modal hapus, dan auto-dismiss alert -->
    <script>
        // Fungsi: openDeleteModal — tampilkan modal konfirmasi hapus dengan data dinamis
        function openDeleteModal(id, nama) {
            var modal = document.getElementById('delete-modal');
            var overlay = document.getElementById('delete-modal-overlay');
            var card = document.getElementById('delete-modal-card');
            var nameEl = document.getElementById('delete-modal-name');
            var idInput = document.getElementById('delete-modal-id');

            // Set data dinamis
            nameEl.textContent = nama;
            idInput.value = id;

            // Tampilkan modal
            modal.classList.remove('hidden');

            // Trigger transition setelah frame berikutnya
            requestAnimationFrame(function() {
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                overlay.classList.add('opacity-100');
                card.classList.remove('opacity-0', 'scale-95');
                card.classList.add('opacity-100', 'scale-100');
            });

            // Render icon Lucide di dalam modal
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // Fungsi: closeDeleteModal — sembunyikan modal konfirmasi hapus
        function closeDeleteModal() {
            var modal = document.getElementById('delete-modal');
            var overlay = document.getElementById('delete-modal-overlay');
            var card = document.getElementById('delete-modal-card');

            // Animasi keluar
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            card.classList.remove('opacity-100', 'scale-100');
            card.classList.add('opacity-0', 'scale-95');

            // Sembunyikan setelah transisi selesai
            setTimeout(function() {
                modal.classList.add('hidden');
            }, 200);
        }
    </script>
    <!-- Fungsi: Load Lucide icons — render semua icon termasuk yang di modal -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
