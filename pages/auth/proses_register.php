<?php
// proses register - handle form submit, redirect setelah selesai
session_start();
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../config/app.php';

// cuma terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('pages/auth/register.php'));
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

// validasi input
if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm)) {
    $_SESSION['register_error'] = 'Semua field harus diisi';
    header('Location: ' . url('pages/auth/register.php'));
    exit;
}

if ($password !== $confirm) {
    $_SESSION['register_error'] = 'Password tidak cocok';
    header('Location: ' . url('pages/auth/register.php'));
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['register_error'] = 'Password minimal 6 karakter';
    header('Location: ' . url('pages/auth/register.php'));
    exit;
}

// cek email sudah terdaftar
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $_SESSION['register_error'] = 'Email sudah terdaftar';
    header('Location: ' . url('pages/auth/register.php'));
    exit;
}
$stmt->close();

// insert user baru
$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role, phone, status) VALUES (?, ?, ?, 'customer', ?, 'active')");
$stmt->bind_param("ssss", $name, $email, $hashed, $phone);
$stmt->execute();
$user_id = $conn->insert_id;
$stmt->close();

// insert ke tabel customers
$stmt = $conn->prepare("INSERT INTO customers (user_id) VALUES (?)");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

$_SESSION['register_success'] = 'Registrasi berhasil! Silakan login';
header('Location: ' . url('pages/auth/register.php'));
exit;
