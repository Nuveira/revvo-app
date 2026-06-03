<?php 
checkRole(['customer']);

// Ambil data user dari session
$user_id = $_SESSION['user_id'] ?? null;
$nama = 'Guest';
$role = '';
$profile_photo = null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT name, role, profile_photo FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $nama = $row['name'];
        $role = $row['role'];
        $profile_photo = $row['profile_photo'];
    }
    $stmt->close();

    $stmt = $conn->prepare("SELECT id FROM customers WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $customer_id = (int) $row['id'];
    }
    $stmt->close();
}
?>