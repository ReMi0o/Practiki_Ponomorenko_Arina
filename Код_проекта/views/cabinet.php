<div class="head-line">
    <h1>Мои заявки</h1>
    <a class="btn" href="/booking">Записаться ещё</a>
</div>
<?php if (!$list): ?>
    <p>Заявок пока нет. <a href="/booking">Записаться на мастер-класс</a></p>
<?php else: ?>
    <div class="cards">
        <?php foreach ($list as $b):
            ob_start();
            if ($b['feedback'] === null && $b['status'] === 'Завершено'): ?>
                <a class="btn btn--line" href="/feedback?id=<?= (int)$b['booking_id'] ?>">Оставить отзыв</a>
            <?php elseif ($b['feedback'] === null): ?>
                <p class="muted">Отзыв можно будет оставить после занятия</p>
            <?php endif;
            $slot = ob_get_clean();
            require __DIR__ . '/_booking_card.php';
        endforeach; ?>
    </div>
<?php endif; ?>
