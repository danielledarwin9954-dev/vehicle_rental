<?php
include '../includes/auth_check.php';
check_role(['admin']);
include '../config/database.php';

$page_title = 'Manage Payments';

$error = '';
$payments = [];
$totalRevenue = 0;

/* =============================
   HANDLE STATUS UPDATE
============================= */
if (isset($_GET['complete'])) {
    $id = intval($_GET['complete']);

    // Check if payment exists and is pending
    $check = $pdo->prepare("SELECT booking_id, status FROM payments WHERE id = ?");
    $check->execute([$id]);
    $payment = $check->fetch(PDO::FETCH_ASSOC);

    if ($payment && $payment['status'] === 'pending') {

        // Update payment status
        $stmt = $pdo->prepare("UPDATE payments SET status = 'completed' WHERE id = ?");
        $stmt->execute([$id]);

        // OPTIONAL: Update booking status to 'paid'
        $updateBooking = $pdo->prepare("UPDATE bookings SET payment_status = 'paid' WHERE id = ?");
        $updateBooking->execute([$payment['booking_id']]);
    }

    header("Location: manage_payments.php");
    exit;
}

/* =============================
   HANDLE DELETE
============================= */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $pdo->prepare("DELETE FROM payments WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: manage_payments.php");
    exit;
}

/* =============================
   FETCH PAYMENTS
============================= */
try {
    $stmt = $pdo->query("
        SELECT p.*, b.id AS booking_id, u.name AS customer_name
        FROM payments p
        JOIN bookings b ON p.booking_id = b.id
        JOIN users u ON b.customer_id = u.id
        ORDER BY p.id DESC
    ");
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // More efficient revenue calculation
    $sumStmt = $pdo->query("
        SELECT SUM(amount) as total 
        FROM payments 
        WHERE status = 'completed'
    ");
    $totalRevenue = $sumStmt->fetchColumn() ?? 0;

} catch (Exception $e) {
    $error = 'Error fetching payments: ' . $e->getMessage();
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Manage Payments</h1>

    <!-- Revenue Summary -->
    <div class="alert alert-success">
        <strong>Total Completed Revenue:</strong> GH₵ <?php echo number_format($totalRevenue, 2); ?>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($payments)): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo $payment['id']; ?></td>
                            <td>#<?php echo $payment['booking_id']; ?></td>
                            <td><?php echo htmlspecialchars($payment['customer_name']); ?></td>
                            <td>GH₵ <?php echo number_format($payment['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($payment['payment_method'])); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $payment['status'] === 'completed' ? 'success' : 
                                         ($payment['status'] === 'pending' ? 'warning' : 'danger'); 
                                ?>">
                                    <?php echo ucfirst($payment['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($payment['created_at'])); ?></td>
                            <td>
                                <?php if ($payment['status'] === 'pending'): ?>
                                    <a href="?complete=<?php echo $payment['id']; ?>" 
                                       class="btn btn-sm btn-success mb-1"
                                       onclick="return confirm('Mark this payment as completed?')">
                                       Complete
                                    </a>
                                <?php endif; ?>

                                <a href="?delete=<?php echo $payment['id']; ?>" 
                                   class="btn btn-sm btn-danger mb-1"
                                   onclick="return confirm('Delete this payment?')">
                                   Delete
                                </a>

                                <a href="../print_receipt.php?payment_id=<?php echo $payment['id']; ?>" 
                                   target="_blank" class="btn btn-sm btn-secondary mb-1">
                                   Print Receipt
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No payments found.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>