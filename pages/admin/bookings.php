<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
checkRole(['admin']);

$search = $_GET['search'] ?? '';
$like = "%{$search}%";

$stmt = $conn->prepare("
SELECT b.*, u.name customer_name
FROM bookings b
JOIN customers c ON b.customer_id = c.id
JOIN users u ON c.user_id = u.id
WHERE (?='' OR u.name LIKE ?)
ORDER BY b.id DESC
");
$stmt->bind_param("ss",$search,$like);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
