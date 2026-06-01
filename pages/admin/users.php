<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Users | REVVO Admin';
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

// GET params
$filter_role   = $_GET['role'] ?? '';
$filter_status = $_GET['status'] ?? '';
$search        = $_GET['search'] ?? '';
$search_like   = $search !== '' ? "%{$search}%" : '';
$show          = $_GET['show'] ?? '';
$edit_id       = (int)($_GET['id'] ?? 0);
$page          = max(1, (int)($_GET['page'] ?? 1));
$per_page      = 10;
$offset        = ($page - 1) * $per_page;

// Sort — whitelist wajib karena nama kolom tidak bisa di-parameterize
$allowed_sort  = ['id', 'name', 'email', 'role', 'status', 'created_at'];
$sort          = in_array($_GET['sort'] ?? '', $allowed_sort) ? $_GET['sort'] : 'id';
$order         = ($_GET['order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

// Ambil data user yang akan di-edit
$edit_user = null;
if ($show === 'edit' && $edit_id > 0) {
    $stmt = $conn->prepare("SELECT id, name, email, role, phone, status FROM users WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Hitung total untuk pagination
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE (? = '' OR role = ?) AND (? = '' OR status = ?) AND (? = '' OR name LIKE ? OR email LIKE ?)");
$stmt->bind_param("sssssss", $filter_role, $filter_role, $filter_status, $filter_status, $search, $search_like, $search_like);
$stmt->execute();
$total_rows = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();
$total_pages = (int)ceil($total_rows / $per_page);

// Ambil list users
$stmt = $conn->prepare("
    SELECT id, name, email, role, phone, status, created_at
    FROM users
    WHERE (? = '' OR role = ?) AND (? = '' OR status = ?) AND (? = '' OR name LIKE ? OR email LIKE ?)
    ORDER BY {$sort} {$order}
    LIMIT ? OFFSET ?
");
$stmt->bind_param("sssssssii", $filter_role, $filter_role, $filter_status, $filter_status, $search, $search_like, $search_like, $per_page, $offset);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Helper untuk build query string — gabungkan filter + sort + pagination
function filter_query($extra = []) {
    global $filter_role, $filter_status, $search, $sort, $order;
    $params = [];
    if ($filter_role !== '')   $params['role']   = $filter_role;
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

    return '<a href="users.php' . $url . '" class="' . $class . '">'
         . htmlspecialchars($label) . $icon_html . '</a>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=exit_to_app" />
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
                    <p class="text-3xl text-white py-1">Manajemen Users</p>
                    <p class="text-white/70 text-sm">Total <?= $total_rows ?> user<?= $filter_role || $filter_status ? ' (difilter)' : '' ?></p>
                </div>
                <div class="px-3">
                    <a href="users.php<?= filter_query(['show' => 'create']) ?>"
                       class="bg-[#FF0000] px-4 py-3 rounded text-white whitespace-nowrap hover:bg-[#6e1111] transition flex items-center gap-2 shadow-red-500/40">
                        + Tambah User
                    </a>
                </div>
            </div>

            <div class="p-6">
                <!-- Pesan feedback -->
                <?php
                $msg_map = [
                    'created'      => ['text' => 'User berhasil ditambahkan.',        'class' => 'bg-green-100 text-green-800 border-green-300'],
                    'updated'      => ['text' => 'User berhasil diperbarui.',         'class' => 'bg-blue-100 text-blue-800 border-blue-300'],
                    'deleted'      => ['text' => 'User berhasil dihapus.',            'class' => 'bg-yellow-100 text-yellow-800 border-yellow-300'],
                    'email_exists' => ['text' => 'Email sudah digunakan user lain.',  'class' => 'bg-orange-100 text-orange-800 border-orange-300'],
                    'error'        => ['text' => 'Terjadi kesalahan, coba lagi.',     'class' => 'bg-red-100 text-red-800 border-red-300'],
                ];
                $msg_key = $_GET['msg'] ?? '';
                if (isset($msg_map[$msg_key])):
                ?>
                <div class="mb-4 px-4 py-3 rounded border <?= $msg_map[$msg_key]['class'] ?>">
                    <?= htmlspecialchars($msg_map[$msg_key]['text']) ?>
                </div>
                <?php endif; ?>

                <!-- Form Tambah User -->
                <?php if ($show === 'create'): ?>
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6 shadow-sm">
                    <h2 class="text-lg font-semibold mb-4">Tambah User Baru</h2>
                    <form method="POST" action="proses_users.php">
                        <input type="hidden" name="action" value="create">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Nama <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Password <span class="text-red-500">*</span></label>
                                <input type="password" name="password" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">No. HP</label>
                                <input type="text" name="phone" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Role</label>
                                <select name="role_baru" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                                    <option value="customer">Customer</option>
                                    <option value="mechanic">Mechanic</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Status</label>
                                <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-5">
                            <button type="submit" class="bg-[#8E1616] text-white px-6 py-2 rounded hover:bg-[#6f1111] transition text-sm">Simpan</button>
                            <a href="users.php<?= filter_query() ?>" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300 transition text-sm">Batal</a>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Form Edit User -->
                <?php if ($show === 'edit' && $edit_user): ?>
                <div class="bg-white rounded-lg border border-blue-200 p-6 mb-6 shadow-sm">
                    <h2 class="text-lg font-semibold mb-4">Edit User: <?= htmlspecialchars($edit_user['name']) ?></h2>
                    <form method="POST" action="proses_users.php">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Nama <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="<?= htmlspecialchars($edit_user['name']) ?>" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="<?= htmlspecialchars($edit_user['email']) ?>" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Password Baru <span class="text-gray-400">(kosongkan jika tidak diubah)</span></label>
                                <input type="password" name="password" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">No. HP</label>
                                <input type="text" name="phone" value="<?= htmlspecialchars($edit_user['phone'] ?? '') ?>" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Role</label>
                                <select name="role_baru" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                                    <option value="customer" <?= $edit_user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                    <option value="mechanic" <?= $edit_user['role'] === 'mechanic' ? 'selected' : '' ?>>Mechanic</option>
                                    <option value="admin"    <?= $edit_user['role'] === 'admin'    ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Status</label>
                                <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                                    <option value="active"   <?= $edit_user['status'] === 'active'   ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $edit_user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-5">
                            <button type="submit" class="bg-[#8E1616] text-white px-6 py-2 rounded hover:bg-[#6f1111] transition text-sm">Update</button>
                            <a href="users.php<?= filter_query() ?>" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300 transition text-sm">Batal</a>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Filter Bar -->
                <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4 shadow-sm">
                    <form method="GET" action="users.php" class="flex gap-4 items-end flex-wrap">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Filter Role</label>
                            <select name="role" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                                <option value="">Semua Role</option>
                                <option value="admin"    <?= $filter_role === 'admin'    ? 'selected' : '' ?>>Admin</option>
                                <option value="mechanic" <?= $filter_role === 'mechanic' ? 'selected' : '' ?>>Mechanic</option>
                                <option value="customer" <?= $filter_role === 'customer' ? 'selected' : '' ?>>Customer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Filter Status</label>
                            <select name="status" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                                <option value="">Semua Status</option>
                                <option value="active"   <?= $filter_status === 'active'   ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $filter_status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Cari Nama / Email</label>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                   placeholder="Cari..."
                                   class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                        </div>
                        <button type="submit" class="bg-[#8E1616] text-white px-4 py-2 rounded text-sm hover:bg-[#6f1111] transition">Filter</button>
                        <?php if ($filter_role || $filter_status || $search): ?>
                            <a href="users.php" class="text-sm text-gray-500 hover:text-gray-700 py-2">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Tabel Users -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <?php
                            // Fungsi kecil untuk class <th> — highlight kolom aktif
                            $th = fn($col) => 'text-left px-4 py-3 font-medium ' . ($sort === $col ? 'bg-[#8E1616]/5' : '');
                            ?>
                            <tr>
                                <th class="<?= $th('id') ?>"><?= sort_link('id', 'ID') ?></th>
                                <th class="<?= $th('name') ?>"><?= sort_link('name', 'Nama') ?></th>
                                <th class="<?= $th('email') ?>"><?= sort_link('email', 'Email') ?></th>
                                <th class="<?= $th('role') ?>"><?= sort_link('role', 'Role') ?></th>
                                <th class="text-left px-4 py-3 font-medium text-gray-500">No. HP</th>
                                <th class="<?= $th('status') ?>"><?= sort_link('status', 'Status') ?></th>
                                <th class="<?= $th('created_at') ?>"><?= sort_link('created_at', 'Terdaftar') ?></th>
                                <th class="text-left px-4 py-3 font-medium text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada user ditemukan</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $u): ?>
                                <?php
                                $role_class = match($u['role']) {
                                    'admin'    => 'bg-purple-100 text-purple-700',
                                    'mechanic' => 'bg-blue-100 text-blue-700',
                                    'customer' => 'bg-green-100 text-green-700',
                                    default    => 'bg-gray-100 text-gray-700',
                                };
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-400">#<?= $u['id'] ?></td>
                                    <td class="px-4 py-3 font-medium"><?= htmlspecialchars($u['name']) ?></td>
                                    <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($u['email']) ?></td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium <?= $role_class ?>">
                                            <?= htmlspecialchars($u['role']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($u['phone'] ?? '-') ?></td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium <?= $u['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                            <?= htmlspecialchars($u['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2 items-center">
                                            <a href="users.php<?= filter_query(['show' => 'edit', 'id' => $u['id']]) ?>"
                                               class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 transition">Edit</a>
                                            <?php if ($u['id'] !== (int)$user_id): ?>
                                            <form method="POST" action="proses_users.php" onsubmit="return confirm('Yakin hapus user <?= htmlspecialchars(addslashes($u['name']), ENT_QUOTES) ?>?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition">Hapus</button>
                                            </form>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-400">(Anda)</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="flex items-center justify-between mt-4">
                    <p class="text-sm text-gray-500">
                        Menampilkan <?= count($users) ?> dari <?= $total_rows ?> user
                    </p>
                    <div class="flex gap-2 items-center">
                        <?php if ($page > 1): ?>
                            <a href="users.php<?= filter_query(['page' => $page - 1]) ?>"
                               class="px-3 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50 transition">&larr; Sebelumnya</a>
                        <?php endif; ?>

                        <span class="px-3 py-2 text-sm text-gray-600">Hal. <?= $page ?> / <?= $total_pages ?></span>

                        <?php if ($page < $total_pages): ?>
                            <a href="users.php<?= filter_query(['page' => $page + 1]) ?>"
                               class="px-3 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50 transition">Selanjutnya &rarr;</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
