<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Booking Walk In | REVVO';

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';

checkRole(['admin']);

$userId = $_SESSION['user_id'] ?? 0;

/*
|--------------------------------------------------------------------------
| Customer
|--------------------------------------------------------------------------
*/

$customers = $conn->query("
    SELECT
        c.id,
        u.name,
        u.phone
    FROM customers c
    JOIN users u
        ON c.user_id = u.id
    ORDER BY u.name ASC
");

/*
|--------------------------------------------------------------------------
| Service Types
|--------------------------------------------------------------------------
*/

$serviceTypes = $conn->query("
    SELECT
        id,
        name,
        base_price
    FROM service_types
    WHERE status = 'active'
    ORDER BY name ASC
");

/*
|--------------------------------------------------------------------------
| Time Slots
|--------------------------------------------------------------------------
*/

$timeSlots = $conn->query("
    SELECT
        id,
        day,
        start_time,
        end_time,
        capacity
    FROM time_slots
    WHERE status = 'active'
    ORDER BY
        FIELD(
            day,
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday'
        ),
        start_time ASC
");

/*
|--------------------------------------------------------------------------
| Submit Booking
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['create_booking'])
) {

    $customerId =
        (int) $_POST['customer_id'];

    $motorId =
        (int) $_POST['motor_id'];

    $serviceTypeId =
        (int) $_POST['service_type_id'];

    $paymentMethod =
    $_POST['payment_method'];

    $timeSlotId =
        (int) $_POST['time_slot_id'];

    $bookingDate =
        $_POST['booking_date'];

    $complaint =
        trim(
            $_POST['customer_complaint']
        );

    /*
    |--------------------------------------------------------------------------
    | Harga Service
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        SELECT
            base_price
        FROM service_types
        WHERE id = ?
    ");

    $stmt->bind_param(
        "i",
        $serviceTypeId
    );

    $stmt->execute();

    $service =
        $stmt
        ->get_result()
        ->fetch_assoc();

    if (!$service) {

        $_SESSION['error'] =
            'Service tidak ditemukan';

        header(
            'Location:create_booking.php'
        );

        exit;
    }

    $servicePrice =
        $service['base_price'];

    /*
    |--------------------------------------------------------------------------
    | Cek Slot Sudah Dipakai
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        SELECT COUNT(*) total
        FROM bookings
        WHERE booking_date = ?
        AND time_slot_id = ?
        AND status NOT IN
        (
            'cancelled'
        )
    ");

    $stmt->bind_param(
        "si",
        $bookingDate,
        $timeSlotId
    );

    $stmt->execute();

    $slotUsed =
        $stmt
        ->get_result()
        ->fetch_assoc()['total'];

    if ($slotUsed > 0) {

        $_SESSION['error'] =
            'Time slot sudah digunakan';

        header(
            'Location:create_booking.php'
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Insert Booking
    |--------------------------------------------------------------------------
    */

    $totalPrice =
        $servicePrice;

    $stmt = $conn->prepare("
        INSERT INTO bookings
        (
            customer_id,
            motor_id,
            service_type_id,
            time_slot_id,
            booking_date,
            service_price,
            total_price,
            status,
            customer_complaint
        )
        VALUES
        (
            ?, ?, ?, ?, ?,
            ?, ?, 'queued', ?
        )
    ");

        $stmt->bind_param(
        "iiiisdds",
        $customerId,
        $motorId,
        $serviceTypeId,
        $timeSlotId,
        $bookingDate,
        $servicePrice,
        $totalPrice,
        $complaint
    );

    $stmt->execute();

    $bookingId =
        $conn->insert_id;

            $stmt = $conn->prepare("
            INSERT INTO payments
            (
                booking_id,
                payment_method,
                amount,
                status
            )
            VALUES
            (
                ?, ?, ?, 'pending'
            )
        ");

        $stmt->bind_param(
            "isd",
            $bookingId,
            $paymentMethod,
            $totalPrice
        );

$stmt->execute();
    /*
    |--------------------------------------------------------------------------
    | Service Log
    |--------------------------------------------------------------------------
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
            ?, ?,
            '',
            'queued',
            'Booking dibuat admin'
        )
    ");

    $stmt->bind_param(
        "ii",
        $bookingId,
        $userId
    );

    $stmt->execute();

    $_SESSION['success'] =
        'Booking berhasil dibuat';

    header(
        'Location:bookings.php'
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Motor Data (untuk Javascript)
|--------------------------------------------------------------------------
*/

$motorQuery = $conn->query("
    SELECT
        id,
        customer_id,
        brand,
        model,
        plate_number
    FROM motors
    ORDER BY brand ASC
");

$motors = [];

while($row = $motorQuery->fetch_assoc()) {
    $motors[] = $row;
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

    <div class="flex-1 flex flex-col min-w-0 bg-gray-100 overflow-y-auto">

        <!-- HEADER -->

        <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] p-5">

            <div class="flex justify-between items-center">

                <div>

                    <p class="text-[#FF0000] text-xs font-semibold tracking-[0.25em] uppercase">
                        WALK IN BOOKING
                    </p>

                    <h1 class="text-white text-4xl font-semibold mt-2">
                        Buat Booking Baru
                    </h1>

                    <p class="text-white mt-2">
                        Input booking customer langsung dari admin.
                    </p>

                </div>

            </div>

        </div>

        <!-- CONTENT -->

        <div class="p-4">

            <?php if(isset($_SESSION['error'])): ?>

                <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">

                    <?= $_SESSION['error']; ?>

                </div>

                <?php unset($_SESSION['error']); ?>

            <?php endif; ?>

            <form method="POST">

                <div class="bg-white rounded-xl shadow-sm p-6">

                    <h2 class="text-xl font-semibold mb-6">
                        Form Booking
                    </h2>

                    <div class="grid md:grid-cols-2 gap-5">

                        <!-- CUSTOMER -->

                        <div>

                            <label class="block text-sm mb-2">
                                Customer
                            </label>

                            <select
                                name="customer_id"
                                id="customerSelect"
                                required
                                class="w-full border rounded-lg p-3"
                            >

                                <option value="">
                                    Pilih Customer
                                </option>

                                <?php while($customer = $customers->fetch_assoc()): ?>

                                <option value="<?= $customer['id'] ?>">

                                    <?= htmlspecialchars(
                                        $customer['name']
                                    ) ?>

                                    -

                                    <?= htmlspecialchars(
                                        $customer['phone']
                                    ) ?>

                                </option>

                                <?php endwhile; ?>

                            </select>

                        </div>

                        <!-- MOTOR -->

                        <div>

                            <label class="block text-sm mb-2">
                                Motor
                            </label>

                            <select
                                name="motor_id"
                                id="motorSelect"
                                required
                                class="w-full border rounded-lg p-3"
                            >

                                <option value="">
                                    Pilih Motor
                                </option>

                            </select>

                        </div>

                        <!-- SERVICE -->

                        <div>

                            <label class="block text-sm mb-2">
                                Service
                            </label>

                            <select
                                name="service_type_id"
                                required
                                class="w-full border rounded-lg p-3"
                            >

                                <option value="">
                                    Pilih Service
                                </option>

                                <?php while($service = $serviceTypes->fetch_assoc()): ?>

                                <option value="<?= $service['id'] ?>">

                                    <?= htmlspecialchars(
                                        $service['name']
                                    ) ?>

                                    -
                                    Rp<?= number_format(
                                        $service['base_price'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                </option>

                                <?php endwhile; ?>

                            </select>

                        </div>

                        <!-- PAYMENT METHOD -->

                            <div>

                                <label class="block text-sm mb-2">
                                    Metode Pembayaran
                                </label>

                                <select
                                    name="payment_method"
                                    required
                                    class="w-full border rounded-lg p-3"
                                >

                                    <option value="">
                                        Pilih Metode Pembayaran
                                    </option>

                                    <option value="cash">
                                        Cash
                                    </option>

                                    <option value="transfer">
                                        Transfer Bank
                                    </option>

                                    <option value="ewallet">
                                        E-Wallet
                                    </option>

                                </select>

                            </div>

                        <!-- TANGGAL -->

                        <div>

                            <label class="block text-sm mb-2">
                                Tanggal Booking
                            </label>

                            <input
                                type="date"
                                name="booking_date"
                                required
                                min="<?= date('Y-m-d') ?>"
                                class="w-full border rounded-lg p-3"
                            >

                        </div>

                        <!-- SLOT -->

                        <div>

                            <label class="block text-sm mb-2">
                                Time Slot
                            </label>

                            <select
    name="time_slot_id"
    required
    class="w-full border rounded-lg px-4 py-3"
>

    <option value="">
        Pilih Slot
    </option>

    <?php while($slot = $timeSlots->fetch_assoc()): ?>

        <option value="<?= $slot['id'] ?>">

            <?= ucfirst($slot['day']) ?>
            |
            <?= date('H:i', strtotime($slot['start_time'])) ?>
            -
            <?= date('H:i', strtotime($slot['end_time'])) ?>

        </option>

    <?php endwhile; ?>

</select>

                        </div>

                    </div>

                    <!-- COMPLAINT -->

                    <div class="mt-5">

                        <label class="block text-sm mb-2">
                            Keluhan Customer
                        </label>

                        <textarea
                            name="customer_complaint"
                            rows="5"
                            required
                            class="w-full border rounded-lg p-3"
                            placeholder="Masukkan keluhan customer..."
                        ></textarea>

                    </div>

                    <!-- BUTTON -->

                    <div class="flex justify-end gap-3 mt-6">

                        <a
                            href="bookings.php"
                            class="bg-gray-200 px-5 py-3 rounded-lg"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            name="create_booking"
                            class="bg-[#8E1616] text-white px-6 py-3 rounded-lg hover:bg-[#6e1111]"
                        >
                            Simpan Booking
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>
<script>

const motors =
<?= json_encode($motors); ?>;

const customerSelect =
document.getElementById(
    'customerSelect'
);

const motorSelect =
document.getElementById(
    'motorSelect'
);

customerSelect.addEventListener(
    'change',
    function () {

        const customerId =
            this.value;

        motorSelect.innerHTML =
            '<option value="">Pilih Motor</option>';

        if (!customerId) {
            return;
        }

        motors.forEach(function(motor){

            if (
                motor.customer_id ==
                customerId
            ) {

                const option =
                    document.createElement(
                        'option'
                    );

                option.value =
                    motor.id;

                option.textContent =
                    motor.brand +
                    ' ' +
                    motor.model +
                    ' - ' +
                    motor.plate_number;

                motorSelect.appendChild(
                    option
                );

            }

        });

    }
);

</script>

</body>
</html>