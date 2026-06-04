<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
require_once '../../config/app.php';

checkRole(['customer']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('pages/customer/profile.php'));
    exit;
}

$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    header('Location: ' . url('pages/auth/login.php'));
    exit;
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$gender = $_POST['gender'] ?? '';
$birth_date = trim($_POST['birth_date'] ?? '');
$no_ktp = trim($_POST['no_ktp'] ?? '');

$_SESSION['profile_old'] = [
    'name' => $name,
    'phone' => $phone,
    'address' => $address,
    'gender' => $gender,
    'birth_date' => $birth_date,
    'no_ktp' => $no_ktp,
];

if ($name === '' || $phone === '') {
    $_SESSION['profile_error'] = 'Nama lengkap dan no. HP wajib diisi.';
    header('Location: ' . url('pages/customer/profile.php'));
    exit;
}

if (strlen($name) > 100) {
    $_SESSION['profile_error'] = 'Nama lengkap maksimal 100 karakter.';
    header('Location: ' . url('pages/customer/profile.php'));
    exit;
}

if (strlen($phone) > 20) {
    $_SESSION['profile_error'] = 'No. HP maksimal 20 karakter.';
    header('Location: ' . url('pages/customer/profile.php'));
    exit;
}

if ($gender !== '' && !in_array($gender, ['male', 'female'], true)) {
    $_SESSION['profile_error'] = 'Jenis kelamin tidak valid.';
    header('Location: ' . url('pages/customer/profile.php'));
    exit;
}

if ($birth_date !== '') {
    $birthDateTime = DateTime::createFromFormat('Y-m-d', $birth_date);
    $birthDateValid = $birthDateTime && $birthDateTime->format('Y-m-d') === $birth_date;

    if (!$birthDateValid) {
        $_SESSION['profile_error'] = 'Format tanggal lahir tidak valid.';
        header('Location: ' . url('pages/customer/profile.php'));
        exit;
    }
}

if (strlen($no_ktp) > 20) {
    $_SESSION['profile_error'] = 'No. KTP maksimal 20 karakter.';
    header('Location: ' . url('pages/customer/profile.php'));
    exit;
}

$stmt = $conn->prepare("
    SELECT c.id AS customer_id, u.profile_photo
    FROM users u
    LEFT JOIN customers c ON c.user_id = u.id
    WHERE u.id = ?
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$currentUser = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$currentUser || empty($currentUser['customer_id'])) {
    $_SESSION['profile_error'] = 'Data customer tidak ditemukan.';
    header('Location: ' . url('pages/customer/profile.php'));
    exit;
}

$customer_id = (int) $currentUser['customer_id'];
$current_profile_photo = $currentUser['profile_photo'] ?? null;
$new_profile_photo = $current_profile_photo;

if (!empty($_FILES['profile_photo']['name'])) {
    if ($_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['profile_error'] = 'Upload foto profil gagal.';
        header('Location: ' . url('pages/customer/profile.php'));
        exit;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 2 * 1024 * 1024;
    $fileType = mime_content_type($_FILES['profile_photo']['tmp_name']);
    $fileSize = (int) $_FILES['profile_photo']['size'];

    if (!in_array($fileType, $allowedTypes, true)) {
        $_SESSION['profile_error'] = 'Format foto profil harus JPG, PNG, atau WEBP.';
        header('Location: ' . url('pages/customer/profile.php'));
        exit;
    }

    if ($fileSize > $maxSize) {
        $_SESSION['profile_error'] = 'Ukuran foto profil maksimal 2MB.';
        header('Location: ' . url('pages/customer/profile.php'));
        exit;
    }

    $extension = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
    $new_profile_photo = 'profile_' . $user_id . '_' . time() . '.' . $extension;
    $uploadDir = ROOT_PATH . '/uploads/profile/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $uploadPath = $uploadDir . $new_profile_photo;

    if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadPath)) {
        $_SESSION['profile_error'] = 'Foto profil gagal disimpan.';
        header('Location: ' . url('pages/customer/profile.php'));
        exit;
    }

    if (!empty($current_profile_photo)) {
        $oldPath = $uploadDir . $current_profile_photo;
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }
}

$address = $address !== '' ? $address : null;
$gender = $gender !== '' ? $gender : null;
$birth_date = $birth_date !== '' ? $birth_date : null;
$no_ktp = $no_ktp !== '' ? $no_ktp : null;

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, profile_photo = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $phone, $new_profile_photo, $user_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE customers SET address = ?, gender = ?, birth_date = ?, no_ktp = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $address, $gender, $birth_date, $no_ktp, $customer_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    $_SESSION['profile_error'] = 'Gagal menyimpan data profil.';
    header('Location: ' . url('pages/customer/profile.php'));
    exit;
}

$_SESSION['name'] = $name;
unset($_SESSION['profile_old']);
$_SESSION['profile_success'] = 'Profil berhasil diperbarui.';

header('Location: ' . url('pages/customer/profile.php'));
exit;
