<?php
/** Экранирование вывода (защита от XSS) */
function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function redirect(string $to): never
{
    header('Location: ' . $to);
    exit;
}

/** Одноразовые сообщения между запросами */
function flash(string $type, string $text): void
{
    $_SESSION['flash'][] = ['type' => $type, 'text' => $text];
}

function take_flash(): array
{
    $list = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $list;
}

function view(string $name, array $data = []): void
{
    extract($data);
    ob_start();
    require __DIR__ . '/../views/' . $name . '.php';
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function ru_date(string $dt): string
{
    $months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля',
               'августа', 'сентября', 'октября', 'ноября', 'декабря'];
    $t = strtotime($dt);
    return date('j', $t) . ' ' . $months[date('n', $t) - 1] . ' ' . date('Y, H:i', $t);
}
