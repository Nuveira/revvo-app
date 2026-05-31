<?php
// halaman register user baru
session_start();
require_once __DIR__ . '/../../config/koneksi.php';

$error = '';
$success = '';

// proses register kalau form di submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // validasi input
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm)) {
        $error = 'Semua field harus diisi';
    } elseif ($password !== $confirm) {
        $error = 'Password tidak cocok';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        // cek email udah terdaftar atau belum
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Email sudah terdaftar';
        }
        $stmt->close();

        // kalau masih gak ada error, insert data
        if (empty($error)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // insert ke tabel users
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

            $success = 'Registrasi berhasil! Silakan login';
        }
    }
}

$pageTitle = 'Register | REVVO';
require_once __DIR__ . '/../../includes/header.php';
?>

<!-- register form -->
<div class="min-h-screen flex items-center justify-center px-4 py-12" style="background-color: #1D1616;">
    <div class="w-full max-w-md">
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
        <div class="rounded-2xl p-8 border" style="background-color: #2a1f1f; border-color: rgba(238,238,238,0.1);">
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

            <form method="POST" action="">
                <!-- nama lengkap -->
                <div class="mb-5">
                    <label for="name" class="block text-sm font-semibold mb-2" style="color: #EEEEEE;">Nama Lengkap</label>
                    <input type="text" id="name" name="name" required
                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                        class="w-full rounded-xl px-4 py-3 text-sm transition-colors"
                        style="background-color: #1D1616; border: 1px solid rgba(238,238,238,0.1); color: #EEEEEE; outline: none; box-shadow: none;"
                        onfocus="this.style.borderColor='rgba(238,238,238,0.3)'" onblur="this.style.borderColor='rgba(238,238,238,0.1)'"
                        placeholder="Masukkan nama lengkap">
                </div>

                <!-- email -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-semibold mb-2" style="color: #EEEEEE;">Email</label>
                    <input type="email" id="email" name="email" required
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        class="w-full rounded-xl px-4 py-3 text-sm transition-colors"
                        style="background-color: #1D1616; border: 1px solid rgba(238,238,238,0.1); color: #EEEEEE; outline: none; box-shadow: none;"
                        onfocus="this.style.borderColor='rgba(238,238,238,0.3)'" onblur="this.style.borderColor='rgba(238,238,238,0.1)'"
                        placeholder="nama@email.com">
                </div>

                <!-- no hp -->
                <div class="mb-5">
                    <label for="phone" class="block text-sm font-semibold mb-2" style="color: #EEEEEE;">No. HP</label>
                    <input type="tel" id="phone" name="phone" required
                        value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
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
