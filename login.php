<?php
session_start();
include 'config.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = escapar($_POST['email']);
    $senha = $_POST['senha'];
    
    $result = $conn->query("SELECT * FROM usuarios WHERE email = '$email'");
    
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        if (hash('sha256', $senha) == $usuario['senha']) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header('Location: index.php');
            exit();
        } else {
            $erro = 'Email ou senha inválidos';
        }
    } else {
        $erro = 'Email ou senha inválidos';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetShop CRM - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #0ca5b0 0%, #088a95 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        
        .login-container {
            background: #fefeeb;
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-top: 5px solid #0ca5b0;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .logo {
            font-size: 48px;
            margin-bottom: 16px;
        }
        
        .login-header h1 {
            font-size: 24px;
            color: #4e3f30;
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        
        .login-header p {
            color: #a5b3aa;
            font-size: 14px;
            margin-top: 8px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #4e3f30;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #a5b3aa;
            border-radius: 8px;
            font-size: 14px;
            background-color: white;
            color: #4e3f30;
            transition: all 0.2s;
            font-family: inherit;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #0ca5b0;
            box-shadow: 0 0 0 3px rgba(12, 165, 176, 0.15);
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #0ca5b0 0%, #1bc5d1 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(12, 165, 176, 0.2);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(12, 165, 176, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .erro {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #ef4444;
            font-size: 14px;
        }
        
        .demo-info {
            background: #f0fdf4;
            color: #166534;
            padding: 12px;
            border-radius: 8px;
            margin-top: 24px;
            border-left: 4px solid #10b981;
            font-size: 13px;
        }
        
        .demo-info strong {
            display: block;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">🐾</div>
            <h1>PetShop CRM</h1>
            <p>Sistema de Gestão para Pet Shops</p>
        </div>
        
        <?php if ($erro): ?>
            <div class="erro"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            
            <button type="submit" class="btn-login">Entrar</button>
        </form>
        
        <div class="demo-info">
            <strong>Dados de Demonstração:</strong>
            Email: admin@petshop.com<br>
            Senha: 123456
        </div>
    </div>
</body>
</html>
