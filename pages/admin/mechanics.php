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
                    <p class="text-3xl text-white py-1">Manajemen Users</p>
                    <p class="text-white/70 text-sm">Total <?= $total_rows ?> user<?= $filter_role || $filter_status ? ' (difilter)' : '' ?></p>
                </div>
                <div class="px-3">
                </div>
            </div>

            <div class="p-6">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm min-h-[500px] flex items-center justify-center">
                    <div class="text-center">
                        <span class="material-symbols-outlined text-7xl text-[#8E1616] mb-4 block">
                            construction
                        </span>

                        <h2 class="text-4xl font-bold text-[#8E1616] mb-3">
                            COMING SOON
                        </h2>

                        <p class="text-gray-600 text-lg">
                            Modul Mechanics sedang dalam pengembangan
                        </p>

                        <p class="text-gray-400 text-sm mt-2">
                            Mohon tunggu update berikutnya
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
