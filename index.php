<?php
require_once __DIR__ . '/auth.php';

// Check token from URL or store in cookie
$token = isset($_GET['token']) ? $_GET['token'] : 
         (isset($_COOKIE['allpneu_token']) ? $_COOKIE['allpneu_token'] : '');

if ($token === AUTH_TOKEN) {
    // Store token in cookie for 7 days
    setcookie('allpneu_token', AUTH_TOKEN, time() + 86400*7, '/', '', true, true);
    // Serve app
    readfile(__DIR__ . '/index.html');
} else {
    header('Location: login.php');
    exit();
}
