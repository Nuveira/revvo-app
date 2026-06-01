<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/app.php';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkRole(array $allowed_roles) {
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . url('pages/auth/login.php'));
        exit;
    }

    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header('Location: ' . url('pages/403.php'));
        exit;
    }
}
