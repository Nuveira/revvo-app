<?php
/**
 * Footer Template
 * Includes: Footer content, closing </body> and </html>
 */
?>
<!-- Footer -->
<footer class="bg-stone-900 py-24 border-t border-white/5">
    <div class="max-w-container-max mx-auto px-margin-desktop">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-20">
            <!-- Brand -->
            <div class="md:col-span-4">
                <div class="flex items-center gap-3 mb-10">
                    <img src="src/images/logo.png" alt="REVVO" class="h-10 w-auto invert brightness-0 invert">
                    <span class="font-headline font-bold text-3xl tracking-tighter">REVVO</span>
                </div>
                <p class="text-stone-400 max-w-xs leading-relaxed text-lg">
                    Sistem manajemen bengkel nomor satu di Indonesia. Dibangun untuk presisi, dirancang untuk pertumbuhan bisnis Anda.
                </p>
            </div>

            <!-- Produk -->
            <div class="md:col-span-2">
                <h5 class="text-xs font-bold uppercase tracking-widest text-primary mb-8">Produk</h5>
                <ul class="space-y-5 text-stone-400 font-semibold text-sm">
                    <li><a class="hover:text-white transition-colors" href="#fitur">Fitur Lengkap</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Integrasi WA</a></li>
                    <li><a class="hover:text-white transition-colors" href="#harga">Harga</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Update</a></li>
                </ul>
            </div>

            <!-- Perusahaan -->
            <div class="md:col-span-2">
                <h5 class="text-xs font-bold uppercase tracking-widest text-primary mb-8">Perusahaan</h5>
                <ul class="space-y-5 text-stone-400 font-semibold text-sm">
                    <li><a class="hover:text-white transition-colors" href="#tentang">Tentang Kami</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Karir</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Blog</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Kontak</a></li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="md:col-span-4">
                <h5 class="text-xs font-bold uppercase tracking-widest text-primary mb-8">Tetap Terhubung</h5>
                <p class="text-sm text-stone-400 mb-8 leading-relaxed">Berlangganan newsletter untuk tips eksklusif mengelola bengkel modern.</p>
                <div class="flex p-2 bg-stone-800 rounded-2xl border border-white/5 focus-within:border-primary/50 transition-all">
                    <input class="bg-transparent border-none px-4 py-3 w-full text-sm focus:ring-0 placeholder:text-stone-600" placeholder="Email Anda" type="email" />
                    <button class="bg-primary text-white px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-widest hover:scale-105 transition-all mechanical-press">IKUTI</button>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="mt-24 pt-10 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-8">
            <p class="text-xs font-technical text-stone-600">&copy; <?= date('Y') ?> REVVO SYSTEMS. SEMUA HAK DILINDUNGI.</p>
            <div class="flex gap-12 text-stone-600 text-xs font-bold tracking-widest uppercase">
                <a class="hover:text-white transition-colors" href="#">Syarat &amp; Ketentuan</a>
                <a class="hover:text-white transition-colors" href="#">Kebijakan Privasi</a>
            </div>
        </div>
    </div>
</footer>

<!-- Initialize Lucide Icons -->
<script>lucide.createIcons();</script>
</body>
</html>
