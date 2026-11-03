<h1>Отзыв о мастер-классе</h1>
<p class="lead"><?= e($b['workshop']) ?> — <?= e(ru_date($b['starts_at'])) ?></p>
<form method="post" class="box" novalidate data-check>
    <?= csrf_field() ?>
    <fieldset class="<?= field_class($errors, 'stars') ?> stars-pick">
        <legend>Оценка</legend>
        <?php for ($i = 5; $i >= 1; $i--): ?>
            <input type="radio" id="s<?= $i ?>" name="stars" value="<?= $i ?>" <?= ($old['stars'] ?? '') == $i ? 'checked' : '' ?>>
            <label for="s<?= $i ?>" title="<?= $i ?>">★</label>
        <?php endfor; ?>
        <?= field_error($errors, 'stars') ?>
    </fieldset>
    <div class="<?= field_class($errors, 'message') ?>">
        <label for="message">Ваши впечатления</label>
        <textarea id="message" name="message" rows="5"><?= e($old['message'] ?? '') ?></textarea>
        <?= field_error($errors, 'message') ?>
    </div>
    <button class="btn" type="submit">Опубликовать</button>
</form>
<script src="/assets/js/forms.js"></script>
