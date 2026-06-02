<?php
session_start();

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['customer']);

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Ambil Customer
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT id
FROM customers
WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$customer = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$customer) {
    die('Customer tidak ditemukan');
}

$customer_id = $customer['id'];

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');
$search_like = "%{$search}%";

/*
|--------------------------------------------------------------------------
| Booking History
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT
    b.*,
    st.name AS service_name,
    m.brand,
    m.model
FROM bookings b

JOIN service_types st
    ON b.service_type_id = st.id

JOIN motors m
    ON b.motor_id = m.id

WHERE
    b.customer_id = ?
AND
(
    ? = ''
    OR st.name LIKE ?
)

ORDER BY b.id DESC
");

$stmt->bind_param(
    "iss",
    $customer_id,
    $search,
    $search_like
);

$stmt->execute();

$bookings = $stmt
    ->get_result()
    ->fetch_all(MYSQLI_ASSOC);

include '../../includes/header.php';
?>

<div class="container">

    <h2>Riwayat Booking</h2>

    <form method="get">

        <input type="text" name="search" placeholder="Cari layanan" value="<?= htmlspecialchars($search) ?>">

        <button type="submit">
            Cari
        </button>

    </form>

    <br>

    <table border="1" cellpadding="5">

        <tr>

            <th>ID</th>
            <th>Motor</th>
            <th>Layanan</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Detail</th>

        </tr>

        <?php foreach ($bookings as $booking): ?>

            <tr>

                <td>

                    <?= $booking['id'] ?>

                </td>

                <td>

                    <?= htmlspecialchars(
                        $booking['brand']
                        . ' ' .
                        $booking['model']
                    ) ?>

                </td>

                <td>

                    <?= htmlspecialchars(
                        $booking['service_name']
                    ) ?>

                </td>

                <td>

                    <?= $booking['booking_date'] ?>

                </td>

                <td>

                    <?= $booking['status'] ?>

                </td>

                <td>

                    <a href="booking_detail.php?id=<?= $booking['id'] ?>">

                        Detail

                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>

<?php include '../../includes/footer.php'; ?>