<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['customer']);

$userId = $_SESSION['user_id'] ?? 0;

/*
|--------------------------------------------------------------------------
| Customer Login
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT id
    FROM customers
    WHERE user_id = ?
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$customer = $stmt->get_result()->fetch_assoc();

if (!$customer) {
    die('Customer tidak ditemukan');
}

$customerId = $customer['id'];

$user_id = $_SESSION['user_id'] ?? null;

$nama = 'Guest';
$role = '';
$profile_photo = null;

if ($user_id) {

    $stmtUser = $conn->prepare("
        SELECT name, role, profile_photo
        FROM users
        WHERE id = ?
    ");

    $stmtUser->bind_param("i", $user_id);
    $stmtUser->execute();

    $userData = $stmtUser->get_result()->fetch_assoc();

    if ($userData) {

        $nama = $userData['name'];
        $role = $userData['role'];
        $profile_photo = $userData['profile_photo'];

    }

    $stmtUser->close();
}

/*
|--------------------------------------------------------------------------
| Motor Customer
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT *
    FROM motors
    WHERE customer_id = ?
    ORDER BY id DESC
");

$stmt->bind_param("i", $customerId);
$stmt->execute();

$motors = $stmt->get_result();

/*
|--------------------------------------------------------------------------
| Service Type
|--------------------------------------------------------------------------
*/
$serviceTypes = $conn->query("
    SELECT *
    FROM service_types
    WHERE status='active'
    ORDER BY name
");

/*
|--------------------------------------------------------------------------
| Time Slot
|--------------------------------------------------------------------------
*/
$timeSlots = $conn->query("
    SELECT *
    FROM time_slots
    WHERE status='active'
    ORDER BY day,start_time
");
?>

<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Tambah Booking</title>

<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>

<body class="font-['Plus_Jakarta_Sans'] bg-gray-100">

<div class="flex h-screen">

```
<?php include 'nav.php'; ?>

<div class="flex-1 overflow-auto">

    <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] p-5">

        <p class="text-[#8E1616] uppercase text-sm">
            Booking Service
        </p>

        <h1 class="text-4xl text-white py-2">
            Tambah Booking
        </h1>

        <p class="text-white">
            Buat booking servis motor Anda.
        </p>

    </div>

    <div class="p-6">

        <div class="bg-white border border-[#eadede] rounded-lg shadow-sm p-6">

            <form
                action="proses_booking.php"
                method="POST"
                class="space-y-5"
            >

                <div>

                    <label class="block mb-2 font-medium">
                        Motor
                    </label>

                    <select
                        name="motor_id"
                        required
                        class="w-full border rounded-lg p-3"
                    >

                        <option value="">
                            Pilih Motor
                        </option>

                        <?php while($motor = $motors->fetch_assoc()): ?>

                            <option value="<?= $motor['id'] ?>">

                                <?= htmlspecialchars(
                                    $motor['brand']
                                    .' '.
                                    $motor['model']
                                    .' ('.
                                    $motor['plate_number']
                                    .')'
                                ) ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div>

                    <label class="block mb-2 font-medium">
                        Jenis Service
                    </label>

                    <select
                        name="service_type_id"
                        required
                        class="w-full border rounded-lg p-3"
                    >

                        <option value="">
                            Pilih Service
                        </option>

                        <?php while($service = $serviceTypes->fetch_assoc()): ?>

                            <option value="<?= $service['id'] ?>">

                                <?= htmlspecialchars(
                                    $service['name']
                                ) ?>

                                -

                                Rp <?= number_format(
                                    $service['base_price']
                                ) ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div>

                    <label class="block mb-2 font-medium">
                        Tanggal Booking
                    </label>

                    <input
                        type="date"
                        name="booking_date"
                        min="<?= date('Y-m-d') ?>"
                        required
                        class="w-full border rounded-lg p-3"
                    >

                </div>

                <div>

                    <label class="block mb-2 font-medium">
                        Jadwal Service
                    </label>

                    <select
                        name="time_slot_id"
                        required
                        class="w-full border rounded-lg p-3"
                    >

                        <option value="">
                            Pilih Jadwal
                        </option>

                        <?php while($slot = $timeSlots->fetch_assoc()): ?>

                            <option value="<?= $slot['id'] ?>">

                                <?= ucfirst($slot['day']) ?>

                                |

                                <?= substr($slot['start_time'],0,5) ?>

                                -

                                <?= substr($slot['end_time'],0,5) ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div>

                    <label class="block mb-2 font-medium">
                        Keluhan
                    </label>

                    <textarea
                        name="customer_complaint"
                        rows="4"
                        class="w-full border rounded-lg p-3"
                        placeholder="Jelaskan keluhan motor..."
                    ></textarea>

                </div>

                <button
                    type="submit"
                    class="bg-[#8E1616] hover:bg-[#6d1111]
                           text-white px-6 py-3 rounded-lg"
                >
                    Buat Booking
                </button>

            </form>

        </div>

    </div>

</div>
```

</div>

</body>
</html>