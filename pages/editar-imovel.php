<?php
require_once __DIR__ . '/../app/programacao/imoveis/imoveis.php';

$sucesso = '';
$erro = '';
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: dashboard.php');
    exit;
}

$imovel = buscarImovelCompleto($pdo, $id);
if (!$imovel) {
    $erro = 'Imóvel não encontrado.';
}

$imagens = [];
if ($imovel) {
    $imagens = listarImagensImovel($pdo, $id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $imovel) {
    $dados = [
        'codigo' => trim($_POST['codigo'] ?? ''),
        'titulo' => trim($_POST['titulo'] ?? ''),
        'descricao' => trim($_POST['descricao'] ?? ''),
        'finalidade' => $_POST['finalidade'] ?? 'venda',
        'tipo' => $_POST['tipo'] ?? 'casa',
        'cep' => trim($_POST['cep'] ?? ''),
        'endereco' => trim($_POST['endereco'] ?? ''),
        'numero' => trim($_POST['numero'] ?? ''),
        'complemento' => trim($_POST['complemento'] ?? ''),
        'bairro' => trim($_POST['bairro'] ?? ''),
        'cidade' => trim($_POST['cidade'] ?? ''),
        'estado' => trim($_POST['estado'] ?? ''),
        'quartos' => (int) ($_POST['quartos'] ?? 0),
        'banheiros' => (int) ($_POST['banheiros'] ?? 0),
        'vagas' => (int) ($_POST['vagas'] ?? 0),
        'area' => $_POST['area'] ?? null,
        'valor' => (float) ($_POST['valor'] ?? 0),
        'condominio' => $_POST['condominio'] ?? null,
        'iptu' => $_POST['iptu'] ?? null,
        'telefone_contato' => trim($_POST['telefone_contato'] ?? ''),
        'whatsapp' => trim($_POST['whatsapp'] ?? ''),
        'email_contato' => trim($_POST['email_contato'] ?? ''),
        'latitude' => $_POST['latitude'] ?? null,
        'longitude' => $_POST['longitude'] ?? null,
        'status' => $_POST['status'] ?? 'disponivel'
    ];

    try {
        atualizarImovel($pdo, $id, $dados);
        $sucesso = 'Imóvel atualizado com sucesso!';
        $imovel = array_merge($imovel, $dados);
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Imóvel | DW2 Imobiliária</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --gold: #c99a3b; --dark: #151515; }
        body { font-family: 'DM Sans', sans-serif; background: #f8f9fa; }
        .btn-gold { background: var(--gold); border: 1px solid var(--gold); color: #151515; font-weight: 700; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: var(--dark);">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">DW2 Dashboard</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard.php">Voltar ao Dashboard</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h5>Editar Imóvel #<?= $id ?></h5>
                </div>
                <div class="card-body">
                    <?php if ($sucesso): ?>
                        <div class="alert alert-success"><?= $sucesso ?></div>
                    <?php endif; ?>
                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?= $erro ?></div>
                    <?php endif; ?>

                    <?php if ($imovel): ?>
                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Código *</label>
                                <input type="text" name="codigo" class="form-control" value="<?= htmlspecialchars($imovel['codigo']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Finalidade *</label>
                                <select name="finalidade" class="form-select" required>
                                    <option value="venda" <?= $imovel['finalidade'] === 'venda' ? 'selected' : '' ?>>Venda</option>
                                    <option value="aluguel" <?= $imovel['finalidade'] === 'aluguel' ? 'selected' : '' ?>>Aluguel</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tipo *</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="casa" <?= $imovel['tipo'] === 'casa' ? 'selected' : '' ?>>Casa</option>
                                    <option value="apartamento" <?= $imovel['tipo'] === 'apartamento' ? 'selected' : '' ?>>Apartamento</option>
                                    <option value="condominio" <?= $imovel['tipo'] === 'condominio' ? 'selected' : '' ?>>Condomínio</option>
                                    <option value="terreno" <?= $imovel['tipo'] === 'terreno' ? 'selected' : '' ?>>Terreno</option>
                                    <option value="na_planta" <?= $imovel['tipo'] === 'na_planta' ? 'selected' : '' ?>>Na Planta</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Título *</label>
                                <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($imovel['titulo']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Valor (R$) *</label>
                                <input type="number" name="valor" class="form-control" value="<?= (float) $imovel['valor'] ?>" required step="0.01" min="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Descrição</label>
                                <textarea name="descricao" class="form-control" rows="3"><?= htmlspecialchars($imovel['descricao'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">CEP</label>
                                <input type="text" name="cep" class="form-control" value="<?= htmlspecialchars($imovel['cep'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Endereço</label>
                                <input type="text" name="endereco" class="form-control" value="<?= htmlspecialchars($imovel['endereco'] ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Número</label>
                                <input type="text" name="numero" class="form-control" value="<?= htmlspecialchars($imovel['numero'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Complemento</label>
                                <input type="text" name="complemento" class="form-control" value="<?= htmlspecialchars($imovel['complemento'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Bairro</label>
                                <input type="text" name="bairro" class="form-control" value="<?= htmlspecialchars($imovel['bairro'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Cidade</label>
                                <input type="text" name="cidade" class="form-control" value="<?= htmlspecialchars($imovel['cidade'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Estado (UF)</label>
                                <input type="text" name="estado" class="form-control" value="<?= htmlspecialchars($imovel['estado'] ?? '') ?>" required maxlength="2">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Quartos</label>
                                <input type="number" name="quartos" class="form-control" value="<?= (int) $imovel['quartos'] ?>" min="0">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Banheiros</label>
                                <input type="number" name="banheiros" class="form-control" value="<?= (int) $imovel['banheiros'] ?>" min="0">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Vagas</label>
                                <input type="number" name="vagas" class="form-control" value="<?= (int) $imovel['vagas'] ?>" min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Área (m²)</label>
                                <input type="number" name="area" class="form-control" value="<?= $imovel['area'] ?? '' ?>" step="0.01" min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Condomínio (R$)</label>
                                <input type="number" name="condominio" class="form-control" value="<?= $imovel['condominio'] ?? '' ?>" step="0.01" min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">IPTU (R$)</label>
                                <input type="number" name="iptu" class="form-control" value="<?= $imovel['iptu'] ?? '' ?>" step="0.01" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Telefone Contato</label>
                                <input type="text" name="telefone_contato" class="form-control" value="<?= htmlspecialchars($imovel['telefone_contato'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">WhatsApp</label>
                                <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($imovel['whatsapp'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">E-mail Contato</label>
                                <input type="email" name="email_contato" class="form-control" value="<?= htmlspecialchars($imovel['email_contato'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Latitude</label>
                                <input type="text" name="latitude" class="form-control" value="<?= htmlspecialchars($imovel['latitude'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Longitude</label>
                                <input type="text" name="longitude" class="form-control" value="<?= htmlspecialchars($imovel['longitude'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="disponivel" <?= $imovel['status'] === 'disponivel' ? 'selected' : '' ?>>Disponível</option>
                                    <option value="indisponivel" <?= $imovel['status'] === 'indisponivel' ? 'selected' : '' ?>>Indisponível</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-gold py-2">Atualizar Imóvel</button>
                            <a href="dashboard.php" class="btn btn-outline-secondary py-2">Cancelar</a>
                        </div>
                    </form>

                    <hr class="mt-5">

                    <h6>Imagens cadastradas</h6>
                    <?php if (empty($imagens)): ?>
                        <p class="text-muted">Nenhuma imagem cadastrada.</p>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($imagens as $img): ?>
                                <div class="col-md-4">
                                    <div class="card border-0">
                                        <img src="assets/casa.jpg" class="card-img-top" alt="Imagem" style="max-height: 200px; object-fit: cover;">
                                        <div class="card-body small text-center">
                                            <span class="badge bg-<?= $img['imagem_principal'] ? 'success' : 'secondary' ?>">
                                                <?= $img['imagem_principal'] ? 'Principal' : 'Secundária' ?>
                                            </span><br>
                                            <a href="#" class="btn btn-sm btn-danger text-white">Excluir</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
