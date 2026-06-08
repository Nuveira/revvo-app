<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Detail Tugas | REVVO';

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['mechanic']);

$userId = $_SESSION['user_id'] ?? 0;

if (!isset($_GET['id'])) {
    header('Location: my_tasks.php');
    exit;
}

$bookingId = (int) $_GET['id'];

/*
|--------------------------------------------------------------------------
| Data Mekanik
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT m.id
    FROM mechanics m
    JOIN users u ON m.user_id = u.id
    WHERE u.id = ?
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
| Tambah Sparepart
|--------------------------------------------------------------------------
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['add_part'])
) {

    $partId = (int) $_POST['spare_part_id'];
    $qty = (int) $_POST['qty'];

    $stmt = $conn->prepare("
        SELECT *
        FROM spare_parts
        WHERE id = ?
    ");

    $stmt->bind_param("i", $partId);
    $stmt->execute();

    $part = $stmt->get_result()->fetch_assoc();

    if ($part && $qty > 0) {

        $price = $part['price'];
        $subtotal = $price * $qty;

        $stmt = $conn->prepare("
            INSERT INTO booking_parts
            (
                booking_id,
                spare_part_id,
                qty,
                price_at_time,
                subtotal
            )
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iiidd",
            $bookingId,
            $partId,
            $qty,
            $price,
            $subtotal
        );

        $stmt->execute();

        $stmt = $conn->prepare("
            UPDATE spare_parts
            SET stock = stock - ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "ii",
            $qty,
            $partId
        );

        $stmt->execute();

        $_SESSION['success'] =
            'Sparepart berhasil ditambahkan';

        header(
            "Location: task_detail.php?id=".$bookingId
        );
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Simpan Catatan Mekanik
|--------------------------------------------------------------------------
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['save_note'])
) {

    $note = trim($_POST['mechanic_note']);

    $stmt = $conn->prepare("
        UPDATE bookings
        SET mechanic_note = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "si",
        $note,
        $bookingId
    );

    $stmt->execute();

    $_SESSION['success'] =
        'Catatan berhasil disimpan';

    header(
        "Location: task_detail.php?id=".$bookingId
    );
    exit;
}

/*
|--------------------------------------------------------------------------
| Mulai Kerjakan
|--------------------------------------------------------------------------
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['start_job'])
) {

    $stmt = $conn->prepare("
        UPDATE bookings
        SET status='in_progress'
        WHERE id=?
    ");

    $stmt->bind_param(
        "i",
        $bookingId
    );

    $stmt->execute();

    $_SESSION['success'] =
        'Status diubah menjadi In Progress';

    header(
        "Location: task_detail.php?id=".$bookingId
    );
    exit;
}

/*
|--------------------------------------------------------------------------
| Selesaikan Pekerjaan
|--------------------------------------------------------------------------
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['finish_job'])
) {

    $stmt = $conn->prepare("
        UPDATE bookings
        SET status='completed'
        WHERE id=?
    ");

    $stmt->bind_param(
        "i",
        $bookingId
    );

    $stmt->execute();

    $_SESSION['success'] =
        'Pekerjaan berhasil diselesaikan';

    header(
        "Location: task_detail.php?id=".$bookingId
    );
    exit;
}
/*
|--------------------------------------------------------------------------
| Detail Booking
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT

    b.*,

    u.name AS customer_name,
    u.phone,

    mo.brand,
    mo.model,
    mo.plate_number,
    mo.production_year,
    mo.color,

    st.name AS service_name,

    ts.start_time,
    ts.end_time

FROM bookings b

JOIN customers c
    ON b.customer_id = c.id

JOIN users u
    ON c.user_id = u.id

JOIN motors mo
    ON b.motor_id = mo.id

JOIN service_types st
    ON b.service_type_id = st.id

JOIN time_slots ts
    ON b.time_slot_id = ts.id

WHERE b.id = ?
AND b.mechanic_id = ?
");

$stmt->bind_param(
    "ii",
    $bookingId,
    $mechanicId
);

$stmt->execute();

$booking =
    $stmt->get_result()->fetch_assoc();

if (!$booking) {
    die('Tugas tidak ditemukan');
}

/*
|--------------------------------------------------------------------------
| Sparepart Aktif
|--------------------------------------------------------------------------
*/
$parts = $conn->query("
    SELECT
        id,
        sku,
        name,
        stock,
        price
    FROM spare_parts
    WHERE status = 'active'
    ORDER BY name ASC
");

/*
|--------------------------------------------------------------------------
| Sparepart Yang Sudah Dipakai
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT

    bp.id,
    bp.qty,
    bp.price_at_time,
    bp.subtotal,

    sp.sku,
    sp.name

FROM booking_parts bp

JOIN spare_parts sp
    ON bp.spare_part_id = sp.id

WHERE bp.booking_id = ?

ORDER BY bp.id DESC
");

$stmt->bind_param(
    "i",
    $bookingId
);

$stmt->execute();

$usedParts =
    $stmt->get_result();

/*
|--------------------------------------------------------------------------
| Total Sparepart
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT
    IFNULL(SUM(subtotal),0) AS total_parts
FROM booking_parts
WHERE booking_id = ?
");

$stmt->bind_param(
    "i",
    $bookingId
);

$stmt->execute();

$totalParts =
    $stmt
    ->get_result()
    ->fetch_assoc()['total_parts'];

/*
|--------------------------------------------------------------------------
| Total Keseluruhan
|--------------------------------------------------------------------------
*/
$grandTotal =
    $booking['service_price']
    + $totalParts;

/*
|--------------------------------------------------------------------------
| Badge Status
|--------------------------------------------------------------------------
*/
$statusBadge =
    'bg-gray-100 text-gray-700';

switch ($booking['status']) {

    case 'queued':
        $statusBadge =
            'bg-yellow-100 text-yellow-700';
        break;

    case 'in_progress':
        $statusBadge =
            'bg-blue-100 text-blue-700';
        break;

    case 'ready_for_pickup':
        $statusBadge =
            'bg-green-100 text-green-700';
        break;

    case 'completed':
        $statusBadge =
            'bg-green-100 text-green-700';
        break;

    case 'cancelled':
        $statusBadge =
            'bg-red-100 text-red-700';
        break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

<title><?= htmlspecialchars($pageTitle) ?></title>

</head>

<body class="font-['Plus_Jakarta_Sans'] overflow-hidden">

<div class="flex h-screen overflow-hidden">

    <?php include 'nav.php'; ?>

    <div class="flex-1 flex-col min-w-0 bg-gray-100 overflow-y-auto overflow-x-hidden">

        <!-- Header -->

        <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex flex-col gap-4 md:flex-row md:justify-between md:items-center w-full p-5">

            <div>

                <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">
                    DETAIL TUGAS
                </p>

                <p class="mt-2 text-2xl sm:text-4xl text-white font-semibold">
                    Booking #<?= $booking['id'] ?>
                </p>

                <p class="text-white">
                    <?= htmlspecialchars($booking['service_name']) ?>
                </p>

            </div>

            <a
                href="my_tasks.php"
                class="bg-[#FF0000] px-4 py-3 rounded text-white hover:bg-[#6e1111] transition inline-flex items-center gap-2"
            >
                <span class="material-symbols-outlined">
                    arrow_back
                </span>

                Kembali
            </a>

        </div>

        <div class="p-4">

            <?php if(isset($_SESSION['success'])): ?>

                <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">
                    <?= $_SESSION['success']; ?>
                </div>

                <?php unset($_SESSION['success']); ?>

            <?php endif; ?>

            <!-- Customer -->

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6">

                <h3 class="font-semibold text-lg mb-4">
                    Informasi Customer
                </h3>

                <div class="grid md:grid-cols-2 gap-4">

                    <div>
                        <p class="text-gray-400 text-sm">Nama</p>
                        <p><?= htmlspecialchars($booking['customer_name']) ?></p>
                    </div>

                    <div>
                        <p class="text-gray-400 text-sm">Telepon</p>
                        <p><?= htmlspecialchars($booking['phone']) ?></p>
                    </div>

                </div>

            </div>

            <!-- Motor -->

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6 mt-4">

                <h3 class="font-semibold text-lg mb-4">
                    Informasi Motor
                </h3>

                <div class="grid md:grid-cols-2 gap-4">

                    <div>
                        <p class="text-gray-400 text-sm">Motor</p>
                        <p>
                            <?= htmlspecialchars(
                                $booking['brand'].' '.$booking['model']
                            ) ?>
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400 text-sm">Plat Nomor</p>
                        <p><?= htmlspecialchars($booking['plate_number']) ?></p>
                    </div>

                    <div>
                        <p class="text-gray-400 text-sm">Tahun</p>
                        <p><?= $booking['production_year'] ?></p>
                    </div>

                    <div>
                        <p class="text-gray-400 text-sm">Warna</p>
                        <p><?= htmlspecialchars($booking['color']) ?></p>
                    </div>

                </div>

            </div>

            <!-- Booking -->

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6 mt-4">

                <h3 class="font-semibold text-lg mb-4">
                    Informasi Booking
                </h3>

                <div class="grid md:grid-cols-2 gap-4">

                    <div>

                        <p class="text-gray-400 text-sm">
                            Status
                        </p>

                        <span class="px-3 py-1 rounded-full text-sm <?= $statusBadge ?>">
                            <?= ucfirst(str_replace('_',' ',$booking['status'])) ?>
                        </span>

                    </div>

                    <div>

                        <p class="text-gray-400 text-sm">
                            Jadwal
                        </p>

                        <p>
                            <?= date('d M Y', strtotime($booking['booking_date'])) ?>
                        </p>

                    </div>

                </div>

                <div class="mt-4">

                    <p class="text-gray-400 text-sm">
                        Keluhan Customer
                    </p>

                    <div class="bg-gray-50 rounded-lg p-4 mt-2">
                        <?= nl2br(htmlspecialchars($booking['customer_complaint'])) ?>
                    </div>

                </div>

            </div>

    <!-- ACTION BUTTON -->

<?php if($booking['status'] == 'queued'): ?>

<div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6 mt-4">

    <form method="POST">

        <button
            type="submit"
            name="start_job"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg inline-flex items-center gap-2"
        >
            <span class="material-symbols-outlined">
                play_arrow
            </span>

            Mulai Kerjakan
        </button>

    </form>

</div>

<?php endif; ?>


<?php if($booking['status'] == 'in_progress'): ?>

<div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6 mt-4">

    <form method="POST">

        <button
            type="submit"
            name="finish_job"
            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg inline-flex items-center gap-2"
        >
            <span class="material-symbols-outlined">
                task_alt
            </span>

            Selesaikan Pekerjaan
        </button>

    </form>

</div>

<?php endif; ?>

<!-- CATATAN MEKANIK -->

<div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6 mt-4">

    <h3 class="font-semibold text-lg mb-4">
        Catatan Mekanik
    </h3>

    <form method="POST">

        <textarea
            name="mechanic_note"
            rows="5"
            class="w-full border rounded-lg p-3"
        ><?= htmlspecialchars($booking['mechanic_note'] ?? '') ?></textarea>

        <?php if(
            $booking['status'] == 'queued'
            || $booking['status'] == 'in_progress'
        ): ?>

        <button
            type="submit"
            name="save_note"
            class="mt-4 bg-[#8E1616] hover:bg-[#6f1111] text-white px-5 py-3 rounded-lg"
        >
            Simpan Catatan
        </button>

        <?php endif; ?>

    </form>

</div>

<!-- TAMBAH SPAREPART -->

<?php if(
    $booking['status'] == 'queued'
    || $booking['status'] == 'in_progress'
): ?>

<div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6 mt-4">

    <h3 class="font-semibold text-lg mb-4">
        Tambah Sparepart
    </h3>

    <form method="POST" class="grid md:grid-cols-3 gap-4">

        <select
            name="spare_part_id"
            required
            class="border rounded-lg p-3"
        >

            <option value="">
                Pilih Sparepart
            </option>

            <?php while($part = $parts->fetch_assoc()): ?>

                <option value="<?= $part['id'] ?>">

                    <?= htmlspecialchars($part['name']) ?>

                    (Stock: <?= $part['stock'] ?>)

                </option>

            <?php endwhile; ?>

        </select>

        <input
            type="number"
            name="qty"
            min="1"
            required
            class="border rounded-lg p-3"
            placeholder="Qty"
        >

        <button
            type="submit"
            name="add_part"
            class="bg-[#8E1616] hover:bg-[#6f1111] text-white rounded-lg"
        >
            Tambah
        </button>

    </form>

</div>

<?php endif; ?>

<!-- SPAREPART DIGUNAKAN -->

<div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6 mt-4">

    <h3 class="font-semibold text-lg mb-4">
        Sparepart Digunakan
    </h3>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead>

                <tr class="border-b">

                    <th class="text-left p-3">Sparepart</th>
                    <th class="text-left p-3">Qty</th>
                    <th class="text-left p-3">Harga</th>
                    <th class="text-left p-3">Subtotal</th>

                </tr>

            </thead>

            <tbody>

            <?php while($part = $usedParts->fetch_assoc()): ?>

                <tr class="border-b">

                    <td class="p-3">
                        <?= htmlspecialchars($part['name']) ?>
                    </td>

                    <td class="p-3">
                        <?= $part['qty'] ?>
                    </td>

                    <td class="p-3">
                        Rp<?= number_format($part['price_at_time'],0,',','.') ?>
                    </td>

                    <td class="p-3">
                        Rp<?= number_format($part['subtotal'],0,',','.') ?>
                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

<!-- TOTAL BIAYA -->

<div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6 mt-4 mb-4">

    <h3 class="font-semibold text-lg mb-4">
        Ringkasan Biaya
    </h3>

    <div class="space-y-3">

        <div class="flex justify-between">

            <span>Biaya Service</span>

            <span>
                Rp<?= number_format($booking['service_price'],0,',','.') ?>
            </span>

        </div>

        <div class="flex justify-between">

            <span>Total Sparepart</span>

            <span>
                Rp<?= number_format($totalParts,0,',','.') ?>
            </span>

        </div>

        <hr>

        <div class="flex justify-between text-xl font-bold text-[#8E1616]">

            <span>Total</span>

            <span>
                Rp<?= number_format($grandTotal,0,',','.') ?>
            </span>

        </div>

    </div>

</div>