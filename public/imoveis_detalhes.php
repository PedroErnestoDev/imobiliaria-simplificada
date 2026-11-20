<?php

// Session must start BEFORE ANY HTML output
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../app/programacao/imoveis/imoveis.php'; // Ajuste o caminho se necessário para sua conexão/funções

function escapar($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

// Pega o ID da URL de forma segura
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: imoveis.php'); // Se não tiver ID válido, manda de volta pra listagem
    exit;
}

// Busca o registro específico no banco
$imovel = buscarImovelPorId($pdo, $id);

// Buscar todas as imagens do imóvel para a galeria
$stmtImgs = $pdo->prepare("
    SELECT id, caminho_imagem, imagem_principal 
    FROM imagens_imoveis 
    WHERE imovel_id = :id 
    ORDER BY imagem_principal DESC, id ASC
");
$stmtImgs->bindValue(':id', $id, PDO::PARAM_INT);
$stmtImgs->execute();
$imagens = $stmtImgs->fetchAll(PDO::FETCH_ASSOC);

// Proteção: exige autenticação
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= isset($imovelNaoEncontrado) ? 'Imóvel não encontrado' : escapar($imovel['titulo']) ?> | DW2 Imobiliária</title>

    <meta name="description" content="<?= isset($imovelNaoEncontrado) ? 'Imóvel indisponível.' : escapar(substr(strip_tags($imovel['descricao'] ?? ''), 0, 150)) ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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

        .section-subtitle {
            color: var(--gold);
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2.5px;
        }

        .property-header-section {
            padding: 40px 0 20px;
        }

        .property-gallery {
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background: linear-gradient(135deg, #2b2b2b, #8c7344);
            height: 420px;
            position: relative;
        }

        .property-gallery img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-placeholder-large {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .badge-destaque {
            position: absolute;
            top: 20px;
            left: 20px;
            background: var(--gold);
            color: #fff;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .details-card {
            background: #fff;
            border-radius: 14px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            margin-bottom: 30px;
        }

        .property-main-price {
            font-size: 32px;
            font-weight: 800;
            color: var(--gold);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 15px;
            margin: 25px 0;
            padding: 20px 0;
            border-top: 1px solid #f3f4f6;
            border-bottom: 1px solid #f3f4f6;
        }

        .feature-item {
            text-align: center;
            background: #fdfbf7;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #f4ebd9;
        }

        .feature-item span {
            font-size: 11px;
            color: var(--gray);
            display: block;
            text-transform: uppercase;
            font-weight: 600;
        }

        .feature-item strong {
            font-size: 16px;
            color: var(--dark);
            font-weight: 700;
        }

        .contact-box {
            background: #fff;
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 20px;
        }

        .form-control {
            min-height: 46px;
            border-color: #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.2rem rgba(201, 154, 59, 0.15);
        }

        @media (max-width: 768px) {
            .property-gallery {
                height: 280px;
            }
        }
    </style>
</head>

<body>

    <main class="py-4 mb-5">
        <div class="container">

            <?php if (isset($imovelNaoEncontrado)): ?>
                <div class="details-card text-center py-5">
                    <span class="section-subtitle">Ops!</span>
                    <h2 class="fw-bold mt-2 mb-3">Imóvel não encontrado</h2>
                    <p class="text-muted mb-4">O imóvel que você está procurando pode ter sido removido ou o link está incorreto.</p>
                    <a href="imoveis.php" class="btn btn-gold">Ver todos os imóveis</a>
                </div>
            <?php else: ?>

                <!-- Navegação / Voltar -->
                <div class="mb-3">
                    <a href="imoveis.php" class="text-muted small fw-bold">
                        ← Voltar para a listagem
                    </a>
                </div>

                <!-- Título e Localização topo -->
                <div class="property-header-section mb-3">
                    <span class="section-subtitle">CÓD: <?= escapar($imovel['codigo']) ?></span>
                    <h1 class="fw-bold mt-1 mb-1" style="font-size: clamp(24px, 3vw, 34px);">
                        <?= escapar($imovel['titulo']) ?>
                    </h1>
                    <p class="text-muted mb-0">
                        <?= escapar($imovel['bairro']) ?>, <?= escapar($imovel['cidade']) ?> - <?= escapar($imovel['estado']) ?>
                    </p>
                </div>

                <div class="row g-4">
                    <!-- Coluna Esquerda: Galeria e Detalhes -->
                    <div class="col-lg-8">
                        
                        <!-- Galeria / Imagem Principal -->
                        <div class="property-gallery mb-4">
                            <?php if (!empty($imovel['imagem'])): ?>
                                <img src="<?= escapar($imovel['imagem']) ?>" alt="<?= escapar($imovel['titulo']) ?>">
                            <?php else: ?>
                                <div class="image-placeholder-large">
                                    DW2 Imobiliária
                                </div>
                            <?php endif; ?>
                            <span class="badge-destaque">
                                <?= $imovel['finalidade'] === 'venda' ? 'À venda' : 'Para alugar' ?>
                            </span>
                        </div>

                        <!-- Galeria de miniaturas -->
                        <?php if (!empty($imagens)): ?>
                            <div class="row g-2 mt-3">
                                <div class="col-12">
                                    <div class="thumbnail-gallery d-flex flex-wrap gap-2">
                                        <?php foreach ($imagens as $idx => $img): ?>
                                            <img
                                                src="<?= escapar($img['caminho_imagem']) ?>"
                                                alt="<?= escapar($imovel['titulo']) ?> - Imagem <?= $idx + 1 ?>"
                                                class="thumbnail-img"
                                                style="width: 100%; height: 100px; object-fit: cover; border-radius: 8px; cursor: pointer; border: <?= $img['imagem_principal'] ? '2px solid #c99a3b' : '2px solid transparent'; ?>;"
                                                onclick="document.querySelector('.property-gallery img').src = this.src;"
                                            >
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Card de Informações Principais -->
                        <div class="details-card">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <span class="text-muted small">Valor do imóvel</span>
                                    <div class="property-main-price">
                                        R$ <?= number_format((float) $imovel['valor'], 2, ',', '.') ?>
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold text-uppercase" style="font-size: 12px;">
                                    <?= ucfirst(str_replace('_', ' ', $imovel['tipo'])) ?>
                                </span>
                            </div>

                            <!-- Características Grid -->
                            <div class="features-grid">
                                <div class="feature-item">
                                    <span>Quartos</span>
                                    <strong><?= (int) $imovel['quartos'] ?></strong>
                                </div>
                                <div class="feature-item">
                                    <span>Banheiros</span>
                                    <strong><?= (int) $imovel['banheiros'] ?></strong>
                                </div>
                                <div class="feature-item">
                                    <span>Vagas</span>
                                    <strong><?= (int) $imovel['vagas'] ?></strong>
                                </div>
                                <div class="feature-item">
                                    <span>Área Útil</span>
                                    <strong>
                                        <?= $imovel['area'] !== null 
                                            ? number_format((float) $imovel['area'], 0, ',', '.') . ' m²' 
                                            : '—' ?>
                                    </strong>
                                </div>
                            </div>

                            <!-- Descrição -->
                            <h3 class="fw-bold mb-3" style="font-size: 20px;">Sobre o imóvel</h3>
                            <div class="text-muted" style="line-height: 1.7; white-space: pre-line;">
                                <?= escapar($imovel['descricao'] ?? 'Nenhuma descrição informada para este imóvel.') ?>
                            </div>
                        </div>

                    </div>

                    <!-- Coluna Direita: Formulário de Contato / Agendamento -->
                    <div class="col-lg-4">
                        <div class="contact-box">
                            <span class="section-subtitle">Interessado?</span>
                            <h3 class="fw-bold mt-1 mb-3" style="font-size: 20px;">Fale com um corretor</h3>
                            <p class="text-muted small mb-4">
                                Tire dúvidas ou agende uma visita para conhecer este imóvel pessoalmente.
                            </p>

                            <form action="enviar-contato.php" method="POST">
                                <input type="hidden" name="imovel_id" value="<?= (int) $imovel['id'] ?>">
                                <input type="hidden" name="imovel_codigo" value="<?= escapar($imovel['codigo']) ?>">

                                <div class="mb-3">
                                    <label class="form-label fw-bold small" for="nome">Seu nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" required placeholder="Digite seu nome completo">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small" for="telefone">Telefone / WhatsApp</label>
                                    <input type="text" class="form-control" id="telefone" name="telefone" required placeholder="(00) 00000-0000">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small" for="email">E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small" for="mensagem">Mensagem</label>
                                    <textarea class="form-control" id="mensagem" name="mensagem" rows="3" placeholder="Olá, gostaria de mais informações sobre o imóvel <?= escapar($imovel['codigo']) ?>."></textarea>
                                </div>

                                <button type="submit" class="btn btn-gold w-100 py-3">
                                    Enviar mensagem
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </main>

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- Mapa de Localização -->
<div class="mt-5">
    <h4 >Localização do Imóvel</h4>
    <div id="mapa-detalhes" style="height: 300px; width: 100%; border-radius: 8px; overflow: hidden; position: relative;"></div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Coordenadas do imóvel (serão preenchidas pelo PHP)
        var lat = <?= isset($imovel["latitude"]) ? (float)$imovel["latitude"] : -23.550520; ?>;
        var lng = <?= isset($imovel["longitude"]) ? (float)$imovel["longitude"] : -46.633309; ?>;
        
        var map = L.map("mapa-detalhes").setView([lat, lng], 16);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 18
        }).addTo(map);
        
        var marker = L.marker([lat, lng]).addTo(map);
        
        // Se tiver endereço, tentar fazer reverse geocode
        var endereco = "<?= isset($imovel["endereco"]) ? addslashes($imovel["endereco"]) : "" ?>";
        if (endereco) {
            fetch("/api/geocoding.php?q=" + encodeURIComponent(endereco) + "&limit=1")
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso && data.dados && data.dados.length > 0) {
                        var loc = data.dados[0];
                        marker.setLatLng([loc.latitude, loc.longitude]);
                        map.setView([loc.latitude, loc.longitude], 16);
                    }
                })
                .catch(() => {});
        }
    });
</script>
 <?php require_once __DIR__ . '/../app/componentes/footer.php'; ?>

</body>
</html>