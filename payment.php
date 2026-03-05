<?php
include '../includes/auth_check.php';
check_role(['customer']);
include '../config/database.php';

$page_title = 'Make Payment';
$customer_id = $_SESSION['user_id'];

// Initialize variables
$success = '';
$error = '';
$payment_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $card_number = $_POST['card_number'] ?? '';
    $card_expiry = $_POST['card_expiry'] ?? '';
    $card_cvv = $_POST['card_cvv'] ?? '';

    if ($booking_id && $amount && $payment_method) {
        try {
            // Validate booking belongs to customer
            $stmt = $pdo->prepare('SELECT id FROM bookings WHERE id = ? AND customer_id = ?');
            $stmt->execute([$booking_id, $customer_id]);
            if (!$stmt->fetch()) {
                $error = 'Invalid booking!';
            } else {
                // Insert payment
                $stmt = $pdo->prepare('INSERT INTO payments (booking_id, amount, payment_method, status, created_at) VALUES (?, ?, ?, ?, NOW())');
                $stmt->execute([$booking_id, $amount, $payment_method, 'completed']);
                
                // Update booking payment_status
                $updateBooking = $pdo->prepare("UPDATE bookings SET payment_status = 'paid' WHERE id = ?");
                $updateBooking->execute([$booking_id]);

                $payment_id = $pdo->lastInsertId(); // Capture payment ID for receipt
                $success = 'Payment processed successfully!';
            }
        } catch (Exception $e) {
            $error = 'Payment failed: ' . $e->getMessage();
        }
    } else {
        $error = "Please fill all required fields.";
    }
}

// Fetch customer bookings
try {
    $stmt = $pdo->prepare('
        SELECT b.id, b.total_cost, v.make, v.model, b.start_date, b.end_date 
        FROM bookings b
        JOIN vehicles v ON b.vehicle_id = v.id
        WHERE b.customer_id = ? AND b.status IN ("pending", "confirmed")
        ORDER BY b.id DESC
    ');
    $stmt->execute([$customer_id]);
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
            <h2 class="mb-4">Make Payment</h2>

            <!-- Success/Error Messages -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                    <?php if ($payment_id): ?>
                        <br>
                        <a href="print_receipt.php?payment_id=<?= $payment_id ?>" target="_blank" class="btn btn-sm btn-secondary mt-2">
                            Print Receipt
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Payment Form -->
            <form method="POST">
                <div class="mb-3">
                    <label for="booking_id" class="form-label">Select Booking</label>
                    <select class="form-control" id="booking_id" name="booking_id" required onchange="updatePaymentAmount()">
                        <option value="">Select a Booking</option>
                        <?php foreach ($bookings as $booking): ?>
                            <option value="<?= $booking['id'] ?>" data-amount="<?= $booking['total_cost'] ?>">
                                <?= htmlspecialchars($booking['make'] . ' ' . $booking['model']); ?> 
                                (<?= date('Y-m-d', strtotime($booking['start_date'])); ?> to <?= date('Y-m-d', strtotime($booking['end_date'])); ?>) 
                                - GH₵ <?= number_format($booking['total_cost'], 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" readonly>
                </div>

                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-control" id="payment_method" name="payment_method" required onchange="toggleCardFields()">
                        <option value="">Select Payment Method</option>
                        <option value="cash">Cash</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>

                <div id="card-fields" style="display: none;">
                    <div class="mb-3">
                        <label for="card_number" class="form-label">Card Number</label>
                        <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="card_expiry" class="form-label">Expiry Date</label>
                                <input type="text" class="form-control" id="card_expiry" name="card_expiry" placeholder="MM/YY" maxlength="5">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="card_cvv" class="form-label">CVV</label>
                                <input type="text" class="form-control" id="card_cvv" name="card_cvv" placeholder="123" maxlength="3">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100">Pay Now</button>
            </form>
        </div>
    </div>
</div>

<script>
function updatePaymentAmount() {
    const select = document.getElementById('booking_id');
    const amount = select.options[select.selectedIndex].dataset.amount;
    document.getElementById('amount').value = amount || '';
}

function toggleCardFields() {
    const paymentMethod = document.getElementById('payment_method').value;
    const cardFields = document.getElementById('card-fields');
    cardFields.style.display = (paymentMethod === 'credit_card' || paymentMethod === 'debit_card') ? 'block' : 'none';
}
</script>

<?php include '../includes/footer.php'; ?>