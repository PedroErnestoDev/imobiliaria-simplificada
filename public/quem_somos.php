<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quem Somos | DW2 Imobiliária</title>

    <meta name="description" content="Conheça a história, os valores e a equipe da DW2 Imobiliária. Especialistas em compra, venda e locação de imóveis.">

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
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
            display: inline-block;
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
            padding: 80px 0;
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

        .about-section {
            padding: 70px 0;
        }

        .about-image {
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background: linear-gradient(135deg, #2b2b2b, #8c7344);
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .about-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pillar-card {
            background: #fff;
            border-radius: 14px;
            padding: 30px;
            height: 100%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border-top: 3px solid var(--gold);
        }

        .pillar-card h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .stats-bar {
            background: var(--dark);
            color: #fff;
            padding: 50px 0;
            border-radius: 14px;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 800;
            color: var(--gold);
            line-height: 1.2;
        }

        .stat-label {
            font-size: 13px;
            color: #d1d5db;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .page-banner {
                padding: 60px 0;
            }

            .about-image {
                height: 280px;
            }
        }
    </style>
</head>

<body>

    <?php require_once __DIR__ . '/../app/componentes/header.php'; ?>

    <section class="page-banner">
        <div class="container">
            <span class="section-subtitle">Nossa História</span>
            <h1 class="mt-2">Conectando pessoas aos seus melhores espaços</h1>
            <p class="mt-2 mb-0">
                Transparência, segurança jurídica e atendimento personalizado no mercado imobiliário.
            </p>
        </div>
    </section>

    <main class="about-section">
        <div class="container">

            <!-- História / Apresentação -->
            <div class="row align-items-center g-5 mb-5">
                <div class="col-lg-6">
                    <div class="about-image">
                        <!-- Substitua pelo caminho de uma foto da sua equipe ou escritório -->
                        <img src="assets/escritorio.jpg" alt="Escritório DW2 Imobiliária">

                    </div>
                </div>

                <div class="col-lg-6">
                    <span class="section-subtitle">Sobre a DW2</span>
                    <h2 class="fw-bold mt-2 mb-3" style="font-size: 30px;">
                        Mais do que imóveis, realizamos projetos de vida.
                    </h2>
                    <p class="text-muted" style="line-height: 1.75;">
                        A <strong>DW2 Imobiliária</strong> nasceu com o propósito de simplificar a jornada de quem busca comprar, vender ou alugar um imóvel. Sabemos que cada negociação envolve sonhos, investimentos importantes e decisões para o futuro.
                    </p>
                    <p class="text-muted mb-4" style="line-height: 1.75;">
                        Com foco na região de Araras e cidades vizinhas, nossa equipe combina conhecimento profundo do mercado local com atendimento próximo e ágil, garantindo tranquilidade desde a primeira visita até a assinatura do contrato.
                    </p>
                    <a href="imoveis.php" class="btn btn-gold">
                        Conhecer nosso catálogo
                    </a>
                </div>
            </div>

            <!-- Números / Destaques -->
            <div class="stats-bar px-4 mb-5">
                <div class="row text-center g-4">
                    <div class="col-6 col-md-3">
                        <div class="stat-number">+10</div>
                        <div class="stat-label">Anos de experiência</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-number">+450</div>
                        <div class="stat-label">Imóveis negociados</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Assessoria jurídica</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-number">98%</div>
                        <div class="stat-label">Clientes satisfeitos</div>
                    </div>
                </div>
            </div>

            <!-- Missão, Visão e Valores -->
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="pillar-card">
                        <span class="section-subtitle">Propósito</span>
                        <h3 class="mt-2">Nossa Missão</h3>
                        <p class="text-muted small mb-0" style="line-height: 1.65;">
                            Oferecer soluções imobiliárias completas com ética, agilidade e excelência no atendimento, superando as expectativas de proprietários, compradores e inquilinos.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="pillar-card">
                        <span class="section-subtitle">Futuro</span>
                        <h3 class="mt-2">Nossa Visão</h3>
                        <p class="text-muted small mb-0" style="line-height: 1.65;">
                            Ser referência regional em confiança e inovação no setor imobiliário, mantendo o atendimento humano e personalizado como nosso principal diferencial.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="pillar-card">
                        <span class="section-subtitle">Princípios</span>
                        <h3 class="mt-2">Nossos Valores</h3>
                        <p class="text-muted small mb-0" style="line-height: 1.65;">
                            Transparência em todas as etapas, compromisso com a verdade, respeito às pessoas, segurança documental e valorização do patrimônio de cada cliente.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php require_once __DIR__ . '/../app/componentes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>