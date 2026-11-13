<?php
function key_field($id, $label, $autocomplete, $hint = '', $error = '', $meter = false, $confirm_for = '') {
    $tag    = $confirm_for ? 'Verification' : 'Class A · Restricted';
    $status = $confirm_for ? 'Repeat the key to bind it' : 'Awaiting key';
    ?>
    <div class="keyfield<?= $error ? ' keyfield--error' : '' ?>"
         data-keyfield<?= $confirm_for ? ' data-confirm-for="' . e($confirm_for) . '"' : '' ?>>

        <div class="keyfield__top">
            <label class="label" for="<?= e($id) ?>"><?= e($label) ?></label>
            <span class="keyfield__tag"><?= e($tag) ?></span>
        </div>

        <div class="keyfield__plate">
            <svg class="keyfield__icon" viewBox="0 0 24 24" width="22" height="22" fill="none"
                 stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
                <circle cx="7.5" cy="12" r="3.5"/>
                <path d="M11 12h10M17 12v3M20 12v2"/>
            </svg>

            <input class="keyfield__input" type="password"
                   id="<?= e($id) ?>" name="<?= e($id) ?>"
                   autocomplete="<?= e($autocomplete) ?>" maxlength="72"
                   placeholder="······  ······  ······" required>

            <button class="keyfield__toggle" type="button" data-toggle>Reveal</button>
        </div>

        <div class="keyfield__strip" aria-hidden="true">
            <?php for ($i = 0; $i < 12; $i++): ?><span class="keyfield__cell" data-cell></span><?php endfor; ?>
        </div>

        <div class="keyfield__meta">
            <span class="keyfield__status" data-status><?= e($status) ?></span>
            <?php if ($meter): ?>
                <span class="keyfield__seal">
                    <span class="meter" data-meter data-level="0" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
                    <span class="keyfield__strength" data-strength>Unsealed</span>
                </span>
            <?php endif; ?>
        </div>

        <?php if ($hint != ''): ?>
            <p class="field-hint"><?= e($hint) ?></p>
        <?php endif; ?>
        <?php if ($error != ''): ?>
            <p class="field-error"><?= e($error) ?></p>
        <?php endif; ?>
    </div>
    <?php
}
