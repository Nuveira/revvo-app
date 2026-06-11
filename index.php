<?php
$pageTitle = 'REVVO | Sistem Informasi Bengkel Motor';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- hero -->
<section id="hero" class="relative min-h-screen flex items-center pt-32 pb-20 grid-bg">

    <!-- svg dekorasi background -->
    <div class="absolute inset-0 z-[1] pointer-events-none overflow-hidden">
        <!-- gear kanan atas -->
        <svg id="svg-gear-1" class="absolute -top-20 -right-20 w-80 h-80 opacity-[0.06]" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 30 L108 30 L110 20 L118 18 L120 28 L128 26 L132 16 L140 18 L138 28 L146 30 L150 22 L156 26 L152 36 L158 40 L164 34 L168 40 L162 46 L166 52 L174 50 L176 58 L168 60 L170 68 L180 68 L180 76 L170 76 L170 84 L178 86 L176 94 L168 92 L166 100 L174 104 L170 110 L162 106 L158 112 L164 118 L158 122 L152 116 L146 120 L150 128 L142 130 L138 122 L130 124 L132 134 L124 134 L120 126 L112 126 L110 136 L102 134 L100 126 L92 126 L90 134 L82 134 L80 124 L72 122 L68 132 L62 128 L66 120 L58 116 L52 122 L48 118 L54 112 L48 106 L40 110 L38 104 L46 100 L44 92 L36 94 L34 86 L42 84 L40 76 L30 76 L30 68 L40 68 L42 60 L34 58 L36 50 L44 52 L48 46 L42 40 L48 34 L54 40 L60 36 L56 26 L64 24 L68 34 L76 30 L74 20 L82 18 L84 28 L92 30 L92 20 L100 20 Z" fill="white"/>
            <circle cx="100" cy="76" r="30" fill="none" stroke="white" stroke-width="4"/>
        </svg>

        <!-- gear kiri bawah -->
        <svg id="svg-gear-2" class="absolute -bottom-10 -left-10 w-60 h-60 opacity-[0.05]" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 30 L108 30 L110 20 L118 18 L120 28 L128 26 L132 16 L140 18 L138 28 L146 30 L150 22 L156 26 L152 36 L158 40 L164 34 L168 40 L162 46 L166 52 L174 50 L176 58 L168 60 L170 68 L180 68 L180 76 L170 76 L170 84 L178 86 L176 94 L168 92 L166 100 L174 104 L170 110 L162 106 L158 112 L164 118 L158 122 L152 116 L146 120 L150 128 L142 130 L138 122 L130 124 L132 134 L124 134 L120 126 L112 126 L110 136 L102 134 L100 126 L92 126 L90 134 L82 134 L80 124 L72 122 L68 132 L62 128 L66 120 L58 116 L52 122 L48 118 L54 112 L48 106 L40 110 L38 104 L46 100 L44 92 L36 94 L34 86 L42 84 L40 76 L30 76 L30 68 L40 68 L42 60 L34 58 L36 50 L44 52 L48 46 L42 40 L48 34 L54 40 L60 36 L56 26 L64 24 L68 34 L76 30 L74 20 L82 18 L84 28 L92 30 L92 20 L100 20 Z" fill="white"/>
            <circle cx="100" cy="76" r="24" fill="none" stroke="white" stroke-width="4"/>
        </svg>

        <!-- kunci inggris -->
        <svg id="svg-wrench" class="absolute top-1/3 right-[10%] w-40 h-40 opacity-[0.04]" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M75 10 C68 10 62 14 60 20 L25 55 C20 53 14 54 10 58 C4 64 4 74 10 80 C16 86 26 86 32 80 C36 76 37 70 35 65 L70 30 C76 32 83 30 87 26 C90 23 90 18 88 14 L80 22 L74 16 L82 8 C79 7 77 10 75 10 Z" fill="white"/>
        </svg>

        <!-- piston -->
        <svg id="svg-piston" class="absolute top-[45%] left-[5%] w-32 h-32 opacity-[0.04]" viewBox="0 0 80 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="20" y="60" width="40" height="50" rx="4" fill="white"/>
            <rect x="25" y="55" width="30" height="10" rx="2" fill="white"/>
            <rect x="35" y="20" width="10" height="40" rx="2" fill="white"/>
            <circle cx="40" cy="15" r="10" fill="none" stroke="white" stroke-width="3"/>
        </svg>

        <!-- speedometer -->
        <svg id="svg-speedo" class="absolute top-[15%] left-[15%] w-48 h-48 opacity-[0.04]" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 80 A40 40 0 1 1 80 80" fill="none" stroke="white" stroke-width="3" stroke-linecap="round"/>
            <path d="M25 75 A35 35 0 1 1 75 75" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="4 6"/>
            <line id="svg-needle" x1="50" y1="50" x2="50" y2="22" stroke="#D32F2F" stroke-width="2.5" stroke-linecap="round"/>
            <circle cx="50" cy="50" r="4" fill="white"/>
        </svg>

        <!-- partikel kecil -->
        <div id="svg-particle-1" class="absolute top-[20%] right-[30%] w-2 h-2 bg-primary/20 rounded-full"></div>
        <div id="svg-particle-2" class="absolute top-[60%] right-[20%] w-3 h-3 bg-white/10 rounded-full"></div>
        <div id="svg-particle-3" class="absolute top-[70%] left-[25%] w-2 h-2 bg-primary/15 rounded-full"></div>
        <div id="svg-particle-4" class="absolute top-[30%] left-[40%] w-1.5 h-1.5 bg-white/10 rounded-full"></div>
    </div>

    <div class="relative z-10 max-w-container-max mx-auto px-margin-desktop w-full">
        <div class="max-w-4xl">
            <!-- judul -->
            <div class="relative" style="animation: fadeInUp 0.8s ease-out 0.4s both;">
                <h1 class="font-headline text-[clamp(48px,10vw,100px)] leading-[0.85] font-extrabold mb-12 mt-16 tracking-tighter">
                    Repair &amp; Vehicle <br />
                    <span class="text-primary italic">
                        Booking
                    </span> <br />Operations
                </h1>
            </div>

            <!-- sub + tombol -->
            <div class="space-y-8" style="animation: fadeInUp 0.8s ease-out 0.6s both;">
                <p class="text-stone-300 text-xl leading-relaxed max-w-md">
                    REVVO adalah sistem informasi bengkel motor berbasis web untuk mengelola booking servis, data pelanggan, spare part, pembayaran, dan laporan secara lebih terstruktur.
                </p>
                <div class="flex flex-row gap-4">
                    <a href="<?= url('pages/auth/login.php') ?>" class="bg-white text-stone-900 px-10 py-5 rounded-xl font-bold text-sm uppercase tracking-widest shadow-xl hover:bg-stone-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 mechanical-press inline-block">Masuk ke Sistem</a>
                    <a href="#fitur" class="bg-stone-800/80 backdrop-blur-md border border-white/10 text-white px-10 py-5 rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-stone-800 hover:border-white/20 hover:-translate-y-1 transition-all duration-300 mechanical-press inline-block">Lihat Fitur</a>
                </div>
            </div>
        </div>
    </div>

    <!-- scroll indicator -->
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10" style="animation: fadeInUp 1s ease-out 1s both;">
        <div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center pt-2">
            <div class="w-1 h-3 bg-white/60 rounded-full animate-bounce"></div>
        </div>
    </div>
</section>

<!-- stats bar -->
<section class="relative z-20 -mt-16 pb-20">
    <div class="max-w-container-max mx-auto px-margin-desktop">
        <div class="animate-on-scroll scale-in bg-gradient-to-br from-stone-800 to-stone-900 border border-white/5 p-12 rounded-3xl shadow-2xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
                <!-- fitur 1 -->
                <div class="animate-on-scroll delay-100 relative group cursor-default">
                    <div class="absolute -left-4 top-0 w-1 h-12 bg-primary rounded-full group-hover:h-16 transition-all duration-500"></div>
                    <span class="text-primary font-technical text-xs font-bold block mb-4 tracking-tighter">01 / BOOKING</span>
                    <h3 class="font-headline text-xl font-bold mb-3 group-hover:text-primary transition-colors duration-300">Booking Servis Lebih Rapi</h3>
                    <p class="text-sm text-stone-400 leading-relaxed">Customer dapat membuat booking servis dan admin dapat memantau antrian servis dengan lebih terstruktur.</p>
                </div>
                <!-- fitur 2 -->
                <div class="animate-on-scroll delay-200 relative group cursor-default">
                    <div class="absolute -left-4 top-0 w-1 h-12 bg-primary rounded-full group-hover:h-16 transition-all duration-500"></div>
                    <span class="text-primary font-technical text-xs font-bold block mb-4 tracking-tighter">02 / STATUS</span>
                    <h3 class="font-headline text-xl font-bold mb-3 group-hover:text-primary transition-colors duration-300">Status Servis Lebih Jelas</h3>
                    <p class="text-sm text-stone-400 leading-relaxed">Progress pengerjaan dapat dilihat berdasarkan status booking sehingga admin, mekanik, dan customer lebih mudah memantau servis.</p>
                </div>
                <!-- fitur 3 -->
                <div class="animate-on-scroll delay-300 relative group cursor-default">
                    <div class="absolute -left-4 top-0 w-1 h-12 bg-primary rounded-full group-hover:h-16 transition-all duration-500"></div>
                    <span class="text-primary font-technical text-xs font-bold block mb-4 tracking-tighter">03 / OPERASIONAL</span>
                    <h3 class="font-headline text-xl font-bold mb-3 group-hover:text-primary transition-colors duration-300">Data Bengkel Lebih Terkelola</h3>
                    <p class="text-sm text-stone-400 leading-relaxed">Pengelolaan spare part, pembayaran, dan laporan dibuat lebih mudah dalam satu sistem.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- tentang -->
<section id="tentang" class="py-24 bg-stone-900 relative overflow-hidden grid-bg border-t border-white/5">
    <div class="max-w-container-max mx-auto px-margin-desktop relative z-10">
        <div class="animate-on-scroll scale-in bg-gradient-to-br from-stone-800 to-stone-900 border border-white/10 p-12 md:p-20 rounded-[3rem] shadow-3xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <!-- info kontak -->
                <div>
                    <h2 class="animate-on-scroll from-left font-headline text-5xl font-bold text-white mb-6 tracking-tight">Tentang REVVO</h2>
                    <p class="animate-on-scroll from-left delay-100 text-stone-400 text-lg leading-relaxed mb-10 max-w-md">REVVO dibuat sebagai sistem informasi bengkel motor untuk membantu proses booking, pengerjaan servis, pengelolaan spare part, pembayaran, dan laporan dalam satu aplikasi web.</p>
                    <a
                        href="https://wa.me/6281283375783"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="animate-on-scroll delay-150 inline-flex items-center gap-3 bg-primary text-white px-8 py-4 rounded-2xl font-bold uppercase tracking-widest shadow-lg hover:scale-105 hover:bg-red-700 transition-all duration-300 mechanical-press mb-10"
                    >
                        <i data-lucide="message-circle" class="w-5 h-5"></i>
                        Hubungi via WhatsApp
                    </a>
                    <div class="flex flex-wrap gap-10">
                        <a class="animate-on-scroll delay-200 flex items-center gap-4 text-white hover:text-primary transition-all duration-300 group" href="#">
                            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center group-hover:bg-primary group-hover:scale-110 transition-all duration-300">
                                <i data-lucide="shield-check" class="w-5 h-5 text-primary group-hover:text-white"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-stone-500 font-bold uppercase tracking-widest">Akses</span>
                                <span class="font-bold">Multi-role</span>
                            </div>
                        </a>
                        <a class="animate-on-scroll delay-300 flex items-center gap-4 text-white hover:text-primary transition-all duration-300 group" href="#">
                            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center group-hover:bg-primary group-hover:scale-110 transition-all duration-300">
                                <i data-lucide="calendar-check-2" class="w-5 h-5 text-primary group-hover:text-white"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-stone-500 font-bold uppercase tracking-widest">Fokus</span>
                                <span class="font-bold">Booking Servis</span>
                            </div>
                        </a>
                        <a class="animate-on-scroll delay-400 flex items-center gap-4 text-white hover:text-primary transition-all duration-300 group" href="#">
                            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center group-hover:bg-primary group-hover:scale-110 transition-all duration-300">
                                <i data-lucide="file-text" class="w-5 h-5 text-primary group-hover:text-white"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-stone-500 font-bold uppercase tracking-widest">Output</span>
                                <span class="font-bold">Laporan Bengkel</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- dekorasi -->
                <div class="hidden lg:block relative">
                    <div class="absolute inset-0 bg-primary/20 blur-[100px] organic-shape animate-float"></div>
                    <div class="animate-on-scroll from-right relative bg-stone-950 border border-white/10 p-8 rounded-3xl shadow-2xl">
                        <div class="border-b border-white/10 pb-5 mb-6">
                            <p class="text-[11px] uppercase tracking-[0.28em] text-primary font-bold mb-2">
                                Ringkasan Sistem
                            </p>
                            <h3 class="text-2xl font-headline font-bold text-white">
                                REVVO
                            </h3>
                            <p class="text-sm text-stone-400 mt-2">
                                Sistem informasi bengkel motor berbasis web.
                            </p>
                        </div>

                        <div class="space-y-6 text-sm">
                            <div>
                                <p class="text-stone-500 uppercase tracking-[0.2em] text-[11px] mb-3">
                                    Role Pengguna
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="border border-white/10 bg-white/5 px-3 py-2 rounded-lg text-stone-200">Admin</span>
                                    <span class="border border-white/10 bg-white/5 px-3 py-2 rounded-lg text-stone-200">Customer</span>
                                    <span class="border border-white/10 bg-white/5 px-3 py-2 rounded-lg text-stone-200">Mekanik</span>
                                </div>
                            </div>

                            <div>
                                <p class="text-stone-500 uppercase tracking-[0.2em] text-[11px] mb-3">
                                    Status Booking
                                </p>
                                <div class="grid grid-cols-2 gap-2 text-stone-300">
                                    <div class="border border-white/10 px-3 py-2 rounded-lg">queued</div>
                                    <div class="border border-white/10 px-3 py-2 rounded-lg">in_progress</div>
                                    <div class="border border-white/10 px-3 py-2 rounded-lg">completed</div>
                                    <div class="border border-white/10 px-3 py-2 rounded-lg">ready_for_pickup</div>
                                    <div class="border border-white/10 px-3 py-2 rounded-lg col-span-2">cancelled</div>
                                </div>
                            </div>

                            <div>
                                <p class="text-stone-500 uppercase tracking-[0.2em] text-[11px] mb-3">
                                    Modul Utama
                                </p>
                                <ul class="space-y-2 text-stone-300">
                                    <li class="flex items-center justify-between border-b border-white/5 pb-2">
                                        <span>Booking Servis</span>
                                        <span class="text-primary">Ada</span>
                                    </li>
                                    <li class="flex items-center justify-between border-b border-white/5 pb-2">
                                        <span>Spare Part</span>
                                        <span class="text-primary">Ada</span>
                                    </li>
                                    <li class="flex items-center justify-between border-b border-white/5 pb-2">
                                        <span>Pembayaran</span>
                                        <span class="text-primary">Ada</span>
                                    </li>
                                    <li class="flex items-center justify-between">
                                        <span>Laporan</span>
                                        <span class="text-primary">Ada</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- alur / workflow -->
<section id="alur" class="py-section-gap-desktop bg-stone-50 text-stone-900 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-white to-transparent pointer-events-none"></div>
    <div class="max-w-container-max mx-auto px-margin-desktop relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
            <!-- kiri -->
            <div class="relative">
                <div class="absolute -top-20 -left-20 w-64 h-64 bg-primary/5 organic-shape -z-10 animate-float"></div>
                <span class="animate-on-scroll from-left text-primary font-technical text-sm font-bold tracking-widest uppercase mb-6 block">Alur Penggunaan</span>
                <h2 class="animate-on-scroll from-left delay-100 font-headline text-6xl font-extrabold leading-[1.1] mb-10 tracking-tight">
                    Sistem bengkel motor <br />dengan alur kerja <br /><span class="text-primary">yang lebih jelas.</span>
                </h2>
                <p class="animate-on-scroll from-left delay-200 text-stone-500 text-xl leading-relaxed max-w-md">REVVO membantu proses booking, pengerjaan servis, dan pengelolaan data bengkel agar tidak lagi bergantung pada pencatatan manual.</p>
                <div class="animate-on-scroll from-left delay-300 mt-12 p-8 bg-white shadow-xl rounded-3xl border border-stone-100 max-w-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-4 text-primary font-bold">
                        <i data-lucide="badge-check" class="w-6 h-6"></i>
                        <span class="text-sm uppercase tracking-widest">Cocok Untuk Pengelolaan Sistem Bengkel</span>
                    </div>
                </div>
            </div>

            <!-- langkah2 -->
            <div class="space-y-8 relative">
                <!-- step 1 -->
                <div class="animate-on-scroll from-right delay-100 group flex gap-8 items-start p-8 rounded-3xl transition-all duration-300 hover:bg-white hover:shadow-2xl hover:shadow-stone-200 hover:-translate-y-1">
                    <div class="relative">
                        <span class="font-headline text-7xl font-extrabold text-stone-200 group-hover:text-primary/20 transition-colors duration-500 leading-none">01</span>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-12 h-12 bg-primary text-white flex items-center justify-center rounded-2xl shadow-glow-red rotate-3 group-hover:rotate-12 transition-transform duration-500">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-headline text-2xl font-bold mb-3">Login Sesuai Peran</h4>
                        <p class="text-stone-600 leading-relaxed">Sistem menyediakan akses berbeda untuk admin, customer, dan mekanik sesuai kebutuhan masing-masing.</p>
                    </div>
                </div>

                <!-- step 2 -->
                <div class="animate-on-scroll from-right delay-200 group flex gap-8 items-start p-8 rounded-3xl transition-all duration-300 hover:bg-white hover:shadow-2xl hover:shadow-stone-200 hover:-translate-y-1">
                    <div class="relative">
                        <span class="font-headline text-7xl font-extrabold text-stone-200 group-hover:text-primary/20 transition-colors duration-500 leading-none">02</span>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-12 h-12 bg-primary text-white flex items-center justify-center rounded-2xl shadow-glow-red -rotate-3 group-hover:rotate-6 transition-transform duration-500">
                            <i data-lucide="calendar" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-headline text-2xl font-bold mb-3">Kelola Booking Servis</h4>
                        <p class="text-stone-600 leading-relaxed">Customer dapat membuat booking servis, lalu admin dan mekanik dapat melanjutkan proses pengerjaan sesuai alur sistem.</p>
                    </div>
                </div>

                <!-- step 3 -->
                <div class="animate-on-scroll from-right delay-300 group flex gap-8 items-start p-8 rounded-3xl transition-all duration-300 hover:bg-white hover:shadow-2xl hover:shadow-stone-200 hover:-translate-y-1">
                    <div class="relative">
                        <span class="font-headline text-7xl font-extrabold text-stone-200 group-hover:text-primary/20 transition-colors duration-500 leading-none">03</span>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-12 h-12 bg-primary text-white flex items-center justify-center rounded-2xl shadow-glow-red rotate-6 group-hover:rotate-[-6deg] transition-transform duration-500">
                            <i data-lucide="trending-up" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-headline text-2xl font-bold mb-3">Pantau Status dan Laporan</h4>
                        <p class="text-stone-600 leading-relaxed">Data servis, spare part, pembayaran, dan laporan bengkel tersimpan lebih rapi dan mudah dipantau.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- cta banner -->
<section id="harga" class="py-12 bg-stone-50">
    <div class="max-w-container-max mx-auto px-margin-desktop">
        <div class="animate-on-scroll scale-in mesh-gradient-red p-16 md:p-24 rounded-[3rem] text-center shadow-2xl relative overflow-hidden group">
            <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
            <div class="relative z-10">
                <h2 class="font-headline text-white text-5xl md:text-7xl font-extrabold mb-12 max-w-4xl mx-auto leading-tight tracking-tight">
                    SIAP MELIHAT ALUR SISTEM INFORMASI BENGKEL REVVO?
                </h2>
                <div class="flex flex-col sm:flex-row justify-center gap-6 items-center">
                    <a href="<?= url('pages/auth/login.php') ?>" class="bg-stone-900 text-white px-12 py-6 rounded-2xl font-bold uppercase tracking-widest shadow-2xl hover:scale-105 hover:shadow-[0_20px_60px_rgba(0,0,0,0.4)] transition-all duration-300 mechanical-press inline-block pulse-glow">Masuk ke Sistem</a>
                    <a href="#tentang" class="bg-white/20 backdrop-blur-md border border-white/30 text-white px-12 py-6 rounded-2xl font-bold uppercase tracking-widest hover:bg-white hover:text-primary hover:scale-105 transition-all duration-300 mechanical-press inline-block">Lihat Informasi</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- fitur utama -->
<section id="fitur" class="py-section-gap-desktop bg-stone-50 text-stone-900">
    <div class="max-w-container-max mx-auto px-margin-desktop">
        <!-- header -->
        <div class="flex flex-col md:flex-row justify-between items-end mb-20 gap-8">
            <div class="max-w-2xl">
                <span class="animate-on-scroll text-primary font-technical text-sm font-bold tracking-widest uppercase mb-4 block">Modul Utama Sistem</span>
                <h2 class="animate-on-scroll delay-100 font-headline text-6xl font-extrabold tracking-tight">Fitur Utama Revvo</h2>
            </div>
            <p class="animate-on-scroll delay-200 text-stone-500 max-w-sm mb-2">Fitur disusun untuk mendukung proses operasional bengkel motor secara lebih terstruktur.</p>
        </div>

        <!-- cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- booking -->
            <div class="animate-on-scroll delay-100 glass-card bg-stone-900/5 p-2 rounded-[2.5rem] flex flex-col group hover:shadow-3xl hover:-translate-y-2 transition-all duration-500 border border-stone-200">
                <div class="aspect-[4/3] bg-stone-900 rounded-[2rem] mb-8 overflow-hidden relative shadow-inner">
                    <img alt="Booking Dashboard" class="w-full h-full object-cover opacity-90 group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida/ADBb0ujHlInSL1d_UIXgC_s-At3DdGWD40BOhlyXuuxzDm06ln87CAn4pT8EKI1Zs4jRL9kT8Qx7rPF_z3whkfG8RXYSG8oCYGxKnDtE0dwkEbg4D1V7G7p4FXVW9SHsx53EvqNDaunDvWR-llr8piHSS7A6LNQEckuU9x3ySqag_F7R5fM5uPyISUQ4346GXB2wqmPNSH1fSo5eIdsrWsLxnd2ijqzTQ-vw58uJM7BIlnJNcj0f91xN57yuIEc" />
                    <div class="absolute inset-0 bg-gradient-to-t from-stone-900/60 to-transparent"></div>
                </div>
                <div class="px-8 pb-10 flex-grow flex flex-col">
                    <h3 class="font-headline text-2xl font-bold mb-3">Manajemen Booking</h3>
                    <p class="text-stone-500 text-sm mb-8 leading-relaxed">Kelola data booking servis customer, detail kendaraan, jadwal, dan status pengerjaan dalam satu alur.</p>
                    <a class="mt-auto text-primary font-bold text-xs flex items-center gap-2 tracking-widest uppercase group-hover:gap-4 transition-all duration-300" href="#">
                        BOOKING &amp; STATUS <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- inventory -->
            <div class="animate-on-scroll delay-200 glass-card bg-stone-900/5 p-2 rounded-[2.5rem] flex flex-col group hover:shadow-3xl hover:-translate-y-2 transition-all duration-500 border border-stone-200">
                <div class="aspect-[4/3] bg-stone-900 rounded-[2rem] mb-8 overflow-hidden relative shadow-inner">
                    <img alt="Inventory Dashboard" class="w-full h-full object-cover opacity-90 group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida/ADBb0ujHlInSL1d_UIXgC_s-At3DdGWD40BOhlyXuuxzDm06ln87CAn4pT8EKI1Zs4jRL9kT8Qx7rPF_z3whkfG8RXYSG8oCYGxKnDtE0dwkEbg4D1V7G7p4FXVW9SHsx53EvqNDaunDvWR-llr8piHSS7A6LNQEckuU9x3ySqag_F7R5fM5uPyISUQ4346GXB2wqmPNSH1fSo5eIdsrWsLxnd2ijqzTQ-vw58uJM7BIlnJNcj0f91xN57yuIEc" />
                    <div class="absolute inset-0 bg-gradient-to-t from-stone-900/60 to-transparent"></div>
                </div>
                <div class="px-8 pb-10 flex-grow flex flex-col">
                    <h3 class="font-headline text-2xl font-bold mb-3">Data Spare Part</h3>
                    <p class="text-stone-500 text-sm mb-8 leading-relaxed">Catat spare part yang tersedia, stok, harga, dan penggunaan part untuk kebutuhan servis bengkel.</p>
                    <a class="mt-auto text-primary font-bold text-xs flex items-center gap-2 tracking-widest uppercase group-hover:gap-4 transition-all duration-300" href="#">
                        SPARE PART <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- analytics -->
            <div class="animate-on-scroll delay-300 glass-card bg-stone-900/5 p-2 rounded-[2.5rem] flex flex-col group hover:shadow-3xl hover:-translate-y-2 transition-all duration-500 border border-stone-200">
                <div class="aspect-[4/3] bg-stone-900 rounded-[2rem] mb-8 overflow-hidden relative shadow-inner">
                    <img alt="Analytics Dashboard" class="w-full h-full object-cover opacity-90 group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida/ADBb0ujHlInSL1d_UIXgC_s-At3DdGWD40BOhlyXuuxzDm06ln87CAn4pT8EKI1Zs4jRL9kT8Qx7rPF_z3whkfG8RXYSG8oCYGxKnDtE0dwkEbg4D1V7G7p4FXVW9SHsx53EvqNDaunDvWR-llr8piHSS7A6LNQEckuU9x3ySqag_F7R5fM5uPyISUQ4346GXB2wqmPNSH1fSo5eIdsrWsLxnd2ijqzTQ-vw58uJM7BIlnJNcj0f91xN57yuIEc" />
                    <div class="absolute inset-0 bg-gradient-to-t from-stone-900/60 to-transparent"></div>
                </div>
                <div class="px-8 pb-10 flex-grow flex flex-col">
                    <h3 class="font-headline text-2xl font-bold mb-3">Pembayaran dan Laporan</h3>
                    <p class="text-stone-500 text-sm mb-8 leading-relaxed">Pantau pembayaran servis dan tampilkan laporan operasional bengkel berdasarkan data yang tersimpan.</p>
                    <a class="mt-auto text-primary font-bold text-xs flex items-center gap-2 tracking-widest uppercase group-hover:gap-4 transition-all duration-300" href="#">
                        PEMBAYARAN &amp; LAPORAN <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$pageScripts = ['assets/js/landing.js'];
include 'includes/footer.php';
?>
