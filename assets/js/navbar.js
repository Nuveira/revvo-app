// navbar scroll effect + smooth scroll
(function () {
    function initNavbar() {
        var navbar = document.getElementById('navbar');
        var wrapper = document.getElementById('navbar-wrapper');

        if (!navbar || !wrapper) return;

        window.addEventListener('scroll', function () {
            if (window.scrollY > 100) {
                navbar.classList.add('navbar-glass-scrolled');
                wrapper.style.top = '16px';
            } else {
                navbar.classList.remove('navbar-glass-scrolled');
                wrapper.style.top = '32px';
            }
        }, { passive: true });
    }

    // custom smooth scroll pake rAF biar gak patah2
    function smoothScrollTo(targetY, duration) {
        var startY = window.pageYOffset;
        var difference = targetY - startY;
        var startTime = null;

        function step(currentTime) {
            if (!startTime) startTime = currentTime;
            var progress = currentTime - startTime;
            var percent = Math.min(progress / duration, 1);

            // easing cubic
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

    // handle klik link anchor
    document.addEventListener('click', function (e) {
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

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNavbar);
    } else {
        initNavbar();
    }
})();
