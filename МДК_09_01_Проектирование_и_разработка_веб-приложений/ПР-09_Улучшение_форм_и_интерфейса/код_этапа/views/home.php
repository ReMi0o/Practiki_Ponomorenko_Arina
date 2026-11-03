<section class="hero">
    <h1>Афиша мастер-классов</h1>
    <p>Творческие и IT-занятия в небольших группах. Выберите занятие и оставьте заявку.</p>
</section>
<div class="grid">
    <?php foreach ($workshops as $w): ?>
        <article class="ws">
            <p class="ws__when"><?= e(ru_date($w['starts_at'])) ?></p>
            <h2 class="ws__title"><?= e($w['title']) ?></h2>
            <p class="ws__meta"><?= e($w['location']) ?> · ведущий: <?= e($w['host']) ?></p>
            <p><?= e($w['about']) ?></p>
            <p class="ws__meta">Мест: <?= (int)$w['seats'] ?></p>
            <a class="btn" href="/booking?w=<?= (int)$w['workshop_id'] ?>">Записаться</a>
        </article>
    <?php endforeach; ?>
</div>
