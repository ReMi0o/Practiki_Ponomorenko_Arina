<?php
function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function redirect(string $to): never
{
    header('Location: ' . $to);
    exit;
}

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

function csrf_field(): string
{
    $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
    return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}

function csrf_ok(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

function field_error(array $errors, string $name): string
{
    return '<span class="field__error" data-for="' . $name . '">' . e($errors[$name] ?? '') . '</span>';
}

function field_class(array $errors, string $name): string
{
    return isset($errors[$name]) ? 'field field--bad' : 'field';
}
