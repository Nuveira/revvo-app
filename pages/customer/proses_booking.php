<?php

session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['customer']);

$user_id = $_SESSION['user_id'];

$action = $_POST['action'] ?? '';

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
| CREATE BOOKING
|--------------------------------------------------------------------------
*/

if ($action === 'create') {

    $stmt = $conn->prepare("
    INSERT INTO bookings
    (
        customer_id,
        motor_id,
        service_type_id,
        time_slot_id,
        booking_date,
        service_price,
        total_price,
        status,
        customer_complaint
    )
    VALUES
    (
        ?, ?, ?, ?, ?,
        0,
        0,
        'queued',
        ?
    )
    ");

    $stmt->bind_param(
        "iiiiss",
        $customer_id,
        $_POST['motor_id'],
        $_POST['service_type_id'],
        $_POST['time_slot_id'],
        $_POST['booking_date'],
        $_POST['customer_complaint']
    );

    $stmt->execute();

    header("Location: booking_history.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE BOOKING
|--------------------------------------------------------------------------
*/

if ($action === 'update') {

    $booking_id = (int) $_POST['id'];

    /*
    | Pastikan booking milik customer
    */

    $stmt = $conn->prepare("
    SELECT *
    FROM bookings
    WHERE id = ?
    AND customer_id = ?
    ");

    $stmt->bind_param(
        "ii",
        $booking_id,
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
    | Hanya bisa diubah jika queued
    */

    if ($booking['status'] !== 'queued') {
        die('Booking tidak dapat diubah');
    }

    $stmt = $conn->prepare("
    UPDATE bookings
    SET
        service_type_id = ?,
        time_slot_id = ?,
        booking_date = ?,
        customer_complaint = ?
    WHERE id = ?
    ");

    $stmt->bind_param(
        "iissi",
        $_POST['service_type_id'],
        $_POST['time_slot_id'],
        $_POST['booking_date'],
        $_POST['customer_complaint'],
        $booking_id
    );

    $stmt->execute();

    header("Location: booking_detail.php?id=" . $booking_id);
    exit;
}

/*
|--------------------------------------------------------------------------
| CANCEL BOOKING
|--------------------------------------------------------------------------
*/

if ($action === 'cancel') {

    $booking_id = (int) $_POST['id'];

    $stmt = $conn->prepare("
    SELECT *
    FROM bookings
    WHERE id = ?
    AND customer_id = ?
    ");

    $stmt->bind_param(
        "ii",
        $booking_id,
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
    | Hanya queued yang boleh dibatalkan
    */

    if ($booking['status'] !== 'queued') {
        die('Booking tidak dapat dibatalkan');
    }

    $stmt = $conn->prepare("
    UPDATE bookings
    SET status = 'cancelled'
    WHERE id = ?
    ");

    $stmt->bind_param(
        "i",
        $booking_id
    );

    $stmt->execute();

    header("Location: booking_history.php");
    exit;
}