<?php
// Configuracoes gerais do sistema
date_default_timezone_set('America/Sao_Paulo');

// Nome do sistema
define('SYSTEM_NAME', 'Pet Shop System');

define('APP_ENV', getenv('APP_ENV') ?: 'production');

$baseUrl = getenv('BASE_URL') ?: 'http://localhost/Petshopsystemv2/';
define('BASE_URL', rtrim($baseUrl, '/') . '/');

// Diretorios
define('ROOT_DIR', dirname(__DIR__));
define('ASSETS_DIR', ROOT_DIR . '/assets');
define('PAGES_DIR', ROOT_DIR . '/pages');
define('INCLUDES_DIR', ROOT_DIR . '/includes');

// Incluir arquivo de banco de dados
require_once __DIR__ . '/database.php';

function startSecureSession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

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
    return trim((string) $data);
}

function nullableInput($data) {
    $value = cleanInput($data);
    return $value === '' ? null : $value;
}

function isValidDate($value, $format = 'Y-m-d') {
    $date = DateTime::createFromFormat($format, $value);
    return $date && $date->format($format) === $value;
}

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function verifyCsrf() {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        exit('Requisicao invalida. Atualize a pagina e tente novamente.');
    }
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
