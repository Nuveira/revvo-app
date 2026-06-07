<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Detail Booking | REVVO';

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['admin']);

if (!isset($_GET['id'])) {
    header('Location: bookings.php');
    exit;
}

$bookingId = (int) $_GET['id'];

/*
|--------------------------------------------------------------------------
| Detail Booking
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT

    b.*,

    c.id AS customer_id,

    u.name AS customer_name,
    u.email,
    u.phone,

    mo.brand,
    mo.model,
    mo.plate_number,
    mo.production_year,
    mo.color,

    st.name AS service_name,
    st.description,

    ts.start_time,
    ts.end_time,

    mu.name AS mechanic_name

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

LEFT JOIN mechanics m
    ON b.mechanic_id = m.id

LEFT JOIN users mu
    ON m.user_id = mu.id

WHERE b.id = ?
LIMIT 1
");

$stmt->bind_param("i", $bookingId);
$stmt->execute();

$booking =
    $stmt->get_result()->fetch_assoc();

if (!$booking) {
    die('Booking tidak ditemukan');
}

/*
|--------------------------------------------------------------------------
| Payment
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT *
FROM payments
WHERE booking_id = ?
LIMIT 1
");

$stmt->bind_param(
    "i",
    $bookingId
);

$stmt->execute();

$payment =
    $stmt->get_result()->fetch_assoc();

/*
|--------------------------------------------------------------------------
| Sparepart Digunakan
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT

    bp.*,

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

$parts =
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
| Service Logs
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT

    sl.*,

    u.name

FROM service_logs sl

LEFT JOIN users u
    ON sl.changed_by = u.id

WHERE sl.booking_id = ?

ORDER BY sl.created_at DESC
");

$stmt->bind_param(
    "i",
    $bookingId
);

$stmt->execute();

$logs =
    $stmt->get_result();

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
| Badge Status Booking
|--------------------------------------------------------------------------
*/
$statusBadge =
    'bg-gray-100 text-gray-700';

switch($booking['status'])
{
    case 'queued':
        $statusBadge =
            'bg-yellow-100 text-yellow-700';
        break;

    case 'in_progress':
        $statusBadge =
            'bg-blue-100 text-blue-700';
        break;

    case 'completed':
        $statusBadge =
            'bg-green-100 text-green-700';
        break;

    case 'ready_for_pickup':
        $statusBadge =
            'bg-green-100 text-green-700';
        break;

    case 'cancelled':
        $statusBadge =
            'bg-red-100 text-red-700';
        break;
}

/*
|--------------------------------------------------------------------------
| Badge Payment
|--------------------------------------------------------------------------
*/
$paymentBadge =
    'bg-yellow-100 text-yellow-700';

if ($payment) {

    if ($payment['status'] == 'paid') {
        $paymentBadge =
            'bg-green-100 text-green-700';
    }

    if ($payment['status'] == 'cancelled') {
        $paymentBadge =
            'bg-red-100 text-red-700';
    }
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
                    DETAIL BOOKING
                </p>

                <p class="mt-2 text-2xl sm:text-4xl text-white font-semibold">
                    Booking #<?= $booking['id'] ?>
                </p>

                <p class="text-white">
                    <?= htmlspecialchars($booking['service_name']) ?>
                </p>

            </div>

            <a
                href="bookings.php"
                class="bg-[#FF0000] px-4 py-3 rounded text-white hover:bg-[#6e1111] transition inline-flex items-center gap-2"
            >
                <span class="material-symbols-outlined">
                    arrow_back
                </span>

                Kembali
            </a>

        </div>

        <div class="p-4">

            <!-- Customer -->

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6">

                <h3 class="font-semibold text-lg mb-4">
                    Informasi Customer
                </h3>

                <div class="grid md:grid-cols-2 gap-4">

                    <div>
                        <p class="text-gray-400 text-sm">
                            Nama
                        </p>

                        <p class="font-medium">
                            <?= htmlspecialchars($booking['customer_name']) ?>
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400 text-sm">
                            Email
                        </p>

                        <p>
                            <?= htmlspecialchars($booking['email']) ?>
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400 text-sm">
                            Telepon
                        </p>

                        <p>
                            <?= htmlspecialchars($booking['phone']) ?>
                        </p>
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

                        <p class="text-gray-400 text-sm">
                            Motor
                        </p>

                        <p class="font-medium">

                            <?= htmlspecialchars(
                                $booking['brand']
                                .' '.
                                $booking['model']
                            ) ?>

                        </p>

                    </div>

                    <div>

                        <p class="text-gray-400 text-sm">
                            Plat Nomor
                        </p>

                        <p>

                            <?= htmlspecialchars(
                                $booking['plate_number']
                            ) ?>

                        </p>

                    </div>

                    <div>

                        <p class="text-gray-400 text-sm">
                            Tahun
                        </p>

                        <p>
                            <?= $booking['production_year'] ?>
                        </p>

                    </div>

                    <div>

                        <p class="text-gray-400 text-sm">
                            Warna
                        </p>

                        <p>

                            <?= htmlspecialchars(
                                $booking['color']
                            ) ?>

                        </p>

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
                            Status Booking
                        </p>

                        <span class="px-3 py-1 rounded-full text-sm <?= $statusBadge ?>">

                            <?= ucfirst(
                                str_replace(
                                    '_',
                                    ' ',
                                    $booking['status']
                                )
                            ) ?>

                        </span>

                    </div>

                    <div>

                        <p class="text-gray-400 text-sm">
                            Mekanik
                        </p>

                        <p>

                            <?= $booking['mechanic_name']
                                ? htmlspecialchars($booking['mechanic_name'])
                                : '-'; ?>

                        </p>

                    </div>

                    <div>

                        <p class="text-gray-400 text-sm">
                            Tanggal Booking
                        </p>

                        <p>

                            <?= date(
                                'd M Y',
                                strtotime(
                                    $booking['booking_date']
                                )
                            ) ?>

                        </p>

                    </div>

                    <div>

                        <p class="text-gray-400 text-sm">
                            Jam Servis
                        </p>

                        <p>

                            <?= substr($booking['start_time'],0,5) ?>
                            -
                            <?= substr($booking['end_time'],0,5) ?>

                        </p>

                    </div>

                </div>

                <div class="mt-5">

                    <p class="text-gray-400 text-sm">
                        Keluhan Customer
                    </p>

                    <div class="bg-gray-50 rounded-lg p-4 mt-2">

                        <?= nl2br(
                            htmlspecialchars(
                                $booking['customer_complaint']
                            )
                        ) ?>

                    </div>

                </div>

                <div class="mt-5">

                    <p class="text-gray-400 text-sm">
                        Catatan Mekanik
                    </p>

                    <div class="bg-gray-50 rounded-lg p-4 mt-2">

                        <?= $booking['mechanic_note']
                            ? nl2br(htmlspecialchars($booking['mechanic_note']))
                            : '-'; ?>

                    </div>

                </div>

            </div>
                        <!-- Pembayaran -->

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6 mt-4">

                <h3 class="font-semibold text-lg mb-4">
                    Informasi Pembayaran
                </h3>

                <?php if($payment): ?>

                    <div class="grid md:grid-cols-2 gap-4">

                        <div>

                            <p class="text-gray-400 text-sm">
                                Metode Pembayaran
                            </p>

                            <p class="font-medium">
                                <?= ucfirst($payment['payment_method']) ?>
                            </p>

                        </div>

                        <div>

                            <p class="text-gray-400 text-sm">
                                Status Pembayaran
                            </p>

                            <span class="px-3 py-1 rounded-full text-sm <?= $paymentBadge ?>">

                                <?= ucfirst($payment['status']) ?>

                            </span>

                        </div>

                        <div>

                            <p class="text-gray-400 text-sm">
                                Nominal
                            </p>

                            <p class="font-semibold text-[#8E1616]">

                                Rp<?= number_format(
                                    $payment['amount'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </p>

                        </div>

                        <div>

                            <p class="text-gray-400 text-sm">
                                Tanggal Bayar
                            </p>

                            <p>

                                <?= $payment['paid_at']
                                    ? date(
                                        'd M Y H:i',
                                        strtotime($payment['paid_at'])
                                    )
                                    : '-'; ?>

                            </p>

                        </div>

                    </div>

                <?php else: ?>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-700">

                        Belum ada data pembayaran.

                    </div>

                <?php endif; ?>

            </div>

            <!-- Sparepart -->

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6 mt-4">

                <div class="flex justify-between items-center mb-4">

                    <h3 class="font-semibold text-lg">
                        Sparepart Digunakan
                    </h3>

                    <span class="text-sm text-gray-500">

                        Total:
                        Rp<?= number_format(
                            $totalParts,
                            0,
                            ',',
                            '.'
                        ) ?>

                    </span>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="p-3 text-left">
                                    SKU
                                </th>

                                <th class="p-3 text-left">
                                    Sparepart
                                </th>

                                <th class="p-3 text-left">
                                    Qty
                                </th>

                                <th class="p-3 text-left">
                                    Harga
                                </th>

                                <th class="p-3 text-left">
                                    Subtotal
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php while($part = $parts->fetch_assoc()): ?>

                            <tr class="border-t">

                                <td class="p-3">

                                    <?= htmlspecialchars(
                                        $part['sku']
                                    ) ?>

                                </td>

                                <td class="p-3">

                                    <?= htmlspecialchars(
                                        $part['name']
                                    ) ?>

                                </td>

                                <td class="p-3">

                                    <?= $part['qty'] ?>

                                </td>

                                <td class="p-3">

                                    Rp<?= number_format(
                                        $part['price_at_time'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                </td>

                                <td class="p-3 font-semibold">

                                    Rp<?= number_format(
                                        $part['subtotal'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            </div>

            <!-- Ringkasan Biaya -->

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6 mt-4">

                <h3 class="font-semibold text-lg mb-4">
                    Ringkasan Biaya
                </h3>

                <div class="space-y-3">

                    <div class="flex justify-between">

                        <span>
                            Biaya Servis
                        </span>

                        <span>

                            Rp<?= number_format(
                                $booking['service_price'],
                                0,
                                ',',
                                '.'
                            ) ?>

                        </span>

                    </div>

                    <div class="flex justify-between">

                        <span>
                            Sparepart
                        </span>

                        <span>

                            Rp<?= number_format(
                                $totalParts,
                                0,
                                ',',
                                '.'
                            ) ?>

                        </span>

                    </div>

                    <hr>

                    <div class="flex justify-between text-lg font-bold text-[#8E1616]">

                        <span>
                            Total
                        </span>

                        <span>

                            Rp<?= number_format(
                                $grandTotal,
                                0,
                                ',',
                                '.'
                            ) ?>

                        </span>

                    </div>

                </div>

            </div>
                        <!-- Service Logs -->

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6 mt-4 mb-4">

                <h3 class="font-semibold text-lg mb-4">
                    Riwayat Perubahan Status
                </h3>

                <?php if($logs->num_rows > 0): ?>

                    <div class="space-y-4">

                        <?php while($log = $logs->fetch_assoc()): ?>

                            <div class="border-l-4 border-[#8E1616] pl-4 py-1">

                                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2">

                                    <div>

                                        <span class="font-medium">

                                            <?= ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $log['previous_status']
                                                )
                                            ) ?>

                                        </span>

                                        <span class="mx-2 text-gray-400">
                                            →
                                        </span>

                                        <span class="font-medium text-[#8E1616]">

                                            <?= ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $log['new_status']
                                                )
                                            ) ?>

                                        </span>

                                    </div>

                                    <div class="text-sm text-gray-500">

                                        <?= date(
                                            'd M Y H:i',
                                            strtotime(
                                                $log['created_at']
                                            )
                                        ) ?>

                                    </div>

                                </div>

                                <?php if(!empty($log['changed_by_name'])): ?>

                                    <div class="text-sm text-gray-500 mt-1">

                                        Oleh:
                                        <?= htmlspecialchars(
                                            $log['changed_by_name']
                                        ) ?>

                                    </div>

                                <?php endif; ?>

                                <?php if(!empty($log['note'])): ?>

                                    <div class="bg-gray-50 rounded-lg p-3 mt-2 text-sm">

                                        <?= nl2br(
                                            htmlspecialchars(
                                                $log['note']
                                            )
                                        ) ?>

                                    </div>

                                <?php endif; ?>

                            </div>

                        <?php endwhile; ?>

                    </div>

                <?php else: ?>

                    <div class="bg-gray-50 rounded-lg p-4 text-gray-500">

                        Belum ada riwayat perubahan status.

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

</body>
</html>