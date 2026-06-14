<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Profil | REVVO';
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
require_once '../../includes/customer_role.php';

$successMessage = $_SESSION['profile_success'] ?? '';
$errorMessage = $_SESSION['profile_error'] ?? '';
$oldInput = $_SESSION['profile_old'] ?? [];

unset($_SESSION['profile_success'], $_SESSION['profile_error'], $_SESSION['profile_old']);

$profileData = [
    'name' => $nama,
    'email' => '',
    'phone' => '',
    'address' => '',
    'gender' => '',
    'birth_date' => '',
    'no_ktp' => '',
];

if ($user_id) {
    $stmt = $conn->prepare("
        SELECT
            u.name,
            u.email,
            u.phone,
            u.profile_photo,
            c.address,
            c.gender,
            c.birth_date,
            c.no_ktp
        FROM users u
        LEFT JOIN customers c ON c.user_id = u.id
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row) {
        $profileData['name'] = $row['name'] ?? '';
        $profileData['email'] = $row['email'] ?? '';
        $profileData['phone'] = $row['phone'] ?? '';
        $profileData['address'] = $row['address'] ?? '';
        $profileData['gender'] = $row['gender'] ?? '';
        $profileData['birth_date'] = $row['birth_date'] ?? '';
        $profileData['no_ktp'] = $row['no_ktp'] ?? '';
        $profile_photo = $row['profile_photo'] ?? $profile_photo;
    }
}

if (!empty($oldInput)) {
    foreach ($oldInput as $key => $value) {
        if (array_key_exists($key, $profileData)) {
            $profileData[$key] = $value;
        }
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
<body class="font-['Plus_Jakarta_Sans'] flex h-screen">
    <?php include 'nav.php'; ?>

    <main class="flex-1 bg-gray-100 overflow-y-auto overflow-x-hidden">
        <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between">
            <div class="min-w-0">
                <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">profil customer</p>
                <h1 class="mt-2 text-2xl sm:text-4xl text-white font-semibold break-words">Kelola data akun dan profil</h1>
            </div>
        </div>

        <div class="p-4 md:p-6">
            <?php if ($successMessage !== ''): ?>
                <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    <?= htmlspecialchars($successMessage) ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMessage !== ''): ?>
                <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 gap-4 xl:grid-cols-[320px_minmax(0,1fr)]">
                <section class="rounded-lg border border-[#eadede] bg-white p-5 shadow-sm">
                    <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase">Ringkasan Akun</p>

                    <div class="mt-5 flex flex-col items-center text-center">
                        <?php if (!empty($profile_photo)): ?>
                            <img src="<?= htmlspecialchars(asset('uploads/profile/' . $profile_photo)) ?>" alt="Foto profil" class="h-28 w-28 rounded-full object-cover border-4 border-[#f8eeee] shadow-sm">
                        <?php else: ?>
                            <div class="flex h-28 w-28 items-center justify-center rounded-full bg-[#f8eeee] text-4xl font-bold text-[#8E1616]">
                                <?= strtoupper(substr($profileData['name'] ?: 'U', 0, 1)) ?>
                            </div>
                        <?php endif; ?>

                        <h2 class="mt-4 text-xl font-semibold text-gray-900"><?= htmlspecialchars($profileData['name']) ?></h2>
                        <p class="mt-1 text-sm text-gray-500"><?= htmlspecialchars($profileData['email']) ?></p>

                        <div class="mt-5 grid w-full grid-cols-1 gap-3 text-left">
                            <div class="rounded border border-gray-100 bg-gray-50 px-4 py-3">
                                <p class="text-[11px] uppercase tracking-[0.15em] text-gray-400">Role</p>
                                <p class="mt-1 text-sm font-medium text-gray-800">Customer</p>
                            </div>
                            <div class="rounded border border-gray-100 bg-gray-50 px-4 py-3">
                                <p class="text-[11px] uppercase tracking-[0.15em] text-gray-400">No. HP</p>
                                <p class="mt-1 text-sm font-medium text-gray-800"><?= $profileData['phone'] !== '' ? htmlspecialchars($profileData['phone']) : '-' ?></p>
                            </div>
                            <div class="rounded border border-gray-100 bg-gray-50 px-4 py-3">
                                <p class="text-[11px] uppercase tracking-[0.15em] text-gray-400">Jenis Kelamin</p>
                                <p class="mt-1 text-sm font-medium text-gray-800">
                                    <?php
                                    if ($profileData['gender'] === 'male') {
                                        echo 'Laki-laki';
                                    } elseif ($profileData['gender'] === 'female') {
                                        echo 'Perempuan';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-lg border border-[#eadede] bg-white p-5 shadow-sm">
                    <div class="mb-5">
                        <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase">Form Profil</p>
                        <h2 class="mt-1 text-2xl font-semibold text-[#8E1616]">Edit profil customer</h2>
                    </div>

                    <form method="POST" action="proses_profile.php" enctype="multipart/form-data" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="name" class="text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input id="name" name="name" type="text" maxlength="100" required
                                    value="<?= htmlspecialchars($profileData['name']) ?>"
                                    class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                            </div>

                            <div>
                                <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                                <input id="email" type="email" value="<?= htmlspecialchars($profileData['email']) ?>" readonly
                                    class="mt-2 w-full rounded border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-500 outline-none">
                            </div>

                            <div>
                                <label for="phone" class="text-sm font-medium text-gray-700">No. HP</label>
                                <input id="phone" name="phone" type="text" maxlength="20" required
                                    value="<?= htmlspecialchars($profileData['phone']) ?>"
                                    class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                            </div>

                            <div>
                                <label for="gender" class="text-sm font-medium text-gray-700">Jenis Kelamin</label>
                                <select id="gender" name="gender"
                                    class="mt-2 w-full rounded border border-gray-200 bg-white px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="male" <?= $profileData['gender'] === 'male' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="female" <?= $profileData['gender'] === 'female' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>

                            <div>
                                <label for="birth_date" class="text-sm font-medium text-gray-700">Tanggal Lahir</label>
                                <input id="birth_date" name="birth_date" type="date"
                                    value="<?= htmlspecialchars($profileData['birth_date']) ?>"
                                    class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                            </div>

                            <div>
                                <label for="no_ktp" class="text-sm font-medium text-gray-700">No. KTP</label>
                                <input id="no_ktp" name="no_ktp" type="text" maxlength="20"
                                    value="<?= htmlspecialchars($profileData['no_ktp']) ?>"
                                    class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                            </div>
                        </div>

                        <div>
                            <label for="address" class="text-sm font-medium text-gray-700">Alamat</label>
                            <textarea id="address" name="address" rows="4"
                                class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]"><?= htmlspecialchars($profileData['address']) ?></textarea>
                        </div>

                        <div>
                            <label for="profile_photo" class="text-sm font-medium text-gray-700">Foto Profil</label>
                            <input id="profile_photo" name="profile_photo" type="file" accept=".jpg,.jpeg,.png,.webp"
                                class="mt-2 w-full rounded border border-gray-200 bg-white px-3 py-3 text-sm text-gray-700 outline-none transition file:mr-4 file:rounded file:border-0 file:bg-[#8E1616] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#6f1111]">
                            <p class="mt-2 text-xs text-gray-400">Format JPG, PNG, atau WEBP. Maksimal 2MB.</p>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded bg-[#8E1616] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#6f1111]">
                                <span class="material-symbols-outlined text-[18px]">save</span>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </div>

        <?php include 'footer.php'; ?>
    </main>

</body>
</html>
