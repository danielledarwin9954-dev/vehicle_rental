<?php
include '../includes/auth_check.php';
check_role(['admin']);
include '../config/database.php';

$page_title = 'Manage Maintenance';

$error = '';
$records = [];
$totalCost = 0;

// =============================
// ADD MAINTENANCE
// =============================
if (isset($_POST['add'])) {
    $vehicle_id = $_POST['vehicle_id'];
    $description = $_POST['description'];
    $cost = $_POST['cost'];
    $maintenance_date = $_POST['maintenance_date'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("
        INSERT INTO maintenance_records 
        (vehicle_id, description, cost, maintenance_date, status) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$vehicle_id, $description, $cost, $maintenance_date, $status]);

    // Set vehicle status to maintenance if pending
    if ($status === 'pending') {
        $pdo->prepare("UPDATE vehicles SET status='maintenance' WHERE id=?")
            ->execute([$vehicle_id]);
    }

    header("Location: manage_maintenance.php");
    exit;
}

// =============================
// UPDATE MAINTENANCE STATUS
// =============================
if (isset($_POST['update_status'])) {
    $id = intval($_POST['id']);
    $new_status = $_POST['status'];

    // Update maintenance record status
    $stmt = $pdo->prepare("UPDATE maintenance_records SET status=?, updated_at=NOW() WHERE id=?");
    $stmt->execute([$new_status, $id]);

    // Update vehicle status
    $record = $pdo->prepare("SELECT vehicle_id FROM maintenance_records WHERE id=?");
    $record->execute([$id]);
    $vehicle_id = $record->fetchColumn();

    if ($new_status === 'pending') {
        $pdo->prepare("UPDATE vehicles SET status='maintenance' WHERE id=?")->execute([$vehicle_id]);
    } else {
        $pdo->prepare("UPDATE vehicles SET status='available' WHERE id=?")->execute([$vehicle_id]);
    }

    header("Location: manage_maintenance.php");
    exit;
}

// =============================
// DELETE MAINTENANCE
// =============================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM maintenance_records WHERE id=?")->execute([$id]);
    header("Location: manage_maintenance.php");
    exit;
}

// =============================
// FETCH RECORDS
// =============================
try {
    $stmt = $pdo->query("
        SELECT m.*, v.make, v.model
        FROM maintenance_records m
        JOIN vehicles v ON m.vehicle_id = v.id
        ORDER BY m.id DESC
    ");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($records as $r) {
        $totalCost += $r['cost'];
    }

} catch (Exception $e) {
    $error = $e->getMessage();
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-5">
    <h1>Manage Maintenance</h1>

    <div class="alert alert-info">
        <strong>Total Maintenance Cost:</strong> GH₵ <?= number_format($totalCost, 2) ?>
    </div>

    <!-- ADD FORM -->
    <h4>Add Maintenance Record</h4>
    <form method="POST" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <select name="vehicle_id" class="form-control" required>
                    <option value="">Select Vehicle</option>
                    <?php
                    $vehicles = $pdo->query("SELECT id, make, model FROM vehicles")->fetchAll();
                    foreach ($vehicles as $v):
                    ?>
                        <option value="<?= $v['id'] ?>"><?= $v['make'] . ' ' . $v['model'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="description" class="form-control" placeholder="Description" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="cost" class="form-control" placeholder="Cost" required>
            </div>
            <div class="col-md-2">
                <input type="date" name="maintenance_date" class="form-control" required>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
        </div>
        <button type="submit" name="add" class="btn btn-primary mt-2">Add Record</button>
    </form>

    <!-- TABLE -->
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Vehicle</th>
                <th>Description</th>
                <th>Cost</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= $r['make'] . ' ' . $r['model'] ?></td>
                <td><?= htmlspecialchars($r['description']) ?></td>
                <td>GH₵ <?= number_format($r['cost'], 2) ?></td>
                <td><?= $r['maintenance_date'] ?></td>
                <td>
                    <form method="POST" class="d-flex">
                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <select name="status" class="form-select form-select-sm me-2">
                            <option value="pending" <?= $r['status']=='pending'?'selected':'' ?>>Pending</option>
                            <option value="completed" <?= $r['status']=='completed'?'selected':'' ?>>Completed</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-sm btn-success">
                            Confirm
                        </button>
                    </form>
                </td>
                <td>
                    <a href="?delete=<?= $r['id'] ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Delete this record?')">
                       Delete
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>