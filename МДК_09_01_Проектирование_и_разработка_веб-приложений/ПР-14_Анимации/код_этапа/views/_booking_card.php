<article class="card">
    <header class="card__head">
        <span class="card__no">Заявка № <?= (int)$b['booking_id'] ?></span>
        <span class="tag tag--s<?= (int)$b['status_id'] ?>"><?= e($b['status']) ?></span>
    </header>
    <h2 class="card__title"><?= e($b['workshop']) ?></h2>
    <ul class="card__facts">
        <li><b>Дата:</b> <?= e(ru_date($b['starts_at'])) ?></li>
        <?php if (!empty($adminView)): ?>
            <li><b>Участник:</b> <?= e($b['fio']) ?> (<?= e($b['login']) ?>)</li>
            <li><b>Контакты:</b> <?= e($b['phone']) ?>, <?= e($b['email']) ?></li>
        <?php endif; ?>
        <li><b>Помещение:</b> <?= e($b['room']) ?></li>
        <li><b>Оплата:</b> <?= e($b['payment']) ?></li>
        <?php if ($b['wishes']): ?><li><b>Пожелания:</b> <?= e($b['wishes']) ?></li><?php endif; ?>
    </ul>
    <?php if ($b['feedback'] !== null): ?>
        <blockquote class="quote">
            <span class="quote__stars" aria-label="Оценка <?= (int)$b['stars'] ?> из 5"><?= str_repeat('★', (int)$b['stars']) . str_repeat('☆', 5 - (int)$b['stars']) ?></span>
            <?= e($b['feedback']) ?>
        </blockquote>
    <?php endif; ?>
    <?= $slot ?? '' ?>
</article>
