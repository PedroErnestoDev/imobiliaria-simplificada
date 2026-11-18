<?php

require_once __DIR__ . '/../app/programacao/imoveis/imoveis.php';

function escapar($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

$tiposPermitidos = ['casa', 'apartamento', 'condominio', 'terreno', 'na_planta'];
$finalidadesPermitidas = ['venda', 'aluguel'];

$tipo = trim($_GET['tipo'] ?? '');
$finalidade = trim($_GET['finalidade'] ?? ($_GET['negocio'] ?? ''));
$busca = trim($_GET['busca'] ?? '');
$valorMin = trim($_GET['valor_min'] ?? '');
$valorMax = trim($_GET['valor_max'] ?? '');

$pagina = filter_var($_GET['pagina'] ?? 1, FILTER_VALIDATE_INT);
$pagina = max(1, $pagina ?: 1);

if ($finalidade === 'locacao') {
    $finalidade = 'aluguel';
}

if (!in_array($tipo, $tiposPermitidos, true)) {
    $tipo = '';
}

if (!in_array($finalidade, $finalidadesPermitidas, true)) {
    $finalidade = '';
}

if ($valorMin !== '' && (!is_numeric($valorMin) || (float) $valorMin < 0)) {
    $valorMin = '';
}

if ($valorMax !== '' && (!is_numeric($valorMax) || (float) $valorMax < 0)) {
    $valorMax = '';
}

if ($valorMin !== '' && $valorMax !== '' && (float) $valorMin > (float) $valorMax) {
    $valorMin = '';
    $valorMax = '';
}

$filtros = [
    'tipo' => $tipo,
    'finalidade' => $finalidade,
    'busca' => $busca,
    'valor_min' => $valorMin,
    'valor_max' => $valorMax
];

$limite = 9;

$totalImoveis = contarImoveis($pdo, $filtros);
$totalPaginas = max(1, (int) ceil($totalImoveis / $limite));

$pagina = min($pagina, $totalPaginas);

$imoveis = listarImoveis($pdo, $filtros, $pagina, $limite);

$inicio = $totalImoveis > 0 ? (($pagina - 1) * $limite) + 1 : 0;
$fim = min($pagina * $limite, $totalImoveis);

function urlPagina(int $pagina): string
{
    $parametros = $_GET;
    $parametros['pagina'] = $pagina;

    return '?' . http_build_query($parametros);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Imóveis Disponíveis | DW2 Imobiliária</title>

    <meta name="description" content="Encontre casas, apartamentos e terrenos disponíveis para compra e locação na DW2 Imobiliária.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..800;1,9..40,400..800&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold: #c99a3b;
            --gold-hover: #b4852e;
            --dark: #151515;
            --gray: #6c757d;
            --background: #f8f9fa;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--background);
            color: var(--dark);
            -webkit-font-smoothing: antialiased;
        }

        a {
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .btn-gold {
            background: var(--gold);
            border: 1px solid var(--gold);
            color: #fff;
            font-weight: 600;
            padding: 12px 22px;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        .btn-gold:hover {
            background: var(--gold-hover);
            border-color: var(--gold-hover);
            color: #fff;
            transform: translateY(-1px);
        }

        .page-banner {
            background:
                linear-gradient(135deg, rgba(15, 15, 15, 0.95), rgba(15, 15, 15, 0.75)),
                url("assets/banner.jpg") center / cover no-repeat;
            padding: 80px 0 90px;
            color: #fff;
        }

        .page-banner h1 {
            font-size: clamp(32px, 4vw, 46px);
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .page-banner p {
            color: #d1d5db;
            font-size: 1.1rem;
        }

        .section-subtitle {
            color: var(--gold);
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2.5px;
        }

        .filter-box {
            background: #fff;
            padding: 28px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            margin-top: -42px;
            position: relative;
            z-index: 10;
        }

        .form-label {
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            min-height: 46px;
            border-color: #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.2rem rgba(201, 154, 59, 0.15);
        }

        .results-section {
            padding: 60px 0 90px;
        }

        .property-card {
            background: #fff;
            border: none;
            border-radius: 14px;
            overflow: hidden;
            height: 100%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .property-image {
            height: 220px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #2b2b2b, #8c7344);
        }

        .property-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .property-card:hover .property-image img {
            transform: scale(1.04);
        }

        .image-placeholder {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .property-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            background: var(--gold);
            color: #fff;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        .property-body {
            padding: 22px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .property-code {
            font-size: 11px;
            color: var(--gray);
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .property-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--dark);
            margin: 6px 0 8px;
            line-height: 1.35;
        }

        .property-location {
            color: var(--gray);
            font-size: 13px;
            margin-bottom: 0;
        }

        .property-price {
            font-size: 22px;
            font-weight: 800;
            color: var(--gold);
            margin-top: 14px;
        }

        .property-features {
            display: flex;
            justify-content: space-between;
            gap: 4px;
            padding: 14px 0;
            margin-top: 14px;
            border-top: 1px solid #f3f4f6;
            border-bottom: 1px solid #f3f4f6;
        }

        .property-feature {
            font-size: 11px;
            color: var(--gray);
            text-align: center;
            flex: 1;
        }

        .property-feature strong {
            display: block;
            color: var(--dark);
            font-size: 14px;
            font-weight: 700;
        }

        .property-footer {
            margin-top: auto;
            padding-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .property-link {
            color: var(--dark);
            font-weight: 700;
            font-size: 13px;
        }

        .property-link:hover {
            color: var(--gold);
        }

        .empty-state {
            background: #fff;
            padding: 60px 20px;
            text-align: center;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }

        .empty-state h3 {
            font-weight: 800;
        }

        .pagination {
            gap: 4px;
        }

        .pagination .page-link {
            color: var(--dark);
            border: 1px solid #e5e7eb;
            border-radius: 8px !important;
            padding: 10px 16px;
            font-weight: 600;
            font-size: 14px;
        }

        .pagination .page-link:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
        }

        .pagination .active .page-link {
            background: var(--gold);
            border-color: var(--gold);
            color: #fff;
        }

        @media (max-width: 768px) {
            .page-banner {
                padding: 60px 0;
            }

            .filter-box {
                padding: 20px;
                margin-top: -25px;
            }

            .results-section {
                padding-top: 40px;
            }
        }
    </style>
</head>

<body>

    <?php require_once __DIR__ . '/../app/componentes/header.php'; ?>

    <section class="page-banner">
        <div class="container">
            <span class="section-subtitle">DW2 Imobiliária</span>

            <h1 class="mt-2">Encontre seu próximo imóvel</h1>

            <p class="mt-2 mb-0">
                Explore nossas oportunidades selecionadas para compra e locação.
            </p>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="filter-box">
                <form action="imoveis.php" method="GET">
                    <div class="row g-3 align-items-end">

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label" for="busca">Localização ou código</label>
                            <input
                                type="text"
                                name="busca"
                                id="busca"
                                class="form-control"
                                placeholder="Ex: Centro, Araras ou DW001"
                                value="<?= escapar($busca) ?>"
                            >
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label" for="tipo">Tipo de imóvel</label>
                            <select name="tipo" id="tipo" class="form-select">
                                <option value="">Todos os tipos</option>
                                <option value="casa" <?= $tipo === 'casa' ? 'selected' : '' ?>>Casa</option>
                                <option value="apartamento" <?= $tipo === 'apartamento' ? 'selected' : '' ?>>Apartamento</option>
                                <option value="condominio" <?= $tipo === 'condominio' ? 'selected' : '' ?>>Condomínio</option>
                                <option value="terreno" <?= $tipo === 'terreno' ? 'selected' : '' ?>>Terreno</option>
                                <option value="na_planta" <?= $tipo === 'na_planta' ? 'selected' : '' ?>>Na planta</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label" for="finalidade">Finalidade</label>
                            <select name="finalidade" id="finalidade" class="form-select">
                                <option value="">Comprar ou alugar</option>
                                <option value="venda" <?= $finalidade === 'venda' ? 'selected' : '' ?>>Comprar</option>
                                <option value="aluguel" <?= $finalidade === 'aluguel' ? 'selected' : '' ?>>Alugar</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label" for="valor_min">Valor mínimo</label>
                            <input
                                type="number"
                                name="valor_min"
                                id="valor_min"
                                class="form-control"
                                min="0"
                                step="0.01"
                                placeholder="R$ 0"
                                value="<?= escapar($valorMin) ?>"
                            >
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label" for="valor_max">Valor máximo</label>
                            <input
                                type="number"
                                name="valor_max"
                                id="valor_max"
                                class="form-control"
                                min="0"
                                step="0.01"
                                placeholder="Sem limite"
                                value="<?= escapar($valorMax) ?>"
                            >
                        </div>

                        <div class="col-lg-1 col-md-12">
                            <button type="submit" class="btn btn-gold w-100">
                                <span>Buscar</span>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </section>

    <main class="results-section">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <span class="section-subtitle">Catálogo completo</span>
                    <h2 class="fw-bold mt-1 mb-1" style="font-size: 26px;">Imóveis disponíveis</h2>
                    <p class="text-muted mb-0 small">
                        <?= $totalImoveis ?> imóvel(is) encontrado(s) para os filtros atuais.
                    </p>
                </div>

                <?php if ($totalImoveis > 0): ?>
                    <span class="text-muted small">
                        Mostrando <?= $inicio ?>–<?= $fim ?> de <?= $totalImoveis ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php if (empty($imoveis)): ?>

                <div class="empty-state">
                    <h3>Nenhum imóvel encontrado</h3>

                    <p class="text-muted mt-2 mb-4">
                        Tente ajustar os filtros ou redefinir a busca para ver mais opções.
                    </p>

                    <a href="imoveis.php" class="btn btn-gold">
                        Limpar filtros
                    </a>
                </div>

            <?php else: ?>

                <div class="row g-4">

                    <?php foreach ($imoveis as $imovel): ?>

                        <div class="col-md-6 col-lg-4">
                            <article class="property-card">

                                <div class="property-image">
                                    <?php if (!empty($imovel['imagem'])): ?>
                                        <img
                                            src="<?= escapar($imovel['imagem']) ?>"
                                            alt="<?= escapar($imovel['titulo']) ?>"
                                            loading="lazy"
                                        >
                                    <?php else: ?>
                                        <div class="image-placeholder">
                                            DW2 Imobiliária
                                        </div>
                                    <?php endif; ?>

                                    <span class="property-badge">
                                        <?= $imovel['finalidade'] === 'venda' ? 'À venda' : 'Para alugar' ?>
                                    </span>
                                </div>

                                <div class="property-body">

                                    <span class="property-code">
                                        CÓD: <?= escapar($imovel['codigo']) ?>
                                    </span>

                                    <h3 class="property-title">
                                        <?= escapar($imovel['titulo']) ?>
                                    </h3>

                                    <p class="property-location">
                                        <?= escapar($imovel['bairro']) ?>,
                                        <?= escapar($imovel['cidade']) ?> -
                                        <?= escapar($imovel['estado']) ?>
                                    </p>

                                    <div class="property-price">
                                        R$ <?= number_format((float) $imovel['valor'], 2, ',', '.') ?>
                                    </div>

                                    <div class="property-features">

                                        <div class="property-feature">
                                            <strong><?= (int) $imovel['quartos'] ?></strong>
                                            Quartos
                                        </div>

                                        <div class="property-feature">
                                            <strong><?= (int) $imovel['banheiros'] ?></strong>
                                            Banheiros
                                        </div>

                                        <div class="property-feature">
                                            <strong><?= (int) $imovel['vagas'] ?></strong>
                                            Vagas
                                        </div>

                                        <div class="property-feature">
                                            <strong>
                                                <?= $imovel['area'] !== null
                                                    ? number_format((float) $imovel['area'], 0, ',', '.') . ' m²'
                                                    : '—' ?>
                                            </strong>
                                            Área
                                        </div>

                                    </div>

                                    <div class="property-footer">
                                        <span class="text-muted small" style="font-weight: 500;">
                                            <?= ucfirst(str_replace('_', ' ', $imovel['tipo'])) ?>
                                        </span>

                                        <a href="imoveis_detalhes.php?id=<?= (int) $imovel['id'] ?>" class="property-link">
                                            Ver detalhes →
                                        </a>
                                    </div>

                                </div>
                            </article>
                        </div>

                    <?php endforeach; ?>

                </div>

                <?php if ($totalPaginas > 1): ?>

                    <nav class="mt-5" aria-label="Paginação dos imóveis">
                        <ul class="pagination justify-content-center flex-wrap">

                            <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= escapar(urlPagina(max(1, $pagina - 1))) ?>">
                                    Anterior
                                </a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <li class="page-item <?= $pagina === $i ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= escapar(urlPagina($i)) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= escapar(urlPagina(min($totalPaginas, $pagina + 1))) ?>">
                                    Próxima
                                </a>
                            </li>

                        </ul>
                    </nav>

                <?php endif; ?>

            <?php endif; ?>

        </div>
    </main>

    <?php require_once __DIR__ . '/../app/componentes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>