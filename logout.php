<?php
session_start();

// --- Clear all session variables ---
$_SESSION = [];

// --- Destroy the session ---
session_destroy();

// --- Regenerate session ID for security ---
session_regenerate_id(true);

// --- Redirect to homepage ---
header('Location: index.php');
exit;
?>