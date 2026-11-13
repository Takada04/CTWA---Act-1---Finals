<?php
// Reading room (members only with CRUD operations & interactive drawers)
require 'includes/config.php';
require 'includes/functions.php';

$user = require_login();   // Access Control: Redirects guests to sign in[cite: 1, 10]

$error   = '';
$success = '';

$drawers = [
    'Memories'           => 'Moments worth keeping',
    'Historical records' => 'Ledgers, registers, minutes',
    'Photographs'        => 'Faces and places',
    'Documents'          => 'Letters, deeds, diaries',
    'Personal stories'   => 'Told in your own words',
];

// Handle Form Submissions (Create, Delete)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $drawer     = trim($_POST['drawer']);
        $title      = trim($_POST['title']);
        $note       = trim($_POST['note']);
        $image_path = null;

        if (!array_key_exists($drawer, $drawers) || $title === '') {
            $error = 'Please select a valid drawer and provide a title.';
        } else {
            // Handle optional photo upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $allowed_types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime  = finfo_file($finfo, $_FILES['photo']['tmp_name']);
                    finfo_close($finfo);

                    if (!array_key_exists($mime, $allowed_types)) {
                        $error = 'Invalid image format. Only JPG, PNG, WEBP, or GIF files are permitted.';
                    } elseif ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
                        $error = 'Photograph exceeds the 5MB archival limit.';
                    } else {
                        if (!is_dir('uploads')) {
                            mkdir('uploads', 0755, true);
                        }
                        $ext      = $allowed_types[$mime];
                        $filename = uniqid('arch_' . $user['id'] . '_', true) . '.' . $ext;
                        $target   = 'uploads/' . $filename;

                        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                            $image_path = $target;
                        } else {
                            $error = 'Failed to preserve the uploaded photograph.';
                        }
                    }
                } else {
                    $error = 'An error occurred while uploading the photograph.';
                }
            }

            if ($error === '') {
                create_item($user['id'], $drawer, $title, $note, $image_path);
                $success = 'New item successfully filed into "' . $drawer . '".';
            }
        }
    } elseif ($action === 'delete') {
        $item_id = (int) $_POST['item_id'];
        delete_item($item_id, $user['id']);
        $success = 'Item expunged from the collection.';
    }
}

$user_items = get_user_items($user['id']);

// Group items by drawer for counting
$item_counts = [];
foreach ($drawers as $key => $desc) {
    $item_counts[$key] = 0;
}
foreach ($user_items as $item) {
    if (isset($item_counts[$item['drawer']])) {
        $item_counts[$item['drawer']]++;
    }
}

$page_title  = 'Reading Room';
$body_class  = 'page-room';
$active      = 'room';
$stage_class = 'stage--solo';

require 'includes/header.php';
?>

    <section class="room" aria-labelledby="room-title">
        <p class="eyebrow">Reading room</p>
        <h1 class="room__title" id="room-title">Welcome, <em><?= e($user['name']) ?>.</em></h1>
        <p class="room__lede">
            Archivist since <?= date('F j, Y', strtotime($user['created_at'])) ?>.[cite: 1]
            Select a drawer below to inspect its ledger.
        </p>

        <div class="rule" aria-hidden="true"></div>

        <?php if ($success != ''): ?>
            <div class="alert alert--success" role="status"><?= e($success) ?></div>
        <?php endif; ?>

        <?php if ($error != ''): ?>
            <div class="alert alert--error" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <!-- Interactive Drawers -->
        <ul class="drawers" role="tablist" aria-label="Archive Drawers">
            <?php foreach ($drawers as $name => $about): ?>
                <li class="drawer" role="tab" tabindex="0" data-drawer-btn="<?= e($name) ?>" aria-selected="false">
                    <span class="drawer__plate"><?= e($name) ?></span>
                    <span class="drawer__about"><?= e($about) ?></span>
                    <span class="drawer__count"><?= $item_counts[$name] ?> item<?= $item_counts[$name] == 1 ? '' : 's' ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Collection Ledger (Revealed when a drawer is clicked) -->
        <div id="ledger-section" style="display: none; margin-top: 2.5rem; text-align: left;">
            <div class="rule" aria-hidden="true"></div>

            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <p class="eyebrow" style="margin-bottom: 0.25rem;">Open Drawer</p>
                    <h2 id="ledger-heading" style="font-family: var(--font-display); font-size: 1.9rem; color: var(--gold-bright);"></h2>
                </div>
                <div style="display: flex; gap: 1.25rem;">
                    <button type="button" id="show-all-btn" class="linkbtn" style="font-size: 0.75rem;">View All Records</button>
                    <button type="button" id="close-ledger-btn" class="linkbtn" style="font-size: 0.75rem; color: var(--muted);">Close Drawer</button>
                </div>
            </div>

            <p id="empty-drawer-msg" style="display: none; text-align: center; padding: 2rem; color: var(--muted); font-style: italic; background: rgba(0, 0, 0, 0.25); border: 1px dashed var(--gold-faint);">
                This drawer is currently empty. File an item below to begin its record.
            </p>

            <div id="ledger-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
                <?php foreach ($user_items as $item): ?>
                    <div class="index-card" data-item-drawer="<?= e($item['drawer']) ?>" style="transform: none; width: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <p class="index-card__label"><?= e($item['drawer']) ?></p>
                            <p class="index-card__title"><?= e($item['title']) ?></p>

                            <?php if (!empty($item['image_path']) && file_exists($item['image_path'])): ?>
                                <div style="margin-top: 0.85rem; padding: 0.35rem; background: #faf3e0; border: 1px solid rgba(107, 82, 51, 0.35); box-shadow: 0 3px 8px rgba(0, 0, 0, 0.25);">
                                    <img src="<?= e($item['image_path']) ?>" alt="<?= e($item['title']) ?>" style="display: block; width: 100%; height: 175px; object-fit: cover; filter: sepia(15%) contrast(1.03);">
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($item['note'])): ?>
                                <p class="index-card__note" style="margin-top: 0.65rem !important;"><?= e($item['note']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div style="margin-top: 1rem; display: flex; justify-content: flex-end;">
                            <form method="post" action="archive.php" onsubmit="return confirm('Are you sure you wish to expunge this record?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                <button type="submit" class="linkbtn" style="color: #782616; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em;">Expunge</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="rule" aria-hidden="true"></div>

        <!-- Create Item Form -->
        <div class="card" style="max-width: 40rem; margin: 2rem auto; text-align: left;">
            <h2 style="font-family: var(--font-display); font-size: 1.5rem; margin-bottom: 1rem; color: var(--gold-bright);">File a New Item</h2>
            <form class="form" method="post" action="archive.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">
                
                <div class="field">
                    <label class="label">Drawer Category</label>
                    <div style="display: flex; flex-direction: column; gap: 0.6rem; margin-top: 0.5rem;">
                        <?php $is_first = true; foreach ($drawers as $name => $about): ?>
                            <label style="display: flex; align-items: center; gap: 0.85rem; padding: 0.75rem 1rem; background: linear-gradient(180deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.38)); border: 1px solid var(--gold-dim); border-radius: 2px; cursor: pointer; transition: border-color 0.2s;">
                                <input type="radio" name="drawer" value="<?= e($name) ?>" <?= $is_first ? 'checked' : '' ?> style="accent-color: var(--gold); width: 1.1rem; height: 1.1rem; cursor: pointer;">
                                <div>
                                    <span style="color: var(--gold-bright); font-family: var(--font-label); font-size: 0.75rem; letter-spacing: 0.15em; text-transform: uppercase; display: block;"><?= e($name) ?></span>
                                    <span style="color: var(--muted); font-size: 0.88rem; font-style: italic;"><?= e($about) ?></span>
                                </div>
                            </label>
                        <?php $is_first = false; endforeach; ?>
                    </div>
                </div>

                <div class="field" style="margin-top: 1.5rem;">
                    <label class="label" for="title">Item Title / Subject</label>
                    <input class="input" type="text" id="title" name="title" placeholder="e.g., Summer at the coast, 1984" required>
                </div>

                <div class="field">
                    <label class="label" for="note">Archival Note / Description</label>
                    <textarea class="input" id="note" name="note" rows="3" placeholder="Condition, origin, or memory details..."></textarea>
                </div>

                <div class="field">
                    <label class="label" for="photo">Attach Photograph <span style="color: var(--muted); font-size: 0.68rem;">(Optional)</span></label>
                    <input class="input" type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp,image/gif">
                    <p class="field-hint">Accepts JPG, PNG, WEBP, or GIF up to 5MB.</p>
                </div>

                <button class="btn" type="submit">File into Archive</button>
            </form>
        </div>
    </section>

    <script>
    (function () {
        var drawerBtns  = document.querySelectorAll('[data-drawer-btn]');
        var ledgerBox   = document.getElementById('ledger-section');
        var heading     = document.getElementById('ledger-heading');
        var cards       = document.querySelectorAll('[data-item-drawer]');
        var emptyMsg    = document.getElementById('empty-drawer-msg');
        var showAllBtn  = document.getElementById('show-all-btn');
        var closeBtn    = document.getElementById('close-ledger-btn');
        var activeName  = null;

        function openDrawer(drawerName) {
            // Clicking the already-open drawer closes it
            if (activeName === drawerName) {
                closeLedger();
                return;
            }

            activeName = drawerName;
            ledgerBox.style.display = 'block';
            heading.textContent = drawerName === 'ALL' ? 'Complete Collection Ledger' : drawerName;

            // Highlight active drawer
            drawerBtns.forEach(function (btn) {
                var isMatch = btn.getAttribute('data-drawer-btn') === drawerName;
                btn.classList.toggle('is-active', isMatch);
                btn.setAttribute('aria-selected', isMatch ? 'true' : 'false');
            });

            // Auto-select the corresponding radio button in the creation form
            if (drawerName !== 'ALL') {
                var radio = document.querySelector('input[name="drawer"][value="' + drawerName + '"]');
                if (radio) { radio.checked = true; }
            }

            // Filter index cards
            var visibleCount = 0;
            cards.forEach(function (card) {
                var matches = drawerName === 'ALL' || card.getAttribute('data-item-drawer') === drawerName;
                card.style.display = matches ? 'flex' : 'none';
                if (matches) { visibleCount++; }
            });

            emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
            ledgerBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function closeLedger() {
            activeName = null;
            ledgerBox.style.display = 'none';
            drawerBtns.forEach(function (btn) {
                btn.classList.remove('is-active');
                btn.setAttribute('aria-selected', 'false');
            });
        }

        drawerBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                openDrawer(btn.getAttribute('data-drawer-btn'));
            });
            btn.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    openDrawer(btn.getAttribute('data-drawer-btn'));
                }
            });
        });

        showAllBtn.addEventListener('click', function () {
            openDrawer('ALL');
        });

        closeBtn.addEventListener('click', closeLedger);
    })();
    </script>

<?php require 'includes/footer.php'; ?>