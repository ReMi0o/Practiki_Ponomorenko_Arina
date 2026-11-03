-- База данных «Мастерская идей» (ПР № 2)
-- СУБД: SQLite 3 (через расширение PDO SQLite в PHP)

CREATE TABLE IF NOT EXISTS users (
    user_id     INTEGER PRIMARY KEY AUTOINCREMENT,
    login       VARCHAR(20)  NOT NULL UNIQUE COLLATE NOCASE,
    pass_hash   VARCHAR(255) NOT NULL,
    fio         VARCHAR(150) NOT NULL,
    phone       VARCHAR(20)  NOT NULL,
    email       VARCHAR(100) NOT NULL,
    role        VARCHAR(10)  NOT NULL DEFAULT 'user' CHECK (role IN ('user', 'admin')),
    registered  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS workshops (
    workshop_id INTEGER PRIMARY KEY AUTOINCREMENT,
    title       VARCHAR(150) NOT NULL,
    starts_at   DATETIME     NOT NULL,
    location    VARCHAR(150) NOT NULL,
    host        VARCHAR(100) NOT NULL,
    seats       INTEGER      NOT NULL CHECK (seats > 0),
    about       TEXT         NOT NULL
);

CREATE TABLE IF NOT EXISTS booking_statuses (
    status_id   INTEGER PRIMARY KEY,
    title       VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS bookings (
    booking_id  INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id     INTEGER NOT NULL,
    workshop_id INTEGER NOT NULL,
    status_id   INTEGER NOT NULL DEFAULT 1,
    payment     VARCHAR(50)  NOT NULL,
    room        VARCHAR(50)  NOT NULL,
    wishes      VARCHAR(500),
    created     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)     REFERENCES users(user_id)          ON DELETE CASCADE,
    FOREIGN KEY (workshop_id) REFERENCES workshops(workshop_id)  ON DELETE RESTRICT,
    FOREIGN KEY (status_id)   REFERENCES booking_statuses(status_id)
);

CREATE TABLE IF NOT EXISTS feedback (
    feedback_id INTEGER PRIMARY KEY AUTOINCREMENT,
    booking_id  INTEGER NOT NULL UNIQUE,
    stars       INTEGER NOT NULL CHECK (stars BETWEEN 1 AND 5),
    message     TEXT    NOT NULL,
    created     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
);

INSERT OR IGNORE INTO booking_statuses (status_id, title) VALUES
    (1, 'Новая'), (2, 'Назначено'), (3, 'Завершено');
