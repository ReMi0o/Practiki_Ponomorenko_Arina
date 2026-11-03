<?php $me = Auth::user(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мастерская идей — запись на мастер-классы</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
<header class="top">
    <div class="wrap top__row">
        <a href="/" class="brand"><span class="brand__mark">М</span> Мастерская идей</a>
        <nav class="menu">
            <a href="/">Афиша</a>
            <?php if ($me && $me['role'] === 'admin'): ?>
                <a href="/admin">Все заявки</a>
            <?php elseif ($me): ?>
                <a href="/cabinet">Личный кабинет</a>
                <a href="/booking">Записаться</a>
            <?php endif; ?>
            <?php if ($me): ?>
                <span class="menu__who"><?= e($me['login']) ?></span>
                <a href="/logout">Выход</a>
            <?php else: ?>
                <a href="/login">Вход</a>
                <a href="/register" class="menu__accent">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="wrap page">
    <?php foreach (take_flash() as $f): ?>
        <p class="note note--<?= e($f['type']) ?>"><?= e($f['text']) ?></p>
    <?php endforeach; ?>
    <?= $content ?>
</main>
<footer class="bottom"><div class="wrap">Мастерская идей, 2026 · учебный проект</div></footer>
</body>
</html>
