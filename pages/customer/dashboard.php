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
                <div class="flex-[2] bg-white border border-gray-200 p-5 w-full shadow-sm">
                    <div class="flex items-start justify-between ">
                        <p class="text-grey-500">BOOKING AKTIF</p>
                        <p class="bg-red-500 px-4 rounded-full ">Terjadwal</p>
                    </div>

                    <p>Service rutin</p>
                    
                    <div class="flex gap-32 mt-4">
                        <div>
                            <p>Motor</p>
                            <p>-</p>
                        </div>
                        <div>
                            <p>Tanggal</p>
                            <p>-</p>
                        </div>
                        <div>
                            <p>Waktu</p>
                            <p>-</p>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <div class="flex-[2] items-center">
                            <p class="bg-black-100 p-5 items-center">lihat detail</p>
                        </div>
                        <div class="flex-1 justify-center">
                            <p class="border p-3 items-center">ubah jadwal</p>
                        </div>
                    </div>
                </div>
                <div class="flex-1 bg-[#D84040] p-5 w-full">
                    <p>Motor terdaftar</p>
                </div>
            </div> 

        </div>

    </div>
</body>
</html>