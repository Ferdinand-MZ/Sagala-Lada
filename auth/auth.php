<?php
session_start();

function is_login() {
    return isset($_SESSION['user_id']);
}

function login_required() {
    if (!is_login()) {
        header("Location: /auth/login.php");
        exit;
    }
}

function role_required($roles = []) {
    login_required();
    
    if (!in_array($_SESSION['role'], (array)$roles)) {
        die("<h1>403 - Akses Ditolak</h1><p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>");
    }
}