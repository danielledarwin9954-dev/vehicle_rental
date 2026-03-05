<?php
include '../includes/auth_check.php';
check_role(['admin']);
include '../config/database.php';
$page_title = 'Manage Vehicles';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $make = $_POST['make'] ?? '';
        $model = $_POST['model'] ?? '';
        $year = $_POST['year'] ?? '';
        $license_plate = $_POST['license_plate'] ?? '';
        $daily_rate = $_POST['daily_rate'] ?? '';
        $status = $_POST['status'] ?? 'available';

        try {
            $stmt = $pdo->prepare('INSERT INTO vehicles (make, model, year, license_plate, daily_rate, status) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$make, $model, $year, $license_plate, $daily_rate, $status]);
            $success = 'Vehicle added successfully!';
        } catch (Exception $e) {
            $error = 'Failed to add vehicle: ' . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        $vehicle_id = $_POST['vehicle_id'] ?? '';
        try {
            $stmt = $pdo->prepare('DELETE FROM vehicles WHERE id = ?');
            $stmt->execute([$vehicle_id]);
            $success = 'Vehicle deleted successfully!';
        } catch (Exception $e) {
            $error = 'Failed to delete vehicle: ' . $e->getMessage();
        }
    }
}

try {
    $stmt = $pdo->query('SELECT * FROM vehicles ORDER BY id DESC');
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Error fetching vehicles: ' . $e->getMessage();
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Manage Vehicles</h1>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Add New Vehicle</div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="row">
                    <div class="col-md-2">
                        <input type="text" name="make" placeholder="Make" class="form-control mb-2" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="model" placeholder="Model" class="form-control mb-2" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="year" placeholder="Year" class="form-control mb-2" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="license_plate" placeholder="License Plate" class="form-control mb-2" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="daily_rate" placeholder="Daily Rate" step="0.01" class="form-control mb-2" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">Add Vehicle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>License Plate</th>
                    <th>Daily Rate</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehicles as $vehicle): ?>
                    <tr>
                        <td><?php echo $vehicle['id']; ?></td>
                        <td><?php echo htmlspecialchars($vehicle['make']); ?></td>
                        <td><?php echo htmlspecialchars($vehicle['model']); ?></td>
                        <td><?php echo $vehicle['year']; ?></td>
                        <td><?php echo htmlspecialchars($vehicle['license_plate']); ?></td>
                        <td>$<?php echo number_format($vehicle['daily_rate'], 2); ?></td>
                        <td><span class="badge bg-<?php echo $vehicle['status'] === 'available' ? 'success' : 'danger'; ?>"><?php echo ucfirst($vehicle['status']); ?></span></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>