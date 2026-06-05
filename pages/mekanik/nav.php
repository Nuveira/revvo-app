<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="h-screen w-64 bg-gradient-to-b from-black via-black via-15% to-[#8E1616] text-white flex flex-col">

    <!-- Logo -->
    <div class="flex flex-col pt-6 gap-2 px-6">

        <span class="font-headline font-bold text-2xl tracking-tighter flex items-center gap-2 mx-5 pb-2">

            <img
                src="../../assets/images/logo.png"
                alt="Revvo Logo"
                class="h-8 w-auto invert brightness-0 invert"
            >

            REVVO

        </span>

        <!-- Profile Summary -->

        <div class="flex items-center gap-3 m-2 pb-1">

            <?php if (!empty($profile_photo)): ?>

                <img
                    src="../../uploads/profile/<?= htmlspecialchars($profile_photo) ?>"
                    alt="Profile"
                    class="w-10 h-10 shrink-0 rounded-full object-cover"
                >

            <?php else: ?>

                <div class="w-10 h-10 shrink-0 rounded-full bg-white/20 flex items-center justify-center text-lg font-bold">

                    <?= strtoupper(substr($nama ?? '', 0, 1)) ?>

                </div>

            <?php endif; ?>

            <div class="min-w-0">

                <p class="text-white font-semibold text-sm leading-tight truncate">

                    <?= htmlspecialchars($nama ?? '') ?>

                </p>

                <p class="text-white/70 text-xs mt-1">
                    Mekanik
                </p>

            </div>

        </div>

        <hr>

    </div>

    <!-- Navigation -->

    <nav class="flex flex-col gap-2 px-6 py-4">

        <!-- Dashboard -->

        <a
            href="dashboard.php"
            class="<?= $current_page == 'dashboard.php' ? 'bg-[#FF0000]' : 'hover:bg-[#FF0000]' ?> flex items-center text-white py-2 px-4 rounded"
        >

            <span class="material-symbols-outlined pr-3">
                home
            </span>

            Dashboard

        </a>

        <!-- Tugas Saya -->

        <a
            href="my_tasks.php"
            class="<?= in_array($current_page, ['my_tasks.php', 'task_detail.php', 'process_task.php']) ? 'bg-[#FF0000]' : 'hover:bg-[#FF0000]' ?> flex items-center text-white py-2 px-4 rounded"
        >

            <span class="material-symbols-outlined pr-3">
                engineering
            </span>

            Tugas Saya

        </a>

        <!-- Riwayat Tugas -->

        <a
            href="history.php"
            class="<?= $current_page == 'history.php' ? 'bg-[#FF0000]' : 'hover:bg-[#FF0000]' ?> flex items-center text-white py-2 px-4 rounded"
        >

            <span class="material-symbols-outlined pr-3">
                history
            </span>

            Riwayat Tugas

        </a>

        <hr>

        <!-- Logout -->

        <a
            href="<?= url('pages/auth/logout.php') ?>"
            class="flex items-center text-white py-2 px-4 hover:bg-white hover:text-black rounded"
        >

            <span class="material-symbols-outlined pr-3">
                logout
            </span>

            Keluar

        </a>

    </nav>

</div>