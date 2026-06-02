<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['mechanic']);

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT id
FROM mechanics
WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$mechanic = $stmt->get_result()->fetch_assoc();

$mechanic_id = $mechanic['id'];

$stmt = $conn->prepare("
SELECT *
FROM bookings
WHERE mechanic_id = ?
AND status = 'completed'
ORDER BY id DESC
");

$stmt->bind_param(
    "i",
    $mechanic_id
);

$stmt->execute();

$history = $stmt
    ->get_result()
    ->fetch_all(MYSQLI_ASSOC);

include '../../includes/header.php';
?>

<h2>Histori Pekerjaan</h2>

<table border="1">

    <tr>
        <th>ID</th>
        <th>Tanggal</th>
        <th>Catatan</th>
    </tr>

    <?php foreach ($history as $job): ?>

        <tr>

            <td>
                <?= $job['id'] ?>
            </td>

            <td>
                <?= $job['booking_date'] ?>
            </td>

            <td>
                <?= htmlspecialchars(
                    $job['mechanic_note']
                ) ?>
            </td>

        </tr>

    <?php endforeach; ?>

</table>

<?php include '../../includes/footer.php'; ?>