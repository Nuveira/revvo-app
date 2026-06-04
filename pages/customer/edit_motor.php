<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Edit Motor | REVVO';
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
require_once '../../includes/customer_role.php';

// ── Helpers (inlined) ─────────────────────────────────────────────────────
function normalize_motor_text($value) {
    $value = trim((string) $value);
    return preg_replace('/\s+/', ' ', $value);
}

function normalize_motor_plate_number($plateNumber) {
    $plateNumber = strtoupper((string) $plateNumber);
    $plateNumber = preg_replace('/[^A-Z0-9]+/', ' ', $plateNumber);
    return normalize_motor_text($plateNumber);
}

function validate_customer_motor_input(array $input, $currentYear = null) {
    $currentYear = $currentYear === null ? (int) date('Y') : (int) $currentYear;
    $data = [
        'brand'           => normalize_motor_text($input['brand'] ?? ''),
        'model'           => normalize_motor_text($input['model'] ?? ''),
        'plate_number'    => normalize_motor_plate_number($input['plate_number'] ?? ''),
        'production_year' => null,
        'color'           => normalize_motor_text($input['color'] ?? ''),
    ];
    $errors = [];

    if ($data['brand'] === '') {
        $errors['brand'] = 'Merk motor wajib diisi.';
    } elseif (strlen($data['brand']) > 50) {
        $errors['brand'] = 'Merk motor maksimal 50 karakter.';
    }

    if ($data['model'] === '') {
        $errors['model'] = 'Model motor wajib diisi.';
    } elseif (strlen($data['model']) > 50) {
        $errors['model'] = 'Model motor maksimal 50 karakter.';
    }

    if ($data['plate_number'] === '') {
        $errors['plate_number'] = 'Nomor plat wajib diisi.';
    } elseif (strlen($data['plate_number']) > 15) {
        $errors['plate_number'] = 'Nomor plat maksimal 15 karakter.';
    } elseif (!preg_match('/^[A-Z0-9 ]+$/', $data['plate_number']) || !preg_match('/\d/', $data['plate_number'])) {
        $errors['plate_number'] = 'Nomor plat hanya boleh berisi huruf, angka, dan spasi.';
    }

    $year = normalize_motor_text($input['production_year'] ?? '');
    if ($year !== '') {
        if (!ctype_digit($year)) {
            $errors['production_year'] = 'Tahun produksi harus berupa angka.';
        } else {
            $data['production_year'] = (int) $year;
            if ($data['production_year'] < 1900 || $data['production_year'] > $currentYear) {
                $errors['production_year'] = 'Tahun produksi harus di antara 1900 dan ' . $currentYear . '.';
            }
        }
    }

    if (strlen($data['color']) > 30) {
        $errors['color'] = 'Warna maksimal 30 karakter.';
    }

    return ['data' => $data, 'errors' => $errors];
}
// ── End helpers ───────────────────────────────────────────────────────────

// Ambil motor_id dari URL
$motor_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$motor_id) {
    header('Location: motor.php');
    exit;
}

// Ambil data motor — pastikan milik customer ini
$stmt = $conn->prepare("SELECT * FROM motors WHERE id = ? AND customer_id = ?");
$stmt->bind_param("ii", $motor_id, $customer_id);
$stmt->execute();
$motor = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$motor) {
    header('Location: motor.php');
    exit;
}

$errors = [];
// Prefill form dengan data motor yang ada
$formData = [
    'brand'           => $motor['brand'],
    'model'           => $motor['model'],
    'plate_number'    => $motor['plate_number'],
    'production_year' => $motor['production_year'] ?? '',
    'color'           => $motor['color'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validation = validate_customer_motor_input($_POST);
    $formData = $validation['data'];
    $errors = $validation['errors'];

    if (!$customer_id) {
        $errors['form'] = 'Data customer tidak ditemukan.';
    }

    // Cek plat duplikat — kecualikan motor ini sendiri
    if (empty($errors)) {
        $plateNumber = $formData['plate_number'];
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM motors WHERE customer_id = ? AND plate_number = ? AND id != ?");
        $stmt->bind_param("isi", $customer_id, $plateNumber, $motor_id);
        $stmt->execute();
        $existingPlate = (int) $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();

        if ($existingPlate > 0) {
            $errors['plate_number'] = 'Nomor plat ini sudah terdaftar di motor lain.';
        }
    }

    // Handle upload gambar baru (opsional)
    $imagePath = $motor['image_path']; // default: tetap pakai gambar lama
    if (!empty($errors['form'])) {
        // skip
    } elseif (!empty($_FILES['image']['name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 2 * 1024 * 1024;
        $fileType = mime_content_type($_FILES['image']['tmp_name']);
        $fileSize = $_FILES['image']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $errors['image'] = 'Format gambar harus JPG, PNG, atau WEBP.';
        } elseif ($fileSize > $maxSize) {
            $errors['image'] = 'Ukuran gambar maksimal 2MB.';
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'motor_' . uniqid() . '.' . strtolower($ext);
            $uploadDir = ROOT_PATH . '/uploads/motors/';
            $uploadPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                // Hapus gambar lama kalau ada
                if (!empty($motor['image_path'])) {
                    $oldPath = ROOT_PATH . '/' . $motor['image_path'];
                    if (file_exists($oldPath)) unlink($oldPath);
                }
                $imagePath = 'uploads/motors/' . $filename;
            } else {
                $errors['image'] = 'Gagal mengupload gambar.';
            }
        }
    }

    if (empty($errors)) {
        $brand           = $formData['brand'];
        $model           = $formData['model'];
        $plateNumber     = $formData['plate_number'];
        $productionYear  = $formData['production_year'];
        $color           = $formData['color'] !== '' ? $formData['color'] : null;

        $stmt = $conn->prepare("
            UPDATE motors
            SET brand = ?, model = ?, plate_number = ?, production_year = ?, color = ?, image_path = ?
            WHERE id = ? AND customer_id = ?
        ");
        $stmt->bind_param("ssssssii",
            $brand, $model, $plateNumber, $productionYear, $color, $imagePath,
            $motor_id, $customer_id
        );

        if ($stmt->execute()) {
            $stmt->close();
            $_SESSION['motor_success'] = 'Motor berhasil diupdate.';
            header('Location: detail_motor.php?id=' . $motor_id);
            exit;
        }

        $errors['form'] = 'Motor gagal diupdate. Coba lagi.';
        $stmt->close();
    }
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
<body class="font-['Plus_Jakarta_Sans'] overflow-hidden">
    <div class="flex h-screen overflow-hidden">
        <?php include 'nav.php'; ?>

        <main class="flex-1 min-w-0 bg-gray-100 overflow-y-auto overflow-x-hidden">
            <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between">
                <div class="min-w-0">
                    <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">Motor Saya</p>
                    <h1 class="mt-2 text-2xl sm:text-4xl text-white font-semibold break-words">
                        Edit <?= htmlspecialchars($motor['brand'] . ' ' . $motor['model']) ?>
                    </h1>
                </div>
                <a href="detail_motor.php?id=<?= $motor_id ?>" class="bg-[#FF0000] px-4 py-3 rounded text-sm font-semibold text-white whitespace-nowrap hover:bg-[#6e1111] transition inline-flex items-center gap-2 shadow-[0_0_15px_rgba(142,22,22,0.3)] shadow-red-500/40">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    Kembali
                </a>
            </div>

            <div class="p-4 mx-auto">
                <?php if (!empty($errors['form'])): ?>
                    <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                        <?= htmlspecialchars($errors['form']) ?>
                    </div>
                <?php endif; ?>

                <div class="rounded-lg border border-[#eadede] bg-white p-5 shadow-sm">
                    <div class="mb-5">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Form Motor</p>
                        <h2 class="mt-2 text-xl font-semibold text-gray-900">Edit Motor</h2>
                    </div>

                    <form method="POST" action="edit_motor.php?id=<?= $motor_id ?>" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label for="brand" class="text-sm font-medium text-gray-700">Brand</label>
                            <input id="brand" name="brand" type="text" maxlength="50"
                                value="<?= htmlspecialchars($formData['brand']) ?>"
                                class="mt-2 w-full rounded border <?= isset($errors['brand']) ? 'border-red-300' : 'border-gray-200' ?> px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                            <?php if (!empty($errors['brand'])): ?>
                                <p class="mt-1 text-xs font-medium text-red-600"><?= htmlspecialchars($errors['brand']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="model" class="text-sm font-medium text-gray-700">Model</label>
                            <input id="model" name="model" type="text" maxlength="50"
                                value="<?= htmlspecialchars($formData['model']) ?>"
                                class="mt-2 w-full rounded border <?= isset($errors['model']) ? 'border-red-300' : 'border-gray-200' ?> px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                            <?php if (!empty($errors['model'])): ?>
                                <p class="mt-1 text-xs font-medium text-red-600"><?= htmlspecialchars($errors['model']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="plate_number" class="text-sm font-medium text-gray-700">Nomor plat</label>
                            <input id="plate_number" name="plate_number" type="text" maxlength="15"
                                value="<?= htmlspecialchars($formData['plate_number']) ?>"
                                class="mt-2 w-full rounded border <?= isset($errors['plate_number']) ? 'border-red-300' : 'border-gray-200' ?> px-3 py-3 text-sm uppercase tracking-wide outline-none transition focus:border-[#8E1616]">
                            <?php if (!empty($errors['plate_number'])): ?>
                                <p class="mt-1 text-xs font-medium text-red-600"><?= htmlspecialchars($errors['plate_number']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="production_year" class="text-sm font-medium text-gray-700">Tahun</label>
                                <input id="production_year" name="production_year" type="number"
                                    min="1900" max="<?= (int) date('Y') ?>"
                                    value="<?= htmlspecialchars((string) ($formData['production_year'] ?? '')) ?>"
                                    class="mt-2 w-full rounded border <?= isset($errors['production_year']) ? 'border-red-300' : 'border-gray-200' ?> px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                                <?php if (!empty($errors['production_year'])): ?>
                                    <p class="mt-1 text-xs font-medium text-red-600"><?= htmlspecialchars($errors['production_year']) ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label for="color" class="text-sm font-medium text-gray-700">Warna</label>
                                <input id="color" name="color" type="text" maxlength="30"
                                    value="<?= htmlspecialchars($formData['color']) ?>"
                                    class="mt-2 w-full rounded border <?= isset($errors['color']) ? 'border-red-300' : 'border-gray-200' ?> px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                                <?php if (!empty($errors['color'])): ?>
                                    <p class="mt-1 text-xs font-medium text-red-600"><?= htmlspecialchars($errors['color']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Preview foto saat ini -->
                        <?php if (!empty($motor['image_path'])): ?>
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Foto saat ini</p>
                            <img src="<?= htmlspecialchars(asset($motor['image_path'])) ?>" alt="Foto motor"
                                 class="h-24 w-24 rounded-lg object-cover border border-gray-200">
                        </div>
                        <?php endif; ?>

                        <div>
                            <label for="image" class="text-sm font-medium text-gray-700">
                                Ganti Foto <span class="text-gray-400 font-normal">(opsional, maks. 2MB)</span>
                            </label>
                            <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"
                                class="mt-2 w-full rounded border <?= isset($errors['image']) ? 'border-red-300' : 'border-gray-200' ?> px-3 py-2.5 text-sm outline-none transition focus:border-[#8E1616] file:mr-3 file:rounded file:border-0 file:bg-[#f8eeee] file:px-3 file:py-1 file:text-xs file:font-semibold file:text-[#8E1616]">
                            <?php if (!empty($errors['image'])): ?>
                                <p class="mt-1 text-xs font-medium text-red-600"><?= htmlspecialchars($errors['image']) ?></p>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded bg-[#8E1616] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#6f1111]">
                            <span class="material-symbols-outlined text-[20px]">save</span>
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            <?php include 'footer.php'; ?>
        </main>
    </div>
</body>
</html>
