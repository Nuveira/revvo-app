<?php
// halaman register - tampilan form aja
session_start();
require_once __DIR__ . '/../../config/app.php';

// ambil error/success dari session kalau ada
$error   = $_SESSION['register_error'] ?? '';
$success = $_SESSION['register_success'] ?? '';
unset($_SESSION['register_error'], $_SESSION['register_success']);

$pageTitle = 'Register | REVVO';
require_once __DIR__ . '/../../includes/header.php';
?>

<!-- register form -->
<div
    class="min-h-screen relative flex items-center justify-center px-4 py-12 bg-cover bg-center bg-no-repeat"
    style="background-image: url('<?= asset('assets/images/background_login.png') ?>'); background-color: #1D1616;"
>
    <div
        class="absolute inset-0"
        style="background: rgba(104, 10, 10, 0.68);"
    ></div>

    <div class="relative z-10 w-full max-w-md">
        <!-- logo -->
        <div class="text-center mb-8">
            <a href="<?= url('index.php') ?>" class="inline-flex items-center gap-2 mb-6">
                <img src="<?= asset('assets/images/logo.png') ?>" alt="REVVO" class="h-10 w-auto invert brightness-0 invert">
                <span class="font-headline font-bold text-3xl tracking-tighter" style="color: #EEEEEE;">REVVO</span>
            </a>
            <h1 class="text-2xl font-bold" style="color: #EEEEEE;">Buat Akun Baru</h1>
            <p class="mt-2 text-sm" style="color: #EEEEEE99;">Daftar untuk mulai menggunakan REVVO</p>
        </div>

        <!-- card form -->
        <div
            class="rounded-2xl p-8 border shadow-xl"
            style="background-color: rgba(29,22,22,0.88); border-color: rgba(238,238,238,0.14); backdrop-filter: blur(4px);"
        >
            <?php if ($error): ?>
                <div class="px-4 py-3 rounded-xl mb-6 text-sm" style="background-color: rgba(216,64,64,0.1); border: 1px solid rgba(216,64,64,0.3); color: #D84040;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="px-4 py-3 rounded-xl mb-6 text-sm" style="background-color: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: #22c55e;">
                    <?= htmlspecialchars($success) ?> <a href="<?= url('pages/auth/login.php') ?>" class="underline font-semibold">Login disini</a>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('pages/auth/proses_register.php') ?>">
                <!-- nama lengkap -->
                <div class="mb-5">
                    <label for="name" class="block text-sm font-semibold mb-2" style="color: #EEEEEE;">Nama Lengkap</label>
                    <input type="text" id="name" name="name" required
                        class="w-full rounded-xl px-4 py-3 text-sm transition-colors"
                        style="background-color: #1D1616; border: 1px solid rgba(238,238,238,0.1); color: #EEEEEE; outline: none; box-shadow: none;"
                        onfocus="this.style.borderColor='rgba(238,238,238,0.3)'" onblur="this.style.borderColor='rgba(238,238,238,0.1)'"
                        placeholder="Masukkan nama lengkap">
                </div>

                <!-- email -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-semibold mb-2" style="color: #EEEEEE;">Email</label>
                    <input type="email" id="email" name="email" required
                        class="w-full rounded-xl px-4 py-3 text-sm transition-colors"
                        style="background-color: #1D1616; border: 1px solid rgba(238,238,238,0.1); color: #EEEEEE; outline: none; box-shadow: none;"
                        onfocus="this.style.borderColor='rgba(238,238,238,0.3)'" onblur="this.style.borderColor='rgba(238,238,238,0.1)'"
                        placeholder="nama@email.com">
                </div>

                <!-- no hp -->
                <div class="mb-5">
                    <label for="phone" class="block text-sm font-semibold mb-2" style="color: #EEEEEE;">No. HP</label>
                    <input type="tel" id="phone" name="phone" required
                        class="w-full rounded-xl px-4 py-3 text-sm transition-colors"
                        style="background-color: #1D1616; border: 1px solid rgba(238,238,238,0.1); color: #EEEEEE; outline: none; box-shadow: none;"
                        onfocus="this.style.borderColor='rgba(238,238,238,0.3)'" onblur="this.style.borderColor='rgba(238,238,238,0.1)'"
                        placeholder="081234567890">
                </div>

                <!-- password -->
                <div class="mb-5">
                    <label for="password" class="block text-sm font-semibold mb-2" style="color: #EEEEEE;">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full rounded-xl px-4 py-3 text-sm transition-colors"
                        style="background-color: #1D1616; border: 1px solid rgba(238,238,238,0.1); color: #EEEEEE; outline: none; box-shadow: none;"
                        onfocus="this.style.borderColor='rgba(238,238,238,0.3)'" onblur="this.style.borderColor='rgba(238,238,238,0.1)'"
                        placeholder="Minimal 6 karakter">
                </div>

                <!-- konfirmasi password -->
                <div class="mb-6">
                    <label for="confirm_password" class="block text-sm font-semibold mb-2" style="color: #EEEEEE;">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                        class="w-full rounded-xl px-4 py-3 text-sm transition-colors"
                        style="background-color: #1D1616; border: 1px solid rgba(238,238,238,0.1); color: #EEEEEE; outline: none; box-shadow: none;"
                        onfocus="this.style.borderColor='rgba(238,238,238,0.3)'" onblur="this.style.borderColor='rgba(238,238,238,0.1)'"
                        placeholder="Ulangi password">
                </div>

                <!-- tombol daftar -->
                <button type="submit"
                    class="w-full py-3 rounded-xl font-bold text-sm transition-all duration-300"
                    style="background-color: #D84040; color: #EEEEEE; box-shadow: 0 0 20px rgba(216,64,64,0.4);"
                    onmouseover="this.style.backgroundColor='#8E1616'; this.style.boxShadow='0 0 30px rgba(216,64,64,0.6)'"
                    onmouseout="this.style.backgroundColor='#D84040'; this.style.boxShadow='0 0 20px rgba(216,64,64,0.4)'">
                    Daftar
                </button>
            </form>

            <!-- link login -->
            <p class="text-center text-sm mt-6" style="color: #EEEEEE99;">
                Sudah punya akun?
                <a href="<?= url('pages/auth/login.php') ?>" class="font-semibold transition-colors" style="color: #D84040;"
                    onmouseover="this.style.color='#8E1616'" onmouseout="this.style.color='#D84040'">Login disini</a>
            </p>
        </div>
    </div>
</div>
