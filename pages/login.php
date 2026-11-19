<?php
// pages/login.php
// Página de login do usuário.

declare(strict_types=1);

session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: /dashboard.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha e-mail e senha.';
    } else {
        require_once __DIR__ . '/../app/db/config.php';

        try {
            $pdo = (new Database())->connect();

            $stmt = $pdo->prepare(
                'SELECT id, nome, email, senha_hash, papel, ativo FROM usuarios WHERE email = :email LIMIT 1'
            );
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario === false) {
                $erro = 'E-mail ou senha inválidos.';
            } elseif ((int) $usuario['ativo'] !== 1) {
                $erro = 'Usuário inativo. Entre em contato com o suporte.';
            } elseif (!password_verify($senha, $usuario['senha_hash'])) {
                $erro = 'E-mail ou senha inválidos.';
            } else {
                $_SESSION['usuario_id'] = (int) $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_papel'] = $usuario['papel'];
                $_SESSION['usuario_ativo'] = (int) $usuario['ativo'];

                header('Location: /dashboard.php');
                exit;
            }
        } catch (Throwable $e) {
            error_log('[Login] ' . $e->getMessage());
            $erro = 'Erro interno ao tentar fazer login. Tente novamente.';
        }
    }
}

function escapar(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | DW2 Imobiliária</title>
    <link rel="icon" href="../public/assets/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #c99a3b;
            --gold-light: #e5bd68;
            --dark: #151515;
            --background: #f7f7f7;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--background);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-header img { width: 150px; margin-bottom: 16px; }
        .login-header h2 { color: var(--dark); font-weight: 800; margin: 0; }
        .login-header p { color: #777777; font-size: 14px; margin: 8px 0 0; }
        .btn-gold { background: var(--gold); color: #151515; border: 1px solid var(--gold); border-radius: 6px; padding: 12px; font-weight: 700; width: 100%; }
        .btn-gold:hover { background: var(--gold-light); border-color: var(--gold-light); color: #151515; }
        .footer-link { text-align: center; margin-top: 20px; font-size: 14px; color: #777777; }
        .footer-link a { color: var(--gold); text-decoration: none; font-weight: 600; }
        .footer-link a:hover { color: var(--gold-light); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <img src="../public/assets/logo.png" alt="DW2 Imobiliária">
            <h2>Bem-vindo de volta</h2>
            <p>Faça login para acessar sua conta</p>
        </div>

        <?php if ($erro !== ''): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= escapar($erro) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" value="<?= escapar($_POST['email'] ?? '') ?>" required autocomplete="email">
            </div>
            <div class="mb-4">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" placeholder="••••••••" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-gold">Entrar</button>
        </form>

        <div class="footer-link">
            Não tem uma conta? <a href="#">Cadastre-se</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>