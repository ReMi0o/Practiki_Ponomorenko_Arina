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
if ($method === 'POST' && !csrf_ok()) {
    http_response_code(419);
    view('error', ['code' => 419, 'message' => 'Страница устарела. Обновите её и отправьте форму ещё раз.']);
    exit;
}
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
        $old = ['workshop_id' => (int)($_GET['w'] ?? 0)];
        if ($method === 'POST') {
            $old = $_POST;
            $errors = Validator::booking($_POST, $workshops, $bookings);
            if (!$errors) {
                $bookings->add(Auth::user()['user_id'], $_POST);
                flash('ok', 'Заявка принята. Следите за статусом в личном кабинете.');
                redirect('/cabinet');
            }
        }
        view('booking', ['workshops' => $workshops->all(), 'errors' => $errors, 'old' => $old]);
        break;

    case $path === '/feedback':
        Auth::needUser();
        $b = $bookings->find((int)($_GET['id'] ?? 0));
        if (!$b || $b['user_id'] != Auth::user()['user_id']) {
            http_response_code(404);
            view('error', ['code' => 404, 'message' => 'Заявка не найдена']);
            break;
        }
        if (!$bookings->canLeaveFeedback($b)) {
            flash('error', 'Отзыв можно оставить после того, как мастер-класс завершится');
            redirect('/cabinet');
        }
        $errors = [];
        $old = [];
        if ($method === 'POST') {
            $old = $_POST;
            $errors = Validator::feedback($_POST);
            if (!$errors) {
                $bookings->addFeedback($b['booking_id'], (int)$_POST['stars'], $_POST['message']);
                flash('ok', 'Спасибо! Отзыв опубликован.');
                redirect('/cabinet');
            }
        }
        view('feedback', compact('b', 'errors', 'old'));
        break;

    // JSON: сколько свободных мест осталось (используется формой записи)
    case preg_match('#^/api/workshops/(\d+)$#', $path, $m) === 1:
        header('Content-Type: application/json; charset=utf-8');
        $w = $workshops->find((int)$m[1]);
        if (!$w) {
            http_response_code(404);
            echo json_encode(['error' => 'not found']);
            break;
        }
        echo json_encode(['id' => (int)$w['workshop_id'], 'title' => $w['title'],
                          'seats' => (int)$w['seats'], 'free' => $bookings->freeSeats($w)], JSON_UNESCAPED_UNICODE);
        break;

    // ---------- администратор ----------
    case $path === '/admin':
        Auth::needAdmin();
        $filter = (int)($_GET['status'] ?? 0) ?: null;
        view('admin', ['list' => $bookings->all($filter), 'statuses' => $bookings->statuses(),
                       'counts' => $bookings->countByStatus(), 'filter' => $filter]);
        break;

    case $path === '/admin/status' && $method === 'POST':
        Auth::needAdmin();
        $bookings->changeStatus((int)$_POST['booking_id'], (int)$_POST['status_id']);
        flash('ok', 'Статус заявки № ' . (int)$_POST['booking_id'] . ' обновлён');
        redirect('/admin' . (!empty($_POST['filter']) ? '?status=' . (int)$_POST['filter'] : ''));

    default:
        http_response_code(404);
        view('error', ['code' => 404, 'message' => 'Такой страницы нет']);
}
