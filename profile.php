<?php
include 'config.php';
include 'auth-middleware.php';
verificarAutenticacao();

$usuario = obterUsuarioAtual();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $novo_nome = escapar($_POST['nome']);
    $nova_senha = isset($_POST['nova_senha']) && !empty($_POST['nova_senha']) ? hash('sha256', $_POST['nova_senha']) : null;
    
    $sql = "UPDATE usuarios SET nome = '$novo_nome'";
    if ($nova_senha) {
        $sql .= ", senha = '$nova_senha'";
    }
    $sql .= " WHERE id = " . $_SESSION['usuario_id'];
    
    if ($conn->query($sql)) {
        $_SESSION['usuario_nome'] = $novo_nome;
        $sucesso = 'Perfil atualizado com sucesso!';
        registrarAtividade($_SESSION['usuario_id'], 'perfil_atualizado', 'Usuário atualizou seu perfil');
    } else {
        $erro = 'Erro ao atualizar perfil: ' . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - PetShop CRM</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .profile-card {
            background: #fefeeb;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #0ca5b0;
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 32px;
            border-bottom: 2px solid #a5b3aa;
            padding-bottom: 24px;
        }
        
        .profile-avatar {
            font-size: 64px;
            margin-bottom: 16px;
        }
        
        .profile-header h2 {
            color: #4e3f30;
            font-size: 24px;
            margin-bottom: 8px;
        }
        
        .profile-header p {
            color: #a5b3aa;
            font-size: 14px;
        }
        
        .sucesso {
            background: #d1fae5;
            color: #065f46;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #10b981;
        }
        
        .erro {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #ef4444;
        }
    </style>
</head>
<body style="background-color: #f8f4e4;">
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">👤</div>
                <h2><?php echo htmlspecialchars($usuario['nome']); ?></h2>
                <p>Membro desde <?php echo formatarData($usuario['created_at']); ?></p>
            </div>
            
            <?php if (isset($sucesso)): ?>
                <div class="sucesso"><?php echo $sucesso; ?></div>
            <?php endif; ?>
            
            <?php if (isset($erro)): ?>
                <div class="erro"><?php echo $erro; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" readonly>
                    <small style="color: #a5b3aa;">Email não pode ser alterado</small>
                </div>
                
                <div class="form-group">
                    <label>Nova Senha (deixe em branco para manter a atual)</label>
                    <input type="password" name="nova_senha" class="form-control" placeholder="Digite uma nova senha">
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 28px;">
                    <a href="index.php" class="btn btn-secondary">Voltar ao Painel</a>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
