<?php
// Settings and database connection. Included at the top of every page.

session_start();

$app_name    = 'The Archive';
$app_tagline = 'A keeping-place for forgotten things';

// XAMPP defaults: user is "root" and the password is empty
$conn = mysqli_connect('localhost', 'root', '', 'archive_db');

if (!$conn) {
    die('Could not connect to the database. Is MySQL running in XAMPP?');
}

mysqli_set_charset($conn, 'utf8mb4');