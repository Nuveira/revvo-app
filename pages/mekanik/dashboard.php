<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['mechanic']);

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Ambil Data Mekanik Login
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT *
FROM mechanics
WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$mechanic = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$mechanic) {
    die('Data mekanik tidak ditemukan');
}

$mechanic_id = $mechanic['id'];

/*
|--------------------------------------------------------------------------
| Total Queued
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT COUNT(*) total
FROM bookings
WHERE mechanic_id = ?
AND status = 'queued'
");

$stmt->bind_param("i", $mechanic_id);
$stmt->execute();

$queued = $stmt
    ->get_result()
    ->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| Total In Progress
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT COUNT(*) total
FROM bookings
WHERE mechanic_id = ?
AND status = 'in_progress'
");

$stmt->bind_param("i", $mechanic_id);
$stmt->execute();

$in_progress = $stmt
    ->get_result()
    ->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| Total Completed
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT COUNT(*) total
FROM bookings
WHERE mechanic_id = ?
AND status = 'completed'
");

$stmt->bind_param("i", $mechanic_id);
$stmt->execute();

$completed = $stmt
    ->get_result()
    ->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| 5 Tugas Terbaru
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT
    b.*,
    st.name AS service_name
FROM bookings b
LEFT JOIN service_types st
    ON st.id = b.service_type_id
WHERE b.mechanic_id = ?
ORDER BY b.id DESC
LIMIT 5
");

$stmt->bind_param("i", $mechanic_id);
$stmt->execute();

$latest_tasks = $stmt
    ->get_result()
    ->fetch_all(MYSQLI_ASSOC);

include '../../includes/header.php';
?>

<div class="container">

    <h2>Dashboard Mekanik</h2>

    <hr>

    <div>

        <h3>Tugas Menunggu</h3>
        <p>
            <?= $queued ?>
        </p>

    </div>

    <div>

        <h3>Sedang Dikerjakan</h3>
        <p>
            <?= $in_progress ?>
        </p>

    </div>

    <div>

        <h3>Tugas Selesai</h3>
        <p>
            <?= $completed ?>
        </p>

    </div>

    <hr>

    <h3>5 Tugas Terbaru</h3>

    <table border="1" cellpadding="5">

        <tr>
            <th>ID Booking</th>
            <th>Tanggal</th>
            <th>Service</th>
            <th>Status</th>
        </tr>

        <?php foreach ($latest_tasks as $task): ?>

            <tr>
                <td>
                    <?= $task['id'] ?>
                </td>
                <td>
                    <?= $task['booking_date'] ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $task['service_name']
                    ) ?>
                </td>
                <td>
                        <?= $task['status'] ?>
                </td>

            </tr>

        <?php endforeach; ?>

    </table>

    <br>

    <a href="my_tasks.php">
        Lihat Semua Tugas
    </a>

</div>

<?php include '../../includes/footer.php'; ?>