<?php
/**
 * Navbar Component
 * Floating liquid-glass navigation bar with scroll effects
 */
?>
<!-- Navigation -->
<div id="navbar-wrapper" class="fixed left-0 right-0 z-50 flex justify-center px-6 transition-all duration-500" style="top: 32px;">
    <nav id="navbar" class="navbar-glass flex justify-between items-center w-full max-w-5xl py-4 px-10 rounded-full shadow-2xl transition-all duration-500">
        <div class="flex items-center gap-12">
            <a href="index.php" class="font-headline font-bold text-2xl tracking-tighter flex items-center gap-2 group">
                <img src="src/images/logo.png" alt="REVVO" class="h-8 w-auto invert brightness-0 invert">
                REVVO
            </a>
            <div class="hidden md:flex gap-8 text-sm font-semibold tracking-tight text-white/60">
                <a class="hover:text-white transition-colors duration-300 relative after:absolute after:bottom-[-4px] after:left-0 after:w-0 after:h-[2px] after:bg-primary after:transition-all after:duration-300 hover:after:w-full" href="#fitur">Fitur</a>
                <a class="hover:text-white transition-colors duration-300 relative after:absolute after:bottom-[-4px] after:left-0 after:w-0 after:h-[2px] after:bg-primary after:transition-all after:duration-300 hover:after:w-full" href="#harga">Harga</a>
                <a class="hover:text-white transition-colors duration-300 relative after:absolute after:bottom-[-4px] after:left-0 after:w-0 after:h-[2px] after:bg-primary after:transition-all after:duration-300 hover:after:w-full" href="#tentang">Tentang Kami</a>
            </div>
        </div>
        <div class="flex items-center gap-8">
            <a href="pages/auth/login.php" class="bg-primary hover:bg-red-700 text-white px-8 py-2.5 rounded-full text-sm font-bold shadow-glow-red transition-all duration-300 mechanical-press hover:shadow-[0_0_30px_rgba(211,47,47,0.6)]">Go to App</a>
        </div>
    </nav>
</div>

<!-- Navbar scroll behavior + Smooth scroll -->
<script>
(function() {
    // Navbar glass effect on scroll
    function initNavbar() {
        var navbar = document.getElementById('navbar');
        var wrapper = document.getElementById('navbar-wrapper');
        
        if (!navbar || !wrapper) return;

        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                navbar.classList.add('navbar-glass-scrolled');
                wrapper.style.top = '16px';
            } else {
                navbar.classList.remove('navbar-glass-scrolled');
                wrapper.style.top = '32px';
            }
        }, { passive: true });
    }

    // Custom smooth scroll function using requestAnimationFrame
    function smoothScrollTo(targetY, duration) {
        var startY = window.pageYOffset;
        var difference = targetY - startY;
        var startTime = null;

        function step(currentTime) {
            if (!startTime) startTime = currentTime;
            var progress = currentTime - startTime;
            var percent = Math.min(progress / duration, 1);
            
            // Easing function: easeInOutCubic
            var eased = percent < 0.5
                ? 4 * percent * percent * percent
                : 1 - Math.pow(-2 * percent + 2, 3) / 2;

            window.scrollTo(0, startY + difference * eased);

            if (progress < duration) {
                window.requestAnimationFrame(step);
            }
        }

        window.requestAnimationFrame(step);
    }

    // Smooth scroll click handler using event delegation
    document.addEventListener('click', function(e) {
        var anchor = e.target.closest('a[href^="#"]');
        if (!anchor) return;

        var targetId = anchor.getAttribute('href');
        if (!targetId || targetId === '#') return;

        var target = document.querySelector(targetId);
        if (!target) return;

        e.preventDefault();

        var navbarHeight = 100;
        var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
        
        smoothScrollTo(targetPosition, 800);
    });

    // Init navbar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNavbar);
    } else {
        initNavbar();
    }
})();
</script>
