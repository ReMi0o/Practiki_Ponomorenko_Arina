<?php
class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM users WHERE user_id = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    public function findByLogin(string $login): ?array
    {
        $st = $this->db->prepare('SELECT * FROM users WHERE login = ?');
        $st->execute([$login]);
        return $st->fetch() ?: null;
    }

    public function create(array $d): int
    {
        $st = $this->db->prepare(
            'INSERT INTO users (login, pass_hash, fio, phone, email) VALUES (:login, :hash, :fio, :phone, :email)'
        );
        $st->execute([
            ':login' => $d['login'],
            ':hash'  => password_hash($d['password'], PASSWORD_DEFAULT),
            ':fio'   => trim($d['fio']),
            ':phone' => $d['phone'],
            ':email' => $d['email'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Возвращает пользователя, если логин и пароль верные */
    public function check(string $login, string $password): ?array
    {
        $user = $this->findByLogin($login);
        return ($user && password_verify($password, $user['pass_hash'])) ? $user : null;
    }
}
