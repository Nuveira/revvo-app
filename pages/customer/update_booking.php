<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['customer']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: history.php');
    exit;
}

$userId = $_SESSION['user_id'] ?? 0;

/*
|--------------------------------------------------------------------------
| Customer
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

    $_SESSION['error'] = 'Customer tidak ditemukan';

    header('Location: history.php');
    exit;
}

$customerId = $customer['id'];

/*
|--------------------------------------------------------------------------
| Data Form
|--------------------------------------------------------------------------
*/
$bookingId = (int) ($_POST['booking_id'] ?? 0);
$motorId = (int) ($_POST['motor_id'] ?? 0);
$serviceTypeId = (int) ($_POST['service_type_id'] ?? 0);

$bookingDate = trim($_POST['booking_date'] ?? '');

$complaint = trim(
    $_POST['customer_complaint'] ?? ''
);

/*
|--------------------------------------------------------------------------
| Booking
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
    $bookingId,
    $customerId
);

$stmt->execute();

$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {

    $_SESSION['error'] = 'Booking tidak ditemukan';

    header('Location: history.php');
    exit;
}

if ($booking['status'] !== 'queued') {

    $_SESSION['error'] =
        'Booking yang sudah diproses tidak dapat diubah';

    header('Location: history.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Harga Service
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT base_price
    FROM service_types
    WHERE id = ?
");

$stmt->bind_param("i", $serviceTypeId);
$stmt->execute();

$service = $stmt->get_result()->fetch_assoc();

if (!$service) {

    $_SESSION['error'] =
        'Jenis service tidak ditemukan';

    header('Location: history.php');
    exit;
}

$servicePrice = $service['base_price'];

/*
|--------------------------------------------------------------------------
| Update Booking
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
UPDATE bookings
SET
    motor_id = ?,
    service_type_id = ?,
    booking_date = ?,
    customer_complaint = ?,
    service_price = ?,
    total_price = ?
WHERE id = ?
");

$stmt->bind_param(
    "iissddi",
    $motorId,
    $serviceTypeId,
    $bookingDate,
    $complaint,
    $servicePrice,
    $servicePrice,
    $bookingId
);

if ($stmt->execute()) {

    $_SESSION['success'] =
        'Booking berhasil diperbarui';

} else {

    $_SESSION['error'] =
        'Gagal memperbarui booking';
}

header('Location: history.php');
exit;