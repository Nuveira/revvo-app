<?php
// proses login - handle form submit
session_start();
require_once __DIR__ . '/../../config/koneksi.php';

// cuma terima POST, selain itu tendang balik
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('pages/auth/login.php'));
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];

// validasi input kosong
if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = 'Email dan password harus diisi';
    header('Location: ' . url('pages/auth/login.php'));
    exit;
}

// cari user berdasarkan email yang aktif
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // cocokin password
    if (password_verify($password, $user['password_hash'])) {
        // simpen data user ke session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        // redirect sesuai role
        if ($user['role'] === 'admin') {
            header('Location: ' . url('pages/admin/dashboard.php'));
        } elseif ($user['role'] === 'mechanic') {
            header('Location: ' . url('pages/mekanik/dashboard.php'));
        } else {
            header('Location: ' . url('pages/customer/dashboard.php'));
        }
        exit;
    }
}

// kalau sampe sini berarti gagal
$_SESSION['login_error'] = 'Email atau password salah';
header('Location: ' . url('pages/auth/login.php'));
exit;
