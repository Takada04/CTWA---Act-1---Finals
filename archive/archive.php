<?php
// Reading room (members only)
require 'includes/config.php';
require 'includes/functions.php';

$user = require_login();   // not signed in? back to the sign in page

$page_title  = 'Reading Room';
$body_class  = 'page-room';
$active      = 'room';
$stage_class = 'stage--solo';

$drawers = [
    ['Memories',           'Moments worth keeping'],
    ['Historical records', 'Ledgers, registers, minutes'],
    ['Photographs',        'Faces and places'],
    ['Documents',          'Letters, deeds, diaries'],
    ['Personal stories',   'Told in your own words'],
];

require 'includes/header.php';
?>

    <section class="room" aria-labelledby="room-title">
        <p class="eyebrow">Reading room</p>
        <h1 class="room__title" id="room-title">Welcome, <em><?= e($user['name']) ?>.</em></h1>
        <p class="room__lede">
            Archivist since <?= date('F j, Y', strtotime($user['created_at'])) ?>.
            Your collection is empty &mdash; for now. What will you file first?
        </p>

        <div class="rule" aria-hidden="true"></div>

        <ul class="drawers">
            <?php foreach ($drawers as $drawer): ?>
                <li class="drawer">
                    <span class="drawer__plate"><?= e($drawer[0]) ?></span>
                    <span class="drawer__about"><?= e($drawer[1]) ?></span>
                    <span class="drawer__count">0 items</span>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="room__note">This is a demo reading room &mdash; filing items isn't wired up yet.</p>
    </section>

<?php require 'includes/footer.php'; ?>
