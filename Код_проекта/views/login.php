<h1>Вход в личный кабинет</h1>
<form method="post" class="box">
    <?= csrf_field() ?>
    <div class="field"><label for="login">Логин</label><input id="login" name="login" required autocomplete="username"></div>
    <div class="field"><label for="password">Пароль</label><input id="password" name="password" type="password" required autocomplete="current-password"></div>
    <button class="btn" type="submit">Войти</button>
    <p class="box__alt">Нет аккаунта? <a href="/register">Зарегистрируйтесь</a></p>
</form>
