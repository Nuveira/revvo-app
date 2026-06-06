<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['customer']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah_booking.php');
    exit;
}

$userId = $_SESSION['user_id'] ?? 0;

/*
|--------------------------------------------------------------------------
| Ambil customer login
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
    header('Location: tambah_booking.php');
    exit;
}

$customerId = $customer['id'];

/*
|--------------------------------------------------------------------------
| Ambil data form
|--------------------------------------------------------------------------
*/
$motorId = (int) ($_POST['motor_id'] ?? 0);
$serviceTypeId = (int) ($_POST['service_type_id'] ?? 0);
$timeSlotId = (int) ($_POST['time_slot_id'] ?? 0);

$bookingDate = trim($_POST['booking_date'] ?? '');
$complaint = trim($_POST['customer_complaint'] ?? '');

/*
|--------------------------------------------------------------------------
| Validasi
|--------------------------------------------------------------------------
*/
if (
    !$motorId ||
    !$serviceTypeId ||
    !$timeSlotId ||
    empty($bookingDate)
) {
    $_SESSION['error'] = 'Lengkapi semua data booking';
    header('Location: tambah_booking.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil harga service
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
    $_SESSION['error'] = 'Jenis service tidak ditemukan';
    header('Location: tambah_booking.php');
    exit;
}

$servicePrice = $service['base_price'];

/*
|--------------------------------------------------------------------------
| Simpan booking
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
INSERT INTO bookings
(
    customer_id,
    motor_id,
    service_type_id,
    mechanic_id,
    time_slot_id,
    booking_date,
    service_price,
    total_price,
    status,
    customer_complaint,
    condition_photo,
    mechanic_note
)
VALUES
(
    ?, ?, ?,
    NULL,
    ?,
    ?,
    ?,
    ?,
    'queued',
    ?,
    NULL,
    NULL
)
");

$stmt->bind_param(
    "iiiisdds",
    $customerId,
    $motorId,
    $serviceTypeId,
    $timeSlotId,
    $bookingDate,
    $servicePrice,
    $servicePrice,
    $complaint
);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Booking berhasil dibuat';
    header('Location: history.php');
    exit;
}

$_SESSION['error'] = 'Booking gagal dibuat';
header('Location: tambah_booking.php');
exit;