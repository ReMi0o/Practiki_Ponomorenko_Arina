<h1>Заявки участников</h1>
<table class="tbl">
    <tr><th>№</th><th>Участник</th><th>Контакты</th><th>Мастер-класс</th><th>Оплата / помещение</th><th>Статус</th></tr>
    <?php foreach ($list as $b): ?>
        <tr>
            <td><?= (int)$b['booking_id'] ?></td>
            <td><?= e($b['fio']) ?><br><small><?= e($b['login']) ?></small></td>
            <td><?= e($b['phone']) ?><br><small><?= e($b['email']) ?></small></td>
            <td><?= e($b['workshop']) ?></td>
            <td><?= e($b['payment']) ?><br><small><?= e($b['room']) ?></small></td>
            <td>
                <form method="post" action="/admin/status" class="row">
                    <input type="hidden" name="booking_id" value="<?= (int)$b['booking_id'] ?>">
                    <select name="status_id">
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?= $s['status_id'] ?>" <?= $s['title'] === $b['status'] ? 'selected' : '' ?>><?= e($s['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn--sm">✓</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
