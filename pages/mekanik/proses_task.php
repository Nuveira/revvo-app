<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['mechanic']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my_tasks.php');
    exit;
}

$userId = $_SESSION['user_id'] ?? 0;

$bookingId = (int)($_POST['booking_id'] ?? 0);

$status = trim($_POST['status'] ?? '');

$mechanicNote = trim($_POST['mechanic_note'] ?? '');

if (!$bookingId) {

    $_SESSION['error'] = 'Booking tidak ditemukan';

    header('Location: my_tasks.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Cari mekanik login
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT id
    FROM mechanics
    WHERE user_id = ?
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$mechanic = $stmt->get_result()->fetch_assoc();

if (!$mechanic) {

    $_SESSION['error'] = 'Data mekanik tidak ditemukan';

    header('Location: my_tasks.php');
    exit;
}

$mechanicId = $mechanic['id'];

/*
|--------------------------------------------------------------------------
| Validasi task milik mekanik
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT id
    FROM bookings
    WHERE id = ?
    AND mechanic_id = ?
");

$stmt->bind_param(
    "ii",
    $bookingId,
    $mechanicId
);

$stmt->execute();

$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {

    $_SESSION['error'] =
        'Task tidak ditemukan';

    header('Location: my_tasks.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Validasi status
|--------------------------------------------------------------------------
*/
$allowedStatus = [
    'queued',
    'in_progress',
    'completed'
];

if (!in_array($status, $allowedStatus)) {

    $_SESSION['error'] =
        'Status tidak valid';

    header(
        'Location: task_detail.php?id='
        .$bookingId
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Update booking
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    UPDATE bookings
    SET
        status = ?,
        mechanic_note = ?
    WHERE id = ?
");

$stmt->bind_param(
    "ssi",
    $status,
    $mechanicNote,
    $bookingId
);

if ($stmt->execute()) {

    $_SESSION['success'] =
        'Task berhasil diperbarui';

} else {

    $_SESSION['error'] =
        'Gagal memperbarui task';
}

header(
    'Location: task_detail.php?id='
    .$bookingId
);

exit;