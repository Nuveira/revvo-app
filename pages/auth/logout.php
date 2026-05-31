<?php
// halaman konfirmasi logout
session_start();
require_once __DIR__ . '/../../config/app.php';

// kalau belum login, langsung ke login
if (empty($_SESSION['user_id'])) {
    header('Location: ' . url('pages/auth/login.php'));
    exit;
}

$pageTitle = 'Logout | REVVO';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center px-4" style="background-color: #1D1616;">
    <div class="w-full max-w-md">
        <!-- logo -->
        <div class="text-center mb-8">
            <a href="<?= url('index.php') ?>" class="inline-flex items-center gap-2 mb-6">
                <img src="<?= asset('assets/images/logo.png') ?>" alt="REVVO" class="h-10 w-auto invert brightness-0 invert">
                <span class="font-headline font-bold text-3xl tracking-tighter" style="color: #EEEEEE;">REVVO</span>
            </a>
        </div>

        <!-- card konfirmasi -->
        <div class="rounded-2xl p-8 border text-center" style="background-color: #2a1f1f; border-color: rgba(238,238,238,0.1);">
            <div class="flex justify-center mb-4">
                <div class="rounded-full p-4" style="background-color: rgba(216,64,64,0.1);">
                    <i data-lucide="log-out" style="color: #D84040; width: 32px; height: 32px;"></i>
                </div>
            </div>

            <h1 class="text-xl font-bold mb-2" style="color: #EEEEEE;">Keluar dari Akun?</h1>
            <p class="text-sm mb-6" style="color: #EEEEEE99;">
                Kamu akan keluar sebagai <strong style="color: #EEEEEE;"><?= htmlspecialchars($_SESSION['name'] ?? 'Pengguna') ?></strong>.
            </p>

            <form method="POST" action="<?= url('pages/auth/proses_logout.php') ?>">
                <button type="submit"
                    class="w-full py-3 rounded-xl font-bold text-sm transition-all duration-300 mb-3"
                    style="background-color: #D84040; color: #EEEEEE; box-shadow: 0 0 20px rgba(216,64,64,0.4);"
                    onmouseover="this.style.backgroundColor='#8E1616'; this.style.boxShadow='0 0 30px rgba(216,64,64,0.6)'"
                    onmouseout="this.style.backgroundColor='#D84040'; this.style.boxShadow='0 0 20px rgba(216,64,64,0.4)'">
                    Ya, Keluar
                </button>
            </form>

            <a href="javascript:history.back()"
                class="block w-full py-3 rounded-xl font-bold text-sm transition-all duration-300"
                style="background-color: rgba(238,238,238,0.05); color: #EEEEEE99; border: 1px solid rgba(238,238,238,0.1);"
                onmouseover="this.style.backgroundColor='rgba(238,238,238,0.1)'"
                onmouseout="this.style.backgroundColor='rgba(238,238,238,0.05)'">
                Batal
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>