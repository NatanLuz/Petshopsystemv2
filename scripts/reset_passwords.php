<?php
// One-off script to reset example user passwords to match documentation
// admin@petshop.com => admin123
// recepcao@petshop.com => recepcao123

header('Content-Type: text/plain; charset=UTF-8');
$root = dirname(__DIR__);
require_once $root . '/config/config.php';

try {
    $conn = getConnection();

    $updates = [
        ['email' => 'admin@petshop.com', 'new' => 'admin123'],
        ['email' => 'recepcao@petshop.com', 'new' => 'recepcao123'],
    ];

    foreach ($updates as $u) {
        $hash = password_hash($u['new'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare('UPDATE usuarios SET senha = ? WHERE email = ?');
        $stmt->bind_param('ss', $hash, $u['email']);
        $stmt->execute();
        echo "Updated: {$u['email']} -> " . substr($hash, 0, 20) . "...\n";
        $stmt->close();
    }

    // Show counts to confirm
    $res = $conn->query('SELECT id, email, LEFT(senha, 20) AS prefix FROM usuarios');
    while ($row = $res->fetch_assoc()) {
        echo $row['id'] . ' | ' . $row['email'] . ' | ' . $row['prefix'] . "...\n";
    }

    $conn->close();
    echo "DONE\n";
} catch (Throwable $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
