<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['customer']);

$userId = $_SESSION['user_id'] ?? 0;
$bookingId = (int) ($_GET['id'] ?? 0);

if (!$bookingId) {
    header('Location: history.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Customer
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
| Cek Booking
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

    $_SESSION['error'] =
        'Booking tidak ditemukan';

    header('Location: history.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Hanya queued boleh dihapus
|--------------------------------------------------------------------------
*/
if ($booking['status'] !== 'queued') {

    $_SESSION['error'] =
        'Booking yang sudah diproses tidak dapat dihapus';

    header('Location: history.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Delete Booking
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    DELETE FROM bookings
    WHERE id = ?
");

$stmt->bind_param("i", $bookingId);

if ($stmt->execute()) {

    $_SESSION['success'] =
        'Booking berhasil dihapus';

} else {

    $_SESSION['error'] =
        'Gagal menghapus booking';
}

header('Location: history.php');
exit;