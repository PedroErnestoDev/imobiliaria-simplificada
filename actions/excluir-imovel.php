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

if (!$id || $id < 1) {
    header('Location: /dashboard.php?acao=erro');
    exit;
}

try {
    $excluido = excluirImovel($pdo, $id);
    header(
        'Location: /dashboard.php?acao='
        . ($excluido ? 'excluido' : 'erro')
    );
} catch (Throwable $e) {
    error_log('[Excluir imóvel] ' . $e->getMessage());
    header('Location: /dashboard.php?acao=erro');
}

exit;
