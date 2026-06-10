<?php
// proses users - handle create / edit / delete, redirect setelah selesai
session_start();
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/auth.php';
checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mechanics.php');
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    create_mechanic($conn, $_POST);
} elseif ($action === 'edit') {
    edit_mechanic($conn, $_POST);
} elseif ($action === 'delete') {
    delete_mechanic($conn, $_POST);
} else {
    header('Location: mechanics.php?msg=error');
    exit;
}

// -------------------------------------------------------

function create_mechanic($conn, $post) {
    $user_id = (int)($post['user_id'] ?? 0);
    $specialization = trim($post['specialization'] ?? '');
    $availability_status = $post['availability_status'] ?? 'available';
    $notes = trim($post['notes'] ?? '');

    if ($user_id <= 0 || $specialization === '') {
        header('Location: mechanics.php?show=create&msg=error');
        exit;
    }

    // Pastikan user yang dipilih memang role mechanic
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'mechanic'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $stmt->close();
        header('Location: mechanics.php?show=create&msg=invalid_user');
        exit;
    }
    $stmt->close();

    // Cek apakah user sudah terdaftar sebagai mechanic
    $stmt = $conn->prepare("SELECT id FROM mechanics WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        header('Location: mechanics.php?show=create&msg=mechanic_exists');
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO mechanics (user_id, specialization, availability_status, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $specialization, $availability_status, $notes);
    $stmt->execute();
    $stmt->close();

    header('Location: mechanics.php?msg=created');
    exit;
}

function edit_mechanic($conn, $post) {
    $id = (int)($post['id'] ?? 0);
    $specialization = trim($post['specialization'] ?? '');
    $availability_status = $post['availability_status'] ?? 'available';
    $notes = trim($post['notes'] ?? '');

    if ($id <= 0 || $specialization === '') {
        header('Location: mechanics.php?show=edit&id=' . $id . '&msg=error');
        exit;
    }

    $stmt = $conn->prepare("UPDATE mechanics SET specialization=?, availability_status=?, notes=? WHERE id=?");
    $stmt->bind_param("sssi", $specialization, $availability_status, $notes, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: mechanics.php?msg=updated');
    exit;
}

function delete_mechanic($conn, $post) {
    $id = (int)($post['id'] ?? 0);

    if ($id <= 0) {
        header('Location: mechanics.php?msg=error');
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM mechanics WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header('Location: mechanics.php?msg=deleted');
    exit;
}