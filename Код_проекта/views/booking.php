<h1>Запись на мастер-класс</h1>
<?php if ($errors): ?>
    <ul class="errors"><?php foreach ($errors as $msg): ?><li><?= e($msg) ?></li><?php endforeach; ?></ul>
<?php endif; ?>
<form method="post" class="box">
    <label>Мастер-класс
        <select name="workshop_id">
            <option value="">— выберите —</option>
            <?php foreach ($workshops as $w): ?>
                <option value="<?= (int)$w['workshop_id'] ?>" <?= $w['workshop_id'] == $chosen ? 'selected' : '' ?>>
                    <?= e($w['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Способ оплаты <input name="payment" placeholder="например, картой"></label>
    <label>Помещение <input name="room" placeholder="например, аудитория 101"></label>
    <label>Пожелания <textarea name="wishes" rows="3"></textarea></label>
    <button class="btn" type="submit">Отправить заявку</button>
</form>
