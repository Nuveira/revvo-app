<?php
// proses users - handle create / edit / delete, redirect setelah selesai
session_start();
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/auth.php';
checkRole(['admin']);

$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users.php');
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    create_user($conn, $_POST);
} elseif ($action === 'edit') {
    edit_user($conn, $_POST, $user_id);
} elseif ($action === 'delete') {
    delete_user($conn, $_POST, $user_id);
} else {
    header('Location: users.php?msg=error');
}
exit;

// -------------------------------------------------------

function create_user($conn, $post) {
    $name   = trim($post['name'] ?? '');
    $email  = trim($post['email'] ?? '');
    $pass   = $post['password'] ?? '';
    $role   = $post['role_baru'] ?? 'customer';
    $phone  = trim($post['phone'] ?? '');
    $status = $post['status'] ?? 'active';

    if ($name === '' || $email === '' || $pass === '') {
        header('Location: users.php?show=create&msg=error');
        exit;
    }

    // cek email sudah terdaftar
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        header('Location: users.php?show=create&msg=email_exists');
        exit;
    }
    $stmt->close();

    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role, phone, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $hash, $role, $phone, $status);
    $stmt->execute();
    $stmt->close();

    header('Location: users.php?msg=created');
    exit;
}

function edit_user($conn, $post, $current_user_id) {
    $id     = (int)($post['id'] ?? 0);
    $name   = trim($post['name'] ?? '');
    $email  = trim($post['email'] ?? '');
    $pass   = $post['password'] ?? '';
    $role   = $post['role_baru'] ?? 'customer';
    $phone  = trim($post['phone'] ?? '');
    $status = $post['status'] ?? 'active';

    if ($id === 0 || $name === '' || $email === '') {
        header('Location: users.php?msg=error');
        exit;
    }

    // cek email sudah dipakai user lain
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        header('Location: users.php?show=edit&id=' . $id . '&msg=email_exists');
        exit;
    }
    $stmt->close();

    if ($pass !== '') {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password_hash=?, role=?, phone=?, status=? WHERE id=?");
        $stmt->bind_param("ssssssi", $name, $email, $hash, $role, $phone, $status, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=?, phone=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", $name, $email, $role, $phone, $status, $id);
    }
    $stmt->execute();
    $stmt->close();

    header('Location: users.php?msg=updated');
    exit;
}

function delete_user($conn, $post, $current_user_id) {
    $id = (int)($post['id'] ?? 0);

    if ($id === 0 || $id === (int)$current_user_id) {
        header('Location: users.php?msg=error');
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header('Location: users.php?msg=deleted');
    exit;
}