<?php
session_start();
require_once __DIR__ . '/../../config/app.php';

// ambil error dari session kalau ada
$error = $_SESSION['Register error :('] ?? '';
unset ($_SESSION['Register error :(']);

$_SESSION['Old input'] = $_SESSION['Old input'] ?? [];
unset ($_SESSION['Old input']);

$pageTitle = 'Register | Revvo;';
require_once __DIR__ . '/../../includes/header.php';

?>
