<?php
include 'includes/auth_check.php';
check_role(['admin','staff','customer']);

include 'config/database.php';

$payment_id = $_GET['payment_id'] ?? 0;
$payment_id = intval($payment_id);

if (!$payment_id) die("Invalid payment ID.");

$stmt = $pdo->prepare("
    SELECT p.*, b.id as booking_id, b.start_date, b.end_date, b.total_cost, 
           v.make, v.model, u.name as customer_name
    FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    JOIN vehicles v ON b.vehicle_id = v.id
    JOIN users u ON b.customer_id = u.id
    WHERE p.id = ?
");
$stmt->execute([$payment_id]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) die("Payment not found.");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt #<?= $payment['id'] ?></title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .receipt { max-width: 600px; margin: auto; border: 1px solid #ccc; padding: 20px; }
        .receipt h2 { text-align: center; }
        .receipt table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .receipt table td { padding: 5px; border-bottom: 1px solid #eee; }
        .btn-print { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
<div class="receipt">
    <h2>Payment Receipt</h2>
    <p><strong>Receipt ID:</strong> <?= $payment['id'] ?></p>
    <p><strong>Customer:</strong> <?= htmlspecialchars($payment['customer_name']) ?></p>
    <p><strong>Booking ID:</strong> <?= $payment['booking_id'] ?></p>
    <p><strong>Vehicle:</strong> <?= htmlspecialchars($payment['make'] . ' ' . $payment['model']) ?></p>
    <p><strong>Rental Period:</strong> <?= date('Y-m-d', strtotime($payment['start_date'])) ?> to <?= date('Y-m-d', strtotime($payment['end_date'])) ?></p>
    <p><strong>Payment Method:</strong> <?= htmlspecialchars($payment['payment_method']) ?></p>
    <p><strong>Status:</strong> <?= ucfirst($payment['status']) ?></p>
    <p><strong>Amount Paid:</strong> GH₵ <?= number_format($payment['amount'], 2) ?></p>
    <p><strong>Date:</strong> <?= date('Y-m-d', strtotime($payment['created_at'])) ?></p>

    <div class="btn-print">
        <button onclick="window.print()">Print Receipt</button>
    </div>
</div>
</body>
</html>