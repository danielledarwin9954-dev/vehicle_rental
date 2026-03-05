<?php
include '../includes/auth_check.php';
check_role(['customer']);
include '../config/database.php';

$page_title = 'My Bookings';

$customer_id = $_SESSION['user_id'];
$error = '';
$bookings = [];

try {
    $stmt = $pdo->prepare("
        SELECT b.*, v.make, v.model 
        FROM bookings b
        JOIN vehicles v ON b.vehicle_id = v.id
        WHERE b.customer_id = ?
        ORDER BY b.id DESC
    ");
    $stmt->execute([$customer_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error = 'Error fetching bookings: ' . $e->getMessage();
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">My Bookings</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Vehicle</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Total Cost</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= $booking['id'] ?></td>
                        <td><?= htmlspecialchars($booking['make'] . ' ' . $booking['model']) ?></td>
                        <td><?= $booking['start_date'] ?></td>
                        <td><?= $booking['end_date'] ?></td>
                        <td>GH₵ <?= number_format($booking['total_cost'], 2) ?></td>

                        <!-- Booking Status -->
                        <td>
                            <span class="badge bg-<?= 
                                $booking['status'] === 'completed' ? 'success' : 
                                ($booking['status'] === 'confirmed' ? 'primary' : 'warning') 
                            ?>">
                                <?= ucfirst($booking['status']) ?>
                            </span>
                        </td>

                        <!-- Payment Status -->
                        <td>
                            <span class="badge bg-<?= 
                                $booking['payment_status'] === 'paid' ? 'success' : 'danger' 
                            ?>">
                                <?= ucfirst($booking['payment_status']) ?>
                            </span>
                        </td>

                        <!-- Action -->
                        <td>
                            <?php if ($booking['payment_status'] === 'unpaid'): ?>
                                <a href="payment.php?booking_id=<?= $booking['id'] ?>" 
                                   class="btn btn-sm btn-warning">
                                   Make Payment
                                </a>
                            <?php else: ?>
                                <span class="text-success">Paid</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">
                        No bookings found.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>

        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>