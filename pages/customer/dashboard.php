<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Dashboard Customer</title>
</head>
<body>
    <div class="flex h-screen">
        <?php include 'nav.php'; ?>

        <div class="flex-1 bg-gray-100">
            <!-- Header -->
            <div class="bg-[#1D1616] flex justify-between items-center w-full p-6">
                <div>
                    <p class="text-[#8E1616]">SELAMAT DATANG KEMBALI</p>
                    <p class="text-4xl text-white">Halo, Geral!</p>
                    <p class="text-white">Kamu punya 1 boking aktif dan 2 motor terdaftar</p>
                </div>
                <div class="bg-[#8E1616] px-6 py-3 rounded inline-block">
                    <p class="text-white">+ Booking Service Baru</p>
                </div>
            </div>

            <!-- Main Dashboard -->
            <div class="flex gap-4 mx-4">
                <div class="flex-[2] bg-white border border-[#eadede] p-6 w-full shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] tracking-[0.2em] text-gray-400 uppercase">Booking Aktif</p>
                            <h3 class="mt-2 text-[32px] leading-none font-medium text-[#8E1616]">Service Rutin</h3>
                        </div>

                        <span class="rounded-full bg-[#f8eeee] px-4 py-1 text-sm font-medium text-[#8E1616]">
                            Terjadwal
                        </span>
                    </div>
                    
                    <div class="mt-8 grid grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-400">Motor</p>
                            <p>-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Tanggal</p>
                            <p>-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Waktu</p>
                            <p>-</p>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-4">
                        <a 
                            href="booking.php"
                            class="flex-[2] bg-[#2f2f2f] px-6 py-4 text-center text-base font-semibold text-white transition hover:bg-black"
                        >
                            lihat detail
                        </a>
                    
                        <a 
                            href="booking_edit.php"
                            class="flex-1 bg-[#8E1616] px-6 py-4 text-center text-base font-semibold text-white transition hover:bg-[#6f1111]"
                        >
                            ubah jadwal
                        </a>
                    </div>
                </div>


            <div class="flex-1 bg-[#8E1616] border border-[#eadede] p-6 w-full shadow-sm">
                <p class="items-start text-[11px] tracking-[0.2em] text-gray-400 uppercase"
                >
                Motor terdaftar
                </p>
                
                <div class="mt-6 space-y-6">
                    <div class="items-center gap-4 bg-[#a32828]/40 px-4 py-4">
                        <p class="text-lg font-semibold text-white">Honda Vario 150</p>
                        <p class="text-sm text-[#f1caca]">B 1234 ABC</p>
                    </div>
                </div>
                
                <a
                    href="motor.php"
                    class="mt-8 block w-full bg-white py-4 text-center text-base font-medium text-[#8E1616] transition hover:bg-[#f8eeee]"
                >
                    Kelola Motor
                </a>
            </div> 

        </div>

    </div>
</body>
</html>
