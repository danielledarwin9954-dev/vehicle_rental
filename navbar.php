<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/Vehicle_Rental_System/index.php">
            Vehicle Rental System
        </a>

        <button class="navbar-toggler" type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">

                <?php if (isset($_SESSION['user_id'])): ?>

                    <?php
                    // Determine dashboard path based on role
                    $dashboard_link = "#";
                    $profile_link   = "#";

                    if ($_SESSION['role'] === 'admin') {
                        $dashboard_link = "/Vehicle_Rental_System/admin/dashboard.php";
                        $profile_link   = "/Vehicle_Rental_System/admin/manage_staff.php"; 
                    } elseif ($_SESSION['role'] === 'staff') {
                        $dashboard_link = "/Vehicle_Rental_System/staff/dashboard.php";
                        $profile_link   = "/Vehicle_Rental_System/staff/dashboard.php";
                    } elseif ($_SESSION['role'] === 'customer') {
                        $dashboard_link = "/Vehicle_Rental_System/customer/dashboard.php";
                        $profile_link   = "/Vehicle_Rental_System/customer/profile.php";
                    }
                    ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $dashboard_link; ?>">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $profile_link; ?>">
                            Profile
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" 
                           href="/Vehicle_Rental_System/logout.php">
                            Logout (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)
                        </a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link" 
                           href="/Vehicle_Rental_System/login.php">
                            Login
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" 
                           href="/Vehicle_Rental_System/register.php">
                            Register
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>