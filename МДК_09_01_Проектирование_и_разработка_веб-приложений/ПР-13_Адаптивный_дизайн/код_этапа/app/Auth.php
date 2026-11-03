<?php
class Auth
{
    public static function user(): ?array
    {
        if (empty($_SESSION['uid'])) {
            return null;
        }
        static $cache = null;
        return $cache ??= (new UserRepository())->find((int)$_SESSION['uid']);
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);   // новая сессия после входа
        $_SESSION['uid'] = $user['user_id'];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? '') === 'admin';
    }

    /** Только для вошедших пользователей */
    public static function needUser(): void
    {
        if (!self::user()) {
            flash('error', 'Сначала войдите в личный кабинет');
            redirect('/login');
        }
    }

    /** Только для администратора */
    public static function needAdmin(): void
    {
        if (!self::isAdmin()) {
            http_response_code(403);
            view('error', ['code' => 403, 'message' => 'У вас нет доступа к этой странице']);
            exit;
        }
    }
}
