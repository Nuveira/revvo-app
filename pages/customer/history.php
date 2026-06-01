<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'History | REVVO';
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/logo.png') ?>">
</head>
<body class="font-['Plus_Jakarta_Sans'] flex h-screen">
    <?php include 'nav.php'; ?>

</body>
</html>