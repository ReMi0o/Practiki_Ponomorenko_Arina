<?php
/**
 * «Мастерская идей» — портал записи на мастер-классы.
 * Единая точка входа: все запросы приходят сюда и распределяются по маршрутам.
 *
 * Запуск: php -S localhost:8000 -t public public/index.php
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// встроенный сервер PHP отдаёт статические файлы (css, js) сам
if (PHP_SAPI === 'cli-server' && $path !== '/' && is_file(__DIR__ . $path)) {
    return false;
}

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

spl_autoload_register(fn($class) => require __DIR__ . "/../app/$class.php");
require __DIR__ . '/../app/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];
$users = new UserRepository();
$workshops = new WorkshopRepository();
$bookings = new BookingRepository();

switch (true) {
    // ---------- гость ----------
    case $path === '/':
        view('home', ['workshops' => $workshops->all()]);
        break;

    case $path === '/register':
        $old = [];
        $errors = [];
        if ($method === 'POST') {
            $old = $_POST;
            $errors = Validator::registration($_POST, $users);
            if (!$errors) {
                $users->create($_POST);
                flash('ok', 'Аккаунт создан! Войдите, используя логин и пароль.');
                redirect('/login');
            }
        }
        view('register', compact('old', 'errors'));
        break;

    case $path === '/login':
        if ($method === 'POST') {
            $user = $users->check($_POST['login'] ?? '', $_POST['password'] ?? '');
            if ($user) {
                Auth::login($user);
                redirect($user['role'] === 'admin' ? '/admin' : '/cabinet');
            }
            flash('error', 'Логин или пароль введены неверно');
            redirect('/login');
        }
        view('login');
        break;

    case $path === '/logout':
        Auth::logout();
        redirect('/');

    // ---------- участник ----------
    case $path === '/cabinet':
        Auth::needUser();
        view('cabinet', ['list' => $bookings->ofUser(Auth::user()['user_id'])]);
        break;

    case $path === '/booking':
        Auth::needUser();
        $errors = [];
        if ($method === 'POST') {
            if (!$workshops->find((int)($_POST['workshop_id'] ?? 0))) {
                $errors[] = 'Выберите мастер-класс';
            }
            if (trim($_POST['payment'] ?? '') === '') {
                $errors[] = 'Укажите способ оплаты';
            }
            if (trim($_POST['room'] ?? '') === '') {
                $errors[] = 'Укажите помещение';
            }
            if (!$errors) {
                $bookings->add(Auth::user()['user_id'], $_POST);
                flash('ok', 'Заявка принята. Следите за статусом в личном кабинете.');
                redirect('/cabinet');
            }
        }
        view('booking', ['workshops' => $workshops->all(), 'errors' => $errors,
                         'chosen' => (int)($_GET['w'] ?? $_POST['workshop_id'] ?? 0)]);
        break;

    // ---------- администратор ----------
    case $path === '/admin':
        Auth::needAdmin();
        view('admin', ['list' => $bookings->all(), 'statuses' => $bookings->statuses()]);
        break;

    case $path === '/admin/status' && $method === 'POST':
        Auth::needAdmin();
        $bookings->changeStatus((int)$_POST['booking_id'], (int)$_POST['status_id']);
        flash('ok', 'Статус заявки № ' . (int)$_POST['booking_id'] . ' обновлён');
        redirect('/admin');

    default:
        http_response_code(404);
        view('error', ['code' => 404, 'message' => 'Такой страницы нет']);
}
