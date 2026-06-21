<?php
define('AUTH_USER', 'admin');
define('AUTH_PASS', 'Abdemedjed84');

if (session_status() === PHP_SESSION_NONE) {
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
