<?php
require_once __DIR__ . '/config/config.php';
startSecureSession();

// encoding: UTF-8

// Se ja esta logado, redireciona para o dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: pages/dashboard.php');
    exit();
}

// Redireciona para página de login
header('Location: pages/login.php');
exit();
?>
