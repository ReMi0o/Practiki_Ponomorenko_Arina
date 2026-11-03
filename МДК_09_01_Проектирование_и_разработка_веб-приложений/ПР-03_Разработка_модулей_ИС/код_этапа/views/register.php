<h1>Регистрация</h1>
<?php if ($errors): ?>
    <ul class="errors">
        <?php foreach ($errors as $msg): ?><li><?= e($msg) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>
<form method="post" class="box" novalidate>
    <label>Логин <input name="login" value="<?= e($old['login'] ?? '') ?>"></label>
    <label>Пароль <input name="password" type="password"></label>
    <label>Повторите пароль <input name="password2" type="password"></label>
    <label>ФИО <input name="fio" value="<?= e($old['fio'] ?? '') ?>"></label>
    <label>Телефон <input name="phone" value="<?= e($old['phone'] ?? '') ?>" placeholder="+7(XXX)XXX-XX-XX"></label>
    <label>Электронная почта <input name="email" value="<?= e($old['email'] ?? '') ?>"></label>
    <button class="btn" type="submit">Создать аккаунт</button>
</form>
