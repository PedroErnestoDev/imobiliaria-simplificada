<style>
            footer {
            background: #101010;
            color: #aaaaaa;
            padding: 45px 0 20px;
        }

        footer img {
            width: 145px;
            margin-bottom: 15px;
        }

        footer h5 {
            color: white;
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 18px;
        }

        footer a {
            color: #aaaaaa;
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
        }

        footer a:hover {
            color: var(--gold-light);
        }

        .footer-bottom {
            border-top: 1px solid #303030;
            margin-top: 35px;
            padding-top: 20px;
            font-size: 13px;
        }
</style>
<footer>
        <div class="container">

            <div class="row g-4">

                <div class="col-lg-5">

                    <img src="assets/logo.png" alt="DW2 Imobiliária">

                    <p class="small">
                        Conectando pessoas a novas oportunidades imobiliárias.
                    </p>

                </div>

                <div class="col-6 col-lg-3">

                    <h5>Navegação</h5>

                    <a href="index.php">Início</a>
                    <a href="#imoveis">Imóveis</a>
                    <a href="#sobre">Quem Somos</a>
                    <a href="#contato">Contato</a>

                </div>

                <div class="col-6 col-lg-4">

                    <h5>Área do cliente</h5>

                    <a href="login.php">Entrar na plataforma</a>
                    <a href="imoveis.php">Consultar imóveis</a>

                </div>

            </div>

            <div class="footer-bottom text-center">
                &copy; <?= date('Y') ?> - DW2 Imobiliária.
                Todos os direitos reservados.
            </div>

        </div>
</footer>

 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>