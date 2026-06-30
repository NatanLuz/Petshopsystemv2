<?php
// Modelo publico de configuracao do banco de dados.
// Copie este arquivo para config/database.php e preencha os dados reais apenas
// no ambiente local ou no servidor. Nunca versione credenciais reais.

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'petshop_system');

function getConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            throw new RuntimeException('Falha na conexao com o banco de dados.');
        }

        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        error_log('Database connection error: ' . $e->getMessage());
        http_response_code(500);
        exit('Nao foi possivel conectar ao banco de dados.');
    }
}

// Funcao para exec queries preparadas na aplicacao
function executeQuery($query, $params = [], $types = "") {
    $conn = getConnection();
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new RuntimeException('Falha ao preparar consulta.');
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    return $stmt;
}
