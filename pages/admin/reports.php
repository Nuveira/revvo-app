<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Fungsi: Inisialisasi — memulai session, koneksi DB, dan cek role admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Reports | REVVO Admin';
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
checkRole(['admin']);

// Fungsi: Ambil data user login — untuk sidebar nav
$user_id = $_SESSION['user_id'] ?? null;
$nama    = $_SESSION['name'] ?? 'Admin';
$role    = $_SESSION['role'] ?? '';
$profile_photo = null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT profile_photo FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $profile_photo = $row['profile_photo'] ?? null;
    $stmt->close();
}

// Fungsi: Filter bulan dan tahun — default bulan dan tahun sekarang
$bulan = (int)($_GET['bulan'] ?? date('n'));
$tahun = (int)($_GET['tahun'] ?? date('Y'));
if ($bulan < 1 || $bulan > 12) $bulan = date('n');
if ($tahun < 2024 || $tahun > 2027) $tahun = date('Y');

// Fungsi: Helper nama bulan Indonesia
function getNamaBulan($bulan) {
    $nama_bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    return $nama_bulan[$bulan] ?? '';
}

// Fungsi: Query summary — total booking periode ini
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE MONTH(booking_date) = ? AND YEAR(booking_date) = ?");
$stmt->bind_param("ii", $bulan, $tahun);
$stmt->execute();
$total_booking = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Fungsi: Query summary — total revenue (paid only)
$stmt = $conn->prepare("
    SELECT COALESCE(SUM(p.amount), 0) as total_revenue
    FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    WHERE p.status = 'paid' AND MONTH(b.booking_date) = ? AND YEAR(b.booking_date) = ?
");
$stmt->bind_param("ii", $bulan, $tahun);
$stmt->execute();
$total_revenue = $stmt->get_result()->fetch_assoc()['total_revenue'];
$stmt->close();

// Fungsi: Query summary — booking selesai (completed + ready_for_pickup)
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE status IN ('completed', 'ready_for_pickup') AND MONTH(booking_date) = ? AND YEAR(booking_date) = ?");
$stmt->bind_param("ii", $bulan, $tahun);
$stmt->execute();
$total_selesai = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Fungsi: Query summary — booking dibatalkan
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE status = 'cancelled' AND MONTH(booking_date) = ? AND YEAR(booking_date) = ?");
$stmt->bind_param("ii", $bulan, $tahun);
$stmt->execute();
$total_batal = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Fungsi: Query detail transaksi — data lengkap booking untuk tabel dan export
$stmt = $conn->prepare("
    SELECT 
        b.id AS booking_id,
        b.booking_date,
        b.total_price,
        b.status AS booking_status,
        u_cust.name AS customer_name,
        CONCAT(m.brand, ' ', m.model) AS motor_name,
        st.name AS service_name,
        p.amount AS payment_amount,
        p.status AS payment_status,
        p.payment_method
    FROM bookings b
    JOIN customers c ON b.customer_id = c.id
    JOIN users u_cust ON c.user_id = u_cust.id
    JOIN motors m ON b.motor_id = m.id
    JOIN service_types st ON b.service_type_id = st.id
    LEFT JOIN payments p ON p.booking_id = b.id
    WHERE MONTH(b.booking_date) = ? AND YEAR(b.booking_date) = ?
    ORDER BY b.booking_date DESC
");
$stmt->bind_param("ii", $bulan, $tahun);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fungsi: Query spare parts per booking — untuk kolom spare parts di tabel
$booking_parts_map = [];
if (!empty($bookings)) {
    $booking_ids = array_column($bookings, 'booking_id');
    $placeholders = implode(',', array_fill(0, count($booking_ids), '?'));
    $types = str_repeat('i', count($booking_ids));

    $stmt = $conn->prepare("
        SELECT bp.booking_id, sp.name, bp.qty
        FROM booking_parts bp
        JOIN spare_parts sp ON bp.spare_part_id = sp.id
        WHERE bp.booking_id IN ($placeholders)
    ");
    $stmt->bind_param($types, ...$booking_ids);
    $stmt->execute();
    $parts_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($parts_result as $part) {
        $booking_parts_map[$part['booking_id']][] = $part['name'] . ' (' . $part['qty'] . ')';
    }
}

// ============================================================
// EXPORT EXCEL — harus sebelum output HTML apapun
// ============================================================
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    // Fungsi: Export Excel — generate file xlsx menggunakan PhpSpreadsheet
    require_once '../../vendor/autoload.php';

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Laporan');

    // Fungsi: Excel baris 1 — judul laporan
    $sheet->setCellValue('A1', 'Laporan Operasional REVVO - ' . getNamaBulan($bulan) . ' ' . $tahun);
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->mergeCells('A1:I1');

    // Fungsi: Excel baris 2 — tanggal generate
    $sheet->setCellValue('A2', 'Digenerate pada: ' . date('d M Y H:i'));
    $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);

    // Baris 3: kosong

    // Fungsi: Excel baris 4 — header kolom dengan styling
    $headers = ['No', 'Tanggal', 'Customer', 'Motor', 'Layanan', 'Spare Parts', 'Total Harga', 'Status Bayar', 'Status Booking'];
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '4', $header);
        $col++;
    }
    $sheet->getStyle('A4:I4')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
    $sheet->getStyle('A4:I4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1B5E20');
    $sheet->getStyle('A4:I4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Fungsi: Excel baris 5+ — data transaksi
    $row_num = 5;
    foreach ($bookings as $index => $booking) {
        $parts_text = $booking_parts_map[$booking['booking_id']] ?? [];
        $parts_str = !empty($parts_text) ? implode(', ', $parts_text) : '-';

        $sheet->setCellValue('A' . $row_num, $index + 1);
        $sheet->setCellValue('B' . $row_num, date('d/m/Y', strtotime($booking['booking_date'])));
        $sheet->setCellValue('C' . $row_num, $booking['customer_name']);
        $sheet->setCellValue('D' . $row_num, $booking['motor_name']);
        $sheet->setCellValue('E' . $row_num, $booking['service_name']);
        $sheet->setCellValue('F' . $row_num, $parts_str);
        $sheet->setCellValue('G' . $row_num, (float)$booking['total_price']);
        $sheet->setCellValue('H' . $row_num, $booking['payment_status'] ?? 'pending');
        $sheet->setCellValue('I' . $row_num, $booking['booking_status']);
        $row_num++;
    }

    // Fungsi: Excel baris terakhir — total revenue
    $row_num++;
    $sheet->setCellValue('F' . $row_num, 'TOTAL REVENUE');
    $sheet->setCellValue('G' . $row_num, (float)$total_revenue);
    $sheet->getStyle('F' . $row_num . ':G' . $row_num)->getFont()->setBold(true);

    // Fungsi: Excel auto-size — atur lebar kolom otomatis
    foreach (range('A', 'I') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Fungsi: Output file — set HTTP headers dan kirim file xlsx
    $filename = 'laporan-revvo-' . $bulan . '-' . $tahun . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// Fungsi: Helper badge status booking — 3 warna (merah, hijau, abu)
function getBookingBadgeClass($status) {
    if ($status === 'cancelled') {
        return 'bg-red-100 text-red-700 text-xs font-medium px-2.5 py-0.5 rounded-full';
    }
    if (in_array($status, ['completed', 'ready_for_pickup'])) {
        return 'bg-green-100 text-green-700 text-xs font-medium px-2.5 py-0.5 rounded-full';
    }
    return 'bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-0.5 rounded-full';
}

// Fungsi: Helper badge payment status — 3 warna (merah, hijau, abu)
function getPaymentBadgeClass($status) {
    if ($status === 'cancelled') {
        return 'bg-red-100 text-red-700 text-xs font-medium px-2.5 py-0.5 rounded-full';
    }
    if ($status === 'paid') {
        return 'bg-green-100 text-green-700 text-xs font-medium px-2.5 py-0.5 rounded-full';
    }
    return 'bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-0.5 rounded-full';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/logo.png') ?>">
</head>
<body class="font-['Plus_Jakarta_Sans']">
    <div class="flex h-screen">
        <?php include 'nav.php'; ?>

        <div class="flex-1 overflow-auto bg-gray-50">
            <!-- Fungsi: Header halaman — judul dan tombol export -->
            <div class="bg-gradient-to-r from-black via-black via-20% to-[#8E1616] flex justify-between items-center w-full p-5">
                <div class="mx-2">
                    <p class="text-[#8E1616] text-sm tracking-widest">ADMIN PANEL</p>
                    <p class="text-3xl text-white py-1">Laporan Operasional</p>
                    <p class="text-white/70 text-sm">Periode: <?= getNamaBulan($bulan) ?> <?= $tahun ?></p>
                </div>
                <div class="px-3">
                    <a href="reports.php?export=excel&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
                       class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg px-4 py-2 flex items-center gap-2 font-semibold text-sm transition">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                        Export Excel
                    </a>
                </div>
            </div>

            <div class="p-6">
                <!-- Fungsi: Card filter — pilih bulan dan tahun -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-6">
                    <form method="GET" action="reports.php" class="flex flex-wrap items-end gap-4">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Bulan</label>
                            <select name="bulan" class="border border-gray-300 rounded-lg bg-white text-gray-700 px-3 py-2 text-sm focus:outline-none focus:border-red-500">
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= $i ?>" <?= $i === $bulan ? 'selected' : '' ?>><?= getNamaBulan($i) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Tahun</label>
                            <select name="tahun" class="border border-gray-300 rounded-lg bg-white text-gray-700 px-3 py-2 text-sm focus:outline-none focus:border-red-500">
                                <?php for ($y = 2024; $y <= 2027; $y++): ?>
                                    <option value="<?= $y ?>" <?= $y === $tahun ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition cursor-pointer">
                            Tampilkan
                        </button>
                    </form>
                </div>

                <!-- Fungsi: Summary cards — 4 kartu ringkasan operasional -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <p class="text-3xl font-bold text-gray-900"><?= $total_booking ?></p>
                        <p class="text-sm text-gray-500 mt-1">Total Booking</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <p class="text-3xl font-bold text-gray-900">Rp <?= number_format((float)$total_revenue, 0, ',', '.') ?></p>
                        <p class="text-sm text-gray-500 mt-1">Total Revenue</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <p class="text-3xl font-bold text-gray-900"><?= $total_selesai ?></p>
                        <p class="text-sm text-gray-500 mt-1">Booking Selesai</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <p class="text-3xl font-bold text-gray-900"><?= $total_batal ?></p>
                        <p class="text-sm text-gray-500 mt-1">Booking Dibatalkan</p>
                    </div>
                </div>

                <!-- Fungsi: Tabel detail transaksi — data lengkap booking periode ini -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">No</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Tanggal</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Customer</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Motor</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Layanan</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Spare Parts</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Total Harga</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Status Bayar</th>
                                    <th class="text-left px-4 py-3 text-gray-500 text-xs uppercase tracking-wider font-medium">Status Booking</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($bookings)): ?>
                                    <tr>
                                        <td colspan="9" class="px-4 py-16 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <i data-lucide="calendar-x" class="w-12 h-12 text-gray-300"></i>
                                                <p class="text-gray-400 text-sm">Tidak ada transaksi untuk periode ini</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($bookings as $index => $booking): ?>
                                    <?php $parts_list = $booking_parts_map[$booking['booking_id']] ?? []; ?>
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-gray-400"><?= $index + 1 ?></td>
                                        <td class="px-4 py-3 text-gray-700 text-xs whitespace-nowrap"><?= date('d M Y', strtotime($booking['booking_date'])) ?></td>
                                        <td class="px-4 py-3 text-gray-900 font-medium"><?= htmlspecialchars($booking['customer_name']) ?></td>
                                        <td class="px-4 py-3 text-gray-700"><?= htmlspecialchars($booking['motor_name']) ?></td>
                                        <td class="px-4 py-3 text-gray-700"><?= htmlspecialchars($booking['service_name']) ?></td>
                                        <td class="px-4 py-3 text-gray-500 text-xs"><?= !empty($parts_list) ? htmlspecialchars(implode(', ', $parts_list)) : '-' ?></td>
                                        <td class="px-4 py-3 text-gray-900 font-medium">Rp <?= number_format((float)$booking['total_price'], 0, ',', '.') ?></td>
                                        <td class="px-4 py-3">
                                            <span class="<?= getPaymentBadgeClass($booking['payment_status'] ?? 'pending') ?>"><?= htmlspecialchars($booking['payment_status'] ?? 'pending') ?></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="<?= getBookingBadgeClass($booking['booking_status']) ?>"><?= htmlspecialchars($booking['booking_status']) ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Fungsi: Info total bawah -->
                <div class="mt-4">
                    <p class="text-sm text-gray-500">Menampilkan <?= count($bookings) ?> transaksi untuk <?= getNamaBulan($bulan) ?> <?= $tahun ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fungsi: Load Lucide icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
