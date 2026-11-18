<?php
require_once __DIR__ . '/../app/programacao/imoveis/imoveis.php';
$tipo = trim($_GET['tipo'] ?? '');
$finalidade = trim($_GET['finalidade'] ?? '');
$status = trim($_GET['status'] ?? '');
$busca = trim($_GET['busca'] ?? '');
$tiposPermitidos = ['casa', 'apartamento', 'condominio', 'terreno', 'na_planta'];
$finalidadesPermitidas = ['venda', 'aluguel'];
$statusPermitidos = ['disponivel', 'indisponivel'];
if (!in_array($tipo, $tiposPermitidos, true)) { $tipo = ''; }
if (!in_array($finalidade, $finalidadesPermitidas, true)) { $finalidade = ''; }
if (!in_array($status, $statusPermitidos, true)) { $status = ''; }
$pagina = filter_var($_GET['pagina'] ?? 1, FILTER_VALIDATE_INT);
$pagina = max(1, $pagina ?: 1);
$filtros = ['tipo' => $tipo, 'finalidade' => $finalidade, 'status' => $status, 'busca' => $busca];
$limite = 9;
$totalImoveis = contarTodosImoveis($pdo, $filtros);
$totalPaginas = max(1, (int) ceil($totalImoveis / $limite));
$pagina = min($pagina, $totalPaginas);
$imoveis = listarTodosImoveis($pdo, $filtros, $pagina, $limite);
function urlPagina(int $pagina): string { $parametros = $_GET; $parametros['pagina'] = $pagina; return '?' . http_build_query($parametros); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | DW2 Imobiliária</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --gold: #c99a3b; --gold-hover: #b4852e; --dark: #151515; }
        body { font-family: 'DM Sans', sans-serif; background: #f8f9fa; }
        .sidebar { background: var(--dark); min-height: 100vh; width: 260px; position: fixed; top: 0; left: 0; padding-top: 20px; }
        .sidebar a { color: #ccc; padding: 15px 25px; display: block; text-decoration: none; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { color: white; background: var(--gold); }
        .main-content { margin-left: 260px; padding: 30px; }
        .btn-gold { background: var(--gold); border: 1px solid var(--gold); color: #151515; font-weight: 700; }
        .btn-gold:hover { background: var(--gold-hover); }
        .status-badge { font-size: 12px; padding: 6px 12px; border-radius: 20px; font-weight: 600; }
        .status-disponivel { background: #d4edda; color: #155724; }
        .status-indisponivel { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
<nav class="sidebar">
    <div class="text-center mb-4"><img src="assets/logo.png" alt="DW2" style="width: 120px;"></div>
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="cadastrar-imovel.php">+ Cadastrar Imóvel</a>
    <a href="imoveis.php">Ver Catálogo Público</a>
    <a href="contato.php">Contato</a>
    <div style="margin-top: auto; padding: 25px;"><a href="login.php" style="color: var(--gold);">Sair</a></div>
</nav>
<div class="main-content">
    <header class="d-flex justify-content-between align-items-center mb-4">
        <div><h2>Dashboard</h2><p class="text-muted">Gerencie os imóveis da imobiliária</p></div>
        <a href="cadastrar-imovel.php" class="btn btn-gold px-4 py-2">+ Novo Imóvel</a>
    </header>
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3"><label class="form-label small">Tipo</label><select name="tipo" class="form-select form-select-sm"><option value="">Todos</option><option value="casa" <?= $tipo === 'casa' ? 'selected' : '' ?>>Casa</option><option value="apartamento" <?= $tipo === 'apartamento' ? 'selected' : '' ?>>Apartamento</option><option value="condominio" <?= $tipo === 'condominio' ? 'selected' : '' ?>>Condomínio</option><option value="terreno" <?= $tipo === 'terreno' ? 'selected' : '' ?>>Terreno</option><option value="na_planta" <?= $tipo === 'na_planta' ? 'selected' : '' ?>>Na Planta</option></select></div>
                <div class="col-md-3"><label class="form-label small">Finalidade</label><select name="finalidade" class="form-select form-select-sm"><option value="">Todas</option><option value="venda" <?= $finalidade === 'venda' ? 'selected' : '' ?>>Venda</option><option value="aluguel" <?= $finalidade === 'aluguel' ? 'selected' : '' ?>>Aluguel</option></select></div>
                <div class="col-md-3"><label class="form-label small">Status</label><select name="status" class="form-select form-select-sm"><option value="">Todos</option><option value="disponivel" <?= $status === 'disponivel' ? 'selected' : '' ?>>Disponível</option><option value="indisponivel" <?= $status === 'indisponivel' ? 'selected' : '' ?>>Indisponível</option></select></div>
                <div class="col-md-3"><label class="form-label small">Busca</label><input type="text" name="busca" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ($busca ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Título, bairro, código..."></div>
            </form>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card text-white bg-dark"><div class="card-body"><h5>Total</h5><p class="display-6"><?= $totalImoveis ?></p></div></div></div>
        <div class="col-md-3"><div class="card text-white bg-success"><div class="card-body"><h5>Disponíveis</h5><p class="display-6"><?= count(array_filter($imoveis, fn($i) => $i['status'] === 'disponivel')) ?></p></div></div></div>
        <div class="col-md-3"><div class="card text-white bg-danger"><div class="card-body"><h5>Indisponíveis</h5><p class="display-6"><?= count(array_filter($imoveis, fn($i) => $i['status'] === 'indisponivel')) ?></p></div></div></div>
    </div>
    <div class="card">
        <div class="card-body">
            <?php if (empty($imoveis)): ?>
                <div class="text-center py-4"><p class="text-muted">Nenhum imóvel encontrado.</p></div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light"><tr><th>Código</th><th>Título</th><th>Tipo</th><th>Finalidade</th><th>Valor</th><th>Localização</th><th>Status</th><th>Ações</th></tr></thead>
                        <tbody>
                            <?php foreach ($imoveis as $imovel): ?>
                                <tr>
                                    <td><?= $imovel['codigo'] ?></td>
                                    <td><?= $imovel['titulo'] ?></td>
                                    <td><?= ucfirst(str_replace('_', ' ', $imovel['tipo'])) ?></td>
                                    <td><?= ucfirst($imovel['finalidade']) ?></td>
                                    <td><?= $imovel['finalidade'] === 'venda' ? 'R$ ' : '' ?><?= number_format((float) $imovel['valor'], 2, ',', '.') ?><?= $imovel['finalidade'] === 'aluguel' ? '/mês' : '' ?></td>
                                    <td><?= $imovel['bairro']?>, <?= $imovel['cidade'] ?> - <?= $imovel['estado'] ?></td>
                                    <td><span class="status-badge status-<?= $imovel['status'] ?>"><?= ucfirst($imovel['status']) ?></span></td>
                                    <td>
                                        <a href="editar-imovel.php?id=<?= (int) $imovel['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                        <a href="actions/excluir-imovel.php?id=<?= (int) $imovel['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Desativar este imóvel?')">Desativar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($totalPaginas > 1): ?>
                    <nav class="mt-4"><ul class="pagination justify-content-center">
                        <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= urlPagina(max(1, $pagina - 1)) ?>">Anterior</a></li>
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?= $pagina === $i ? 'active' : '' ?>"><a class="page-link" href="<?= urlPagina($i) ?>"><?= $i ?></a></li>
                        <?php endfor; ?>
                        <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>"><a class="page-link" href="<?= urlPagina(min($totalPaginas, $pagina + 1)) ?>">Próxima</a></li>
                    </ul></nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>