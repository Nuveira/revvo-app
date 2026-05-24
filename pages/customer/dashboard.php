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
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white flex flex-col">
            <div> 
                <p>PP</p>
                <P>Geral maulana</P>
                <p>Customer</p>
            </div>
                <nav class="flex-1">
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">Dashboard</a>
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">Motor Saya</a>
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">Booking</a>
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">History</a>
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">Profil</a>
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">Keluar</a>                
                </nav>
        </div>





        <!-- Main Content -->
        <div class="flex-1 bg-gray-100">
            <div class="bg-black flex justify-between items-center w-full p-6">
                <div>
                    <p class="text-yellow-500">SELAMAT DATANG KEMBALI</p>
                    <p class="text-4xl text-white">Halo, Geral!</p>
                    <p class="text-white">Kamu punya 1 boking aktif dan 2 motor terdaftar</p>
                </div>
                <div class="bg-yellow-500 px-6 py-3 rounded inline-block">
                    <p class="text-white">+ Booking Service Baru</p>
                </div>
            </div>
        </div>

    </div>
</body>
</html>