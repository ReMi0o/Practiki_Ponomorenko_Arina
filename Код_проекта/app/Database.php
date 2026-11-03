<?php
class Database
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            $file = getenv('DB_FILE') ?: __DIR__ . '/../database/masterskaya.sqlite';
            $isNew = !file_exists($file);
            self::$pdo = new PDO('sqlite:' . $file);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$pdo->exec('PRAGMA foreign_keys = ON');
            if ($isNew) {
                self::install();
            }
        }
        return self::$pdo;
    }

    private static function install(): void
    {
        self::$pdo->exec(file_get_contents(__DIR__ . '/../database/schema.sql'));
        $add = self::$pdo->prepare(
            'INSERT INTO users (login, pass_hash, fio, phone, email, role) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $add->execute(['admin', password_hash('Master2026', PASSWORD_DEFAULT),
            'Администратор', '+7(495)000-00-00', 'admin@masterskaya.ru', 'admin']);

        $ws = self::$pdo->prepare(
            'INSERT INTO workshops (title, starts_at, location, host, seats, about) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $list = [
            ['Вёрстка лендинга за один вечер', '2026-11-15 18:00', 'Москва, ул. Лесная, 7', 'Ольга Миронова', 12,
                'Соберём адаптивную посадочную страницу на HTML и CSS: сетка, типографика, кнопки.'],
            ['Гончарный круг для начинающих', '2026-10-18 12:00', 'Москва, Арт-кластер «Ткацкие»', 'Денис Ковалёв', 8,
                'Первое знакомство с гончарным кругом: центровка глины и простая чаша.'],
            ['Figma: прототип мобильного приложения', '2026-11-29 11:00', 'Онлайн', 'Мария Литвинова', 30,
                'Разберём компоненты, автолэйаут и сделаем кликабельный прототип.'],
            ['Скетчинг маркерами', '2026-12-13 15:00', 'Москва, Библиотека им. Тургенева', 'Анна Белова', 15,
                'Быстрые зарисовки интерьеров и предметов маркерами на спиртовой основе.'],
            ['Python: первый Telegram-бот', '2027-01-24 17:00', 'Москва, ул. Лесная, 7', 'Игорь Степанов', 10,
                'Напишем бота-помощника на aiogram и запустим его на сервере.'],
        ];
        foreach ($list as $row) {
            $ws->execute($row);
        }
    }
}
