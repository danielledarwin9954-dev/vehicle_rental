<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Automatically determine base URL for assets depending on folder depth
if (!isset($base_url)) {
    $base_url = '';
    $request_uri = $_SERVER['REQUEST_URI'];

    // Check for admin, staff, or customer subfolders
    if (strpos($request_uri, '/admin/') !== false ||
        strpos($request_uri, '/staff/') !== false ||
        strpos($request_uri, '/customer/') !== false) {
        $base_url = '../';
    }
}

// Set default page title if not already set
if (!isset($page_title)) {
    $page_title = 'Vehicle Rental System';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
</head>
<body>