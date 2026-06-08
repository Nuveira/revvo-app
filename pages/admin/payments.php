<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Payments | REVVO';

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['admin']);

$userId = $_SESSION['user_id'] ?? 0;

/*
|--------------------------------------------------------------------------
| Data Admin
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        id,
        name,
        role,
        profile_photo
    FROM users
    WHERE id = ?
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$admin = $stmt->get_result()->fetch_assoc();

$nama = $admin['name'];
$role = $admin['role'];
$profile_photo = $admin['profile_photo'];

/*
|--------------------------------------------------------------------------
| Verifikasi Pembayaran
|--------------------------------------------------------------------------
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['verify_payment'])
) {

    $paymentId = (int) $_POST['payment_id'];

    $stmt = $conn->prepare("
        UPDATE payments
        SET
            status = 'paid',
            paid_at = NOW(),
            verified_by = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ii",
        $userId,
        $paymentId
    );

    $stmt->execute();

    $_SESSION['success'] =
        'Pembayaran berhasil diverifikasi';

    header("Location: payments.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Batalkan Pembayaran
|--------------------------------------------------------------------------
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['cancel_payment'])
) {

    $paymentId = (int) $_POST['payment_id'];

    $stmt = $conn->prepare("
        UPDATE payments
        SET status='cancelled'
        WHERE id=?
    ");

    $stmt->bind_param(
        "i",
        $paymentId
    );

    $stmt->execute();

    $_SESSION['success'] =
        'Pembayaran dibatalkan';

    header("Location: payments.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Filter
|--------------------------------------------------------------------------
*/
$status =
    $_GET['status'] ?? '';

$where = '';

$params = [];
$types = '';

if (!empty($status)) {

    $where .= " WHERE p.status = ? ";

    $params[] = $status;
    $types .= 's';
}

/*
|--------------------------------------------------------------------------
| Data Payments
|--------------------------------------------------------------------------
*/
$sql = "
SELECT

    p.*,

    b.booking_date,
    b.status AS booking_status,

    u.name AS customer_name,

    mo.brand,
    mo.model,
    mo.plate_number

FROM payments p

JOIN bookings b
    ON p.booking_id = b.id

JOIN customers c
    ON b.customer_id = c.id

JOIN users u
    ON c.user_id = u.id

JOIN motors mo
    ON b.motor_id = mo.id

$where

ORDER BY b.id asc
";

$stmt = $conn->prepare($sql);

if (!empty($params)) {

    $stmt->bind_param(
        $types,
        ...$params
    );
}

$stmt->execute();

$payments =
    $stmt->get_result();

/*
|--------------------------------------------------------------------------
| Statistik
|--------------------------------------------------------------------------
*/
$totalPending =
$conn->query("
    SELECT COUNT(*) total
    FROM payments
    WHERE status='pending'
")->fetch_assoc()['total'];

$totalPaid =
$conn->query("
    SELECT COUNT(*) total
    FROM payments
    WHERE status='paid'
")->fetch_assoc()['total'];

$totalCancelled =
$conn->query("
    SELECT COUNT(*) total
    FROM payments
    WHERE status='cancelled'
")->fetch_assoc()['total'];
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
                    PAYMENT MANAGEMENT
                </p>

                <p class="mt-2 text-2xl sm:text-4xl text-white font-semibold">
                    Pembayaran
                </p>

                <p class="text-white">
                    Kelola dan verifikasi pembayaran customer.
                </p>

            </div>
                <a
    href="bookings.php"
    class="bg-[#FF0000] px-4 py-3 rounded text-white hover:bg-[#6e1111] transition inline-flex items-center gap-2"
>

    <span class="material-symbols-outlined">
        arrow_back
    </span>

    Kembali ke Bookings

</a>
        </div>

        <!-- Content -->

        <div class="p-4">

            <?php if(isset($_SESSION['success'])): ?>

                <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">

                    <?= $_SESSION['success']; ?>

                </div>

                <?php unset($_SESSION['success']); ?>

            <?php endif; ?>

            <!-- Statistik -->

            <div class="grid md:grid-cols-3 gap-4 mb-4">

                <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6">

                    <div class="flex justify-between items-center">

                        <div>

                            <p class="text-xs uppercase tracking-wider text-gray-400">
                                Pending
                            </p>

                            <h3 class="text-3xl font-bold text-yellow-600 mt-2">

                                <?= $totalPending ?>

                            </h3>

                        </div>

                        <span class="material-symbols-outlined text-5xl text-yellow-600">
                            schedule
                        </span>

                    </div>

                </div>

                <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6">

                    <div class="flex justify-between items-center">

                        <div>

                            <p class="text-xs uppercase tracking-wider text-gray-400">
                                Paid
                            </p>

                            <h3 class="text-3xl font-bold text-green-600 mt-2">

                                <?= $totalPaid ?>

                            </h3>

                        </div>

                        <span class="material-symbols-outlined text-5xl text-green-600">
                            task_alt
                        </span>

                    </div>

                </div>

                <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-6">

                    <div class="flex justify-between items-center">

                        <div>

                            <p class="text-xs uppercase tracking-wider text-gray-400">
                                Cancelled
                            </p>

                            <h3 class="text-3xl font-bold text-red-600 mt-2">

                                <?= $totalCancelled ?>

                            </h3>

                        </div>

                        <span class="material-symbols-outlined text-5xl text-red-600">
                            cancel
                        </span>

                    </div>

                </div>

            </div>

            <!-- Filter -->

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-4 mb-4">

                <form method="GET">

                    <div class="flex flex-wrap gap-3">

                        <select
                            name="status"
                            class="border rounded-lg px-4 py-2"
                        >

                            <option value="">
                                Semua Status
                            </option>

                            <option
                                value="pending"
                                <?= $status=='pending'?'selected':'' ?>
                            >
                                Pending
                            </option>

                            <option
                                value="paid"
                                <?= $status=='paid'?'selected':'' ?>
                            >
                                Paid
                            </option>

                            <option
                                value="cancelled"
                                <?= $status=='cancelled'?'selected':'' ?>
                            >
                                Cancelled
                            </option>

                        </select>

                        <button
                            type="submit"
                            class="bg-[#8E1616] text-white px-5 py-2 rounded-lg hover:bg-[#6f1111]"
                        >

                            Filter

                        </button>

                        <a
                            href="payments.php"
                            class="bg-gray-200 px-5 py-2 rounded-lg"
                        >

                            Reset

                        </a>

                    </div>

                </form>

            </div>

                        <!-- Tabel Payments -->

            <div class="bg-white rounded-lg border border-[#eadede] shadow-sm overflow-hidden">

                <div class="p-4 border-b border-[#eadede]">

                    <h2 class="font-semibold text-lg">
                        Daftar Pembayaran
                    </h2>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="p-4 text-left">
                                    Booking
                                </th>

                                <th class="p-4 text-left">
                                    Customer
                                </th>

                                <th class="p-4 text-left">
                                    Kendaraan
                                </th>

                                <th class="p-4 text-left">
                                    Metode
                                </th>

                                <th class="p-4 text-left">
                                    Nominal
                                </th>

                                <th class="p-4 text-left">
                                    Status
                                </th>

                                <th class="p-4 text-left">
                                    Aksi
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php while($row = $payments->fetch_assoc()): ?>

                            <?php

                            $badge =
                                'bg-gray-100 text-gray-700';

                            if($row['status'] == 'pending'){
                                $badge =
                                    'bg-yellow-100 text-yellow-700';
                            }

                            if($row['status'] == 'paid'){
                                $badge =
                                    'bg-green-100 text-green-700';
                            }

                            if($row['status'] == 'cancelled'){
                                $badge =
                                    'bg-red-100 text-red-700';
                            }

                            ?>

                            <tr class="border-t border-[#f2f2f2]">

                                <td class="p-4">

                                    <div class="font-medium">
                                        #<?= $row['booking_id']; ?>
                                    </div>

                                    <div class="text-sm text-gray-500">

                                        <?= date(
                                            'd M Y',
                                            strtotime(
                                                $row['booking_date']
                                            )
                                        ); ?>

                                    </div>

                                </td>

                                <td class="p-4">

                                    <?= htmlspecialchars(
                                        $row['customer_name']
                                    ); ?>

                                </td>

                                <td class="p-4">

                                    <div>

                                        <?= htmlspecialchars(
                                            $row['brand']
                                            .' '.
                                            $row['model']
                                        ); ?>

                                    </div>

                                    <div class="text-sm text-gray-500">

                                        <?= htmlspecialchars(
                                            $row['plate_number']
                                        ); ?>

                                    </div>

                                </td>

                                <td class="p-4">

                                    <?= ucfirst(
                                        $row['payment_method']
                                    ); ?>

                                </td>

                                <td class="p-4 font-semibold">

                                    Rp<?= number_format(
                                        $row['amount'],
                                        0,
                                        ',',
                                        '.'
                                    ); ?>

                                </td>

                                <td class="p-4">

                                    <span class="px-3 py-1 rounded-full text-sm <?= $badge ?>">

                                        <?= ucfirst(
                                            $row['status']
                                        ); ?>

                                    </span>

                                </td>

                                <td class="p-4">

                                    <div class="flex flex-wrap gap-2">

                                        <?php if($row['status'] == 'pending'): ?>

                                            <form method="POST">

                                                <input
                                                    type="hidden"
                                                    name="payment_id"
                                                    value="<?= $row['id']; ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    name="verify_payment"
                                                    class="bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700"
                                                >

                                                    Verifikasi

                                                </button>

                                            </form>

                                            <form method="POST">

                                                <input
                                                    type="hidden"
                                                    name="payment_id"
                                                    value="<?= $row['id']; ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    name="cancel_payment"
                                                    class="bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700"
                                                >

                                                    Batalkan

                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <span class="text-gray-400 text-sm">

                                                Tidak ada aksi

                                            </span>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                        <?php if($payments->num_rows == 0): ?>

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center py-10 text-gray-500"
                                >

                                    Tidak ada data pembayaran.

                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>