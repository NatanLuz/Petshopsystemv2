<?php
// Configuracoes gerais do sistema
date_default_timezone_set('America/Sao_Paulo');

// Nome do sistema
define('SYSTEM_NAME', 'Pet Shop System');

// URL base do sistema
define('BASE_URL', 'http://localhost/Petshopsystemv2/');

// Diretorios
define('ROOT_DIR', dirname(__DIR__));
define('ASSETS_DIR', ROOT_DIR . '/assets');
define('PAGES_DIR', ROOT_DIR . '/pages');
define('INCLUDES_DIR', ROOT_DIR . '/includes');

// Incluir arquivo de banco de dados
require_once __DIR__ . '/database.php';

// Funcao para verificar se o usuario esta logado
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'pages/login.php');
        exit();
    }
}

// Funcao para verificar se e admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Funcao para limpar dados de entrada
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Funcao para formatar data brasileira
function formatDateBR($date) {
    if (empty($date)) return '';
    return date('d/m/Y', strtotime($date));
}

// Funcao para formatar data e hora brasileira
function formatDateTimeBR($datetime) {
    if (empty($datetime)) return '';
    return date('d/m/Y H:i', strtotime($datetime));
}

// Funcao para formatar moeda
function formatMoney($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}
?>
