<h1>Заявки участников</h1>
<nav class="filter" aria-label="Фильтр по статусу">
    <a href="/admin" class="filter__item <?= !$filter ? 'is-on' : '' ?>">Все <span><?= array_sum($counts) ?></span></a>
    <?php foreach ($statuses as $s): ?>
        <a href="/admin?status=<?= $s['status_id'] ?>" class="filter__item <?= $filter == $s['status_id'] ? 'is-on' : '' ?>">
            <?= e($s['title']) ?> <span><?= (int)($counts[$s['status_id']] ?? 0) ?></span>
        </a>
    <?php endforeach; ?>
</nav>
<?php if (!$list): ?><p class="muted">Нет заявок с выбранным статусом.</p><?php endif; ?>
<div class="cards">
    <?php foreach ($list as $b):
        ob_start(); ?>
        <form method="post" action="/admin/status" class="card__form">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= (int)$b['booking_id'] ?>">
            <input type="hidden" name="filter" value="<?= (int)$filter ?>">
            <select name="status_id" aria-label="Статус заявки <?= (int)$b['booking_id'] ?>">
                <?php foreach ($statuses as $s): ?>
                    <option value="<?= $s['status_id'] ?>" <?= $s['status_id'] == $b['status_id'] ? 'selected' : '' ?>><?= e($s['title']) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn--sm" type="submit">Сохранить</button>
        </form>
        <?php $slot = ob_get_clean(); $adminView = true;
        require __DIR__ . '/_booking_card.php';
    endforeach; ?>
</div>
