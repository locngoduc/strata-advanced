<?php
session_start();

function setUserCookie($userId, $username) {
    // Set a secure cookie that expires in 30 days
    setcookie('user_id', $userId, time() + (86400 * 30), '/', '', true, true);
    setcookie('username', $username, time() + (86400 * 30), '/', '', true, true);
}

function clearUserCookie() {
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('username', '', time() - 3600, '/');
    session_destroy();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_COOKIE['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit();
    }
}