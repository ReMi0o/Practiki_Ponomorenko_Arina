<h1>Регистрация</h1>
<form method="post" class="box" id="reg" novalidate data-check>
    <?= csrf_field() ?>
    <div class="<?= field_class($errors, 'login') ?>">
        <label for="login">Логин</label>
        <input id="login" name="login" value="<?= e($old['login'] ?? '') ?>" autocomplete="username">
        <small class="field__hint">4–20 символов: латиница, цифры, «_»</small>
        <?= field_error($errors, 'login') ?>
    </div>
    <div class="<?= field_class($errors, 'password') ?>">
        <label for="password">Пароль</label>
        <input id="password" name="password" type="password" autocomplete="new-password">
        <small class="field__hint">не короче 8 символов, хотя бы одна цифра</small>
        <?= field_error($errors, 'password') ?>
    </div>
    <div class="<?= field_class($errors, 'password2') ?>">
        <label for="password2">Повторите пароль</label>
        <input id="password2" name="password2" type="password" autocomplete="new-password">
        <?= field_error($errors, 'password2') ?>
    </div>
    <div class="<?= field_class($errors, 'fio') ?>">
        <label for="fio">ФИО</label>
        <input id="fio" name="fio" value="<?= e($old['fio'] ?? '') ?>" placeholder="Иванова Анна Петровна">
        <?= field_error($errors, 'fio') ?>
    </div>
    <div class="<?= field_class($errors, 'phone') ?>">
        <label for="phone">Телефон</label>
        <input id="phone" name="phone" value="<?= e($old['phone'] ?? '') ?>" placeholder="+7(XXX)XXX-XX-XX" inputmode="tel">
        <?= field_error($errors, 'phone') ?>
    </div>
    <div class="<?= field_class($errors, 'email') ?>">
        <label for="email">Электронная почта</label>
        <input id="email" name="email" type="email" value="<?= e($old['email'] ?? '') ?>" placeholder="name@mail.ru">
        <?= field_error($errors, 'email') ?>
    </div>
    <button class="btn" type="submit">Создать аккаунт</button>
    <p class="box__alt">Уже зарегистрированы? <a href="/login">Войти</a></p>
</form>
<script src="/assets/js/forms.js"></script>
