<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Auth.php';

// Se já está autenticado, redireciona para dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: /public/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $auth = new Auth();
    $user = $auth->login($email, $password);
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['nome'];
        header('Location: /public/index.php');
        exit;
    } else {
        $error = 'Email ou senha inválidos';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PetShop CRM</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>PetShop CRM</h1>
            <p class="subtitle">Sistema de Gestão</p>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="admin@petshop.com">
                </div>
                
                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" required placeholder="Sua senha">
                </div>
                
                <button type="submit" class="btn btn-primary">Entrar</button>
            </form>
            
            <p class="demo-info">Demo: admin@petshop.com / 123456</p>
        </div>
    </div>
</body>
</html>
