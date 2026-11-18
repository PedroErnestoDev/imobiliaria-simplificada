
<?php
declare(strict_types=1);

// pages/dashboard.php

require_once __DIR__ . '/../app/programacao/imoveis/imoveis.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Escape de saída HTML.
function escapar(mixed $valor): string
{
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// Validar filtros.
$tiposPermitidos = [
    'casa',
    'apartamento',
    'condominio',
    'terreno',
    'na_planta'
];

$finalidadesPermitidas = ['venda', 'aluguel'];

$statusPermitidos = ['disponivel', 'indisponivel'];

$tipo = $_GET['tipo'] ?? '';
$finalidade = $_GET['finalidade'] ?? '';
$status = $_GET['status'] ?? '';
$busca = trim($_GET['busca'] ?? '');

if (!is_string($tipo)) {
    $tipo = '';
}

if (!is_string($finalidade)) {
    $finalidade = '';
}

if (!is_string($status)) {
    $status = '';
}

if (!is_string($busca)) {
    $busca = '';
}

if (!in_array($tipo, $tiposPermitidos, true)) {
    $tipo = '';
}

if (!in_array($finalidade, $finalidadesPermitidas, true)) {
    $finalidade = '';
}

if (!in_array($status, $statusPermitidos, true)) {
    $status = '';
}

// Paginação.
$pagina = filter_var(
    $_GET['pagina'] ?? 1,
    FILTER_VALIDATE_INT
);

$pagina = max(1, $pagina ?: 1);

$limite = 9;

$filtros = [
    'tipo' => $tipo,
    'finalidade' => $finalidade,
    'status' => $status,
    'busca' => $busca
];

// Buscar quantidade de imóveis.
$totalImoveis = contarTodosImoveis($pdo, $filtros);

$totalPaginas = max(
    1,
    (int) ceil($totalImoveis / $limite)
);

$pagina = min($pagina, $totalPaginas);

// Buscar imóveis da página atual.
$imoveis = listarTodosImoveis(
    $pdo,
    $filtros,
    $pagina,
    $limite
);

// Intervalo de registros.
$inicio = $totalImoveis > 0
    ? (($pagina - 1) * $limite) + 1
    : 0;

$fim = min($pagina * $limite, $totalImoveis);

// Gerar URL preservando filtros.
function urlPagina(int $pagina): string
{
    $parametros = $_GET;
    $parametros['pagina'] = $pagina;

    return '?' . http_build_query($parametros);
}

// Estatísticas da página atual.
// Para estatísticas globais, implementar consultas COUNT no banco.
$disponiveis = count(array_filter(
    $imoveis,
    fn($imovel) => ($imovel['status'] ?? '') === 'disponivel'
));

$indisponiveis = count(array_filter(
    $imoveis,
    fn($imovel) => ($imovel['status'] ?? '') === 'indisponivel'
));

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | DW2 Imobiliária</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>

        :root {
            --gold: #c99a3b;
            --gold-hover: #b4852e;
            --dark: #151515;
            --gray: #6c757d;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f8f9fa;
        }

        .sidebar {
            background: var(--dark);
            min-height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            z-index: 1000;
        }

        .sidebar a {
            color: #ccc;
            padding: 15px 25px;
            display: block;
            text-decoration: none;
            font-weight: 500;
        }

        .sidebar a:hover,
        .sidebar a.active {
            color: white;
            background: var(--gold);
        }

        .main-content {
            margin-left: 260px;
            padding: 30px;
        }

        .btn-gold {
            background: var(--gold);
            border: 1px solid var(--gold);
            color: var(--dark);
            font-weight: 700;
        }

        .btn-gold:hover {
            background: var(--gold-hover);
            border-color: var(--gold-hover);
            color: var(--dark);
        }

        .status-badge {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-disponivel {
            background: #d4edda;
            color: #155724;
        }

        .status-indisponivel {
            background: #f8d7da;
            color: #721c24;
        }

        .stat-card {
            border: none;
            border-radius: 10px;
        }

        .card {
            border: none;
            border-radius: 10px;
        }

        .pagination .page-link {
            color: var(--dark);
        }

        .pagination .active .page-link {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--dark);
        }

        @media (max-width: 768px) {

            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
                padding: 10px 0;
            }

            .sidebar img {
                max-height: 55px;
                width: auto !important;
            }

            .main-content {
                margin-left: 0;
                padding: 20px 12px;
            }

        }

    </style>

</head>

<body>

<!-- Sidebar -->

<nav class="sidebar">

    <div class="text-center mb-4">
        <img
            src="../public/assets/logo.png"
            alt="DW2 Imobiliária"
            style="width: 120px;"
        >
    </div>

    <a href="dashboard.php" class="active">
        Dashboard
    </a>

    <a href="cadastrar-imovel.php">
        + Cadastrar Imóvel
    </a>

    <a href="../public/imoveis.php">
        Ver Catálogo Público
    </a>

    <a href="../public/contato.php">
        Contato
    </a>

    <div class="mt-4 px-3">

        <a
            href="logout.php"
            style="color: var(--gold);"
        >
            Sair
        </a>

    </div>

</nav>

<!-- Conteúdo principal -->

<main class="main-content">

    <header class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

        <div>
            <h2 class="fw-bold">Dashboard</h2>

            <p class="text-muted mb-0">
                Gerencie os imóveis da imobiliária
            </p>
        </div>

        <a
            href="cadastrar-imovel.php"
            class="btn btn-gold px-4 py-2"
        >
            + Novo Imóvel
        </a>

    </header>

    <!-- Filtros -->

    <section class="card mb-4">

        <div class="card-body">

            <form method="GET" class="row g-3">

                <div class="col-md-3">

                    <label class="form-label small">
                        Tipo
                    </label>

                    <select name="tipo" class="form-select form-select-sm">

                        <option value="">Todos</option>

                        <?php foreach ($tiposPermitidos as $opcao): ?>

                            <option
                                value="<?= escapar($opcao) ?>"
                                <?= $tipo === $opcao ? 'selected' : '' ?>
                            >
                                <?= escapar(ucfirst(str_replace('_', ' ', $opcao))) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="col-md-3">

                    <label class="form-label small">
                        Finalidade
                    </label>

                    <select name="finalidade" class="form-select form-select-sm">

                        <option value="">Todas</option>

                        <option value="venda" <?= $finalidade === 'venda' ? 'selected' : '' ?>>
                            Venda
                        </option>

                        <option value="aluguel" <?= $finalidade === 'aluguel' ? 'selected' : '' ?>>
                            Aluguel
                        </option>

                    </select>

                </div>

                <div class="col-md-3">

                    <label class="form-label small">
                        Status
                    </label>

                    <select name="status" class="form-select form-select-sm">

                        <option value="">Todos</option>

                        <option value="disponivel" <?= $status === 'disponivel' ? 'selected' : '' ?>>
                            Disponível
                        </option>

                        <option value="indisponivel" <?= $status === 'indisponivel' ? 'selected' : '' ?>>
                            Indisponível
                        </option>

                    </select>

                </div>

                <div class="col-md-3">

                    <label class="form-label small">
                        Busca
                    </label>

                    <input
                        type="text"
                        name="busca"
                        class="form-control form-control-sm"
                        value="<?= escapar($busca) ?>"
                        placeholder="Título, bairro, código..."
                        maxlength="150"
                    >

                </div>

                <div class="col-12 d-flex gap-2">

                    <button type="submit" class="btn btn-gold">
                        Pesquisar
                    </button>

                    <a href="dashboard.php" class="btn btn-outline-secondary">
                        Limpar filtros
                    </a>

                </div>

            </form>

        </div>

    </section>

    <!-- Estatísticas -->

    <section class="row g-3 mb-4">

        <div class="col-md-4">

            <div class="card stat-card text-white bg-dark">

                <div class="card-body">

                    <h5>Total de imóveis</h5>

                    <p class="display-6 mb-0">
                        <?= (int) $totalImoveis ?>
                    </p>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card stat-card text-white bg-success">

                <div class="card-body">

                    <h5>Disponíveis nesta página</h5>

                    <p class="display-6 mb-0">
                        <?= $disponiveis ?>
                    </p>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card stat-card text-white bg-danger">

                <div class="card-body">

                    <h5>Indisponíveis nesta página</h5>

                    <p class="display-6 mb-0">
                        <?= $indisponiveis ?>
                    </p>

                </div>

            </div>

        </div>

    </section>

    <!-- Tabela -->

    <section class="card">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

                <h5 class="mb-0">
                    Imóveis cadastrados
                </h5>

                <small class="text-muted">
                    Exibindo <?= $inicio ?>–<?= $fim ?>
                    de <?= (int) $totalImoveis ?> registros
                </small>

            </div>

            <?php if (empty($imoveis)): ?>

                <div class="text-center py-5">

                    <h5>Nenhum imóvel encontrado</h5>

                    <p class="text-muted">
                        Tente alterar os filtros de pesquisa.
                    </p>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">

                            <tr>
                                <th>Código</th>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Finalidade</th>
                                <th>Valor</th>
                                <th>Localização</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($imoveis as $imovel): ?>

                                <tr>

                                    <td>
                                        <?= escapar($imovel['codigo']) ?>
                                    </td>

                                    <td>
                                        <?= escapar($imovel['titulo']) ?>
                                    </td>

                                    <td>
                                        <?= escapar(ucfirst(str_replace('_', ' ', $imovel['tipo']))) ?>
                                    </td>

                                    <td>
                                        <?= escapar(ucfirst($imovel['finalidade'])) ?>
                                    </td>

                                    <td>

                                        <?= $imovel['finalidade'] === 'venda' ? 'R$ ' : 'R$ ' ?>

                                        <?= number_format(
                                            (float) $imovel['valor'],
                                            2,
                                            ',',
                                            '.'
                                        ) ?>

                                        <?= $imovel['finalidade'] === 'aluguel' ? '/mês' : '' ?>

                                    </td>

                                    <td>
                                        <?= escapar($imovel['bairro']) ?>,
                                        <?= escapar($imovel['cidade']) ?> -
                                        <?= escapar($imovel['estado']) ?>
                                    </td>

                                    <td>

                                        <?php
                                        $statusImovel = $imovel['status'] ?? '';

                                        $classeStatus = in_array(
                                            $statusImovel,
                                            ['disponivel', 'indisponivel'],
                                            true
                                        ) ? $statusImovel : 'indisponivel';
                                        ?>

                                        <span class="status-badge status-<?= escapar($classeStatus) ?>">

                                            <?= escapar(ucfirst($statusImovel)) ?>

                                        </span>

                                    </td>

                                    <td>

                                        <div class="d-flex gap-1">

                                            <a
                                                href="editar-imovel.php?id=<?= (int) $imovel['id'] ?>"
                                                class="btn btn-sm btn-primary"
                                            >
                                                Editar
                                            </a>

                                            <!-- A desativação deverá usar POST com CSRF. -->

                                            <a
                                                href="actions/excluir-imovel.php?id=<?= (int) $imovel['id'] ?>"
                                                class="btn btn-sm btn-danger"
                                            >
                                                Desativar
                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <!-- Paginação -->

                <?php if ($totalPaginas > 1): ?>

                    <nav class="mt-4" aria-label="Paginação de imóveis">

                        <ul class="pagination justify-content-center flex-wrap">

                            <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">

                                <a
                                    class="page-link"
                                    href="<?= escapar(urlPagina(max(1, $pagina - 1))) ?>"
                                >
                                    Anterior
                                </a>

                            </li>

                            <?php

                            $inicioPagina = max(1, $pagina - 2);
                            $fimPagina = min($totalPaginas, $pagina + 2);

                            for ($i = $inicioPagina; $i <= $fimPagina; $i++):
                            ?>

                                <li class="page-item <?= $pagina === $i ? 'active' : '' ?>">

                                    <a
                                        class="page-link"
                                        href="<?= escapar(urlPagina($i)) ?>"
                                    >
                                        <?= $i ?>
                                    </a>

                                </li>

                            <?php endfor; ?>

                            <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">

                                <a
                                    class="page-link"
                                    href="<?= escapar(urlPagina(min($totalPaginas, $pagina + 1))) ?>"
                                >
                                    Próxima
                                </a>

                            </li>

                        </ul>

                    </nav>

                <?php endif; ?>

            <?php endif; ?>

        </div>

    </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
