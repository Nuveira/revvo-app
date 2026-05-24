<?php
// halaman login - tampilan form aja
session_start();
require_once __DIR__ . '/../../config/app.php';

// ambil error dari session kalau ada
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);

$pageTitle = 'Login | REVVO';
require_once __DIR__ . '/../../includes/header.php';
?>

<!-- login form -->
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <!-- logo -->
        <div class="text-center mb-8">
            <a href="<?= url('index.php') ?>" class="inline-flex items-center gap-2 mb-6">
                <img src="<?= asset('assets/images/logo.png') ?>" alt="REVVO" class="h-10 w-auto invert brightness-0 invert">
                <span class="font-headline font-bold text-3xl tracking-tighter">REVVO</span>
            </a>
            <h1 class="text-2xl font-bold">Masuk ke Akun Anda</h1>
            <p class="text-stone-400 mt-2 text-sm">Silakan login untuk melanjutkan</p>
        </div>

        <!-- card form -->
        <div class="bg-stone-800 border border-white/10 rounded-2xl p-8">
            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mb-6 text-sm">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('pages/auth/proses_login.php') ?>">
                <!-- email -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-semibold mb-2">Email</label>
                    <input type="email" id="email" name="email" required
                        class="w-full bg-stone-900 border border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                        placeholder="nama@email.com">
                </div>

                <!-- password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full bg-stone-900 border border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                        placeholder="Masukkan password">
                </div>

                <!-- tombol login -->
                <button type="submit"
                    class="w-full bg-primary hover:bg-red-700 text-white py-3 rounded-xl font-bold text-sm shadow-glow-red transition-all duration-300 hover:shadow-[0_0_30px_rgba(211,47,47,0.6)]">
                    Masuk
                </button>
            </form>

            <!-- link register -->
            <p class="text-center text-stone-400 text-sm mt-6">
                Belum punya akun?
                <a href="<?= url('pages/auth/register.php') ?>" class="text-primary hover:text-red-400 font-semibold transition-colors">Daftar disini</a>
            </p>
        </div>
    </div>
</div>
