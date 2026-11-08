<?php
// Sign in page
require 'includes/config.php';
require 'includes/functions.php';
require 'includes/key-field.php';

guest_only();   // already signed in? go to the reading room

$error   = '';
$email   = '';
$message = '';

// A message from another page (for example after registering)
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if (isset($_SESSION['new_email'])) {
    $email = $_SESSION['new_email'];
    unset($_SESSION['new_email']);
}

// The form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    $user = find_user($email);

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        redirect('archive.php');
    } else {
        // Same message for both cases so nobody can guess which part was wrong
        $error = 'No record matches that email and access key.';
    }
}

$page_title = 'Sign In';
$body_class = 'page-signin';
$active     = 'signin';

$ledger = [
    'quote' => 'Nothing is ever truly lost. It is only waiting to be filed.',
    'cite'  => 'Archive motto',
    'cards' => [
        ['label' => 'Box 014 · Personal stories',     'title' => 'Letters never sent, 1923–1931',  'note' => 'Found behind a wardrobe.'],
        ['label' => 'Folder 07 · Photographs',        'title' => 'Harbour, summer. Nobody named.', 'note' => 'Nine faces. Two names recovered.'],
        ['label' => 'Item 3302 · Historical records', 'title' => 'Ledger of a bakery that closed', 'note' => 'Condition: fragile.', 'stamp' => 'Unclaimed'],
    ],
];

require 'includes/header.php';
require 'includes/ledger.php';
?>

    <section class="card" aria-labelledby="card-title">
        <span class="corner corner--tl" aria-hidden="true"></span>
        <span class="corner corner--tr" aria-hidden="true"></span>
        <span class="corner corner--bl" aria-hidden="true"></span>
        <span class="corner corner--br" aria-hidden="true"></span>

        <header class="card__head">
            <p class="eyebrow">Reading room · Members' entrance</p>
            <h1 class="card__title" id="card-title">Welcome back, <em>Archivist.</em></h1>
            <p class="card__lede">Your collection has been kept safe. Present your access key to re-enter the stacks.</p>
        </header>

        <div class="rule" aria-hidden="true"></div>

        <?php if ($message != ''): ?>
            <div class="alert alert--success" role="status"><?= e($message) ?></div>
        <?php endif; ?>

        <?php if ($error != ''): ?>
            <div class="alert alert--error" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <form class="form" method="post" action="index.php">
            <div class="field">
                <label class="label" for="email">Email address</label>
                <input class="input" type="email" id="email" name="email"
                       value="<?= e($email) ?>" placeholder="you@example.com" required>
            </div>

            <?php key_field('password', 'Access key', 'current-password'); ?>

            <button class="btn" type="submit">Enter the Archive</button>
        </form>

        <p class="card__foot">New to the archive? <a href="register.php">Begin your collection</a></p>
    </section>

<?php require 'includes/footer.php'; ?>
