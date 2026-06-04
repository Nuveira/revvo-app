<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['customer']);

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Ambil Customer
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT *
FROM customers
WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$customer = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$customer) {
    die('Data customer tidak ditemukan');
}

$customer_id = $customer['id'];

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

$stmt->bind_param(
    "i",
    $customer_id
);

$stmt->execute();

$motors = $stmt
    ->get_result()
    ->fetch_all(MYSQLI_ASSOC);

/*
|--------------------------------------------------------------------------
| Service Types
|--------------------------------------------------------------------------
*/

$service_types = $conn
    ->query("
    SELECT *
    FROM service_types
    ORDER BY name ASC
")
    ->fetch_all(MYSQLI_ASSOC);

/*
|--------------------------------------------------------------------------
| Time Slots
|--------------------------------------------------------------------------
*/

$time_slots = $conn
    ->query("
    SELECT *
    FROM time_slots
    ORDER BY day,start_time
")
    ->fetch_all(MYSQLI_ASSOC);

include '../../includes/header.php';
?>

<div class="container">

    <h2>Buat Booking Servis</h2>

    <form method="post" action="proses_booking.php">

        <input type="hidden" name="action" value="create">

        <div>

            <label>Motor</label>

            <select name="motor_id" required>

                <option value="">
                    Pilih Motor
                </option>

                <?php foreach ($motors as $motor): ?>

                    <option value="<?= $motor['id'] ?>">

                        <?= htmlspecialchars(
                            $motor['brand']
                            . ' ' .
                            $motor['model']
                        ) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <br>

        <div>

            <label>Jenis Servis</label>

            <select name="service_type_id" required>

                <option value="">
                    Pilih Servis
                </option>

                <?php foreach ($service_types as $service): ?>

                    <option value="<?= $service['id'] ?>">

                        <?= htmlspecialchars(
                            $service['name']
                        ) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <br>

        <div>

            <label>Time Slot</label>

            <select name="time_slot_id" required>

                <option value="">
                    Pilih Jadwal
                </option>

                <?php foreach ($time_slots as $slot): ?>

                    <option value="<?= $slot['id'] ?>">

                        <?= htmlspecialchars(
                            $slot['day']
                        ) ?>

                        -

                        <?= $slot['start_time'] ?>

                        s/d

                        <?= $slot['end_time'] ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <br>

        <div>

            <label>Tanggal Booking</label>

            <input type="date" name="booking_date" required>

        </div>

        <br>

        <div>

            <label>Keluhan</label>

            <textarea name="customer_complaint" rows="4"></textarea>

        </div>

        <br>

        <button type="submit">

            Buat Booking

        </button>

    </form>

</div>

<?php include '../../includes/footer.php'; ?>