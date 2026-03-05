<?php
include '../includes/auth_check.php';
check_role(['staff']);
include '../config/database.php';

$page_title = 'Confirm Booking';

// --- Initialize variables ---
$bookings = [];
$success = '';
$error = '';

// Handle booking confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? '';
    
    if (!empty($booking_id)) {
        try {
            $stmt = $pdo->prepare('UPDATE bookings SET status = "confirmed" WHERE id = ?');
            $stmt->execute([$booking_id]);
            $success = 'Booking confirmed successfully!';
        } catch (Exception $e) {
            $error = 'Failed to confirm booking: ' . $e->getMessage();
        }
    } else {
        $error = 'Invalid booking selected.';
    }
}

// Fetch pending bookings
try {
    $stmt = $pdo->query('
        SELECT b.id, u.name as customer_name, v.make, v.model, b.start_date, b.end_date, b.total_cost 
        FROM bookings b
        JOIN users u ON b.customer_id = u.id
        JOIN vehicles v ON b.vehicle_id = v.id
        WHERE b.status = "pending"
        ORDER BY b.id DESC
    ');
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Error fetching bookings: ' . $e->getMessage();
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">Confirm Booking</h2>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($bookings)): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Vehicle</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Cost</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo $booking['id']; ?></td>
                            <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['make'] . ' ' . $booking['model']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($booking['start_date'])); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($booking['end_date'])); ?></td>
                            <td>$<?php echo number_format($booking['total_cost'], 2); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" class="btn btn-success btn-sm">Confirm</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No pending bookings to confirm.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>