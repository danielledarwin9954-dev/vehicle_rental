<?php
session_start();

// --- Role Constants ---
define('ROLE_ADMIN', 'admin');
define('ROLE_STAFF', 'staff');
define('ROLE_CUSTOMER', 'customer');

// --- Check if user is logged in ---
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please login to access this page.";
    header('Location: login.php');
    exit;
}

// --- Function to check user role ---
/**
 * Restrict access based on allowed roles
 * @param array $allowed_roles
 * @param string $redirect Optional redirect page if unauthorized
 */
function check_role(array $allowed_roles, $redirect = 'unauthorized.php') {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
        $_SESSION['error'] = "You do not have permission to access this page.";
        header("Location: $redirect");
        exit;
    }
}

// --- Get current user role ---
$user_role = $_SESSION['role'] ?? null;

// --- Optional: Auto redirect by role after login ---
// Uncomment if you want role-based landing page automatically
/*
switch($user_role) {
    case ROLE_ADMIN:
        header('Location: /admin/dashboard.php');
        break;
    case ROLE_STAFF:
        header('Location: /staff/dashboard.php');
        break;
    case ROLE_CUSTOMER:
        header('Location: /customer/dashboard.php');
        break;
    default:
        header('Location: login.php');
        break;
}
exit;
*/
?>