<?php
// ============================================================
// GREEN HAVEN - Logout
// ============================================================
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';

session_start();

// Unset all session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to home page with a message
session_start();
$_SESSION['flash'] = [
    'type' => 'info',
    'message' => 'You have been successfully logged out.'
];

header('Location: ' . SITE_URL . '/index.php');
exit;
