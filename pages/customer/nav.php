<?php
session_start();
require_once '../../config/koneksi.php';

// Ambil data user dari session
$user_id = $_SESSION['user_id'] ?? null;
$nama = 'Guest';
$role = '';
$profile_photo = null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT nama, role, profile_photo FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $nama = $row['nama'];
        $role = $row['role'];
        $profile_photo = $row['profile_photo'];
    }
    $stmt->close();
}
?>

<body>
    <div class="h-screen w-64 bg-[#8E1616] text-white flex flex-col">
            <div class="flex flex-col pt-6 gap-2 px-6"> 
                <span class="font-headline font-bold text-2xl tracking-tighter flex items-center gap-2 mx-5 pb-2">
                    <img src="../../assets/images/logo.png" alt="Revvo Logo" class="h-8 w-auto invert brightness-0 invert">
                    REVVO
                </span>
                <?php if ($profile_photo): ?>
                    <img src="../../uploads/profile/<?= htmlspecialchars($profile_photo) ?>" alt="Profile" class="w-16 h-16 rounded-full object-cover mx-auto">
                <?php else: ?>
                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto text-2xl font-bold">
                        <?= strtoupper(substr($nama, 0,1)) ?>
                    </div>
                <?php endif; ?>


                <P class="text-white text-center"><?= htmlspecialchars($nama) ?></P>
                <p class="text-white text-center"><?= htmlspecialchars($role) ?></p>
                <hr>
            </div>
                <nav class="flex flex-col gap-2 px-6 py-4">
                    <a href="dashboard.php" class="inline-block text-white py-2 px-4 hover:bg-[#1D1616] rounded">Dashboard</a>
                    <a href="motor.php" class="inline-block text-white py-2 px-4 hover:bg-[#1D1616] rounded">Motor Saya</a>
                    <a href="booking.php" class="inline-block text-white py-2 px-4 hover:bg-[#1D1616] rounded">Booking</a>
                    <a href="history.php" class="inline-block text-white py-2 px-4 hover:bg-[#1D1616] rounded ">History</a>
                    <a href="profile.php" class="inline-block text-white py-2 px-4 hover:bg-[#1D1616] rounded">Profil</a>
                    <hr>
                    <a href="#" class="inline-block text-white py-2 px-4 hover:bg-[#1D1616] rounded">Keluar</a>                
                </nav>
        </div>
</body>