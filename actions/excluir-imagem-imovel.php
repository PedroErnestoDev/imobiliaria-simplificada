<?php
// actions/excluir-imagem-imovel.php
// Exclui uma imagem de um imóvel.

declare(strict_types=1);

require_once __DIR__ . '/../app/programacao/imoveis/imoveis.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Proteção: exige autenticação
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /login.php');
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$response = ['sucesso' => false, 'mensagem' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $response['mensagem'] = 'Método não permitido.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

$imagemId = filter_var($_POST['imagem_id'] ?? null, FILTER_VALIDATE_INT);

if (!$imagemId || $imagemId < 1) {
    http_response_code(400);
    $response['mensagem'] = 'ID de imagem inválido.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sucesso = excluirImagemImovel($pdo, $imagemId);

    if ($sucesso) {
        $response['sucesso'] = true;
        $response['mensagem'] = 'Imagem excluída com sucesso.';
    } else {
        http_response_code(404);
        $response['mensagem'] = 'Imagem não encontrada.';
    }
} catch (Throwable $e) {
    error_log('[Excluir imagem] ' . $e->getMessage());
    http_response_code(500);
    $response['mensagem'] = 'Erro interno ao excluir a imagem.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);