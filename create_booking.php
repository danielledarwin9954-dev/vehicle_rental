<?php
include '../includes/auth_check.php';
check_role(['staff']);
include '../config/database.php';
$page_title = 'Create Booking';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'] ?? '';
    $vehicle_id = $_POST['vehicle_id'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    try {
        // Get vehicle daily rate
        $stmt = $pdo->prepare('SELECT daily_rate FROM vehicles WHERE id = ?');
        $stmt->execute([$vehicle_id]);
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $days = (strtotime($end_date) - strtotime($start_date)) / 86400 + 1;
        $total_cost = $vehicle['daily_rate'] * $days;

        $stmt = $pdo->prepare('INSERT INTO bookings (customer_id, vehicle_id, start_date, end_date, total_cost, status) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$customer_id, $vehicle_id, $start_date, $end_date, $total_cost, 'pending']);
        $success = 'Booking created successfully!';
    } catch (Exception $e) {
        $error = 'Failed to create booking: ' . $e->getMessage();
    }
}

try {
    $customers = $pdo->query('SELECT id, name FROM users WHERE role = "customer"')->fetchAll(PDO::FETCH_ASSOC);
    $vehicles = $pdo->query('SELECT id, make, model, daily_rate FROM vehicles WHERE status = "available"')->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Error fetching data: ' . $e->getMessage();
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h2 class="mb-4">Create Booking</h2>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Customer</label>
                    <select class="form-control" id="customer_id" name="customer_id" required>
                        <option value="">Select Customer</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="vehicle_id" class="form-label">Vehicle</label>
                    <select class="form-control" id="vehicle_id" name="vehicle_id" required>
                        <option value="">Select Vehicle</option>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <option value="<?php echo $vehicle['id']; ?>"><?php echo htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model']); ?> - $<?php echo number_format($vehicle['daily_rate'], 2); ?>/day</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                </div>
                <div class="mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Create Booking</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>