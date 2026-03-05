<?php
$passwords = [
    'Admin@123',
    'Staff1@123',
    'Staff2@123'
];

foreach ($passwords as $pw) {
    echo password_hash($pw, PASSWORD_BCRYPT) . "\n";
}