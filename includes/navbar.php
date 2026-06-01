<?php
$isLoggedIn = !empty($_SESSION['user_id']);
?>
<!-- navbar -->
<div id="navbar-wrapper" class="fixed left-0 right-0 z-50 flex justify-center px-6 transition-all duration-500" style="top: 32px;">
    <nav id="navbar" class="navbar-glass flex justify-between items-center w-full max-w-5xl py-4 px-10 rounded-full shadow-2xl transition-all duration-500">
        <div class="flex items-center gap-12">
            <a href="<?= url('index.php') ?>" class="font-headline font-bold text-2xl tracking-tighter flex items-center gap-2 group">
                <img src="<?= asset('assets/images/logo.png') ?>" alt="REVVO" class="h-8 w-auto invert brightness-0 invert">
                REVVO
            </a>
            <div class="hidden md:flex gap-8 text-sm font-semibold tracking-tight text-white/60">
                <a class="hover:text-white transition-colors duration-300 relative after:absolute after:bottom-[-4px] after:left-0 after:w-0 after:h-[2px] after:bg-primary after:transition-all after:duration-300 hover:after:w-full" href="#fitur">Fitur</a>
                <a class="hover:text-white transition-colors duration-300 relative after:absolute after:bottom-[-4px] after:left-0 after:w-0 after:h-[2px] after:bg-primary after:transition-all after:duration-300 hover:after:w-full" href="#harga">Harga</a>
                <a class="hover:text-white transition-colors duration-300 relative after:absolute after:bottom-[-4px] after:left-0 after:w-0 after:h-[2px] after:bg-primary after:transition-all after:duration-300 hover:after:w-full" href="#tentang">Tentang Kami</a>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <?php if ($isLoggedIn): ?>
                <!-- logout button dengan GIF hover icon -->
                <a href="<?= url('pages/auth/logout.php') ?>"
                    class="group flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold transition-all duration-300 mechanical-press"
                    style="border: 1px solid rgba(238,238,238,0.15); color: #EEEEEE99;"
                    onmouseover="this.style.borderColor='rgba(216,64,64,0.5)'; this.style.color='#D84040'; this.style.backgroundColor='rgba(216,64,64,0.08)'"
                    onmouseout="this.style.borderColor='rgba(238,238,238,0.15)'; this.style.color='#EEEEEE99'; this.style.backgroundColor='transparent'">
                    <i data-lucide="log-out" style="width:16px; height:16px;"></i>
                    Keluar
                </a>
            <?php else: ?>
                <a href="<?= url('pages/auth/login.php') ?>" class="bg-primary hover:bg-red-700 text-white px-8 py-2.5 rounded-full text-sm font-bold shadow-glow-red transition-all duration-300 mechanical-press hover:shadow-[0_0_30px_rgba(211,47,47,0.6)]">Go to App</a>
            <?php endif; ?>
        </div>
    </nav>
</div>
