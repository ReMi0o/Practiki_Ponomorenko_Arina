<?php
/**
 * Проверка данных формы регистрации на сервере.
 * Возвращает массив ошибок: ['поле' => 'текст ошибки'].
 */
class Validator
{
    public static function registration(array $d, UserRepository $users): array
    {
        $err = [];
        $login = $d['login'] ?? '';
        $pass  = $d['password'] ?? '';

        if (!preg_match('/^[A-Za-z0-9_]{4,20}$/', $login)) {
            $err['login'] = 'Логин — от 4 до 20 символов: латинские буквы, цифры, «_»';
        } elseif ($users->findByLogin($login)) {
            $err['login'] = 'Этот логин уже занят';
        }

        if (mb_strlen($pass) < 8 || !preg_match('/\d/', $pass)) {
            $err['password'] = 'Пароль — не короче 8 символов и хотя бы одна цифра';
        } elseif ($pass !== ($d['password2'] ?? '')) {
            $err['password2'] = 'Пароли не совпадают';
        }

        if (!preg_match('/^[А-ЯЁа-яё][А-ЯЁа-яё\s\-]*$/u', trim($d['fio'] ?? ''))) {
            $err['fio'] = 'ФИО пишется русскими буквами';
        }

        if (!preg_match('/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $d['phone'] ?? '')) {
            $err['phone'] = 'Телефон в формате +7(XXX)XXX-XX-XX';
        }

        if (!filter_var($d['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $err['email'] = 'Некорректный адрес электронной почты';
        }

        return $err;
    }

    public static function booking(array $d, WorkshopRepository $workshops, BookingRepository $bookings): array
    {
        $err = [];
        $w = $workshops->find((int)($d['workshop_id'] ?? 0));
        if (!$w) {
            $err['workshop_id'] = 'Выберите мастер-класс из списка';
        } elseif ($bookings->freeSeats($w) <= 0) {
            $err['workshop_id'] = 'На этот мастер-класс мест больше нет';
        }
        if (!array_key_exists($d['payment'] ?? '', Options::PAYMENT)) {
            $err['payment'] = 'Выберите способ оплаты';
        }
        if (!array_key_exists($d['room'] ?? '', Options::ROOMS)) {
            $err['room'] = 'Выберите помещение';
        }
        if (mb_strlen($d['wishes'] ?? '') > 500) {
            $err['wishes'] = 'Не больше 500 символов';
        }
        return $err;
    }

    public static function feedback(array $d): array
    {
        $err = [];
        if (!in_array($d['stars'] ?? '', ['1', '2', '3', '4', '5'], true)) {
            $err['stars'] = 'Поставьте оценку от 1 до 5';
        }
        if (mb_strlen(trim($d['message'] ?? '')) < 15) {
            $err['message'] = 'Напишите хотя бы пару предложений (от 15 символов)';
        }
        return $err;
    }
}
