<h1>Мои заявки</h1>
<?php if (!$list): ?>
    <p>Заявок пока нет. <a href="/booking">Записаться на мастер-класс</a></p>
<?php else: ?>
    <table class="tbl">
        <tr><th>№</th><th>Мастер-класс</th><th>Когда</th><th>Помещение</th><th>Оплата</th><th>Статус</th></tr>
        <?php foreach ($list as $b): ?>
            <tr>
                <td><?= (int)$b['booking_id'] ?></td>
                <td><?= e($b['workshop']) ?></td>
                <td><?= e(ru_date($b['starts_at'])) ?></td>
                <td><?= e($b['room']) ?></td>
                <td><?= e($b['payment']) ?></td>
                <td><?= e($b['status']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
