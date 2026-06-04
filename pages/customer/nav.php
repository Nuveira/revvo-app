<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<body>
    <div class="h-screen w-64 bg-gradient-to-b from-black via-black via-15% to-[#8E1616] text-white flex flex-col">
            <div class="flex flex-col pt-6 gap-2 px-6"> 
                <span class="font-headline font-bold text-2xl tracking-tighter flex items-center gap-2 mx-5 pb-2">
                    <img src="../../assets/images/logo.png" alt="Revvo Logo" class="h-8 w-auto invert brightness-0 invert">
                    REVVO
                </span>
                <div class="flex items-center gap-3 m-2 pb-1">
                    <?php if ($profile_photo): ?>
                        <img src="../../uploads/profile/<?= htmlspecialchars($profile_photo) ?>" alt="Profile" class="w-10 h-10 shirnk-0 rounded-full object-cover">
                    <?php else: ?>
                        <div class="w-10 h-10 shrink-0 rounded-full bg-white/20 flex items-center justify-center text-lg font-bold">
                            <?= strtoupper(substr($nama, 0,1)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="min-w-0">   
                        <P class="text-white text-center font-semibold text-sm leading-tight truncate"><?= htmlspecialchars($nama) ?></P>
                        <p class="text-white/70 text-xs mt-1"><?= htmlspecialchars($role) ?></p>
                    </div>
                </div>
                <hr>
            </div>
                <nav class="flex flex-col gap-2 px-6 py-4">
                    <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'bg-[#FF0000]' : 'hover:bg-[#FF0000]' ?> flex text-white py-2 px-4  rounded">
                        <span class="material-symbols-outlined pr-3">
                            home
                        </span>Dashboard
                    </a>
                    <a href="motor.php" class="<?= in_array($current_page, ['motor.php', 'tambah_motor.php', 'detail_motor.php', 'edit_motor.php']) ? 'bg-[#FF0000]' : 'hover:bg-[#FF0000]' ?> flex text-white py-2 px-4 hover:bg-[#FF0000] rounded">
                        <span class="material-symbols-outlined pr-3">
                            sports_motorsports
                        </span>Motor Saya
                    </a>
                    <a href="booking_new.php" class="<?= $current_page == 'booking.php' ? 'bg-[#FF0000]' : 'hover:bg-[#FF0000]' ?> flex text-white py-2 px-4 hover:bg-[#FF0000] rounded">
                        <span class="material-symbols-outlined pr-3">
                            calendar_today
                        </span>Booking
                    </a>
                    <a href="history.php" class="<?= in_array($current_page, ['history.php', 'detail_history.php']) ? 'bg-[#FF0000]' : 'hover:bg-[#FF0000]' ?> flex text-white py-2 px-4 hover:bg-[#FF0000] rounded ">
                        <span class="material-symbols-outlined pr-3">
                            history
                        </span>History
                    </a>
                    <a href="profile.php" class="<?= $current_page == 'profile.php' ? 'bg-[#FF0000]' : 'hover:bg-[#FF0000]' ?> flex text-white py-2 px-4 hover:bg-[#FF0000] rounded">
                        <span class="material-symbols-outlined pr-3">
                            account_box
                        </span>Profil
                    </a>
                    <hr>
                    <a href="<?= url('pages/auth/logout.php') ?>" class="flex text-white py-2 px-4 hover:bg-white hover:text-black rounded">
                        <span class="material-symbols-outlined">
                            logout
                        </span>Keluar
                    </a>                
                </nav>
        </div>
</body>