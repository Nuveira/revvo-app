<?php
// Fungsi: Inisialisasi — memulai session, koneksi DB, dan cek role admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Spare Parts | REVVO Admin';
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

// Fungsi: Inisialisasi variabel pesan feedback
$msg_type = '';
$msg_text = '';

// ============================================================
// HANDLER POST — Proses Tambah, Edit, Hapus
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Fungsi: Handler Tambah — insert spare part baru ke database
    if ($action === 'create') {
        $sku           = trim($_POST['sku'] ?? '');
        $name          = trim($_POST['name'] ?? '');
        $unit          = trim($_POST['unit'] ?? '');
        $stock         = (int)($_POST['stock'] ?? 0);
        $minimum_stock = (int)($_POST['minimum_stock'] ?? 5);
        $price         = (float)($_POST['price'] ?? 0);
        $status        = $_POST['status'] ?? 'active';

        // Validasi: semua field wajib diisi
        if ($sku === '' || $name === '' || $unit === '' || $price === 0.0) {
            $msg_type = 'error';
            $msg_text = 'Semua field wajib diisi.';
        }
        // Validasi: stok dan harga tidak boleh negatif
        elseif ($stock < 0 || $minimum_stock < 0 || $price < 0) {
            $msg_type = 'error';
            $msg_text = 'Stok dan harga tidak boleh negatif.';
        }
        // Validasi: status harus valid
        elseif (!in_array($status, ['active', 'inactive'])) {
            $msg_type = 'error';
            $msg_text = 'Status tidak valid.';
        } else {
            // Validasi: SKU harus unik
            $stmt = $conn->prepare("SELECT id FROM spare_parts WHERE sku = ?");
            $stmt->bind_param("s", $sku);
            $stmt->execute();
            $exists = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($exists) {
                $msg_type = 'error';
                $msg_text = 'SKU sudah digunakan, gunakan SKU lain.';
            } else {
                $stmt = $conn->prepare("INSERT INTO spare_parts (sku, name, unit, stock, minimum_stock, price, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $stmt->bind_param("sssiids", $sku, $name, $unit, $stock, $minimum_stock, $price, $status);

                if ($stmt->execute()) {
                    $msg_type = 'success';
                    $msg_text = 'Spare part berhasil ditambahkan.';
                } else {
                    $msg_type = 'error';
                    $msg_text = 'Gagal menambahkan spare part.';
                }
                $stmt->close();
            }
        }
    }

    // Fungsi: Handler Edit — update data spare part yang sudah ada
    elseif ($action === 'edit') {
        $id            = (int)($_POST['id'] ?? 0);
        $sku           = trim($_POST['sku'] ?? '');
        $name          = trim($_POST['name'] ?? '');
        $unit          = trim($_POST['unit'] ?? '');
        $stock         = (int)($_POST['stock'] ?? 0);
        $minimum_stock = (int)($_POST['minimum_stock'] ?? 5);
        $price         = (float)($_POST['price'] ?? 0);
        $status        = $_POST['status'] ?? 'active';

        // Validasi: semua field wajib diisi
        if ($id <= 0 || $sku === '' || $name === '' || $unit === '' || $price === 0.0) {
            $msg_type = 'error';
            $msg_text = 'Semua field wajib diisi.';
        }
        // Validasi: stok dan harga tidak boleh negatif
        elseif ($stock < 0 || $minimum_stock < 0 || $price < 0) {
            $msg_type = 'error';
            $msg_text = 'Stok dan harga tidak boleh negatif.';
        }
        // Validasi: status harus valid
        elseif (!in_array($status, ['active', 'inactive'])) {
            $msg_type = 'error';
            $msg_text = 'Status tidak valid.';
        } else {
            // Validasi: SKU unik kecuali milik diri sendiri
            $stmt = $conn->prepare("SELECT id FROM spare_parts WHERE sku = ? AND id != ?");
            $stmt->bind_param("si", $sku, $id);
            $stmt->execute();
            $exists = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($exists) {
                $msg_type = 'error';
                $msg_text = 'SKU sudah digunakan oleh spare part lain.';
            } else {
                $stmt = $conn->prepare("UPDATE spare_parts SET sku = ?, name = ?, unit = ?, stock = ?, minimum_stock = ?, price = ?, status = ?, updated_at = NOW() WHERE id = ?");
                $stmt->bind_param("sssiidsi", $sku, $name, $unit, $stock, $minimum_stock, $price, $status, $id);

                if ($stmt->execute()) {
                    $msg_type = 'success';
                    $msg_text = 'Spare part berhasil diperbarui.';
                } else {
                    $msg_type = 'error';
                    $msg_text = 'Gagal memperbarui spare part.';
                }
                $stmt->close();
            }
        }
    }

    // Fungsi: Handler Hapus — hapus spare part jika tidak dipakai di booking_parts
    elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $msg_type = 'error';
            $msg_text = 'ID spare part tidak valid.';
        } else {
            // Cek apakah spare part dipakai di tabel booking_parts
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM booking_parts WHERE spare_part_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $used = $stmt->get_result()->fetch_assoc()['total'];
            $stmt->close();

            if ($used > 0) {
                $msg_type = 'error';
                $msg_text = 'Spare part tidak bisa dihapus karena sudah digunakan di ' . $used . ' booking.';
            } else {
                $stmt = $conn->prepare("DELETE FROM spare_parts WHERE id = ?");
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    $msg_type = 'success';
                    $msg_text = 'Spare part berhasil dihapus.';
                } else {
                    $msg_type = 'error';
                    $msg_text = 'Gagal menghapus spare part.';
                }
                $stmt->close();
            }
        }
    }
}

// ============================================================
// QUERY LIST — Ambil semua spare parts untuk ditampilkan
// ============================================================

// Fungsi: Ambil data edit — jika ada parameter edit di GET
$show    = $_GET['show'] ?? '';
$edit_id = (int)($_GET['id'] ?? 0);
$edit_part = null;

if ($show === 'edit' && $edit_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM spare_parts WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_part = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Fungsi: Ambil semua spare parts — untuk ditampilkan di tabel
$stmt = $conn->prepare("SELECT * FROM spare_parts ORDER BY created_at DESC");
$stmt->execute();
$spare_parts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$total_rows = count($spare_parts);
?>

<!DOCTYPE html>
<html lang="id">
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
                    <p class="text-3xl text-white py-1">Manajemen Spare Parts</p>
                    <p class="text-white/70 text-sm">Total <?= $total_rows ?> spare part</p>
                </div>
                <div class="px-3">
                    <button onclick="toggleForm('form-create')"
                       class="bg-[#FF0000] px-4 py-3 rounded text-white whitespace-nowrap hover:bg-[#6e1111] transition flex items-center gap-2 shadow-red-500/40 cursor-pointer">
                        + Tambah Spare Part
                    </button>
                </div>
            </div>

            <div class="p-6">
                <!-- Fungsi: Alert feedback — tampilkan pesan sukses/error setelah aksi -->
                <?php if ($msg_text !== ''): ?>
                <div id="alert-msg" class="mb-4 px-4 py-3 rounded border <?= $msg_type === 'success' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-red-100 text-red-800 border-red-300' ?>">
                    <?= htmlspecialchars($msg_text) ?>
                </div>
                <?php endif; ?>

                <!-- Fungsi: Form Tambah — form inline untuk menambah spare part baru -->
                <div id="form-create" class="bg-white rounded-lg border border-gray-200 p-6 mb-6 shadow-sm <?= $show === 'create' ? '' : 'hidden' ?>">
                    <h2 class="text-lg font-semibold mb-4">Tambah Spare Part Baru</h2>
                    <form method="POST" action="spare_parts.php">
                        <input type="hidden" name="action" value="create">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">SKU <span class="text-red-500">*</span></label>
                                <input type="text" name="sku" required placeholder="Contoh: SP-001" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Nama <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required placeholder="Nama spare part" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Satuan <span class="text-red-500">*</span></label>
                                <input type="text" name="unit" required placeholder="pcs, liter, set" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" name="price" required min="0" step="0.01" placeholder="0" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Stok <span class="text-red-500">*</span></label>
                                <input type="number" name="stock" required min="0" value="0" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Minimum Stok</label>
                                <input type="number" name="minimum_stock" min="0" value="5" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
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
                            <button type="submit" class="bg-[#8E1616] text-white px-6 py-2 rounded hover:bg-[#6f1111] transition text-sm cursor-pointer">Simpan</button>
                            <button type="button" onclick="toggleForm('form-create')" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300 transition text-sm cursor-pointer">Batal</button>
                        </div>
                    </form>
                </div>

                <!-- Fungsi: Form Edit — form inline pre-filled untuk mengedit spare part -->
                <?php if ($show === 'edit' && $edit_part): ?>
                <div id="form-edit" class="bg-white rounded-lg border border-blue-200 p-6 mb-6 shadow-sm">
                    <h2 class="text-lg font-semibold mb-4">Edit Spare Part: <?= htmlspecialchars($edit_part['name']) ?></h2>
                    <form method="POST" action="spare_parts.php">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= $edit_part['id'] ?>">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">SKU <span class="text-red-500">*</span></label>
                                <input type="text" name="sku" required value="<?= htmlspecialchars($edit_part['sku']) ?>" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Nama <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required value="<?= htmlspecialchars($edit_part['name']) ?>" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Satuan <span class="text-red-500">*</span></label>
                                <input type="text" name="unit" required value="<?= htmlspecialchars($edit_part['unit']) ?>" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" name="price" required min="0" step="0.01" value="<?= $edit_part['price'] ?>" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Stok <span class="text-red-500">*</span></label>
                                <input type="number" name="stock" required min="0" value="<?= $edit_part['stock'] ?>" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Minimum Stok</label>
                                <input type="number" name="minimum_stock" min="0" value="<?= $edit_part['minimum_stock'] ?>" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Status</label>
                                <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#8E1616]">
                                    <option value="active" <?= $edit_part['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $edit_part['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-5">
                            <button type="submit" class="bg-[#8E1616] text-white px-6 py-2 rounded hover:bg-[#6f1111] transition text-sm cursor-pointer">Update</button>
                            <a href="spare_parts.php" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300 transition text-sm inline-flex items-center">Batal</a>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Fungsi: Tabel Spare Parts — menampilkan semua data spare part -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200 sticky top-0">
                                <tr>
                                    <th class="text-left px-4 py-3 font-medium text-gray-500">No</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-500">SKU</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-500">Nama</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-500">Satuan</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-500">Stok</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-500">Min Stok</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-500">Harga</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-500">Status</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if (empty($spare_parts)): ?>
                                    <tr>
                                        <td colspan="9" class="px-4 py-8 text-center text-gray-400">Belum ada data spare part</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($spare_parts as $index => $part): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-400"><?= $index + 1 ?></td>
                                        <td class="px-4 py-3 font-mono text-xs text-gray-700"><?= htmlspecialchars($part['sku']) ?></td>
                                        <td class="px-4 py-3 font-medium"><?= htmlspecialchars($part['name']) ?></td>
                                        <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($part['unit']) ?></td>
                                        <td class="px-4 py-3">
                                            <?php if ((int)$part['stock'] <= (int)$part['minimum_stock']): ?>
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                    <?= $part['stock'] ?> ⚠
                                                </span>
                                            <?php else: ?>
                                                <span class="text-gray-700"><?= $part['stock'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-gray-500"><?= $part['minimum_stock'] ?></td>
                                        <td class="px-4 py-3 text-gray-700">Rp <?= number_format((float)$part['price'], 0, ',', '.') ?></td>
                                        <td class="px-4 py-3">
                                            <?php if ($part['status'] === 'active'): ?>
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">active</span>
                                            <?php else: ?>
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex gap-2 items-center">
                                                <a href="spare_parts.php?show=edit&id=<?= $part['id'] ?>"
                                                   class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 transition">Edit</a>
                                                <button type="button" onclick="openDeleteModal(<?= $part['id'] ?>, '<?= htmlspecialchars(addslashes($part['name']), ENT_QUOTES) ?>')" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition cursor-pointer">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Fungsi: Info total — menampilkan jumlah data -->
                <div class="mt-4">
                    <p class="text-sm text-gray-500">Menampilkan <?= $total_rows ?> spare part</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fungsi: Modal Konfirmasi Hapus — custom modal dark theme pengganti confirm() -->
    <div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- Overlay -->
        <div id="delete-modal-overlay" onclick="closeDeleteModal()" class="absolute inset-0 bg-black/50 transition-opacity duration-200 opacity-0"></div>
        <!-- Card Modal -->
        <div id="delete-modal-card" class="relative bg-stone-800 border border-white/10 rounded-xl p-6 w-full max-w-sm mx-4 transition-all duration-200 opacity-0 scale-95">
            <div class="flex flex-col items-center text-center">
                <!-- Icon Warning -->
                <div class="mb-4 text-yellow-400">
                    <i data-lucide="triangle-alert" class="w-12 h-12"></i>
                </div>
                <!-- Judul -->
                <h3 class="text-lg font-semibold text-white mb-1">Hapus Spare Part?</h3>
                <!-- Nama spare part dinamis -->
                <p id="delete-modal-name" class="text-sm text-stone-300 mb-2"></p>
                <!-- Peringatan -->
                <p class="text-xs text-stone-400 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
                <!-- Form POST hapus -->
                <form id="delete-modal-form" method="POST" action="spare_parts.php" class="w-full">
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
        // Fungsi: toggleForm — menampilkan/menyembunyikan form berdasarkan ID
        function toggleForm(formId) {
            var form = document.getElementById(formId);
            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
            } else {
                form.classList.add('hidden');
            }
        }

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
                overlay.classList.remove('opacity-0');
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
            overlay.classList.add('opacity-0');
            card.classList.remove('opacity-100', 'scale-100');
            card.classList.add('opacity-0', 'scale-95');

            // Sembunyikan setelah transisi selesai
            setTimeout(function() {
                modal.classList.add('hidden');
            }, 200);
        }

        // Fungsi: Auto-dismiss alert — menghilangkan pesan feedback setelah 3 detik
        (function() {
            var alert = document.getElementById('alert-msg');
            if (alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.3s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                }, 3000);
            }
        })();
    </script>
    <!-- Fungsi: Load Lucide icons — render semua icon termasuk yang di modal -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
