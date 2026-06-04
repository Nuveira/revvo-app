<?php
session_start();
require_once __DIR__ . '/../config/app.php';

http_response_code(403);
$pageTitle = '403 Forbidden | REVVO';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center px-4" style="background-color: #1D1616;">
    <div class="text-center">
        <p class="font-technical text-6xl font-bold mb-4" style="color: #D84040;">403</p>
        <h1 class="text-2xl font-bold mb-2" style="color: #EEEEEE;">Akses Ditolak</h1>
        <p class="text-sm mb-8" style="color: #EEEEEE99;">Kamu tidak punya izin untuk mengakses halaman ini.</p>
        <a href="<?= url('index.php') ?>"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm transition-all duration-300"
            style="background-color: #D84040; color: #EEEEEE;"
            onmouseover="this.style.backgroundColor='#8E1616'"
            onmouseout="this.style.backgroundColor='#D84040'">
            <i data-lucide="arrow-left" style="width:16px; height:16px;"></i>
            Kembali ke Beranda
        </a>
    </div>
</div>