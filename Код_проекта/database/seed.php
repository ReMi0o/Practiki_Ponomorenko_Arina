<?php
spl_autoload_register(fn($c) => require __DIR__ . "/../app/$c.php");
$db = Database::get();
$users = new UserRepository();
$people = [
    ['login' => 'kotova_m', 'fio' => 'Котова Мария Андреевна', 'phone' => '+7(916)204-18-33', 'email' => 'kotova.m@yandex.ru'],
    ['login' => 'smirnov_d', 'fio' => 'Смирнов Даниил Олегович', 'phone' => '+7(903)771-05-42', 'email' => 'd.smirnov@mail.ru'],
    ['login' => 'leila_h', 'fio' => 'Хасанова Лейла Ринатовна', 'phone' => '+7(925)318-90-07', 'email' => 'leila.h@gmail.com'],
];
$ids = [];
foreach ($people as $p) {
    $ids[$p['login']] = $users->create($p + ['password' => 'Uchastnik1']);
}
$add = $db->prepare('INSERT INTO bookings (user_id, workshop_id, payment, room, wishes, status_id) VALUES (?, ?, ?, ?, ?, ?)');
$rows = [
    [$ids['kotova_m'],  2, 'Банковская карта онлайн', 'Мастерская керамики', '', 3],
    [$ids['kotova_m'],  1, 'Банковская карта онлайн', 'Аудитория 101', 'Возьму свой ноутбук', 1],
    [$ids['smirnov_d'], 5, 'Наличными на месте', 'Аудитория 101', '', 2],
    [$ids['leila_h'],   3, 'Банковская карта онлайн', 'Онлайн (ссылка придёт на почту)', 'Нужна запись занятия', 1],
    [$ids['leila_h'],   2, 'Наличными на месте', 'Мастерская керамики', '', 3],
];
foreach ($rows as $r) {
    $add->execute($r);
}
if (in_array('feedback', array_column($db->query("SELECT name FROM sqlite_master")->fetchAll(), 'name'))) {
    $db->prepare('INSERT INTO feedback (booking_id, stars, message) VALUES (?, ?, ?)')
       ->execute([1, 5, 'Очень душевно! Получилась кривоватая, но своя чашка :) Спасибо Денису за терпение.']);
}
echo "Тестовые данные добавлены\n";
