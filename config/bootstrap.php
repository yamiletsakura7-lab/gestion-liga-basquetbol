
<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/database.php';

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function logged_in()
{
    return isset($_SESSION['user_id']);
}

function require_login()
{
    if (!logged_in()) {
        header('Location: index.php?page=login');
        exit;
    }
}

function flash($message, $type = 'success')
{
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

function redirect($page)
{
    header(
        'Location: index.php?page=' . urlencode($page)
    );
    exit;
}
