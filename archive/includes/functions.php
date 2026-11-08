<?php
// Small helper functions used by the pages.

// Make text safe to print in HTML
function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Send the visitor to another page
function redirect($page) {
    header('Location: ' . $page);
    exit;
}

// Look up a user by email. Returns an array, or null if not found.
function find_user($email) {
    global $conn;

    $stmt = mysqli_prepare($conn, 'SELECT * FROM archive_users WHERE email = ?');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

// Save a new user. The password is hashed so the real one is never stored.
function create_user($name, $email, $password) {
    global $conn;

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, 'INSERT INTO archive_users (name, email, password_hash) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $hash);
    mysqli_stmt_execute($stmt);
}

// Get the user who is signed in (or null)
function current_member() {
    global $conn;

    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $id = (int) $_SESSION['user_id'];
    $result = mysqli_query($conn, "SELECT * FROM archive_users WHERE id = $id");

    return mysqli_fetch_assoc($result);
}

// For pages only members can see
function require_login() {
    $user = current_member();
    if ($user == null) {
        redirect('index.php');
    }
    return $user;
}

// For sign in / register pages: members go to the reading room instead
function guest_only() {
    if (current_member() != null) {
        redirect('archive.php');
    }
}
