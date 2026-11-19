<?php
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit;
}

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$status = $_POST['status'] ?? '';

if (!$id || $id < 1 || !in_array($status, ['disponivel', 'indisponivel'], true)) {
    header('Location: /dashboard.php?acao=erro');
    exit;
}

try {
    $alterado = alterarStatusImovel($pdo, $id, $status);
    $resultado = $status === 'disponivel' ? 'ativado' : 'desativado';
    header(
        'Location: /dashboard.php?acao='
        . ($alterado ? $resultado : 'erro')
    );
} catch (Throwable $e) {
    error_log('[Alterar status do imóvel] ' . $e->getMessage());
    header('Location: /dashboard.php?acao=erro');
}

exit;
