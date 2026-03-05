<?php
include '../includes/auth_check.php';
check_role(['admin']);
include '../config/database.php';

$page_title = 'Manage Bookings';

// Initialize $bookings so foreach always has an array
$bookings = [];
$error = '';

// Fetch all bookings
try {
    $stmt = $pdo->query('
        SELECT b.*, u.name as customer_name, v.make, v.model
        FROM bookings b 
        JOIN users u ON b.customer_id = u.id 
        JOIN vehicles v ON b.vehicle_id = v.id 
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
    <h1 class="mb-4">Manage Bookings</h1>

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
                        <th>Status</th>
                        <th>Total Cost</th>
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
                            <td>
                                <span class="badge bg-<?php echo $booking['status'] === 'confirmed' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </td>
                            <td>$<?php echo number_format($booking['total_cost'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>