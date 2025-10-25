<?php

header('Content-Type: text/plain; charset=UTF-8');

$root = dirname(__DIR__);
require_once $root . '/config/config.php';

$requiredTables = ['usuarios','clientes','pets','servicos','atendimentos'];

function line($msg) { echo $msg . "\n"; }

line('=== Pet Shop System - DB Health Check ===');
line('Timestamp: ' . date('Y-m-d H:i:s'));
line('BASE_URL: ' . BASE_URL);
line('DB_HOST: ' . DB_HOST . ' | DB_USER: ' . DB_USER . ' | DB_NAME: ' . DB_NAME);

try {
    $conn = getConnection();
    line('Connection: OK');

   
    $dbRes = $conn->query('SELECT DATABASE() AS db');
    $dbRow = $dbRes ? $dbRes->fetch_assoc() : null;
    line('Current DB: ' . ($dbRow['db'] ?? '-'));

 
    $tables = [];
    if ($res = $conn->query('SHOW TABLES')) {
        while ($row = $res->fetch_array()) { $tables[] = $row[0]; }
    }

    line('Tables found: ' . (empty($tables) ? 'none' : implode(', ', $tables)));

  
    $missing = [];
    foreach ($requiredTables as $t) {
        if (!in_array($t, $tables, true)) { $missing[] = $t; }
    }

    if (empty($missing)) {
        line('Required tables: OK');

       
        $counts = [];
        foreach ($requiredTables as $t) {
            $res = $conn->query("SELECT COUNT(*) AS c FROM `$t`");
            $counts[$t] = $res ? (int)$res->fetch_assoc()['c'] : 0;
        }
        line('Counts: ' . json_encode($counts));
        line('Status: HEALTHY');
    } else {
        line('Missing tables: ' . implode(', ', $missing));
        line('Status: NEEDS_IMPORT');
        line('Next step: import the SQL file into the database.');
        line('SQL path: ' . $root . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'database.sql');
        line('Import via phpMyAdmin: http://localhost/phpmyadmin');
    }

    $conn->close();
} catch (Throwable $e) {
    line('Connection: ERROR');
    line('Error: ' . $e->getMessage());
    line('Hint: verify config/database.php (user, password, db name) and MySQL service.');
    line('Status: CONNECTION_FAILED');
}