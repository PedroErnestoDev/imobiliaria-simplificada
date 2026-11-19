<?php
// actions/autenticar.php
// Processamento de autenticação de usuário.

declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=utf-8');

$response = ['sucesso' => false, 'mensagem' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $response['mensagem'] = 'Método não permitido.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($email === '' || $senha === '') {
    http_response_code(400);
    $response['mensagem'] = 'Preencha e-mail e senha.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

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
        http_response_code(401);
        $response['mensagem'] = 'E-mail ou senha inválidos.';
    } elseif ((int) $usuario['ativo'] !== 1) {
        http_response_code(401);
        $response['mensagem'] = 'Usuário inativo. Entre em contato com o suporte.';
    } elseif (!password_verify($senha, $usuario['senha_hash'])) {
        http_response_code(401);
        $response['mensagem'] = 'E-mail ou senha inválidos.';
    } else {
        $_SESSION['usuario_id'] = (int) $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_papel'] = $usuario['papel'];
        $_SESSION['usuario_ativo'] = (int) $usuario['ativo'];

        $response['sucesso'] = true;
        $response['mensagem'] = 'Login realizado com sucesso.';
        $response['usuario'] = [
            'id' => (int) $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'papel' => $usuario['papel'],
        ];
    }
} catch (Throwable $e) {
    error_log('[Autenticar] ' . $e->getMessage());
    http_response_code(500);
    $response['mensagem'] = 'Erro interno ao tentar fazer login. Tente novamente.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);