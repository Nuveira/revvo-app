<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
checkRole(['admin']);

$action=$_POST['action'] ?? '';

if($action==='create'){
$stmt=$conn->prepare('INSERT INTO bookings
(customer_id,motor_id,service_type_id,time_slot_id,booking_date,service_price,total_price,status,customer_complaint)
VALUES (?,?,?,?,?,0,0,"queued",?)');
$stmt->bind_param('iiiiss',
$_POST['customer_id'],
$_POST['motor_id'],
$_POST['service_type_id'],
$_POST['time_slot_id'],
$_POST['booking_date'],
$_POST['customer_complaint']);
$stmt->execute();
}

if($action==='delete'){
$stmt=$conn->prepare('DELETE FROM bookings WHERE id=?');
$stmt->bind_param('i',$_POST['id']);
$stmt->execute();
}

header('Location: bookings.php');
