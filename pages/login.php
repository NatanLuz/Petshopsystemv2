<?php
session_start();
require_once '../config/config.php';

// Se ja esta logado, vai ser redirecionado
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = cleanInput($_POST['email']);
    $senha = $_POST['senha'];
    
    if (empty($email) || empty($senha)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT id, nome, email, senha, role FROM usuarios WHERE email = ? AND ativo = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($senha, $user['senha'])) {
                // Login bem-sucedido
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Email ou senha invalidos.';
            }
        } else {
            $error = 'Email ou senha invalidos.';
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SYSTEM_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="fas fa-paw"></i> <?php echo SYSTEM_NAME; ?></h1>
                <p>Faca login para continuar</p>
            </div>

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" id="email" name="email" required autofocus>
                </div>

                <div class="form-group">
                    <label for="senha">
                        <i class="fas fa-lock"></i> Senha
                    </label>
                    <input type="password" id="senha" name="senha" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
            </form>

            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #dee2e6;">
                <p style="text-align: center; color: #858796; font-size: 0.85rem; margin-bottom: 0.5rem;">
                    <strong>Usuarios de teste:</strong>
                </p>
                <p style="text-align: center; color: #858796; font-size: 0.8rem; margin: 0.25rem 0;">
                    Admin: <strong>admin@petshop.com</strong> / Senha: <strong>admin123</strong>
                </p>
                <p style="text-align: center; color: #858796; font-size: 0.8rem; margin: 0.25rem 0;">
                    Recepcionista: <strong>recepcao@petshop.com</strong> / Senha: <strong>recepcao123</strong>
                </p>
            </div>
        </div>
    </div>
</body>

</html>