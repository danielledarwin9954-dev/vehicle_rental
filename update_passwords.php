<?php
// generate_login_credentials.php
require_once __DIR__ . '/config/database.php';

// Users to generate credentials for
$users = [
    'admin@vehiclerental.com' => 'admin@vehiclerental.com',
    'staff1@vehiclerental.com' => 'staff1@vehiclerental.com',
    'staff2@vehiclerental.com' => 'staff2@vehiclerental.com',
];

// Function to generate a strong random password
function generatePassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET email = :new_email, password = :new_password WHERE email = :current_email");

    echo "<h2>Generated Login Credentials:</h2>";

    foreach ($users as $current_email => $new_email) {
        // Generate a secure random password
        $new_password = generatePassword(12);

        // Hash the password before storing in DB
        $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);

        // Execute the update
        $stmt->execute([
            ':new_email' => $new_email,
            ':new_password' => $hashedPassword,
            ':current_email' => $current_email
        ]);

        if ($stmt->rowCount() > 0) {
            echo "✅ User updated successfully:<br>";
            echo "Email: <b>$new_email</b><br>";
            // DISPLAY THE PLAIN PASSWORD for login
            echo "Password: <b>$new_password</b><br><br>";
        } else {
            echo "⚠ No user found with email: <b>$current_email</b><br><br>";
        }
    }

    echo "<a href='login.php'>Go to Login</a>";

} catch (Exception $e) {
    echo "Error updating credentials: " . $e->getMessage();
}