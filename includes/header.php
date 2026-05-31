<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/app.php';
}
$pageTitle = $pageTitle ?? 'REVVO | Workshop Management System';
?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/logo.png') ?>">

    <!-- tailwind + fonts + icons -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=Plus+Jakarta+Sans:wght@400;600;700&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>

    <script src="<?= asset('assets/js/tailwind.config.js') ?>"></script>

    <link rel="stylesheet" href="<?= asset('assets/css/custom.css') ?>">
</head>
<body class="bg-stone-900 text-white font-body overflow-x-hidden selection:bg-primary selection:text-white">
