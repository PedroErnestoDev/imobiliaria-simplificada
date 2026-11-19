<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function escapar($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

$mensagemSucesso = false;
$erroFormulario = '';

// Processamento básico caso o formulário envie para a própria página
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $assunto = trim($_POST['assunto'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');

    if ($nome === '' || $telefone === '' || $mensagem === '') {
        $erroFormulario = 'Por favor, preencha os campos obrigatórios (Nome, Telefone e Mensagem).';
    } else {
        // Aqui você pode adicionar a lógica de envio de e-mail (PHPMailer / mail) ou salvar no banco de dados
        $mensagemSucesso = true;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contato | DW2 Imobiliária</title>

    <meta name="description" content="Entre em contato com a DW2 Imobiliária. Tire suas dúvidas, anuncie seu imóvel ou agende uma visita com nossos corretores.">

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

        .contact-section {
            padding: 70px 0 90px;
        }

        .contact-card {
            background: #fff;
            border-radius: 14px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            height: 100%;
        }

        .info-item {
            padding-bottom: 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid #f3f4f6;
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--gold);
            display: block;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 15px;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .info-value a {
            color: var(--dark);
        }

        .info-value a:hover {
            color: var(--gold);
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

        .map-container {
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            height: 320px;
            background: #e5e7eb;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        @media (max-width: 768px) {
            .page-banner {
                padding: 60px 0;
            }

            .contact-card {
                padding: 24px;
            }
        }
    </style>
</head>

<body>

    <?php require_once __DIR__ . '/../app/componentes/header.php'; ?>

    <section class="page-banner">
        <div class="container">
            <span class="section-subtitle">Fale Conosco</span>
            <h1 class="mt-2">Estamos prontos para atender você</h1>
            <p class="mt-2 mb-0">
                Tire dúvidas, agende uma visita ou fale com nossa equipe sobre o seu imóvel.
            </p>
        </div>
    </section>

    <main class="contact-section">
        <div class="container">

            <div class="row g-4 mb-5">
                <!-- Coluna Esquerda: Canais de Atendimento -->
                <div class="col-lg-5">
                    <div class="contact-card">
                        <span class="section-subtitle">Atendimento</span>
                        <h2 class="fw-bold mt-1 mb-3" style="font-size: 24px;">Nossos canais</h2>
                        <p class="text-muted small mb-4" style="line-height: 1.65;">
                            Prefere um atendimento mais direto? Entre em contato por telefone, WhatsApp ou venha tomar um café em nosso escritório.
                        </p>

                        <div class="info-item">
                            <span class="info-label">Endereço</span>
                            <p class="info-value">
                                Rua Exemplo, 123 — Centro<br>
                                <span class="text-muted fw-normal small">Araras - SP, CEP 13600-000</span>
                            </p>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Telefone & WhatsApp</span>
                            <p class="info-value">
                                <a href="tel:+551935410000">(19) 3541-0000</a><br>
                                <a href="https://wa.me/5519999990000" target="_blank" rel="noopener">(19) 99999-0000 (WhatsApp)</a>
                            </p>
                        </div>

                        <div class="info-item">
                            <span class="info-label">E-mail</span>
                            <p class="info-value">
                                <a href="mailto:contato@dw2imobiliaria.com.br">contato@dw2imobiliaria.com.br</a>
                            </p>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Horário de Funcionamento</span>
                            <p class="info-value">
                                Segunda a Sexta: 08h30 às 18h00<br>
                                <span class="text-muted fw-normal small">Sábado: 08h30 às 12h00</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Coluna Direita: Formulário de Contato -->
                <div class="col-lg-7">
                    <div class="contact-card">
                        <span class="section-subtitle">Mensagem</span>
                        <h2 class="fw-bold mt-1 mb-3" style="font-size: 24px;">Envie sua solicitação</h2>

                        <?php if ($mensagemSucesso): ?>
                            <div class="alert alert-success border-0 rounded-3 mb-4" role="alert">
                                <strong>Mensagem enviada com sucesso!</strong> Em breve um de nossos corretores entrará em contato.
                            </div>
                        <?php endif; ?>

                        <?php if ($erroFormulario !== ''): ?>
                            <div class="alert alert-danger border-0 rounded-3 mb-4" role="alert">
                                <?= escapar($erroFormulario) ?>
                            </div>
                        <?php endif; ?>

                        <form action="contato.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="nome">Nome completo *</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="nome"
                                        name="nome"
                                        required
                                        placeholder="Digite seu nome"
                                        value="<?= !$mensagemSucesso ? escapar($_POST['nome'] ?? '') : '' ?>"
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="telefone">Telefone / WhatsApp *</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="telefone"
                                        name="telefone"
                                        required
                                        placeholder="(19) 99999-9999"
                                        value="<?= !$mensagemSucesso ? escapar($_POST['telefone'] ?? '') : '' ?>"
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="email">E-mail</label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        placeholder="seu@email.com"
                                        value="<?= !$mensagemSucesso ? escapar($_POST['email'] ?? '') : '' ?>"
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="assunto">Assunto</label>
                                    <select class="form-select" id="assunto" name="assunto">
                                        <option value="comprar">Quero comprar um imóvel</option>
                                        <option value="alugar">Quero alugar um imóvel</option>
                                        <option value="anunciar">Quero anunciar meu imóvel</option>
                                        <option value="duvidas">Dúvidas gerais / Outros</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label" for="mensagem">Como podemos ajudar? *</label>
                                    <textarea
                                        class="form-control"
                                        id="mensagem"
                                        name="mensagem"
                                        rows="4"
                                        required
                                        placeholder="Descreva o que você procura ou deixe sua dúvida..."
                                    ><?= !$mensagemSucesso ? escapar($_POST['mensagem'] ?? '') : '' ?></textarea>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-gold px-4 py-3">
                                        Enviar mensagem
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mapa de Localização -->
            <div class="map-container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d58989.70827806196!2d-47.4191843!3d-22.3572222!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94c87091722959d9%3A0x8e8314842f83135e!2sAraras%2C%20SP!5e0!3m2!1spt-BR!2sbr!4v1700000000000!5m2!1spt-BR!2sbr"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Localização DW2 Imobiliária"
                ></iframe>
            </div>

        </div>
    </main>

    <?php require_once __DIR__ . '/../app/componentes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>