<?php
// Sign out: remove the login from the session, then go back to sign in.
require 'includes/config.php';
require 'includes/functions.php';

unset($_SESSION['user_id']);
session_regenerate_id(true);

$_SESSION['message'] = 'You have left the reading room. Your collection is kept safe.';
redirect('index.php');
