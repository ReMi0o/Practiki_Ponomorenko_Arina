<?php
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
}
