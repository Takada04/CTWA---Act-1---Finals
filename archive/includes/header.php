<?php
if (!isset($stage_class)) {
    $stage_class = '';
}
$member = current_member();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?> · <?= e($app_name) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500;1,600&amp;family=EB+Garamond:ital,wght@0,400;0,500;1,400&amp;family=Special+Elite&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?= e($body_class) ?>">
<a class="skip-link" href="#main">Skip to content</a>

<header class="site-head">
    <a class="wordmark" href="<?= $member ? 'archive.php' : 'index.php' ?>">
        <svg viewBox="0 0 64 64" width="34" height="34" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="32" cy="32" r="29"/>
            <circle cx="32" cy="32" r="23" stroke-dasharray="1.5 3.4"/>
            <path d="M32 21A6 6 0 0 0 29 32.3L27.5 45H36.5L35 32.3A6 6 0 0 0 32 21Z" fill="currentColor" stroke="none"/>
        </svg>
        <span><?= e($app_name) ?></span>
    </a>

    <nav class="site-nav" aria-label="Account">
        <?php if ($member): ?>
            <a href="archive.php"<?= $active == 'room' ? ' aria-current="page"' : '' ?>>Reading room</a>
            <a href="logout.php">Sign out</a>
        <?php else: ?>
            <a href="index.php"<?= $active == 'signin' ? ' aria-current="page"' : '' ?>>Sign in</a>
            <a href="register.php"<?= $active == 'register' ? ' aria-current="page"' : '' ?>>Register</a>
        <?php endif; ?>
    </nav>
</header>

<main id="main" class="stage <?= e($stage_class) ?>">
