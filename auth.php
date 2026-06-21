<?php
define('AUTH_USER', 'admin');
define('AUTH_PASS', 'Abdemedjed84');

if (session_status() === PHP_SESSION_NONE) {
    // Cookie secure pour HTTPS (Railway)
    $isHttps = isset($_SERVER['HTTPS']) || 
               (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    session_set_cookie_params([
        'lifetime' => 86400 * 7,  // 7 jours
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

function isAuthenticated() {
    return isset($_SESSION['allpneu_auth']) && $_SESSION['allpneu_auth'] === true;
}

function requireAuth() {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(array('error' => 'Non authentifie', 'login' => 'login.php'));
        exit();
    }
}
