<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>

<style>
    :root {
        --gold: #c99a3b;
        --gold-light: #e5bd68;
        --dark: #151515;
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

    .navbar .btn-gold {
        background: var(--gold);
        color: #151515;
        border: 1px solid var(--gold);
        border-radius: 6px;
        padding: 11px 24px;
        font-weight: 700;
        transition: .3s;
    }

    .navbar .btn-gold:hover {
        background: var(--gold-light);
        border-color: var(--gold-light);
        color: #151515;
        transform: translateY(-2px);
    }

    .navbar-toggler {
        border-color: var(--gold);
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 .2rem rgba(201, 154, 59, .25);
    }

    .navbar-toggler-icon {
        filter: invert(1);
    }
</style>

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
                <h5 class="offcanvas-title" id="navbarDrawerLabel">Menu DW2</h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="offcanvas"
                        aria-label="Fechar"></button>
            </div>

            <div class="offcanvas-body">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>"
                           href="index.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'imoveis.php' ? 'active' : '' ?>"
                           href="imoveis.php">Imóveis</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'quem_somos.php' ? 'active' : '' ?>"
                           href="quem_somos.php">Quem Somos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'contato.php' ? 'active' : '' ?>"
                           href="contato.php">Contato</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a href="login.php" class="btn btn-gold">Entrar</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
