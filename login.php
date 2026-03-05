<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Redirect already logged-in users
if (isset($_SESSION['role'])) {
    switch($_SESSION['role']) {
        case 'admin': header('Location: admin/dashboard.php'); break;
        case 'staff': header('Location: staff/dashboard.php'); break;
        default: header('Location: customer/dashboard.php'); break;
    }
    exit;
}

$page_title = 'Login - Vehicle Rental System';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            // --- Regenerate session ID ---
            session_regenerate_id(true);

            // Store session
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['email']     = $user['email'];
            $_SESSION['role']      = $user['role'];

            // Role-based redirect
            switch($user['role']) {
                case 'admin': header('Location: admin/dashboard.php'); break;
                case 'staff': header('Location: staff/dashboard.php'); break;
                default: header('Location: customer/dashboard.php'); break;
            }
            exit;

        } else {
            $_SESSION['error'] = 'Invalid email or password!';
        }

    } catch (Exception $e) {
        $_SESSION['error'] = 'Login failed. Please try again.';
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <h2 class="mb-4">Login</h2>

            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <form method="POST" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <p class="mt-3 text-center">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>