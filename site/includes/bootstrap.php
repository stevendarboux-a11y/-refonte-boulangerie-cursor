<?php

declare(strict_types=1);

ini_set('display_errors', '0');
ini_set('log_errors', '1');

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function csrf_ok(?string $token): bool
{
    $attendu = $_SESSION['csrf'] ?? '';

    return is_string($attendu)
        && $attendu !== ''
        && is_string($token)
        && hash_equals($attendu, $token);
}

function csrf_renouveler(): void
{
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
