<?php
define('AUTH_TOKEN', 'allpneu84_token_secret_xK9mP');

function isAuthenticated() {
    $token = '';
    if (isset($_SERVER['HTTP_X_AUTH_TOKEN'])) {
        $token = $_SERVER['HTTP_X_AUTH_TOKEN'];
    } elseif (isset($_COOKIE['allpneu_token'])) {
        $token = $_COOKIE['allpneu_token'];
    } elseif (isset($_GET['token'])) {
        $token = $_GET['token'];
    }
    return $token === AUTH_TOKEN;
}

function requireAuth() {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(array('error' => 'Non authentifie'));
        exit();
    }
}
