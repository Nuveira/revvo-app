<?php
// proses users - handle create / edit / delete, redirect setelah selesai
session_start();
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/auth.php';
checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: service_types.php');
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    create_service_type($conn, $_POST);
} elseif ($action === 'edit') {
    edit_service_type($conn, $_POST);
} elseif ($action === 'delete') {
    delete_service_type($conn, $_POST);
} else {
    header('Location: service_types.php?msg=error');
}
exit;

// -------------------------------------------------------

function create_service_type($conn, $post) {
    $name = trim($post['name'] ?? '');
    $description = trim($post['description'] ?? '');
    $duration = (int)($post['estimated_duration_minutes'] ?? 0);
    $price = (float)($post['base_price'] ?? 0);
    $status = $post['status'] ?? 'active';

    if ($name === '' || $duration <= 0 || $price < 0) {
        header('Location: service_types.php?show=create&msg=error');
        exit;
    }

    // cek nama layanan service sudah terdaftar
    $stmt = $conn->prepare("SELECT id FROM service_types WHERE LOWER(name) = LOWER(?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        header('Location: service_types.php?show=create&msg=name_exists');
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO service_types (name, description, estimated_duration_minutes, base_price, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssids", $name, $description, $duration, $price, $status);
    $stmt->execute();
    $stmt->close();

    header('Location: service_types.php?msg=created');
    exit;
}

function edit_service_type($conn, $post) {
    $id     = (int)($post['id'] ?? 0);
    $name = trim($post['name'] ?? '');
    $description = trim($post['description'] ?? '');
    $duration = (int)($post['estimated_duration_minutes'] ?? 0);
    $price = (float)($post['base_price'] ?? 0);
    $status = $post['status'] ?? 'active';

    if ($id <= 0 || $name === '' || $duration <= 0 || $price < 0) {
        header('Location: service_types.php?msg=error');
        exit;
    }

    // cek email sudah dipakai user lain
    $stmt = $conn->prepare("SELECT id FROM service_types WHERE LOWER(name) = LOWER(?) AND id != ?");
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        header('Location: service_types.php?show=edit&id=' . $id . '&msg=email_exists');
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE service_types SET name=?, description=?, estimated_duration_minutes=?, base_price=?, status=? WHERE id=?");
    $stmt->bind_param("ssidsi", $name, $description, $duration, $price, $status, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: service_types.php?msg=updated');
    exit;
}

function delete_service_type($conn, $post) {
    $id = (int)($post['id'] ?? 0);

    if ($id <= 0) {
        header('Location: service_types.php?msg=error');
        exit;
    }

    try {
        $stmt = $conn->prepare(" DELETE FROM service_types WHERE id = ? "); $stmt->bind_param("i", $id); 
        $stmt->execute(); 
        $stmt->close(); 
        header('Location: service_types.php?msg=deleted');
    } catch (mysqli_sql_exception $e) { 
        header('Location: service_types.php?msg=in_use'); 
    }

    exit;
}