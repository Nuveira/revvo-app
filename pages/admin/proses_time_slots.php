<?php
// proses users - handle create / edit / delete, redirect setelah selesai
session_start();
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/auth.php';
checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: time_slots.php');
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    create_time_slot($conn, $_POST);
} elseif ($action === 'edit') {
    edit_time_slot($conn, $_POST);
} elseif ($action === 'delete') {
    delete_time_slot($conn, $_POST);
} else {
    header('Location: time_slots.php?msg=error');
}
exit;

// -------------------------------------------------------

function create_time_slot($conn, $post) {
    $day = trim($post['day'] ?? '');
    $start_time = trim($post['start_time'] ?? '');
    $end_time = trim($post['end_time'] ?? '');
    $capacity = (int)($post['capacity'] ?? 0);
    $status = $post['status'] ?? 'active';

    if ($day === '' || $start_time === '' || $end_time === '' || $capacity <= 0) {
        header('Location: time_slots.php?show=create&msg=error');
        exit;
    }
    // Jam selesai harus lebih besar dari jam mulai
    if ($end_time <= $start_time) {
        header('Location: time_slots.php?show=create&msg=invalid_time');
        exit;
    }

    // Cek slot identik
    $stmt = $conn->prepare("SELECT id FROM time_slots WHERE day = ? AND start_time = ? AND end_time = ?");
    $stmt->bind_param("sss", $day, $start_time, $end_time);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        header('Location: time_slots.php?show=create&msg=slot_exists');
        exit;
    }
    $stmt->close();

    // Cek slot bentrok
    $stmt = $conn->prepare("SELECT id FROM time_slots WHERE day = ? AND (start_time < ? AND end_time > ?)");
    $stmt->bind_param("sss", $day, $end_time, $start_time);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        header('Location: time_slots.php?show=create&msg=slot_overlap');
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO time_slots (day, start_time, end_time, capacity, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $day, $start_time, $end_time, $capacity, $status);
    $stmt->execute();
    $stmt->close();

    header('Location: time_slots.php?msg=created');
    exit;
}

function edit_time_slot($conn, $post) {
    $id     = (int)($post['id'] ?? 0);
    $day = trim($post['day'] ?? '');
    $start_time = trim($post['start_time'] ?? '');
    $end_time = trim($post['end_time'] ?? '');
    $capacity = (int)($post['capacity'] ?? 0);
    $status = $post['status'] ?? 'active';

    if ($id <= 0 || $day === '' || $start_time === '' || $end_time === '' || $capacity <= 0) {
        header('Location: time_slots.php?msg=error');
        exit;
    }

    // Jam selesai harus lebih besar dari jam mulai
    if ($end_time <= $start_time) {
        header('Location: time_slots.php?show=edit&id=' . $id . '&msg=invalid_time');
        exit;
    }

    // Cek slot identik (selain dirinya sendiri)
    $stmt = $conn->prepare("SELECT id FROM time_slots WHERE day = ? AND start_time = ? AND end_time = ? AND id != ?");
    $stmt->bind_param("sssi", $day, $start_time, $end_time, $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        header('Location: time_slots.php?show=edit&id=' . $id . '&msg=slot_exists');
        exit;
    }
    $stmt->close();

    // Cek slot bentrok (selain dirinya sendiri)
    $stmt = $conn->prepare("SELECT id FROM time_slots WHERE day = ? AND id != ? AND (start_time < ? AND end_time > ?)");

    $stmt->bind_param("siss",$day,$id,$end_time,$start_time);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        header('Location: time_slots.php?show=edit&id=' . $id . '&msg=slot_overlap');
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE time_slots SET day=?, start_time=?, end_time=?, capacity=?, status=? WHERE id=?");
    $stmt->bind_param("sssisi", $day, $start_time, $end_time, $capacity, $status, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: time_slots.php?msg=updated');
    exit;
}

function delete_time_slot($conn, $post) {
    $id = (int)($post['id'] ?? 0);

    if ($id <= 0) {
        header('Location: time_slots.php?msg=error');
        exit;
    }

    try {
        $stmt = $conn->prepare("DELETE FROM time_slots WHERE id = ?"); 
        $stmt->bind_param("i", $id); 
        $stmt->execute(); 
        $stmt->close(); 
        header('Location: time_slots.php?msg=deleted');
    } catch (mysqli_sql_exception $e) { 
        header('Location: time_slots.php?msg=in_use'); 
    }

    exit;
}