// animasi landing page - scroll reveal + parallax svg
document.addEventListener('DOMContentLoaded', function () {
    // observer buat fade in pas scroll
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    document.querySelectorAll('.animate-on-scroll').forEach(function (el) {
        observer.observe(el);
    });

    // parallax buat svg dekorasi di hero
    var gear1 = document.getElementById('svg-gear-1');
    var gear2 = document.getElementById('svg-gear-2');
    var wrench = document.getElementById('svg-wrench');
    var piston = document.getElementById('svg-piston');
    var speedo = document.getElementById('svg-speedo');
    var needle = document.getElementById('svg-needle');
    var particle1 = document.getElementById('svg-particle-1');
    var particle2 = document.getElementById('svg-particle-2');
    var particle3 = document.getElementById('svg-particle-3');
    var particle4 = document.getElementById('svg-particle-4');
    var heroImg = document.querySelector('#hero img');

    var ticking = false;

    function onScroll() {
        if (!ticking) {
            window.requestAnimationFrame(function () {
                var scrollY = window.pageYOffset;
                var heroHeight = window.innerHeight;
                var progress = Math.min(scrollY / heroHeight, 1);

                // gear muter sesuai scroll
                if (gear1) gear1.style.transform = 'rotate(' + (scrollY * 0.15) + 'deg)';
                if (gear2) gear2.style.transform = 'rotate(' + (scrollY * -0.1) + 'deg)';
                if (wrench) wrench.style.transform = 'rotate(' + (-15 + scrollY * 0.05) + 'deg) translateY(' + (scrollY * 0.1) + 'px)';
                if (piston) piston.style.transform = 'translateY(' + (Math.sin(scrollY * 0.01) * 20) + 'px)';
                if (needle) needle.setAttribute('transform', 'rotate(' + (-60 + progress * 120) + ' 50 50)');
                if (speedo) speedo.style.transform = 'translateY(' + (scrollY * 0.05) + 'px) rotate(' + (scrollY * 0.02) + 'deg)';

                // partikel geser dikit
                if (particle1) particle1.style.transform = 'translateY(' + (scrollY * 0.3) + 'px) translateX(' + (scrollY * 0.05) + 'px)';
                if (particle2) particle2.style.transform = 'translateY(' + (scrollY * 0.2) + 'px) translateX(' + (scrollY * -0.08) + 'px)';
                if (particle3) particle3.style.transform = 'translateY(' + (scrollY * 0.25) + 'px) translateX(' + (scrollY * 0.06) + 'px)';
                if (particle4) particle4.style.transform = 'translateY(' + (scrollY * 0.35) + 'px) translateX(' + (scrollY * -0.04) + 'px)';

                if (heroImg && scrollY < heroHeight) {
                    heroImg.style.transform = 'scale(1.05) translateY(' + (scrollY * 0.15) + 'px)';
                }

                ticking = false;
            });
            ticking = true;
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // lightbox
    var lightbox = document.getElementById('lightbox');
    var lightboxImg = document.getElementById('lightbox-img');
    var lightboxClose = document.getElementById('lightbox-close');

    function openLightbox(src, alt) {
        lightboxImg.src = src;
        lightboxImg.alt = alt;
        lightbox.classList.remove('opacity-0', 'pointer-events-none');
        lightboxImg.classList.remove('scale-95');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.add('opacity-0', 'pointer-events-none');
        lightboxImg.classList.add('scale-95');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-lightbox]').forEach(function (img) {
        img.addEventListener('click', function () {
            openLightbox(img.src, img.alt);
        });
    });

    lightboxClose.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', function (e) {
        if (e.target === lightbox) closeLightbox();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLightbox();
    });
});
