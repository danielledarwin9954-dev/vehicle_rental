<?php
include '../includes/auth_check.php';
check_role(['customer']);
include '../config/database.php';
$page_title = 'Book a Vehicle';

$customer_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_id = $_POST['vehicle_id'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    try {
        $stmt = $pdo->prepare('SELECT daily_rate FROM vehicles WHERE id = ?');
        $stmt->execute([$vehicle_id]);
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $days = (strtotime($end_date) - strtotime($start_date)) / 86400 + 1;
        $total_cost = $vehicle['daily_rate'] * $days;

        $stmt = $pdo->prepare('INSERT INTO bookings (customer_id, vehicle_id, start_date, end_date, total_cost, status) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$customer_id, $vehicle_id, $start_date, $end_date, $total_cost, 'pending']);
        $success = 'Booking request submitted! Awaiting staff confirmation.';
    } catch (Exception $e) {
        $error = 'Failed to create booking: ' . $e->getMessage();
    }
}

try {
    $vehicles = $pdo->query('SELECT id, make, model, daily_rate FROM vehicles WHERE status = "available"')->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Error fetching vehicles: ' . $e->getMessage();
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h2 class="mb-4">Book a Vehicle</h2>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="vehicle_id" class="form-label">Select Vehicle</label>
                    <select class="form-control" id="vehicle_id" name="vehicle_id" required onchange="updatePrice()">
                        <option value="">Choose a Vehicle</option>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <option value="<?php echo $vehicle['id']; ?>" data-price="<?php echo $vehicle['daily_rate']; ?>"><?php echo htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model']); ?> - $<?php echo number_format($vehicle['daily_rate'], 2); ?>/day</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" required onchange="calculateTotal()">
                </div>
                <div class="mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" required onchange="calculateTotal()">
                </div>
                <div class="mb-3">
                    <label for="total_cost" class="form-label">Estimated Total Cost</label>
                    <input type="text" class="form-control" id="total_cost" disabled>
                </div>
                <button type="submit" class="btn btn-primary w-100">Book Now</button>
            </form>
        </div>
    </div>
</div>

<script>
function updatePrice() {
    calculateTotal();
}

function calculateTotal() {
    const vehicleSelect = document.getElementById('vehicle_id');
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const totalCostInput = document.getElementById('total_cost');

    if (vehicleSelect.value && startDate && endDate) {
        const dailyRate = vehicleSelect.options[vehicleSelect.selectedIndex].dataset.price;
        const days = Math.ceil((new Date(endDate) - new Date(startDate)) / (1000 * 60 * 60 * 24)) + 1;
        const total = dailyRate * days;
        totalCostInput.value = '$' + total.toFixed(2);
    }
}
</script>

<?php include '../includes/footer.php'; ?>