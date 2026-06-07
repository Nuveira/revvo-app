<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Bookings Management | REVVO';

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['admin']);

$userId = $_SESSION['user_id'] ?? 0;

/*
|--------------------------------------------------------------------------
| Filter
|--------------------------------------------------------------------------
*/

$status =
    $_GET['status'] ?? '';

$date =
    $_GET['date'] ?? '';

$where = [];
$params = [];
$types = '';

if ($status != '') {

    $where[] =
        "b.status = ?";

    $params[] =
        $status;

    $types .= 's';
}

if ($date != '') {

    $where[] =
        "b.booking_date = ?";

    $params[] =
        $date;

    $types .= 's';
}

$whereSql = '';

if (!empty($where)) {

    $whereSql =
        'WHERE ' .
        implode(
            ' AND ',
            $where
        );
}

/*
|--------------------------------------------------------------------------
| Assign Mechanic
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['assign_mechanic'])
) {

    $bookingId =
        (int) $_POST['booking_id'];

    $mechanicId =
        (int) $_POST['mechanic_id'];

    $stmt = $conn->prepare("
        UPDATE bookings
        SET mechanic_id = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ii",
        $mechanicId,
        $bookingId
    );

    $stmt->execute();

    /*
    | Service Log
    */

    $stmt = $conn->prepare("
        INSERT INTO service_logs
        (
            booking_id,
            changed_by,
            previous_status,
            new_status,
            note
        )
        SELECT
            id,
            ?,
            status,
            status,
            'Mekanik di-assign oleh admin'
        FROM bookings
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ii",
        $userId,
        $bookingId
    );

    $stmt->execute();

    $_SESSION['success'] =
        'Mekanik berhasil ditugaskan';

    header(
        'Location: bookings.php'
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Change Status
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['change_status'])
) {

    $bookingId =
        (int) $_POST['booking_id'];

    $newStatus =
        $_POST['status'];

    /*
    | Ambil status lama
    */

    $stmt = $conn->prepare("
        SELECT status
        FROM bookings
        WHERE id = ?
    ");

    $stmt->bind_param(
        "i",
        $bookingId
    );

    $stmt->execute();

    $booking =
        $stmt
        ->get_result()
        ->fetch_assoc();

    if ($booking) {

        $oldStatus =
            $booking['status'];

        $stmt = $conn->prepare("
            UPDATE bookings
            SET status = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "si",
            $newStatus,
            $bookingId
        );

        $stmt->execute();

        /*
        | Service Log
        */

        $stmt = $conn->prepare("
            INSERT INTO service_logs
            (
                booking_id,
                changed_by,
                previous_status,
                new_status,
                note
            )
            VALUES
            (
                ?, ?, ?, ?,
                'Status diubah oleh admin'
            )
        ");

        $stmt->bind_param(
            "iiss",
            $bookingId,
            $userId,
            $oldStatus,
            $newStatus
        );

        $stmt->execute();

        $_SESSION['success'] =
            'Status booking berhasil diubah';
    }

    header(
        'Location: bookings.php'
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Cancel Booking (Soft Delete)
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['cancel_booking'])
) {

    $bookingId =
        (int) $_POST['booking_id'];

    $stmt = $conn->prepare("
        UPDATE bookings
        SET status = 'cancelled'
        WHERE id = ?
    ");

    $stmt->bind_param(
        "i",
        $bookingId
    );

    $stmt->execute();

    $stmt = $conn->prepare("
        INSERT INTO service_logs
        (
            booking_id,
            changed_by,
            previous_status,
            new_status,
            note
        )
        VALUES
        (
            ?,
            ?,
            '',
            'cancelled',
            'Booking dibatalkan admin'
        )
    ");

    $stmt->bind_param(
        "ii",
        $bookingId,
        $userId
    );

    $stmt->execute();

    $_SESSION['success'] =
        'Booking berhasil dibatalkan';

    header(
        'Location: bookings.php'
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Data Mekanik Untuk Dropdown
|--------------------------------------------------------------------------
*/

$mechanics = $conn->query("
    SELECT
        m.id,
        u.name
    FROM mechanics m
    JOIN users u
        ON m.user_id = u.id
    ORDER BY u.name ASC
");
?>
<?php
/*
|--------------------------------------------------------------------------
| Statistik Booking
|--------------------------------------------------------------------------
*/

$totalBookings = $conn->query("
    SELECT COUNT(*) total
    FROM bookings
")->fetch_assoc()['total'];

$totalQueued = $conn->query("
    SELECT COUNT(*) total
    FROM bookings
    WHERE status='queued'
")->fetch_assoc()['total'];

$totalProgress = $conn->query("
    SELECT COUNT(*) total
    FROM bookings
    WHERE status='in_progress'
")->fetch_assoc()['total'];

$totalCompleted = $conn->query("
    SELECT COUNT(*) total
    FROM bookings
    WHERE status IN
    (
        'completed',
        'ready_for_pickup'
    )
")->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| Query Booking List
|--------------------------------------------------------------------------
*/

$sql = "
SELECT

    b.id,
    b.booking_date,
    b.status,
    b.service_price,
    b.total_price,
    b.created_at,

    c.id AS customer_id,

    u.name AS customer_name,
    u.phone,

    mo.brand,
    mo.model,
    mo.plate_number,

    st.name AS service_name,

    mu.name AS mechanic_name,

    p.status AS payment_status,
    p.payment_method

FROM bookings b

JOIN customers c
    ON b.customer_id = c.id

JOIN users u
    ON c.user_id = u.id

JOIN motors mo
    ON b.motor_id = mo.id

JOIN service_types st
    ON b.service_type_id = st.id

LEFT JOIN mechanics m
    ON b.mechanic_id = m.id

LEFT JOIN users mu
    ON m.user_id = mu.id

LEFT JOIN payments p
    ON p.booking_id = b.id

$whereSql

ORDER BY
    b.id asc
";

$stmt = $conn->prepare($sql);

if (!empty($params)) {

    $stmt->bind_param(
        $types,
        ...$params
    );
}

$stmt->execute();

$bookings =
    $stmt->get_result();

/*
|--------------------------------------------------------------------------
| Badge Booking Status
|--------------------------------------------------------------------------
*/

function bookingBadge($status)
{
    switch ($status) {

        case 'queued':
            return
                'bg-yellow-100 text-yellow-700';

        case 'in_progress':
            return
                'bg-blue-100 text-blue-700';

        case 'completed':
            return
                'bg-green-100 text-green-700';

        case 'ready_for_pickup':
            return
                'bg-emerald-100 text-emerald-700';

        case 'cancelled':
            return
                'bg-red-100 text-red-700';

        default:
            return
                'bg-gray-100 text-gray-700';
    }
}

/*
|--------------------------------------------------------------------------
| Badge Payment Status
|--------------------------------------------------------------------------
*/

function paymentBadge($status)
{
    switch ($status) {

        case 'paid':
            return
                'bg-green-100 text-green-700';

        case 'pending':
            return
                'bg-yellow-100 text-yellow-700';

        case 'cancelled':
            return
                'bg-red-100 text-red-700';

        default:
            return
                'bg-gray-100 text-gray-700';
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

        <div class="flex-1 flex flex-col min-w-0 bg-gray-100 overflow-y-auto overflow-x-hidden">

            <!-- Header -->

            <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex flex-col gap-4 md:flex-row md:justify-between md:items-center w-full p-5">

                <div>

                    <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">
                        BOOKING MANAGEMENT
                    </p>

                    <p class="mt-2 text-2xl sm:text-4xl text-white font-semibold">
                        Kelola Booking
                    </p>

                    <p class="text-white">
                        Kelola seluruh booking customer, assign mekanik dan pembayaran.
                    </p>

                </div>

                <div>

                    <a
                        href="create_booking.php"
                        class="bg-[#FF0000] px-5 py-3 rounded text-white hover:bg-[#6e1111] transition inline-flex items-center gap-2 shadow-[0_0_15px_rgba(142,22,22,0.3)]">

                        <span class="material-symbols-outlined">
                            add_circle
                        </span>

                        Booking Baru

                    </a>

                </div>

            </div>

            <!-- Content -->

            <div class="p-4">

                <?php if (isset($_SESSION['success'])): ?>

                    <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-4">

                        <?= $_SESSION['success']; ?>

                    </div>

                    <?php unset($_SESSION['success']); ?>

                <?php endif; ?>

                <!-- Statistik -->

                <div class="grid md:grid-cols-4 gap-4 mb-4">

                    <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-5">

                        <p class="text-gray-400 text-xs uppercase tracking-wider">
                            Total Booking
                        </p>

                        <h3 class="text-3xl font-bold text-[#8E1616] mt-2">
                            <?= $totalBookings ?>
                        </h3>

                    </div>

                    <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-5">

                        <p class="text-gray-400 text-xs uppercase tracking-wider">
                            Queued
                        </p>

                        <h3 class="text-3xl font-bold text-yellow-600 mt-2">
                            <?= $totalQueued ?>
                        </h3>

                    </div>

                    <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-5">

                        <p class="text-gray-400 text-xs uppercase tracking-wider">
                            In Progress
                        </p>

                        <h3 class="text-3xl font-bold text-blue-600 mt-2">
                            <?= $totalProgress ?>
                        </h3>

                    </div>

                    <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-5">

                        <p class="text-gray-400 text-xs uppercase tracking-wider">
                            Selesai
                        </p>

                        <h3 class="text-3xl font-bold text-green-600 mt-2">
                            <?= $totalCompleted ?>
                        </h3>

                    </div>

                </div>

                <!-- Filter -->

                <div class="bg-white rounded-lg border border-[#eadede] shadow-sm p-4 mb-4">

                    <form method="GET">

                        <div class="grid md:grid-cols-3 gap-4">

                            <div>

                                <label class="block text-sm mb-2">
                                    Status
                                </label>

                                <select
                                    name="status"
                                    class="w-full border rounded-lg px-3 py-2">

                                    <option value="">
                                        Semua Status
                                    </option>

                                    <option value="queued"
                                        <?= $status == 'queued' ? 'selected' : '' ?>>
                                        Queued
                                    </option>

                                    <option value="in_progress"
                                        <?= $status == 'in_progress' ? 'selected' : '' ?>>
                                        In Progress
                                    </option>

                                    <option value="completed"
                                        <?= $status == 'completed' ? 'selected' : '' ?>>
                                        Completed
                                    </option>

                                    <option value="ready_for_pickup"
                                        <?= $status == 'ready_for_pickup' ? 'selected' : '' ?>>
                                        Ready For Pickup
                                    </option>

                                    <option value="cancelled"
                                        <?= $status == 'cancelled' ? 'selected' : '' ?>>
                                        Cancelled
                                    </option>

                                </select>

                            </div>

                            <div>

                                <label class="block text-sm mb-2">
                                    Tanggal Booking
                                </label>

                                <input
                                    type="date"
                                    name="date"
                                    value="<?= htmlspecialchars($date) ?>"
                                    class="w-full border rounded-lg px-3 py-2">

                            </div>

                            <div class="flex items-end gap-2">

                                <button
                                    type="submit"
                                    class="bg-[#8E1616] text-white px-5 py-2 rounded-lg hover:bg-[#6e1111]">
                                    Filter
                                </button>

                                <a
                                    href="bookings.php"
                                    class="bg-gray-200 px-5 py-2 rounded-lg hover:bg-gray-300">
                                    Reset
                                </a>

                            </div>

                        </div>

                    </form>

                </div>
                <!-- Tabel Booking -->

                <div class="bg-white rounded-lg border border-[#eadede] shadow-sm overflow-hidden">

                    <div class="p-4 border-b border-[#eadede]">

                        <h2 class="font-semibold text-lg">
                            Daftar Booking
                        </h2>

                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th class="p-4 text-left">
                                        ID
                                    </th>

                                    <th class="p-4 text-left">
                                        Customer
                                    </th>

                                    <th class="p-4 text-left">
                                        Motor
                                    </th>

                                    <th class="p-4 text-left">
                                        Service
                                    </th>

                                    <th class="p-4 text-left">
                                        Mekanik
                                    </th>

                                    <th class="p-4 text-left">
                                        Status
                                    </th>

                                    <th class="p-4 text-left">
                                        Payment
                                    </th>

                                    <th class="p-4 text-left">
                                        Total
                                    </th>

                                    <th class="p-4 text-left">
                                        Aksi
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php while ($row = $bookings->fetch_assoc()): ?>

                                    <tr class="border-t border-[#f1f1f1] hover:bg-gray-50">

                                        <td class="p-4 font-medium">

                                            #<?= $row['id']; ?>

                                            <div class="text-xs text-gray-400 mt-1">

                                                <?= date(
                                                    'd M Y',
                                                    strtotime(
                                                        $row['booking_date']
                                                    )
                                                ); ?>

                                            </div>

                                        </td>

                                        <td class="p-4">

                                            <div class="font-medium">

                                                <?= htmlspecialchars(
                                                    $row['customer_name']
                                                ); ?>

                                            </div>

                                            <div class="text-sm text-gray-500">

                                                <?= htmlspecialchars(
                                                    $row['phone']
                                                ); ?>

                                            </div>

                                        </td>

                                        <td class="p-4">

                                            <div class="font-medium">

                                                <?= htmlspecialchars(
                                                    $row['brand']
                                                        . ' ' .
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

                                            <?= htmlspecialchars(
                                                $row['service_name']
                                            ); ?>

                                        </td>

                                        <td class="p-4">

                                            <?php if ($row['mechanic_name']): ?>

                                                <span class="text-green-700 font-medium">

                                                    <?= htmlspecialchars(
                                                        $row['mechanic_name']
                                                    ); ?>

                                                </span>

                                            <?php else: ?>

                                                <span class="text-red-600">

                                                    Belum Assign

                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td class="p-4">

                                            <span
                                                class="px-3 py-1 rounded-full text-sm <?= bookingBadge($row['status']); ?>">

                                                <?= ucfirst(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $row['status']
                                                    )
                                                ); ?>

                                            </span>

                                        </td>

                                        <td class="p-4">

                                            <span
                                                class="px-3 py-1 rounded-full text-sm <?= paymentBadge($row['payment_status']); ?>">

                                                <?= ucfirst(
                                                    $row['payment_status']
                                                        ?? 'pending'
                                                ); ?>

                                            </span>

                                        </td>

                                        <td class="p-4 font-semibold">

                                            Rp<?= number_format(
                                                    $row['total_price'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ); ?>

                                        </td>

                                        <td class="p-4">

                                            <div class="flex flex-wrap gap-2">

                                                <!-- Detail -->

                                                <a
                                                    href="booking_detail.php?id=<?= $row['id']; ?>"
                                                    class="bg-[#8E1616] text-white px-3 py-2 rounded-lg hover:bg-[#6f1111]">
                                                    Detail
                                                </a>

                                                <!-- Assign -->

                                                <?php if (!$row['mechanic_name']): ?>

                                                    <button
                                                        type="button"
                                                        onclick="openAssignModal(
                                            <?= $row['id']; ?>
                                        )"
                                                        class="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700">
                                                        Assign
                                                    </button>

                                                <?php endif; ?>

                                                <!-- Status -->

                                                <?php if (
                                                    $row['status']
                                                    != 'cancelled'
                                                ): ?>

                                                    <button
                                                        type="button"
                                                        onclick="openStatusModal(
                                            <?= $row['id']; ?>
                                        )"
                                                        class="bg-yellow-500 text-white px-3 py-2 rounded-lg hover:bg-yellow-600">
                                                        Status
                                                    </button>

                                                <?php endif; ?>

                                                <!-- Payment -->

                                                <a
                                                    href="payments.php?booking_id=<?= $row['id']; ?>"
                                                    class="bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700">
                                                    Payment
                                                </a>

                                                <!-- Cancel -->

                                                <?php if (
                                                    !in_array(
                                                        $row['status'],
                                                        [
                                                            'cancelled',
                                                            'completed'
                                                        ]
                                                    )
                                                ): ?>

                                                    <form
                                                        method="POST"
                                                        onsubmit="return confirm('Batalkan booking ini?')">

                                                        <input
                                                            type="hidden"
                                                            name="booking_id"
                                                            value="<?= $row['id']; ?>">

                                                        <button
                                                            type="submit"
                                                            name="cancel_booking"
                                                            class="bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700">
                                                            Cancel
                                                        </button>

                                                    </form>

                                                <?php endif; ?>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                                <?php if ($bookings->num_rows == 0): ?>

                                    <tr>

                                        <td
                                            colspan="9"
                                            class="text-center py-10 text-gray-500">

                                            Tidak ada data booking.

                                        </td>

                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>
                        <!-- Assign Mechanic Modal -->

        <div
            id="assignModal"
            class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center"
        >

            <div class="bg-white rounded-xl w-full max-w-md p-6">

                <h3 class="text-xl font-semibold mb-4">
                    Assign Mekanik
                </h3>

                <form method="POST">

                    <input
                        type="hidden"
                        id="assignBookingId"
                        name="booking_id"
                    >

                    <label class="block text-sm mb-2">
                        Pilih Mekanik
                    </label>

                    <select
                        name="mechanic_id"
                        required
                        class="w-full border rounded-lg px-3 py-2 mb-4"
                    >

                        <option value="">
                            -- Pilih Mekanik --
                        </option>

                        <?php
                        mysqli_data_seek($mechanics, 0);

                        while($m = $mechanics->fetch_assoc()):
                        ?>

                            <option value="<?= $m['id']; ?>">

                                <?= htmlspecialchars(
                                    $m['name']
                                ); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                    <div class="flex justify-end gap-2">

                        <button
                            type="button"
                            onclick="closeAssignModal()"
                            class="px-4 py-2 bg-gray-200 rounded-lg"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            name="assign_mechanic"
                            class="px-4 py-2 bg-[#8E1616] text-white rounded-lg"
                        >
                            Simpan
                        </button>

                    </div>

                </form>

            </div>

        </div>

        <!-- Status Modal -->

        <div
            id="statusModal"
            class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center"
        >

            <div class="bg-white rounded-xl w-full max-w-md p-6">

                <h3 class="text-xl font-semibold mb-4">
                    Ubah Status Booking
                </h3>

                <form method="POST">

                    <input
                        type="hidden"
                        id="statusBookingId"
                        name="booking_id"
                    >

                    <label class="block text-sm mb-2">
                        Status Baru
                    </label>

                    <select
                        name="status"
                        required
                        class="w-full border rounded-lg px-3 py-2 mb-4"
                    >

                        <option value="queued">
                            Queued
                        </option>

                        <option value="in_progress">
                            In Progress
                        </option>

                        <option value="completed">
                            Completed
                        </option>

                        <option value="ready_for_pickup">
                            Ready For Pickup
                        </option>

                        <option value="cancelled">
                            Cancelled
                        </option>

                    </select>

                    <div class="flex justify-end gap-2">

                        <button
                            type="button"
                            onclick="closeStatusModal()"
                            class="px-4 py-2 bg-gray-200 rounded-lg"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            name="change_status"
                            class="px-4 py-2 bg-[#8E1616] text-white rounded-lg"
                        >
                            Simpan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


</div>

</div>

<script>

function openAssignModal(id)
{
    document
        .getElementById('assignBookingId')
        .value = id;

    document
        .getElementById('assignModal')
        .classList
        .remove('hidden');
}

function closeAssignModal()
{
    document
        .getElementById('assignModal')
        .classList
        .add('hidden');
}

function openStatusModal(id)
{
    document
        .getElementById('statusBookingId')
        .value = id;

    document
        .getElementById('statusModal')
        .classList
        .remove('hidden');
}

function closeStatusModal()
{
    document
        .getElementById('statusModal')
        .classList
        .add('hidden');
}

window.onclick = function(event)
{
    const assignModal =
        document.getElementById('assignModal');

    const statusModal =
        document.getElementById('statusModal');

    if(event.target === assignModal)
    {
        closeAssignModal();
    }

    if(event.target === statusModal)
    {
        closeStatusModal();
    }
}

</script>

</body>
</html>