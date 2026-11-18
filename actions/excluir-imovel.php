<?php
require_once __DIR__ . '/../app/programacao/imoveis/imoveis.php';

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if ($id) {
    excluirImovel($pdo, $id);
}

header('Location: ../public/dashboard.php?status=excluido');
exit;
