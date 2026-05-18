<?php
$pageTitle = 'REVVO | Workshop Systems for Precision';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Hero Section -->
<section id="hero" class="relative min-h-screen flex items-center pt-32 pb-20 grid-bg">

    <!-- SVG Animated Workshop Elements (scroll-driven) -->
    <div class="absolute inset-0 z-[1] pointer-events-none overflow-hidden">
        <!-- Large Gear - Top Right -->
        <svg id="svg-gear-1" class="absolute -top-20 -right-20 w-80 h-80 opacity-[0.06]" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 30 L108 30 L110 20 L118 18 L120 28 L128 26 L132 16 L140 18 L138 28 L146 30 L150 22 L156 26 L152 36 L158 40 L164 34 L168 40 L162 46 L166 52 L174 50 L176 58 L168 60 L170 68 L180 68 L180 76 L170 76 L170 84 L178 86 L176 94 L168 92 L166 100 L174 104 L170 110 L162 106 L158 112 L164 118 L158 122 L152 116 L146 120 L150 128 L142 130 L138 122 L130 124 L132 134 L124 134 L120 126 L112 126 L110 136 L102 134 L100 126 L92 126 L90 134 L82 134 L80 124 L72 122 L68 132 L62 128 L66 120 L58 116 L52 122 L48 118 L54 112 L48 106 L40 110 L38 104 L46 100 L44 92 L36 94 L34 86 L42 84 L40 76 L30 76 L30 68 L40 68 L42 60 L34 58 L36 50 L44 52 L48 46 L42 40 L48 34 L54 40 L60 36 L56 26 L64 24 L68 34 L76 30 L74 20 L82 18 L84 28 L92 30 L92 20 L100 20 Z" fill="white"/>
            <circle cx="100" cy="76" r="30" fill="none" stroke="white" stroke-width="4"/>
        </svg>

        <!-- Medium Gear - Bottom Left -->
        <svg id="svg-gear-2" class="absolute -bottom-10 -left-10 w-60 h-60 opacity-[0.05]" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 30 L108 30 L110 20 L118 18 L120 28 L128 26 L132 16 L140 18 L138 28 L146 30 L150 22 L156 26 L152 36 L158 40 L164 34 L168 40 L162 46 L166 52 L174 50 L176 58 L168 60 L170 68 L180 68 L180 76 L170 76 L170 84 L178 86 L176 94 L168 92 L166 100 L174 104 L170 110 L162 106 L158 112 L164 118 L158 122 L152 116 L146 120 L150 128 L142 130 L138 122 L130 124 L132 134 L124 134 L120 126 L112 126 L110 136 L102 134 L100 126 L92 126 L90 134 L82 134 L80 124 L72 122 L68 132 L62 128 L66 120 L58 116 L52 122 L48 118 L54 112 L48 106 L40 110 L38 104 L46 100 L44 92 L36 94 L34 86 L42 84 L40 76 L30 76 L30 68 L40 68 L42 60 L34 58 L36 50 L44 52 L48 46 L42 40 L48 34 L54 40 L60 36 L56 26 L64 24 L68 34 L76 30 L74 20 L82 18 L84 28 L92 30 L92 20 L100 20 Z" fill="white"/>
            <circle cx="100" cy="76" r="24" fill="none" stroke="white" stroke-width="4"/>
        </svg>

        <!-- Wrench - Center Right -->
        <svg id="svg-wrench" class="absolute top-1/3 right-[10%] w-40 h-40 opacity-[0.04]" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M75 10 C68 10 62 14 60 20 L25 55 C20 53 14 54 10 58 C4 64 4 74 10 80 C16 86 26 86 32 80 C36 76 37 70 35 65 L70 30 C76 32 83 30 87 26 C90 23 90 18 88 14 L80 22 L74 16 L82 8 C79 7 77 10 75 10 Z" fill="white"/>
        </svg>

        <!-- Piston - Left Center -->
        <svg id="svg-piston" class="absolute top-[45%] left-[5%] w-32 h-32 opacity-[0.04]" viewBox="0 0 80 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="20" y="60" width="40" height="50" rx="4" fill="white"/>
            <rect x="25" y="55" width="30" height="10" rx="2" fill="white"/>
            <rect x="35" y="20" width="10" height="40" rx="2" fill="white"/>
            <circle cx="40" cy="15" r="10" fill="none" stroke="white" stroke-width="3"/>
        </svg>

        <!-- Speedometer Arc - Top Left -->
        <svg id="svg-speedo" class="absolute top-[15%] left-[15%] w-48 h-48 opacity-[0.04]" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 80 A40 40 0 1 1 80 80" fill="none" stroke="white" stroke-width="3" stroke-linecap="round"/>
            <path d="M25 75 A35 35 0 1 1 75 75" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="4 6"/>
            <line id="svg-needle" x1="50" y1="50" x2="50" y2="22" stroke="#D32F2F" stroke-width="2.5" stroke-linecap="round"/>
            <circle cx="50" cy="50" r="4" fill="white"/>
        </svg>

        <!-- Small floating particles -->
        <div id="svg-particle-1" class="absolute top-[20%] right-[30%] w-2 h-2 bg-primary/20 rounded-full"></div>
        <div id="svg-particle-2" class="absolute top-[60%] right-[20%] w-3 h-3 bg-white/10 rounded-full"></div>
        <div id="svg-particle-3" class="absolute top-[70%] left-[25%] w-2 h-2 bg-primary/15 rounded-full"></div>
        <div id="svg-particle-4" class="absolute top-[30%] left-[40%] w-1.5 h-1.5 bg-white/10 rounded-full"></div>
    </div>

    <div class="relative z-10 max-w-container-max mx-auto px-margin-desktop w-full">
        <div class="max-w-4xl">
            <!-- Headline -->
            <div class="relative" style="animation: fadeInUp 0.8s ease-out 0.4s both;">
                <h1 class="font-headline text-[clamp(48px,10vw,100px)] leading-[0.85] font-extrabold mb-12 mt-16 tracking-tighter">
                    Repair &amp; Vehicle <br />
                    <span class="text-primary italic">
                        Booking
                    </span> <br />Operations
                </h1>
            </div>

            <!-- Subheadline + CTA -->
            <div class="space-y-8" style="animation: fadeInUp 0.8s ease-out 0.6s both;">
                <p class="text-stone-300 text-xl leading-relaxed max-w-md">
                    Kelola booking, sparepart, dan laporan bengkelmu tanpa ribet buku tulis. Alat tempur presisi untuk bengkel masa depan.
                </p>
                <div class="flex flex-row gap-4">
                    <a href="pages/auth/login.php" class="bg-white text-stone-900 px-10 py-5 rounded-xl font-bold text-sm uppercase tracking-widest shadow-xl hover:bg-stone-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 mechanical-press inline-block">Daftar Demo</a>
                    <a href="#fitur" class="bg-stone-800/80 backdrop-blur-md border border-white/10 text-white px-10 py-5 rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-stone-800 hover:border-white/20 hover:-translate-y-1 transition-all duration-300 mechanical-press inline-block">Dokumentasi</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10" style="animation: fadeInUp 1s ease-out 1s both;">
        <div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center pt-2">
            <div class="w-1 h-3 bg-white/60 rounded-full animate-bounce"></div>
        </div>
    </div>
</section>

<!-- Stats/Features Bar -->
<section class="relative z-20 -mt-16 pb-20">
    <div class="max-w-container-max mx-auto px-margin-desktop">
        <div class="animate-on-scroll scale-in bg-gradient-to-br from-stone-800 to-stone-900 border border-white/5 p-12 rounded-3xl shadow-2xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
                <!-- Feature 1 -->
                <div class="animate-on-scroll delay-100 relative group cursor-default">
                    <div class="absolute -left-4 top-0 w-1 h-12 bg-primary rounded-full group-hover:h-16 transition-all duration-500"></div>
                    <span class="text-primary font-technical text-xs font-bold block mb-4 tracking-tighter">01 / EFFICIENCY</span>
                    <h3 class="font-headline text-xl font-bold mb-3 group-hover:text-primary transition-colors duration-300">Otomasi Alur Kerja</h3>
                    <p class="text-sm text-stone-400 leading-relaxed">Hasil kerja mekanik yang lebih presisi, terukur, dan terdokumentasi sempurna.</p>
                </div>
                <!-- Feature 2 -->
                <div class="animate-on-scroll delay-200 relative group cursor-default">
                    <div class="absolute -left-4 top-0 w-1 h-12 bg-primary rounded-full group-hover:h-16 transition-all duration-500"></div>
                    <span class="text-primary font-technical text-xs font-bold block mb-4 tracking-tighter">02 / TRANSPARENCY</span>
                    <h3 class="font-headline text-xl font-bold mb-3 group-hover:text-primary transition-colors duration-300">Real-time WhatsApp</h3>
                    <p class="text-sm text-stone-400 leading-relaxed">Pelanggan mendapatkan update progres servis langsung ke genggaman mereka.</p>
                </div>
                <!-- Feature 3 -->
                <div class="animate-on-scroll delay-300 relative group cursor-default">
                    <div class="absolute -left-4 top-0 w-1 h-12 bg-primary rounded-full group-hover:h-16 transition-all duration-500"></div>
                    <span class="text-primary font-technical text-xs font-bold block mb-4 tracking-tighter">03 / PROFITABILITY</span>
                    <h3 class="font-headline text-xl font-bold mb-3 group-hover:text-primary transition-colors duration-300">Analitik Akurat</h3>
                    <p class="text-sm text-stone-400 leading-relaxed">Lacak margin keuntungan per jasa dan per sparepart tanpa kesalahan hitung.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Process Section -->
<section id="tentang" class="py-section-gap-desktop bg-stone-50 text-stone-900 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-white to-transparent pointer-events-none"></div>
    <div class="max-w-container-max mx-auto px-margin-desktop relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
            <!-- Left Column -->
            <div class="relative">
                <div class="absolute -top-20 -left-20 w-64 h-64 bg-primary/5 organic-shape -z-10 animate-float"></div>
                <span class="animate-on-scroll from-left text-primary font-technical text-sm font-bold tracking-widest uppercase mb-6 block">The Workflow</span>
                <h2 class="animate-on-scroll from-left delay-100 font-headline text-6xl font-extrabold leading-[1.1] mb-10 tracking-tight">
                    Migrasi digital <br />hanya dalam <br /><span class="text-primary">3 langkah mudah.</span>
                </h2>
                <p class="animate-on-scroll from-left delay-200 text-stone-500 text-xl leading-relaxed max-w-md">Tidak perlu training berhari-hari. Sistem kami dirancang untuk intuitif bagi siapapun di tim bengkel Anda.</p>
                <div class="animate-on-scroll from-left delay-300 mt-12 p-8 bg-white shadow-xl rounded-3xl border border-stone-100 max-w-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-4 text-primary font-bold">
                        <i data-lucide="badge-check" class="w-6 h-6"></i>
                        <span class="text-sm uppercase tracking-widest">Siap Pakai Dalam 10 Menit</span>
                    </div>
                </div>
            </div>

            <!-- Right Column - Steps -->
            <div class="space-y-8 relative">
                <!-- Step 1 -->
                <div class="animate-on-scroll from-right delay-100 group flex gap-8 items-start p-8 rounded-3xl transition-all duration-300 hover:bg-white hover:shadow-2xl hover:shadow-stone-200 hover:-translate-y-1">
                    <div class="relative">
                        <span class="font-headline text-7xl font-extrabold text-stone-200 group-hover:text-primary/20 transition-colors duration-500 leading-none">01</span>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-12 h-12 bg-primary text-white flex items-center justify-center rounded-2xl shadow-glow-red rotate-3 group-hover:rotate-12 transition-transform duration-500">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-headline text-2xl font-bold mb-3">Daftar Akun</h4>
                        <p class="text-stone-600 leading-relaxed">Input profil bengkel, data mekanik, dan daftar layanan jasa Anda dalam hitungan menit.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="animate-on-scroll from-right delay-200 group flex gap-8 items-start p-8 rounded-3xl transition-all duration-300 hover:bg-white hover:shadow-2xl hover:shadow-stone-200 hover:-translate-y-1">
                    <div class="relative">
                        <span class="font-headline text-7xl font-extrabold text-stone-200 group-hover:text-primary/20 transition-colors duration-500 leading-none">02</span>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-12 h-12 bg-primary text-white flex items-center justify-center rounded-2xl shadow-glow-red -rotate-3 group-hover:rotate-6 transition-transform duration-500">
                            <i data-lucide="calendar" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-headline text-2xl font-bold mb-3">Customer Booking</h4>
                        <p class="text-stone-600 leading-relaxed">Bagikan link booking online ke sosial media. Biarkan pelanggan memilih jadwal sendiri secara otomatis.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="animate-on-scroll from-right delay-300 group flex gap-8 items-start p-8 rounded-3xl transition-all duration-300 hover:bg-white hover:shadow-2xl hover:shadow-stone-200 hover:-translate-y-1">
                    <div class="relative">
                        <span class="font-headline text-7xl font-extrabold text-stone-200 group-hover:text-primary/20 transition-colors duration-500 leading-none">03</span>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-12 h-12 bg-primary text-white flex items-center justify-center rounded-2xl shadow-glow-red rotate-6 group-hover:rotate-[-6deg] transition-transform duration-500">
                            <i data-lucide="trending-up" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-headline text-2xl font-bold mb-3">Kelola &amp; Cuan</h4>
                        <p class="text-stone-600 leading-relaxed">Pantau semua aktivitas dari dashboard pusat. Cetak invoice profesional otomatis dalam satu klik.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Red CTA Banner -->
<section id="harga" class="py-12 bg-stone-50">
    <div class="max-w-container-max mx-auto px-margin-desktop">
        <div class="animate-on-scroll scale-in mesh-gradient-red p-16 md:p-24 rounded-[3rem] text-center shadow-2xl relative overflow-hidden group">
            <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
            <div class="relative z-10">
                <h2 class="font-headline text-white text-5xl md:text-7xl font-extrabold mb-12 max-w-4xl mx-auto leading-tight tracking-tight">
                    SIAP MENGUBAH BENGKEL ANDA MENJADI PROFESIONAL?
                </h2>
                <div class="flex flex-col sm:flex-row justify-center gap-6 items-center">
                    <a href="pages/auth/login.php" class="bg-stone-900 text-white px-12 py-6 rounded-2xl font-bold uppercase tracking-widest shadow-2xl hover:scale-105 hover:shadow-[0_20px_60px_rgba(0,0,0,0.4)] transition-all duration-300 mechanical-press inline-block pulse-glow">Coba Demo Sekarang</a>
                    <a href="#kontak" class="bg-white/20 backdrop-blur-md border border-white/30 text-white px-12 py-6 rounded-2xl font-bold uppercase tracking-widest hover:bg-white hover:text-primary hover:scale-105 transition-all duration-300 mechanical-press inline-block">Hubungi Sales</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Feature Grid -->
<section id="fitur" class="py-section-gap-desktop bg-stone-50 text-stone-900">
    <div class="max-w-container-max mx-auto px-margin-desktop">
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row justify-between items-end mb-20 gap-8">
            <div class="max-w-2xl">
                <span class="animate-on-scroll text-primary font-technical text-sm font-bold tracking-widest uppercase mb-4 block">Engineered for Precision</span>
                <h2 class="animate-on-scroll delay-100 font-headline text-6xl font-extrabold tracking-tight">Fitur Utama Revvo</h2>
            </div>
            <p class="animate-on-scroll delay-200 text-stone-500 max-w-sm mb-2">Sistem yang dirancang oleh pemilik bengkel untuk pemilik bengkel.</p>
        </div>

        <!-- Feature Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1: Booking Management -->
            <div class="animate-on-scroll delay-100 glass-card bg-stone-900/5 p-2 rounded-[2.5rem] flex flex-col group hover:shadow-3xl hover:-translate-y-2 transition-all duration-500 border border-stone-200">
                <div class="aspect-[4/3] bg-stone-900 rounded-[2rem] mb-8 overflow-hidden relative shadow-inner">
                    <img alt="Booking Dashboard" class="w-full h-full object-cover opacity-90 group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida/ADBb0ujHlInSL1d_UIXgC_s-At3DdGWD40BOhlyXuuxzDm06ln87CAn4pT8EKI1Zs4jRL9kT8Qx7rPF_z3whkfG8RXYSG8oCYGxKnDtE0dwkEbg4D1V7G7p4FXVW9SHsx53EvqNDaunDvWR-llr8piHSS7A6LNQEckuU9x3ySqag_F7R5fM5uPyISUQ4346GXB2wqmPNSH1fSo5eIdsrWsLxnd2ijqzTQ-vw58uJM7BIlnJNcj0f91xN57yuIEc" />
                    <div class="absolute inset-0 bg-gradient-to-t from-stone-900/60 to-transparent"></div>
                </div>
                <div class="px-8 pb-10 flex-grow flex flex-col">
                    <h3 class="font-headline text-2xl font-bold mb-3">Booking Management</h3>
                    <p class="text-stone-500 text-sm mb-8 leading-relaxed">Visualisasi antrian interaktif yang memudahkan alokasi mekanik secara real-time.</p>
                    <a class="mt-auto text-primary font-bold text-xs flex items-center gap-2 tracking-widest uppercase group-hover:gap-4 transition-all duration-300" href="#">
                        PELAJARI FITUR <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Card 2: Inventory Control -->
            <div class="animate-on-scroll delay-200 glass-card bg-stone-900/5 p-2 rounded-[2.5rem] flex flex-col group hover:shadow-3xl hover:-translate-y-2 transition-all duration-500 border border-stone-200">
                <div class="aspect-[4/3] bg-stone-900 rounded-[2rem] mb-8 overflow-hidden relative shadow-inner">
                    <img alt="Inventory Dashboard" class="w-full h-full object-cover opacity-90 group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida/ADBb0ujHlInSL1d_UIXgC_s-At3DdGWD40BOhlyXuuxzDm06ln87CAn4pT8EKI1Zs4jRL9kT8Qx7rPF_z3whkfG8RXYSG8oCYGxKnDtE0dwkEbg4D1V7G7p4FXVW9SHsx53EvqNDaunDvWR-llr8piHSS7A6LNQEckuU9x3ySqag_F7R5fM5uPyISUQ4346GXB2wqmPNSH1fSo5eIdsrWsLxnd2ijqzTQ-vw58uJM7BIlnJNcj0f91xN57yuIEc" />
                    <div class="absolute inset-0 bg-gradient-to-t from-stone-900/60 to-transparent"></div>
                </div>
                <div class="px-8 pb-10 flex-grow flex flex-col">
                    <h3 class="font-headline text-2xl font-bold mb-3">Inventory Control</h3>
                    <p class="text-stone-500 text-sm mb-8 leading-relaxed">Sistem stok cerdas yang memberi peringatan dini saat sparepart mulai menipis.</p>
                    <a class="mt-auto text-primary font-bold text-xs flex items-center gap-2 tracking-widest uppercase group-hover:gap-4 transition-all duration-300" href="#">
                        PELAJARI FITUR <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Card 3: Precision Analytics -->
            <div class="animate-on-scroll delay-300 glass-card bg-stone-900/5 p-2 rounded-[2.5rem] flex flex-col group hover:shadow-3xl hover:-translate-y-2 transition-all duration-500 border border-stone-200">
                <div class="aspect-[4/3] bg-stone-900 rounded-[2rem] mb-8 overflow-hidden relative shadow-inner">
                    <img alt="Analytics Dashboard" class="w-full h-full object-cover opacity-90 group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida/ADBb0ujHlInSL1d_UIXgC_s-At3DdGWD40BOhlyXuuxzDm06ln87CAn4pT8EKI1Zs4jRL9kT8Qx7rPF_z3whkfG8RXYSG8oCYGxKnDtE0dwkEbg4D1V7G7p4FXVW9SHsx53EvqNDaunDvWR-llr8piHSS7A6LNQEckuU9x3ySqag_F7R5fM5uPyISUQ4346GXB2wqmPNSH1fSo5eIdsrWsLxnd2ijqzTQ-vw58uJM7BIlnJNcj0f91xN57yuIEc" />
                    <div class="absolute inset-0 bg-gradient-to-t from-stone-900/60 to-transparent"></div>
                </div>
                <div class="px-8 pb-10 flex-grow flex flex-col">
                    <h3 class="font-headline text-2xl font-bold mb-3">Precision Analytics</h3>
                    <p class="text-stone-500 text-sm mb-8 leading-relaxed">Laporan keuangan komprehensif dan performa bengkel dalam satu dashboard ringkas.</p>
                    <a class="mt-auto text-primary font-bold text-xs flex items-center gap-2 tracking-widest uppercase group-hover:gap-4 transition-all duration-300" href="#">
                        PELAJARI FITUR <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact/Support Section -->
<section id="kontak" class="py-24 bg-stone-900 relative overflow-hidden grid-bg border-t border-white/5">
    <div class="max-w-container-max mx-auto px-margin-desktop relative z-10">
        <div class="animate-on-scroll scale-in bg-gradient-to-br from-stone-800 to-stone-900 border border-white/10 p-12 md:p-20 rounded-[3rem] shadow-3xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <!-- Left: Contact Info -->
                <div>
                    <h2 class="animate-on-scroll from-left font-headline text-5xl font-bold text-white mb-6 tracking-tight">Butuh Bantuan Setup?</h2>
                    <p class="animate-on-scroll from-left delay-100 text-stone-400 text-lg leading-relaxed mb-10 max-w-md">Tim spesialis kami siap membantu migrasi data bengkel lama Anda secara gratis. Tidak ada data yang tertinggal.</p>
                    <div class="flex flex-wrap gap-10">
                        <a class="animate-on-scroll delay-200 flex items-center gap-4 text-white hover:text-primary transition-all duration-300 group" href="#">
                            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center group-hover:bg-primary group-hover:scale-110 transition-all duration-300">
                                <i data-lucide="message-circle" class="w-5 h-5 text-primary group-hover:text-white"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-stone-500 font-bold uppercase tracking-widest">Chat on</span>
                                <span class="font-bold">WhatsApp</span>
                            </div>
                        </a>
                        <a class="animate-on-scroll delay-300 flex items-center gap-4 text-white hover:text-primary transition-all duration-300 group" href="#">
                            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center group-hover:bg-primary group-hover:scale-110 transition-all duration-300">
                                <i data-lucide="instagram" class="w-5 h-5 text-primary group-hover:text-white"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-stone-500 font-bold uppercase tracking-widest">Follow on</span>
                                <span class="font-bold">Instagram</span>
                            </div>
                        </a>
                        <a class="animate-on-scroll delay-400 flex items-center gap-4 text-white hover:text-primary transition-all duration-300 group" href="#">
                            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center group-hover:bg-primary group-hover:scale-110 transition-all duration-300">
                                <i data-lucide="mail" class="w-5 h-5 text-primary group-hover:text-white"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-stone-500 font-bold uppercase tracking-widest">Send an</span>
                                <span class="font-bold">Email Us</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Right: Decorative Card -->
                <div class="hidden lg:block relative">
                    <div class="absolute inset-0 bg-primary/20 blur-[100px] organic-shape animate-float"></div>
                    <div class="animate-on-scroll from-right relative bg-white/5 backdrop-blur-2xl border border-white/10 p-8 rounded-3xl rotate-3 hover:rotate-0 transition-transform duration-500">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-full bg-stone-700"></div>
                            <div class="space-y-2">
                                <div class="w-32 h-3 bg-stone-700 rounded-full"></div>
                                <div class="w-20 h-2 bg-stone-800 rounded-full"></div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="w-full h-20 bg-stone-800/50 rounded-2xl"></div>
                            <div class="w-2/3 h-4 bg-primary/40 rounded-full shimmer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- Scroll Animation Observer + SVG Scroll Animations -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Intersection Observer for fade-in animations
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    document.querySelectorAll('.animate-on-scroll').forEach(function(el) {
        observer.observe(el);
    });

    // === Background SVG Scroll Animations (parallax) ===
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
            window.requestAnimationFrame(function() {
                var scrollY = window.pageYOffset;
                var heroHeight = window.innerHeight;
                var progress = Math.min(scrollY / heroHeight, 1);

                if (gear1) gear1.style.transform = 'rotate(' + (scrollY * 0.15) + 'deg)';
                if (gear2) gear2.style.transform = 'rotate(' + (scrollY * -0.1) + 'deg)';
                if (wrench) wrench.style.transform = 'rotate(' + (-15 + scrollY * 0.05) + 'deg) translateY(' + (scrollY * 0.1) + 'px)';
                if (piston) piston.style.transform = 'translateY(' + (Math.sin(scrollY * 0.01) * 20) + 'px)';
                if (needle) needle.setAttribute('transform', 'rotate(' + (-60 + progress * 120) + ' 50 50)');
                if (speedo) speedo.style.transform = 'translateY(' + (scrollY * 0.05) + 'px) rotate(' + (scrollY * 0.02) + 'deg)';

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
});
</script>
