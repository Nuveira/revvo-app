<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Detail Task | REVVO';

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['mechanic']);

$userId = $_SESSION['user_id'] ?? 0;
$bookingId = (int)($_GET['id'] ?? 0);

if (!$bookingId) {
    header('Location: my_tasks.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Mekanik
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT id
    FROM mechanics
    WHERE user_id = ?
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$mechanic = $stmt->get_result()->fetch_assoc();

if (!$mechanic) {
    die('Data mekanik tidak ditemukan');
}

$mechanicId = $mechanic['id'];

/*
|--------------------------------------------------------------------------
| Detail Task
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT
    b.*,

    m.brand,
    m.model,
    m.plate_number,

    st.name AS service_name,
    st.base_price,

    c.id AS customer_id,
    u.name AS customer_name

FROM bookings b

JOIN motors m
    ON b.motor_id = m.id

JOIN service_types st
    ON b.service_type_id = st.id

JOIN customers c
    ON b.customer_id = c.id

JOIN users u
    ON c.user_id = u.id

WHERE b.id = ?
AND b.mechanic_id = ?
");

$stmt->bind_param(
    "ii",
    $bookingId,
    $mechanicId
);

$stmt->execute();

$task = $stmt->get_result()->fetch_assoc();

if (!$task) {
    die('Task tidak ditemukan');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<title><?= htmlspecialchars($pageTitle) ?></title>

</head>

<body class="font-['Plus_Jakarta_Sans']">

<div class="flex h-screen">

    <?php include 'nav.php'; ?>

    <div class="flex-1 bg-gray-100 overflow-auto">

        <!-- HEADER -->

        <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] p-5">

            <p class="text-[#FF0000] uppercase text-sm">
                Task Detail
            </p>

            <h1 class="text-4xl text-white py-2">
                Booking #<?= $task['id']; ?>
            </h1>

            <p class="text-white">
                Detail pekerjaan servis motor.
            </p>

        </div>

        <!-- CONTENT -->

        <div class="p-6">

            <div class="grid md:grid-cols-2 gap-5">

                <!-- DATA BOOKING -->

                <div class="bg-white border border-[#eadede] rounded-lg p-6 shadow-sm">

                    <h2 class="text-xl font-semibold mb-5">
                        Informasi Booking
                    </h2>

                    <div class="space-y-4">

                        <div>
                            <p class="text-gray-500 text-sm">
                                Customer
                            </p>

                            <p class="font-medium">
                                <?= htmlspecialchars($task['customer_name']); ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-sm">
                                Motor
                            </p>

                            <p class="font-medium">

                                <?= htmlspecialchars(
                                    $task['brand']
                                    .' '.
                                    $task['model']
                                ); ?>

                            </p>

                            <p class="text-sm text-gray-500">

                                <?= htmlspecialchars(
                                    $task['plate_number']
                                ); ?>

                            </p>

                        </div>

                        <div>
                            <p class="text-gray-500 text-sm">
                                Service
                            </p>

                            <p class="font-medium">
                                <?= htmlspecialchars($task['service_name']); ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-sm">
                                Tanggal Booking
                            </p>

                            <p>
                                <?= date('d M Y', strtotime($task['booking_date'])); ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-sm">
                                Keluhan Customer
                            </p>

                            <p>
                                <?= nl2br(htmlspecialchars($task['customer_complaint'])); ?>
                            </p>
                        </div>

                    </div>

                </div>

                <!-- FORM UPDATE -->

                <div class="bg-white border border-[#eadede] rounded-lg p-6 shadow-sm">

                    <h2 class="text-xl font-semibold mb-5">
                        Update Pekerjaan
                    </h2>

                    <form
                        action="proses_task.php"
                        method="POST"
                        class="space-y-5"
                    >

                        <input
                            type="hidden"
                            name="booking_id"
                            value="<?= $task['id']; ?>"
                        >

                        <div>

                            <label class="block mb-2 font-medium">
                                Status
                            </label>

                            <select
                                name="status"
                                class="w-full border rounded-lg p-3"
                                required
                            >

                                <option
                                    value="queued"
                                    <?= $task['status'] == 'queued' ? 'selected' : ''; ?>
                                >
                                    Queued
                                </option>

                                <option
                                    value="in_progress"
                                    <?= $task['status'] == 'in_progress' ? 'selected' : ''; ?>
                                >
                                    In Progress
                                </option>

                                <option
                                    value="completed"
                                    <?= $task['status'] == 'completed' ? 'selected' : ''; ?>
                                >
                                    Completed
                                </option>

                            </select>

                        </div>

                        <div>

                            <label class="block mb-2 font-medium">
                                Catatan Mekanik
                            </label>

                            <textarea
                                name="mechanic_note"
                                rows="8"
                                class="w-full border rounded-lg p-3"
                            ><?= htmlspecialchars($task['mechanic_note']); ?></textarea>

                        </div>

                        <button
                            type="submit"
                            class="bg-[#8E1616] text-white px-6 py-3 rounded-lg hover:bg-[#6f1111]"
                        >
                            Simpan Perubahan
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>