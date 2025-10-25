<?php
session_start();

// encoding: UTF-8

// Se ja esta logado, redireciona para dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: pages/dashboard.php');
    exit();
}

// Redireciona para pagina de login
header('Location: pages/login.php');
exit();
?>