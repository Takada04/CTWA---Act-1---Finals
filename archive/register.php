<?php
// Register page
require 'includes/config.php';
require 'includes/functions.php';
require 'includes/key-field.php';

guest_only();

$errors = [];
$name   = '';
$email  = '';
$oath   = false;

// The form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name']);
    $email    = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm  = $_POST['password_confirm'];
    $oath     = isset($_POST['oath']);

    // Check each field
    if ($name == '') {
        $errors['name'] = 'Tell us what to call you.';
    } elseif (strlen($name) < 2 || strlen($name) > 40) {
        $errors['name'] = 'Your name must be 2–40 characters long.';
    }

    if ($email == '') {
        $errors['email'] = 'An email address is needed to keep your record.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'That doesn\'t look like a valid email address.';
    } elseif (find_user($email) != null) {
        $errors['email'] = 'An archivist is already registered with this email.';
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'Your access key must be at least 8 characters long.';
    } elseif ($password != $confirm) {
        $errors['password_confirm'] = 'The two access keys do not correspond.';
    }

    if (!$oath) {
        $errors['oath'] = 'Please take the archivist\'s pledge to continue.';
    }

    // No errors? Save the user and go to the sign in page
    if (count($errors) == 0) {
        create_user($name, $email, $password);
        $_SESSION['message']   = 'Your collection is open. Sign in with your new access key to enter.';
        $_SESSION['new_email'] = $email;
        redirect('index.php');
    }
}

$page_title = 'Register';
$body_class = 'page-register';
$active     = 'register';

$ledger = [
    'quote' => 'Every collection begins with a single forgotten thing.',
    'cite'  => 'Archive motto',
    'cards' => [
        ['label' => 'Box 001 · Your first item', 'title' => 'Something you almost forgot', 'note' => 'Begin here.'],
        ['label' => 'Folder 02 · Documents',     'title' => 'Deeds, diaries, receipts',    'note' => 'Every paper had a reason.'],
        ['label' => 'Item 0003 · Memories',      'title' => 'A grandmother\'s kitchen',    'note' => 'Describe it before it fades.', 'stamp' => 'Unfiled'],
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
            <p class="eyebrow">New accession · Form A-1</p>
            <h1 class="card__title" id="card-title">Begin your <em>collection.</em></h1>
            <p class="card__lede">Enter your name in the register, forge an access key, and open your first drawer.</p>
        </header>

        <div class="rule" aria-hidden="true"></div>

        <?php if (count($errors) > 0): ?>
            <div class="alert alert--error" role="alert">The register could not be filed. Please correct the marked entries.</div>
        <?php endif; ?>

        <form class="form" method="post" action="register.php">
            <div class="field<?= isset($errors['name']) ? ' field--error' : '' ?>">
                <label class="label" for="name">Archivist name</label>
                <input class="input" type="text" id="name" name="name"
                       value="<?= e($name) ?>" maxlength="40"
                       placeholder="How you'll be addressed" required>
                <?php if (isset($errors['name'])): ?>
                    <p class="field-error"><?= e($errors['name']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field<?= isset($errors['email']) ? ' field--error' : '' ?>">
                <label class="label" for="email">Email address</label>
                <input class="input" type="email" id="email" name="email"
                       value="<?= e($email) ?>" placeholder="you@example.com" required>
                <?php if (isset($errors['email'])): ?>
                    <p class="field-error"><?= e($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <?php
            $password_error = isset($errors['password']) ? $errors['password'] : '';
            $confirm_error  = isset($errors['password_confirm']) ? $errors['password_confirm'] : '';

            key_field('password', 'Access key', 'new-password',
                      'At least 8 characters. A longer key makes a stronger seal.', $password_error, true);

            key_field('password_confirm', 'Confirm access key', 'new-password',
                      '', $confirm_error, false, 'password');
            ?>

            <div class="oath<?= isset($errors['oath']) ? ' oath--error' : '' ?>">
                <input class="oath__box" type="checkbox" id="oath" name="oath" value="yes" required
                       <?= $oath ? 'checked' : '' ?>>
                <label class="oath__label" for="oath">I pledge to keep faith with every memory entrusted to my care.</label>
                <?php if (isset($errors['oath'])): ?>
                    <p class="field-error"><?= e($errors['oath']) ?></p>
                <?php endif; ?>
            </div>

            <button class="btn" type="submit">Open my collection</button>
        </form>

        <p class="card__foot">Already keep a collection? <a href="index.php">Sign in</a></p>
    </section>

<?php require 'includes/footer.php'; ?>
