<?php
// Decorative side panel. The page sets $ledger (a quote and some cards) first.
?>
<aside class="ledger" aria-label="About The Archive">
    <p class="eyebrow">Est. in forgotten years</p>

    <blockquote class="ledger__quote">
        <p><?= e($ledger['quote']) ?></p>
        <footer class="ledger__cite"><?= e($ledger['cite']) ?></footer>
    </blockquote>

    <div class="ledger__cards" aria-hidden="true">
        <?php foreach ($ledger['cards'] as $card): ?>
            <div class="index-card">
                <p class="index-card__label"><?= e($card['label']) ?></p>
                <p class="index-card__title"><?= e($card['title']) ?></p>
                <p class="index-card__note"><?= e($card['note']) ?></p>
                <?php if (isset($card['stamp'])): ?>
                    <span class="index-card__stamp"><?= e($card['stamp']) ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</aside>
