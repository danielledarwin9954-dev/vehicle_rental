<?php
session_start();
if (isset($_SESSION['role'])) {

    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    } elseif ($_SESSION['role'] === 'staff') {
        header("Location: staff/dashboard.php");
        exit();
    } elseif ($_SESSION['role'] === 'customer') {
        header("Location: customer/dashboard.php");
        exit();
    }
}
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container mt-5">
    <h1>Welcome to Vehicle Rental System</h1>

    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
    <?php else: ?>
        <p>Please <a href="login.php">Login</a> or <a href="register.php">Register</a>.</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>