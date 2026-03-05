<?php
include '../includes/auth_check.php';
check_role(['admin']);
include '../config/database.php';

$page_title = 'Reports';

// Initialize variables
$revenue_report = [];
$booking_report = [];
$error = '';

try {
    // Revenue report
    $stmt = $pdo->query('
        SELECT DATE(created_at) as date, SUM(amount) as total
        FROM payments
        WHERE status = "completed"
        GROUP BY DATE(created_at)
        ORDER BY date DESC
        LIMIT 30
    ');
    $revenue_report = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Booking report
    $stmt = $pdo->query('
        SELECT status, COUNT(*) as count
        FROM bookings
        GROUP BY status
    ');
    $booking_report = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error = 'Error fetching reports: ' . $e->getMessage();
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Reports</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <h3>Revenue Report (Last 30 Days)</h3>
            <?php if (!empty($revenue_report)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($revenue_report as $report): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d', strtotime($report['date'])); ?></td>
                                    <td>$<?php echo number_format($report['total'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No revenue data available.</p>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <h3>Booking Status Report</h3>
            <?php if (!empty($booking_report)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($booking_report as $report): ?>
                                <tr>
                                    <td><?php echo ucfirst($report['status']); ?></td>
                                    <td><?php echo $report['count']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No booking data available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>