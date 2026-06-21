<?php
require_once __DIR__ . '/auth.php';

if (!isAuthenticated()) {
    header('Location: login.php');
    exit();
}

readfile(__DIR__ . '/index.html');
