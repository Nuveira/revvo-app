<?php
// proses logout - destroy session dan redirect ke login
session_start();
require_once __DIR__ . '/../../config/app.php';

// cuma terima POST, selain itu tendang balik
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('pages/auth/logout.php'));
    exit;
}

// hapus semua data session
$_SESSION = [];

// hapus session cookie kalau ada
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: ' . url('pages/auth/login.php'));
exit;
