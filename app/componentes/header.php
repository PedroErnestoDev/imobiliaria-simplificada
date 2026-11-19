<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
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

        .navbar {
            background: var(--dark);
            padding: 12px 0;
        }

        .navbar-brand img {
            width: 150px;
            height: auto;
            object-fit: contain;
        }

        .navbar .nav-link {
            color: #eeeeee;
            font-weight: 500;
            margin: 0 10px;
            transition: .3s;
        }

        .navbar .nav-link:hover,
        .navbar .nav-link.active {
            color: var(--gold-light);
        }

        .btn-gold {
            background: var(--gold);
            color: #151515;
            border: 1px solid var(--gold);
            border-radius: 6px;
            padding: 11px 24px;
            font-weight: 700;
            transition: .3s;
        }

        .btn-gold:hover {
            background: var(--gold-light);
            border-color: var(--gold-light);
            color: #151515;
            transform: translateY(-2px);
        }

        .navbar-toggler {
            border-color: var(--gold);
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 .2rem rgba(201,154,59,.25);
        }

        .navbar-toggler-icon {
            filter: invert(1);
        }
    </style>
</head>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">

        <a class="navbar-brand" href="index.php">
            <img src="assets/logo.png" alt="DW2 Imobiliária">
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#navbarDrawer"
                aria-controls="navbarDrawer"
                aria-label="Abrir menu">

            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1"
             id="navbarDrawer" aria-labelledby="navbarDrawerLabel">

            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="navbarDrawerLabel">
                    Menu DW2
                </h5>

                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="offcanvas"
                        aria-label="Fechar"></button>
            </div>

            <div class="offcanvas-body">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">

                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            Início
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="imoveis.php">
                            Imóveis
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="quem_somos.php">
                            Quem Somos
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="contato.php">
                            Contato
                        </a>
                    </li>

                    <?php
                    $logado = isset($_SESSION['usuario_id']);
                    ?>

                    <?php if ($logado): ?>
                        <li class="nav-item ms-lg-3 d-flex align-items-center">
                            <span class="nav-link text-white-50 me-2">
                                Olá, <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?>
                            </span>
                            <a href="/actions/logout.php" class="btn btn-gold btn-sm">
                                Sair
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-3">
                            <a href="login.php" class="btn btn-gold">
                                Entrar
                            </a>
                        </li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </div>
</nav>
