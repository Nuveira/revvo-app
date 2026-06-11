<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
require_once '../../includes/customer_role.php';
require_once '../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$booking_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$booking_id) {
    header('Location: booking.php');
    exit;
}

// ambil data booking — harus milik customer ini
$stmt = $conn->prepare("
    SELECT b.id, b.booking_date, b.total_price, b.service_price, b.status,
           b.customer_complaint, b.mechanic_note, b.created_at,
           u.name AS customer_name, u.email, u.phone,
           m.brand, m.model, m.plate_number, m.color, m.production_year,
           st.name AS service_name,
           ts.start_time, ts.end_time,
           mechanic_user.name AS mechanic_name,
           p.status AS payment_status, p.payment_method, p.paid_at
    FROM bookings b
    JOIN customers c ON b.customer_id = c.id
    JOIN users u ON c.user_id = u.id
    JOIN motors m ON b.motor_id = m.id
    JOIN service_types st ON b.service_type_id = st.id
    JOIN time_slots ts ON b.time_slot_id = ts.id
    LEFT JOIN mechanics me ON b.mechanic_id = me.id
    LEFT JOIN users mechanic_user ON me.user_id = mechanic_user.id
    LEFT JOIN payments p ON p.booking_id = b.id
    WHERE b.id = ? AND b.customer_id = ?
");
$stmt->bind_param("ii", $booking_id, $customer_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    header('Location: booking.php');
    exit;
}

// invoice hanya untuk booking yang sudah selesai
$allowed = ['completed', 'ready_for_pickup'];
if (!in_array($booking['status'], $allowed)) {
    header('Location: booking_detail.php?id=' . $booking_id);
    exit;
}

// ambil spare parts
$stmt = $conn->prepare("
    SELECT bp.qty, bp.price_at_time, bp.subtotal, sp.name AS part_name, sp.unit
    FROM booking_parts bp
    JOIN spare_parts sp ON bp.spare_part_id = sp.id
    WHERE bp.booking_id = ?
");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$parts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$booking_no = 'BK-' . str_pad((string) $booking['id'], 4, '0', STR_PAD_LEFT);
$total_parts = array_sum(array_column($parts, 'subtotal'));

// bangun HTML invoice
ob_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
    .header { background-color: #1D1616; color: #EEEEEE; padding: 24px 30px; }
    .header-brand { font-size: 28px; font-weight: bold; color: #D84040; letter-spacing: 3px; }
    .header-sub { font-size: 11px; color: #aaa; margin-top: 2px; }
    .header-right { float: right; text-align: right; }
    .header-right .invoice-label { font-size: 11px; color: #aaa; }
    .header-right .invoice-no { font-size: 20px; font-weight: bold; color: #EEEEEE; margin-top: 2px; }
    .clearfix::after { content: ""; display: table; clear: both; }

    .section { padding: 20px 30px; }
    .section-title { font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: #8E1616; font-weight: bold; border-bottom: 1px solid #f0e8e8; padding-bottom: 6px; margin-bottom: 12px; }

    .grid-2 { width: 100%; }
    .grid-2 td { width: 50%; vertical-align: top; padding: 0; }
    .info-label { font-size: 10px; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
    .info-value { font-size: 12px; color: #333; font-weight: bold; }

    table.parts { width: 100%; border-collapse: collapse; }
    table.parts thead tr { background-color: #1D1616; }
    table.parts thead th { color: #EEEEEE; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; padding: 8px 10px; text-align: left; }
    table.parts tbody td { padding: 8px 10px; border-bottom: 1px solid #f5f0f0; font-size: 11px; }
    table.parts tbody tr:nth-child(even) { background-color: #fdf9f9; }

    .divider { border: none; border-top: 1px solid #f0e8e8; margin: 0 30px; }

    .totals { padding: 16px 30px; }
    .total-row { display: table; width: 100%; margin-bottom: 6px; }
    .total-label { display: table-cell; font-size: 11px; color: #666; }
    .total-amount { display: table-cell; text-align: right; font-size: 11px; color: #333; }
    .total-final { background-color: #f8eeee; border-radius: 4px; padding: 10px 30px; margin: 0 30px 20px; display: table; width: calc(100% - 60px); }
    .total-final .label { display: table-cell; font-size: 13px; font-weight: bold; color: #8E1616; }
    .total-final .amount { display: table-cell; text-align: right; font-size: 18px; font-weight: bold; color: #8E1616; }

    .footer { background-color: #f9f5f5; padding: 14px 30px; border-top: 2px solid #8E1616; }
    .footer-text { font-size: 10px; color: #999; text-align: center; }
</style>
</head>
<body>

<!-- Header -->
<div class="header clearfix">
    <div style="float:left;">
        <div class="header-brand">REVVO</div>
        <div class="header-sub">Sistem Booking &amp; Tracking Servis Bengkel Motor</div>
    </div>
    <div class="header-right">
        <div class="invoice-label">INVOICE</div>
        <div class="invoice-no"><?= htmlspecialchars($booking_no) ?></div>
        <div style="font-size:10px; color:#aaa; margin-top:4px;">
            <?= date('d M Y', strtotime($booking['booking_date'])) ?>
        </div>
    </div>
</div>

<!-- Info Customer & Motor -->
<div class="section">
    <div class="section-title">Informasi</div>
    <table class="grid-2">
        <tr>
            <td style="padding-right:20px;">
                <p class="info-label">Customer</p>
                <p class="info-value"><?= htmlspecialchars($booking['customer_name']) ?></p>
                <p style="font-size:11px; color:#666; margin-top:2px;"><?= htmlspecialchars($booking['phone'] ?? '-') ?></p>
                <p style="font-size:11px; color:#666;"><?= htmlspecialchars($booking['email'] ?? '-') ?></p>
            </td>
            <td>
                <p class="info-label">Motor</p>
                <p class="info-value"><?= htmlspecialchars($booking['brand'] . ' ' . $booking['model']) ?></p>
                <p style="font-size:11px; color:#666; margin-top:2px;">
                    <?= htmlspecialchars($booking['plate_number']) ?>
                    &bull; <?= htmlspecialchars($booking['color'] ?? '-') ?>
                    &bull; <?= $booking['production_year'] ?? '-' ?>
                </p>
            </td>
        </tr>
    </table>

    <table class="grid-2" style="margin-top:14px;">
        <tr>
            <td style="padding-right:20px;">
                <p class="info-label">Jadwal Servis</p>
                <p class="info-value"><?= date('d M Y', strtotime($booking['booking_date'])) ?></p>
                <p style="font-size:11px; color:#666; margin-top:2px;">
                    <?= date('H:i', strtotime($booking['start_time'])) ?> - <?= date('H:i', strtotime($booking['end_time'])) ?>
                </p>
            </td>
            <td>
                <p class="info-label">Mekanik</p>
                <p class="info-value"><?= htmlspecialchars($booking['mechanic_name'] ?? '-') ?></p>
                <?php if ($booking['payment_status']): ?>
                <p style="font-size:11px; color:#666; margin-top:2px;">
                    Pembayaran: <?= strtoupper($booking['payment_method'] ?? '-') ?>
                    <?php if ($booking['paid_at']): ?>
                    &bull; <?= date('d M Y', strtotime($booking['paid_at'])) ?>
                    <?php endif; ?>
                </p>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

<hr class="divider">

<!-- Rincian Biaya -->
<div class="section">
    <div class="section-title">Rincian Biaya</div>

    <table class="parts">
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Qty</th>
                <th style="text-align:right;">Harga</th>
                <th style="text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <!-- Baris service -->
            <tr>
                <td><?= htmlspecialchars($booking['service_name']) ?></td>
                <td>1</td>
                <td style="text-align:right;">Rp<?= number_format($booking['service_price'], 0, ',', '.') ?></td>
                <td style="text-align:right; font-weight:bold;">Rp<?= number_format($booking['service_price'], 0, ',', '.') ?></td>
            </tr>
            <!-- Spare parts -->
            <?php foreach ($parts as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['part_name']) ?></td>
                <td><?= $p['qty'] ?> <?= htmlspecialchars($p['unit'] ?? '') ?></td>
                <td style="text-align:right;">Rp<?= number_format($p['price_at_time'], 0, ',', '.') ?></td>
                <td style="text-align:right; font-weight:bold;">Rp<?= number_format($p['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Subtotals -->
<div class="totals">
    <div class="total-row">
        <div class="total-label">Biaya Service</div>
        <div class="total-amount">Rp<?= number_format($booking['service_price'], 0, ',', '.') ?></div>
    </div>
    <div class="total-row">
        <div class="total-label">Total Spare Parts</div>
        <div class="total-amount">Rp<?= number_format($total_parts, 0, ',', '.') ?></div>
    </div>
</div>

<!-- Grand Total -->
<div class="total-final">
    <div class="label">TOTAL</div>
    <div class="amount">Rp<?= number_format($booking['total_price'], 0, ',', '.') ?></div>
</div>

<!-- Catatan mekanik -->
<?php if ($booking['mechanic_note']): ?>
<div style="padding: 0 30px 16px;">
    <p style="font-size:10px; color:#999; text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Catatan Mekanik</p>
    <p style="font-size:11px; color:#555;"><?= htmlspecialchars($booking['mechanic_note']) ?></p>
</div>
<?php endif; ?>

<!-- Footer -->
<div class="footer">
    <p class="footer-text">
        TERIMA KASIH SUDAH ORDER DI REVVO | <?= date('d M Y H:i') ?> | REVVO SEMAKIN DI DEPAN!
    </p>
</div>

</body>
</html>
<?php
$html = ob_get_clean();

// render PDF dengan DomPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', false);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = 'Invoice-' . $booking_no . '-' . date('Ymd') . '.pdf';
$dompdf->stream($filename, ['Attachment' => true]);
exit;
