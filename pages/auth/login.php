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
<div class="min-h-screen flex items-center justify-center px-4" style="background-color: #1D1616;">
    <div class="w-full max-w-md">
        <!-- logo -->
        <div class="text-center mb-8">
            <a href="<?= url('index.php') ?>" class="inline-flex items-center gap-2 mb-6">
                <img src="<?= asset('assets/images/logo.png') ?>" alt="REVVO" class="h-10 w-auto invert brightness-0 invert">
                <span class="font-headline font-bold text-3xl tracking-tighter" style="color: #EEEEEE;">REVVO</span>
            </a>
            <h1 class="text-2xl font-bold" style="color: #EEEEEE;">Masuk ke Akun Anda</h1>
            <p class="mt-2 text-sm" style="color: #EEEEEE99;">Silakan login untuk melanjutkan</p>
        </div>

        <!-- card form -->
        <div class="rounded-2xl p-8 border" style="background-color: #2a1f1f; border-color: rgba(238,238,238,0.1);">
            <?php if ($error): ?>
                <div class="px-4 py-3 rounded-xl mb-6 text-sm" style="background-color: rgba(216,64,64,0.1); border: 1px solid rgba(216,64,64,0.3); color: #D84040;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('pages/auth/proses_login.php') ?>">
                <!-- email -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-semibold mb-2" style="color: #EEEEEE;">Email</label>
                    <input type="email" id="email" name="email" required
                        class="w-full rounded-xl px-4 py-3 text-sm transition-colors"
                        style="background-color: #1D1616; border: 1px solid rgba(238,238,238,0.1); color: #EEEEEE; outline: none; box-shadow: none;"
                        onfocus="this.style.borderColor='rgba(238,238,238,0.3)'; this.style.outline='none'; this.style.boxShadow='none'" onblur="this.style.borderColor='rgba(238,238,238,0.1)'"
                        placeholder="nama@email.com">
                </div>

                <!-- password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold mb-2" style="color: #EEEEEE;">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full rounded-xl px-4 py-3 text-sm transition-colors"
                        style="background-color: #1D1616; border: 1px solid rgba(238,238,238,0.1); color: #EEEEEE; outline: none; box-shadow: none;"
                        onfocus="this.style.borderColor='rgba(238,238,238,0.3)'; this.style.outline='none'; this.style.boxShadow='none'" onblur="this.style.borderColor='rgba(238,238,238,0.1)'"
                        placeholder="Masukkan password">
                </div>

                <!-- tombol login -->
                <button type="submit"
                    class="w-full py-3 rounded-xl font-bold text-sm transition-all duration-300"
                    style="background-color: #D84040; color: #EEEEEE; box-shadow: 0 0 20px rgba(216,64,64,0.4);"
                    onmouseover="this.style.backgroundColor='#8E1616'; this.style.boxShadow='0 0 30px rgba(216,64,64,0.6)'"
                    onmouseout="this.style.backgroundColor='#D84040'; this.style.boxShadow='0 0 20px rgba(216,64,64,0.4)'">
                    Masuk
                </button>
            </form>

            <!-- link register -->
            <p class="text-center text-sm mt-6" style="color: #EEEEEE99;">
                Belum punya akun?
                <a href="<?= url('pages/auth/register.php') ?>" class="font-semibold transition-colors" style="color: #D84040;"
                    onmouseover="this.style.color='#8E1616'" onmouseout="this.style.color='#D84040'">Daftar disini</a>
            </p>
        </div>
    </div>
</div>