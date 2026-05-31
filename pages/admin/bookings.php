<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
checkRole(['admin']);

$search = $_GET['search'] ?? '';
$like = "%$search%";

$stmt = $conn->prepare("
SELECT b.*, u.name customer_name
FROM bookings b
JOIN customers c ON b.customer_id=c.id
JOIN users u ON c.user_id=u.id
WHERE (?='' OR u.name LIKE ?)
ORDER BY b.id DESC");
$stmt->bind_param("ss",$search,$like);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<h2>Bookings</h2>

<form method="get">
<input name="search" placeholder="Cari customer">
<button>Cari</button>
</form>

<form method="post" action="proses_bookings.php">
<input type="hidden" name="action" value="create">
<input name="customer_id" placeholder="Customer ID" required>
<input name="motor_id" placeholder="Motor ID" required>
<input name="service_type_id" placeholder="Service Type ID" required>
<input name="time_slot_id" placeholder="Time Slot ID" required>
<input type="date" name="booking_date" required>
<textarea name="customer_complaint" placeholder="Keluhan"></textarea>
<button>Simpan</button>
</form>

<table border="1">
<tr><th>ID</th><th>Customer</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>
<?php foreach($bookings as $b): ?>
<tr>
<td><?= $b['id'] ?></td>
<td><?= htmlspecialchars($b['customer_name']) ?></td>
<td><?= $b['booking_date'] ?></td>
<td><?= $b['status'] ?></td>
<td>
<form method="post" action="proses_bookings.php">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= $b['id'] ?>">
<button>Delete</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
