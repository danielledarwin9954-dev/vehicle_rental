<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SESSION['role'] !== 'staff') {
    header('Location: ../index.php');
    exit;
}

include '../config/database.php';
$page_title = 'Staff Dashboard';

try {
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM bookings WHERE status = "pending"');
    $pending_bookings = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt = $pdo->query('SELECT COUNT(*) as count FROM bookings WHERE status = "confirmed"');
    $confirmed_bookings = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (Exception $e) {
    $error = 'Error fetching data: ' . $e->getMessage();
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Staff Dashboard</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Pending Bookings</h5>
                    <h2><?php echo $pending_bookings ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Confirmed Bookings</h5>
                    <h2><?php echo $confirmed_bookings ?? 0; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <h3>Quick Actions</h3>
            <div class="list-group">
                <a href="create_booking.php" class="list-group-item list-group-item-action">Create New Booking</a>
                <a href="confirm_booking.php" class="list-group-item list-group-item-action">Confirm Booking</a>
                <a href="return_vehicle.php" class="list-group-item list-group-item-action">Return Vehicle</a>
                <a href="record_payment.php" class="list-group-item list-group-item-action">Record Payment</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>