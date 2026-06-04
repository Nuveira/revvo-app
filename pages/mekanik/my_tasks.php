<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['mechanic']);

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Cari mechanic_id
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT id
FROM mechanics
WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$mechanic = $stmt->get_result()->fetch_assoc();

if (!$mechanic) {
    die('Data mekanik tidak ditemukan');
}

$mechanic_id = $mechanic['id'];

/*
|--------------------------------------------------------------------------
| Ambil tugas mekanik
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT
    b.*,
    st.name AS service_name
FROM bookings b
JOIN service_types st
    ON st.id = b.service_type_id
WHERE b.mechanic_id = ?
ORDER BY b.id DESC
");

$stmt->bind_param("i", $mechanic_id);
$stmt->execute();

$tasks = $stmt
    ->get_result()
    ->fetch_all(MYSQLI_ASSOC);

include '../../includes/header.php';
?>

<h2>Tugas Saya</h2>

<table border="1" cellpadding="5">

    <tr>
        <th>ID</th>
        <th>Tanggal</th>
        <th>Service</th>
        <th>Status</th>
        <th>Catatan</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($tasks as $task): ?>

        <tr>

            <td><?= $task['id'] ?></td>

            <td><?= $task['booking_date'] ?></td>

            <td><?= htmlspecialchars($task['service_name']) ?></td>

            <td><?= $task['status'] ?></td>

            <td>
                <?= htmlspecialchars(
                    $task['mechanic_note']
                ) ?>
            </td>

            <td>

                <?php if ($task['status'] === 'queued'): ?>

                    <form method="post" action="proses_task.php">

                        <input type="hidden" name="action" value="start">

                        <input type="hidden" name="id" value="<?= $task['id'] ?>">

                        <button>
                            Mulai
                        </button>

                    </form>

                <?php endif; ?>

                <?php if ($task['status'] === 'in_progress'): ?>

                    <form method="post" action="proses_task.php">

                        <input type="hidden" name="action" value="complete">

                        <input type="hidden" name="id" value="<?= $task['id'] ?>">

                        <textarea name="mechanic_note" placeholder="Catatan pengerjaan" required></textarea>

                        <button>
                            Selesai
                        </button>

                    </form>

                <?php endif; ?>

            </td>

        </tr>

    <?php endforeach; ?>

</table>

<?php include '../../includes/footer.php'; ?>