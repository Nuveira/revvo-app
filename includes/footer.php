<?php
?>
<!-- footer -->
<footer class="bg-stone-900 py-24 border-t border-white/5">
    <div class="max-w-container-max mx-auto px-margin-desktop">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-20">
            <!-- brand -->
            <div class="md:col-span-4">
                <div class="flex items-center gap-3 mb-10">
                    <img src="<?= asset('assets/images/logo.png') ?>" alt="REVVO" class="h-10 w-auto invert brightness-0 invert">
                    <span class="font-headline font-bold text-3xl tracking-tighter">REVVO</span>
                </div>
                <p class="text-stone-400 max-w-xs leading-relaxed text-lg">
                    Sistem informasi bengkel motor berbasis web untuk membantu proses booking servis, data spare part, pembayaran, dan laporan.
                </p>
            </div>

            <!-- produk -->
            <div class="md:col-span-2">
                <h5 class="text-xs font-bold uppercase tracking-widest text-primary mb-8">Fitur</h5>
                <ul class="space-y-5 text-stone-400 font-semibold text-sm">
                    <li><a class="hover:text-white transition-colors" href="#fitur">Manajemen Booking</a></li>
                    <li><a class="hover:text-white transition-colors" href="#fitur">Data Spare Part</a></li>
                    <li><a class="hover:text-white transition-colors" href="#fitur">Pembayaran</a></li>
                    <li><a class="hover:text-white transition-colors" href="#fitur">Laporan</a></li>
                </ul>
            </div>

            <!-- perusahaan -->
            <div class="md:col-span-2">
                <h5 class="text-xs font-bold uppercase tracking-widest text-primary mb-8">Navigasi</h5>
                <ul class="space-y-5 text-stone-400 font-semibold text-sm">
                    <li><a class="hover:text-white transition-colors" href="#hero">Beranda</a></li>
                    <li><a class="hover:text-white transition-colors" href="#fitur">Fitur</a></li>
                    <li><a class="hover:text-white transition-colors" href="#alur">Alur Sistem</a></li>
                    <li><a class="hover:text-white transition-colors" href="#tentang">Tentang REVVO</a></li>
                </ul>
            </div>

            <!-- ringkasan -->
            <div class="md:col-span-4">
                <h5 class="text-xs font-bold uppercase tracking-widest text-primary mb-8">Ringkasan Sistem</h5>
                <p class="text-sm text-stone-400 mb-4 leading-relaxed">REVVO mendukung tiga peran utama:</p>
                <ul class="space-y-3 text-sm text-stone-400">
                    <li>Admin mengelola data master, booking, pembayaran, dan laporan.</li>
                    <li>Customer mengelola motor dan membuat booking servis.</li>
                    <li>Mekanik melihat tugas dan memperbarui status pengerjaan.</li>
                </ul>
            </div>
        </div>

        <!-- bottom -->
        <div class="mt-24 pt-10 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-8">
            <p class="text-xs font-technical text-stone-600">&copy; <?= date('Y') ?> REVVO. Sistem Informasi Bengkel Motor.</p>
            <div class="flex gap-12 text-stone-600 text-xs font-bold tracking-widest uppercase">
                <a class="hover:text-white transition-colors" href="#fitur">Fitur</a>
                <a class="hover:text-white transition-colors" href="<?= url('pages/auth/login.php') ?>">Login</a>
            </div>
        </div>
    </div>
</footer>

<script src="<?= asset('assets/js/main.js') ?>"></script>
<script src="<?= asset('assets/js/navbar.js') ?>"></script>

<?php if (!empty($pageScripts)): ?>
    <?php foreach ($pageScripts as $script): ?>
        <script src="<?= asset($script) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
