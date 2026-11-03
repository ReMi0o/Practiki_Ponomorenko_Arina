<h1>Запись на мастер-класс</h1>
<form method="post" class="box" novalidate data-check>
    <?= csrf_field() ?>
    <div class="<?= field_class($errors, 'workshop_id') ?>">
        <label for="workshop_id">Мастер-класс</label>
        <select id="workshop_id" name="workshop_id">
            <option value="">— выберите занятие —</option>
            <?php foreach ($workshops as $w): ?>
                <option value="<?= (int)$w['workshop_id'] ?>" <?= $w['workshop_id'] == ($old['workshop_id'] ?? 0) ? 'selected' : '' ?>>
                    <?= e($w['title']) ?> · <?= e(date('d.m.Y', strtotime($w['starts_at']))) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="field__hint" id="seats"></small>
        <?= field_error($errors, 'workshop_id') ?>
    </div>
    <div class="<?= field_class($errors, 'payment') ?>">
        <label for="payment">Способ оплаты</label>
        <select id="payment" name="payment">
            <option value="">— выберите —</option>
            <?php foreach (Options::PAYMENT as $key => $title): ?>
                <option value="<?= $key ?>" <?= ($old['payment'] ?? '') === $key ? 'selected' : '' ?>><?= e($title) ?></option>
            <?php endforeach; ?>
        </select>
        <?= field_error($errors, 'payment') ?>
    </div>
    <div class="<?= field_class($errors, 'room') ?>">
        <label for="room">Помещение</label>
        <select id="room" name="room">
            <option value="">— выберите —</option>
            <?php foreach (Options::ROOMS as $key => $title): ?>
                <option value="<?= $key ?>" <?= ($old['room'] ?? '') === $key ? 'selected' : '' ?>><?= e($title) ?></option>
            <?php endforeach; ?>
        </select>
        <?= field_error($errors, 'room') ?>
    </div>
    <div class="<?= field_class($errors, 'wishes') ?>">
        <label for="wishes">Пожелания <span class="opt">(необязательно)</span></label>
        <textarea id="wishes" name="wishes" rows="3" maxlength="500"><?= e($old['wishes'] ?? '') ?></textarea>
        <?= field_error($errors, 'wishes') ?>
    </div>
    <button class="btn" type="submit">Отправить заявку</button>
</form>
<script src="/assets/js/forms.js"></script>
