<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['customer']);

$user_id = $_SESSION['user_id'];

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    die('Booking tidak ditemukan');
}

/*
|--------------------------------------------------------------------------
| Ambil Customer Login
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT id
FROM customers
WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$customer = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$customer) {
    die('Customer tidak ditemukan');
}

$customer_id = $customer['id'];

/*
|--------------------------------------------------------------------------
| Ambil Booking
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT *
FROM bookings
WHERE id = ?
AND customer_id = ?
");

$stmt->bind_param(
    "ii",
    $id,
    $customer_id
);

$stmt->execute();

$booking = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$booking) {
    die('Booking tidak ditemukan');
}

/*
|--------------------------------------------------------------------------
| Dropdown Data
|--------------------------------------------------------------------------
*/

$service_types = $conn
    ->query("
    SELECT *
    FROM service_types
    ORDER BY name
")
    ->fetch_all(MYSQLI_ASSOC);

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

    <h2>Detail Booking</h2>

    <table border="1">

        <tr>
            <td>ID</td>
            <td><?= $booking['id'] ?></td>
        </tr>

        <tr>
            <td>Status</td>
            <td><?= $booking['status'] ?></td>
        </tr>

        <tr>
            <td>Tanggal</td>
            <td><?= $booking['booking_date'] ?></td>
        </tr>

        <tr>
            <td>Keluhan</td>
            <td><?= htmlspecialchars(
                $booking['customer_complaint']
            ) ?></td>
        </tr>

    </table>

    <br>

<?php if($booking['status'] === 'queued'): ?>

    <h3>Edit Booking</h3>

    <form
        method="post"
        action="proses_booking.php"
    >

        <input
            type="hidden"
            name="action"
            value="update"
        >

        <input
            type="hidden"
            name="id"
            value="<?= $booking['id'] ?>"
        >

        <div>

            <label>Tanggal Booking</label>

            <input
                type="date"
                name="booking_date"
                value="<?= $booking['booking_date'] ?>"
                required
            >

        </div>

        <br>

        <div>

            <label>Jenis Service</label>

            <select
                name="service_type_id"
                required
            >

                <?php foreach($service_types as $service): ?>

                    <option
                        value="<?= $service['id'] ?>"
                        <?= $service['id'] == $booking['service_type_id']
                            ? 'selected'
                            : '' ?>
                    >

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

            <select
                name="time_slot_id"
                required
            >

                <?php foreach($time_slots as $slot): ?>

                    <option
                        value="<?= $slot['id'] ?>"
                        <?= $slot['id'] == $booking['time_slot_id']
                            ? 'selected'
                            : '' ?>
                    >

                        <?= htmlspecialchars(
                            $slot['day']
                        ) ?>

                        -

                        <?= $slot['start_time'] ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <br>

        <div>

            <label>Keluhan</label>

            <textarea
                name="customer_complaint"
            ><?= htmlspecialchars(
                $booking['customer_complaint']
            ) ?></textarea>

        </div>

        <br>

        <button type="submit">

            Update Booking

        </button>

    </form>

    <br>

    <form
        method="post"
        action="proses_booking.php"
        onsubmit="return confirm('Batalkan booking ini?')"
    >

        <input
            type="hidden"
            name="action"
            value="cancel"
        >

        <input
            type="hidden"
            name="id"
            value="<?= $booking['id'] ?>"
        >

        <button type="submit">

            Batalkan Booking

        </button>

    </form>

<?php endif; ?>

</div>

<?php include '../../includes/footer.php'; ?>