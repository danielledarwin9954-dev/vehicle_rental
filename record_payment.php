<?php
include '../includes/auth_check.php';
check_role(['staff']);
include '../config/database.php';
$page_title = 'Record Payment';

$payment_id = null; // store newly inserted payment id

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';

    try {
        $stmt = $pdo->prepare('INSERT INTO payments (booking_id, amount, payment_method, status, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([$booking_id, $amount, $payment_method, 'completed']);
        
        // Get the newly inserted payment ID
        $payment_id = $pdo->lastInsertId();

        $success = 'Payment recorded successfully!';
    } catch (Exception $e) {
        $error = 'Failed to record payment: ' . $e->getMessage();
    }
}

// Fetch confirmed bookings
try {
    $stmt = $pdo->query('SELECT b.id, u.name as customer_name, b.total_cost FROM bookings b
                        JOIN users u ON b.customer_id = u.id
                        WHERE b.status = "confirmed"
                        ORDER BY b.id DESC');
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Error fetching bookings: ' . $e->getMessage();
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h2 class="mb-4">Record Payment</h2>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?= $success ?>

                    <?php if ($payment_id): ?>
                        <div class="mt-3">
                            <a href="print_receipt.php?payment_id=<?= $payment_id ?>" 
                               target="_blank" class="btn btn-sm btn-secondary">
                               Print Receipt
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="booking_id" class="form-label">Booking</label>
                    <select class="form-control" id="booking_id" name="booking_id" required onchange="updateAmount()">
                        <option value="">Select Booking</option>
                        <?php foreach ($bookings as $booking): ?>
                            <option value="<?= $booking['id'] ?>" data-amount="<?= $booking['total_cost'] ?>">
                                ID: <?= $booking['id'] ?> - <?= htmlspecialchars($booking['customer_name']) ?> - $<?= number_format($booking['total_cost'], 2) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-control" id="payment_method" name="payment_method" required>
                        <option value="">Select Method</option>
                        <option value="cash">Cash</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Record Payment</button>
            </form>
        </div>
    </div>
</div>

<script>
function updateAmount() {
    const select = document.getElementById('booking_id');
    const amount = select.options[select.selectedIndex].dataset.amount;
    document.getElementById('amount').value = amount;
}
</script>

<?php include '../includes/footer.php'; ?>