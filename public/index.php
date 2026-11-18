<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DW2 Imobiliária | Encontre seu novo lar</title>

    <meta name="description" content="Encontre o imóvel ideal com a DW2 Imobiliária. Conheça nossas oportunidades de compra e locação.">

    <!-- Favicons -->
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
    <link rel="icon" href="assets/favicon-16x16.png" type="image/png" sizes="16x16">
    <link rel="icon" href="assets/favicon-32x32.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="assets/apple-touch-icon.png">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold: #c99a3b;
            --gold-light: #e5bd68;
            --dark: #151515;
            --dark-secondary: #202020;
            --white: #ffffff;
            --gray: #777777;
            --background: #f7f7f7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--background);
            color: var(--dark);
        }

        a {
            text-decoration: none;
        }

        /* HERO */
        .hero {
            min-height: 540px;
            display: flex;
            align-items: center;
            color: white;
            padding: 90px 0;

            background:
                linear-gradient(
                    90deg,
                    rgba(15, 15, 15, 0.90),
                    rgba(15, 15, 15, 0.45)
                ),
                url("assets/banner.jpg") center center / cover no-repeat;
        }

        .hero-content {
            max-width: 700px;
        }

        .hero-label {
            color: var(--gold-light);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 13px;
        }

        .hero h1 {
            font-size: clamp(38px, 5vw, 64px);
            font-weight: 800;
            line-height: 1.12;
            margin: 22px 0;
        }

        .hero h1 span {
            color: var(--gold-light);
        }

        .hero p {
            color: #cccccc;
            font-size: 17px;
            line-height: 1.8;
            max-width: 550px;
        }

        /* SEARCH */

        .search-section {
            margin-top: -45px;
            position: relative;
            z-index: 2;
        }

        .search-box {
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 10px 35px rgba(0,0,0,.10);
        }

        .form-label {
            font-size: 13px;
            font-weight: 700;
        }

        .form-select,
        .form-control {
            min-height: 46px;
            border-color: #e5e5e5;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 .2rem rgba(201,154,59,.15);
        }

        /* SECTION */

        .section-padding {
            padding: 85px 0;
        }

        .section-subtitle {
            color: var(--gold);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 2px;
        }

        .section-title {
            font-size: 34px;
            font-weight: 800;
            margin-top: 10px;
        }

        .section-description {
            color: var(--gray);
        }

        /* PROPERTY CARDS */

        .property-card {
            background: white;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0,0,0,.05);
            transition: .3s;
            height: 100%;
        }

        .property-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 12px 35px rgba(0,0,0,.12);
        }

        .property-image {
            height: 220px;
            background: linear-gradient(135deg, #363636, #a68a55);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .property-body {
            padding: 23px;
        }

        .property-type {
            color: var(--gold);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .property-title {
            font-size: 19px;
            font-weight: 700;
            margin: 10px 0;
        }

        .property-description {
            color: var(--gray);
            font-size: 14px;
        }

        .property-footer {
            border-top: 1px solid #eeeeee;
            padding-top: 16px;
            margin-top: 18px;
        }

        .property-link {
            color: var(--dark);
            font-weight: 700;
            font-size: 14px;
        }

        .property-link:hover {
            color: var(--gold);
        }

        /* CTA */

        .cta {
            background: var(--dark);
            color: white;
            padding: 65px 0;
        }

        .cta h2 {
            font-size: 32px;
            font-weight: 800;
        }

        .cta p {
            color: #bbbbbb;
        }

        .capa-banner-casa{
          background-image: url("assets/banner.jpg");

        }

        @media (max-width: 768px) {
            .hero {
                min-height: 470px;
                padding: 65px 0;
            }

            .hero h1 {
                font-size: 40px;
            }

            .section-padding {
                padding: 60px 0;
            }

            .section-title {
                font-size: 28px;
            }

            .search-section {
                margin-top: -25px;
            }

            .search-box {
                padding: 20px;
            }

            .cta h2 {
                font-size: 26px;
            }
        }
    </style>
</head>

<body>

    <?php require_once __DIR__ . '/../app/componentes/header.php'; ?>

    <!-- HERO -->
    <header class="hero">
        <div class="container">
            <div class="hero-content">

                <span class="hero-label">
                    DW2 Imobiliária
                </span>

                <h1>
                    O imóvel ideal para um novo <span>capítulo</span> da sua vida.
                </h1>

                <p>
                    Encontre oportunidades para comprar ou alugar.
                    A DW2 ajuda você a encontrar o lugar certo para seus planos.
                </p>

                <a href="#imoveis" class="btn btn-gold mt-3">
                    Explorar imóveis
                </a>

            </div>
        </div>
    </header>

    <!-- SEARCH -->
    <section class="search-section">
        <div class="container">

            <div class="search-box">

                <form action="imoveis.php" method="GET">

                    <div class="row g-3 align-items-end">

                        <div class="col-md-4">
                            <label for="tipo" class="form-label">
                                Tipo de imóvel
                            </label>

                            <select name="tipo" id="tipo" class="form-select">
                                <option value="">Todos os imóveis</option>
                                <option value="casa">Casa</option>
                                <option value="apartamento">Apartamento</option>
                                <option value="terreno">Terreno</option>
                                <option value="comercial">Comercial</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="negocio" class="form-label">
                                Finalidade
                            </label>

                            <select name="negocio" id="negocio" class="form-select">
                                <option value="">Comprar ou alugar</option>
                                <option value="venda">Comprar</option>
                                <option value="locacao">Alugar</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <button type="submit" class="btn btn-gold w-100">
                                Buscar imóveis
                            </button>
                        </div>

                    </div>

                </form>

            </div>
        </div>
    </section>

    <!-- IMÓVEIS -->
    <section class="section-padding" id="imoveis">
        <div class="container">

            <div class="text-center mb-5">

                <span class="section-subtitle">
                    Oportunidades
                </span>

                <h2 class="section-title">
                    Encontre seu próximo imóvel
                </h2>

                <p class="section-description">
                    Conheça algumas das oportunidades disponíveis.
                </p>

            </div>

            <div class="row g-4">

                <!-- CARD 1 -->
                <div class="col-md-6 col-lg-4">

                    <article class="property-card">

                        <div class="property-image">
                            <img src="assets/casa.jpg" alt="" style="max-height: 100%; width: 100%;">
                        </div>

                        <div class="property-body">

                            <span class="property-type">
                                Residencial
                            </span>

                            <h3 class="property-title">
                                Casa
                            </h3>

                            <p class="property-description">
                                Consulte as opções de casas disponíveis em nosso catálogo.
                            </p>

                            <div class="property-footer d-flex justify-content-between align-items-center">
                                <span class="text-muted small">
                                    Confira os detalhes
                                </span>

                                <a href="imoveis.php?tipo=casa" class="property-link">
                                    Ver imóveis →
                                </a>
                            </div>

                        </div>
                    </article>

                </div>

                <!-- CARD 2 -->
                <div class="col-md-6 col-lg-4">

                    <article class="property-card">

                        <div class="property-image">
                            <img src="assets/predio.jpg" alt="" style="max-height: 100%; width: 100%;">
                        </div>

                        <div class="property-body">

                            <span class="property-type">
                                Residencial
                            </span>

                            <h3 class="property-title">
                                Apartamentos
                            </h3>

                            <p class="property-description">
                                Explore as opções de apartamentos disponíveis.
                            </p>

                            <div class="property-footer d-flex justify-content-between align-items-center">
                                <span class="text-muted small">
                                    Confira os detalhes
                                </span>

                                <a href="imoveis.php?tipo=apartamento" class="property-link">
                                    Ver imóveis →
                                </a>
                            </div>

                        </div>
                    </article>

                </div>

                <!-- CARD 3 -->
                <div class="col-md-6 col-lg-4">

                    <article class="property-card">

                        <div class="property-image">
                            <img src="assets/terreno.jpg" alt="" style="max-height: 100%; width: 100%;">
                        </div>

                        <div class="property-body">

                            <span class="property-type">
                                Investimento
                            </span>

                            <h3 class="property-title">
                                Terrenos
                            </h3>

                            <p class="property-description">
                                Confira terrenos disponíveis para seus projetos.
                            </p>

                            <div class="property-footer d-flex justify-content-between align-items-center">
                                <span class="text-muted small">
                                    Confira os detalhes
                                </span>

                                <a href="imoveis.php?tipo=terreno" class="property-link">
                                    Ver imóveis →
                                </a>
                            </div>

                        </div>
                    </article>

                </div>

            </div>

            <div class="text-center mt-5">
                <a href="imoveis.php" class="btn btn-gold">
                    Ver todos os imóveis
                </a>
            </div>

        </div>
    </section>

    <!-- SOBRE -->
    <section class="section-padding bg-white" id="sobre">
        <div class="container">

            <div class="row align-items-center g-5">

                <div class="col-lg-6">

                    <span class="section-subtitle">
                        Sobre a DW2
                    </span>

                    <h2 class="section-title">
                        Mais do que encontrar imóveis, encontrar possibilidades.
                    </h2>

                    <p class="section-description mt-3">
                        A DW2 Imobiliária busca conectar pessoas às oportunidades
                        imobiliárias, oferecendo uma experiência simples e
                        transparente na busca pelo imóvel ideal.
                    </p>

                    <a href="#contato" class="btn btn-gold mt-3">
                        Fale conosco
                    </a>

                </div>

                <div class="col-lg-6">

                    <div class="p-5 rounded-4 text-center"
                         style="background: var(--dark); color: white;">

                        <img src="assets/logo.png"
                             alt="DW2 Imobiliária"
                             class="img-fluid mb-3"
                             style="max-width: 230px;">

                        <p class="mb-0 text-white-50">
                            Seu próximo endereço começa aqui.
                        </p>

                    </div>

                </div>

            </div>

        </div>
    </section>
    
    <?php require_once __DIR__ . '/../app/componentes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
