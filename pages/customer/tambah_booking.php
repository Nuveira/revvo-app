<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Tambah Booking | REVVO';
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
require_once '../../includes/customer_role.php';

// Ambil motor milik customer
$motors = [];
$stmt = $conn->prepare("SELECT id, brand, model, plate_number FROM motors WHERE customer_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$motors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Ambil service types
$service_types = $conn->query("SELECT id, name, base_price, estimated_duration_minutes FROM service_types WHERE status = 'active' ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Ambil time slots
$time_slots = $conn->query("SELECT id, day, start_time, end_time FROM time_slots WHERE status = 'active' ORDER BY day, start_time")->fetch_all(MYSQLI_ASSOC);

$errors = [];
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/logo.png') ?>">
</head>
<body class="font-['Plus_Jakarta_Sans'] overflow-hidden">
    <div class="flex h-screen overflow-hidden">
        <?php include 'nav.php'; ?>

        <main class="flex flex-col flex-1 min-w-0 bg-gray-100 overflow-y-auto overflow-x-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between">
                <div class="min-w-0">
                    <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">Booking</p>
                    <h1 class="mt-2 text-2xl sm:text-4xl text-white font-semibold break-words">Buat Booking Baru</h1>
                </div>
                <a href="booking.php" class="bg-[#FF0000] px-4 py-3 rounded text-sm font-semibold text-white whitespace-nowrap hover:bg-[#6e1111] transition inline-flex items-center gap-2 shadow-[0_0_15px_rgba(142,22,22,0.3)] shadow-red-500/40">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    Kembali
                </a>
            </div>

            <div class="p-4 w-full mx-auto flex-1 flex flex-col">

                <?php if ($error): ?>
                    <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($motors)): ?>
                    <div class="flex-1 rounded-lg border border-[#eadede] bg-white p-8 shadow-sm flex flex-col items-center justify-center text-center">
                        <span class="material-symbols-outlined text-4xl text-gray-300">sports_motorsports</span>
                        <p class="mt-3 font-semibold text-gray-700">Belum ada motor terdaftar</p>
                        <p class="mt-1 text-sm text-gray-400">Tambahkan motor terlebih dahulu sebelum membuat booking.</p>
                        <a href="tambah_motor.php" class="mt-4 inline-flex items-center gap-2 rounded bg-[#8E1616] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#6f1111]">
                            <span class="material-symbols-outlined text-[18px]">add_circle</span>
                            Tambah Motor
                        </a>
                    </div>
                <?php else: ?>

                <div class="rounded-lg border border-[#eadede] bg-white p-5 shadow-sm">
                    <div class="mb-5">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Form Booking</p>
                        <h2 class="mt-2 text-xl font-semibold text-gray-900">Isi detail booking</h2>
                    </div>

                    <form action="proses_booking.php" method="POST" class="space-y-4">

                        <!-- Motor -->
                        <div>
                            <label for="motor_id" class="text-sm font-medium text-gray-700">Pilih Motor</label>
                            <select id="motor_id" name="motor_id" required
                                class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                                <?php foreach ($motors as $motor): ?>
                                    <option value="<?= $motor['id'] ?>">
                                        <?= htmlspecialchars($motor['brand'] . ' ' . $motor['model'] . ' - ' . $motor['plate_number']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Jenis Service -->
                        <div>
                            <label for="service_type_id" class="text-sm font-medium text-gray-700">Jenis Service</label>
                            <select id="service_type_id" name="service_type_id" required
                                class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                                <?php foreach ($service_types as $service): ?>
                                    <option value="<?= $service['id'] ?>">
                                        <?= htmlspecialchars($service['name']) ?> — Rp<?= number_format($service['base_price'], 0, ',', '.') ?> · <?= $service['estimated_duration_minutes'] ?> menit
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tanggal Booking -->
                        <div>
                            <label for="booking_date" class="text-sm font-medium text-gray-700">Tanggal Booking</label>
                            <input id="booking_date" name="booking_date" type="date" required
                                min="<?= date('Y-m-d') ?>"
                                class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                        </div>

                        <!-- Time Slot — difilter otomatis berdasarkan hari -->
                        <div>
                            <label for="time_slot_id" class="text-sm font-medium text-gray-700">Pilih Waktu</label>
                            <select id="time_slot_id" name="time_slot_id" required
                                class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616]">
                                <option value="">— Pilih tanggal terlebih dahulu —</option>
                            </select>
                            <p id="slot-empty-msg" class="mt-1 text-xs text-red-500 hidden">Tidak ada slot waktu tersedia untuk hari ini.</p>
                        </div>

                        <!-- Keluhan -->
                        <div>
                            <label for="customer_complaint" class="text-sm font-medium text-gray-700">
                                Keluhan <span class="text-gray-400 font-normal">(opsional)</span>
                            </label>
                            <textarea id="customer_complaint" name="customer_complaint" rows="4"
                                placeholder="Jelaskan keluhan motor kamu..."
                                class="mt-2 w-full rounded border border-gray-200 px-3 py-3 text-sm outline-none transition focus:border-[#8E1616] resize-none"></textarea>
                        </div>

                        <!-- Tombol -->
                        <div class="flex gap-3 pt-2">
                            <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded bg-[#8E1616] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#6f1111]">
                                <span class="material-symbols-outlined text-[20px]">calendar_add_on</span>
                                Simpan Booking
                            </button>
                            <a href="booking.php" class="inline-flex items-center justify-center rounded border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>

                <?php endif; ?>
            </div>

            <?php include 'footer.php'; ?>
        </main>
    </div>
<script>
var allSlots = <?= json_encode($time_slots) ?>;

var dayMap = {
    0: 'sunday',
    1: 'monday',
    2: 'tuesday',
    3: 'wednesday',
    4: 'thursday',
    5: 'friday',
    6: 'saturday'
};

var dateInput  = document.getElementById('booking_date');
var slotSelect = document.getElementById('time_slot_id');
var emptyMsg   = document.getElementById('slot-empty-msg');

function filterSlots() {
    var dateVal = dateInput.value;
    slotSelect.innerHTML = '';
    emptyMsg.classList.add('hidden');

    if (!dateVal) {
        slotSelect.innerHTML = '<option value="">— Pilih tanggal terlebih dahulu —</option>';
        return;
    }

    var parts   = dateVal.split('-');
    var date    = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
    var dayName = dayMap[date.getDay()];

    // Ambil jam sekarang (HH:MM) untuk filter slot yang sudah lewat hari ini
    var now        = new Date();
    var todayStr   = now.getFullYear() + '-' +
                     String(now.getMonth() + 1).padStart(2, '0') + '-' +
                     String(now.getDate()).padStart(2, '0');
    var isToday    = (dateVal === todayStr);
    var nowMinutes = now.getHours() * 60 + now.getMinutes();

    var filtered = allSlots.filter(function(slot) {
        if (slot.day !== dayName) return false;

        // Kalau hari ini, filter slot yang end_time-nya sudah lewat
        if (isToday) {
            var endParts   = slot.end_time.substring(0, 5).split(':');
            var endMinutes = parseInt(endParts[0]) * 60 + parseInt(endParts[1]);
            return endMinutes > nowMinutes;
        }

        return true;
    });

    if (filtered.length === 0) {
        slotSelect.innerHTML = '<option value="">— Tidak ada slot tersedia —</option>';
        emptyMsg.classList.remove('hidden');
        return;
    }

    filtered.forEach(function(slot) {
        var start  = slot.start_time.substring(0, 5);
        var end    = slot.end_time.substring(0, 5);
        var day    = dayName.charAt(0).toUpperCase() + dayName.slice(1);
        var option = document.createElement('option');
        option.value       = slot.id;
        option.textContent = day + ' | ' + start + ' - ' + end;
        slotSelect.appendChild(option);
    });
}

dateInput.addEventListener('change', filterSlots);
</script>
</body>
</html>