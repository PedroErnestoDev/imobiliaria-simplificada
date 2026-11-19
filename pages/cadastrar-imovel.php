
<?php
declare(strict_types=1);

// pages/cadastrar-imovel.php

require_once __DIR__ . '/../app/programacao/imoveis/imoveis.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Somente POST e GET são permitidos.
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) {
    http_response_code(405);
    exit('Método não permitido.');
}

$sucesso = '';
$erro = '';

$dados = [
    'codigo' => '',
    'titulo' => '',
    'descricao' => '',
    'finalidade' => 'venda',
    'tipo' => 'casa',
    'cep' => '',
    'endereco' => '',
    'numero' => '',
    'complemento' => '',
    'bairro' => '',
    'cidade' => '',
    'estado' => '',
    'quartos' => '0',
    'banheiros' => '0',
    'vagas' => '0',
    'area' => '',
    'valor' => '',
    'condominio' => '',
    'iptu' => '',
    'telefone_contato' => '',
    'whatsapp' => '',
    'email_contato' => '',
    'status' => 'disponivel'
];



// Normaliza campos numéricos opcionais.
function numeroOpcional(string $valor): ?float
{
    if ($valor === '') {
        return null;
    }

    if (!is_numeric($valor)) {
        throw new InvalidArgumentException('Valor numérico inválido.');
    }

    $numero = (float) $valor;

    if (!is_finite($numero) || $numero < 0) {
        throw new InvalidArgumentException('Informe um valor numérico válido.');
    }

    return $numero;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($dados as $campo => $valorPadrao) {
        if (isset($_POST[$campo]) && is_string($_POST[$campo])) {
            $dados[$campo] = trim($_POST[$campo]);
        }
    }

    try {


        if (
            !is_string($token) ||
            !hash_equals($_SESSION['csrf_token'], $token)
        ) {
            throw new RuntimeException(
                'Sua sessão expirou ou a solicitação é inválida. Atualize a página.'
            );
        }

        // Validação dos campos obrigatórios.
        $obrigatorios = [
            'codigo',
            'titulo',
            'valor',
            'bairro',
            'cidade',
            'estado'
        ];

        foreach ($obrigatorios as $campo) {
            if ($dados[$campo] === '') {
                throw new InvalidArgumentException(
                    "O campo {$campo} é obrigatório."
                );
            }
        }

        // Validação de finalidade, tipo e status.
        if (!in_array($dados['finalidade'], ['venda', 'aluguel'], true)) {
            throw new InvalidArgumentException('Finalidade inválida.');
        }

        $tiposPermitidos = [
            'casa',
            'apartamento',
            'condominio',
            'terreno',
            'na_planta'
        ];

        if (!in_array($dados['tipo'], $tiposPermitidos, true)) {
            throw new InvalidArgumentException('Tipo de imóvel inválido.');
        }

        if (!in_array($dados['status'], ['disponivel', 'indisponivel'], true)) {
            throw new InvalidArgumentException('Status inválido.');
        }

        // Validação de e-mail.
        if (
            $dados['email_contato'] !== '' &&
            !filter_var($dados['email_contato'], FILTER_VALIDATE_EMAIL)
        ) {
            throw new InvalidArgumentException('E-mail de contato inválido.');
        }

        // Validação do estado.
        $dados['estado'] = strtoupper($dados['estado']);

        if (!preg_match('/^[A-Z]{2}$/', $dados['estado'])) {
            throw new InvalidArgumentException('Informe uma UF válida.');
        }

        // Validação do CEP.
        $cep = preg_replace('/\D/', '', $dados['cep']);

        if ($cep !== '' && strlen($cep) !== 8) {
            throw new InvalidArgumentException('CEP inválido.');
        }

        $dados['cep'] = $cep;

        // Validação dos campos inteiros.
        foreach (['quartos', 'banheiros', 'vagas'] as $campo) {
            if (
                filter_var($dados[$campo], FILTER_VALIDATE_INT) === false ||
                (int) $dados[$campo] < 0
            ) {
                throw new InvalidArgumentException(
                    "O campo {$campo} deve ser um número inteiro positivo ou zero."
                );
            }

            $dados[$campo] = (int) $dados[$campo];
        }

        // Validação dos valores financeiros e da área.
        $dados['valor'] = numeroOpcional($dados['valor']);
        $dados['area'] = numeroOpcional($dados['area']);
        $dados['condominio'] = numeroOpcional($dados['condominio']);
        $dados['iptu'] = numeroOpcional($dados['iptu']);

        if ($dados['valor'] === null || $dados['valor'] <= 0) {
            throw new InvalidArgumentException(
                'O valor do imóvel deve ser maior que zero.'
            );
        }

        if (empty($_SESSION['usuario']['id'])) {
            throw new RuntimeException(
                'Usuário não autenticado. Faça login para cadastrar imóveis.'
            );
        }

        $usuarioId = (int) $_SESSION['usuario']['id'];

        if ($usuarioId <= 0) {
            throw new RuntimeException('Identificação de usuário inválida.');
        }

        // Cadastro no banco.
        $id = cadastrarImovel($pdo, $dados, $usuarioId);
       

        // Redirecionamento após sucesso.
        $_SESSION['mensagem_sucesso'] =
            "Imóvel cadastrado com sucesso! ID: {$id}";

        header('Location: cadastrar-imovel.php');
        exit;

    } catch (InvalidArgumentException | RuntimeException $e) {
        $erro = $e->getMessage();

    } catch (Throwable $e) {
        // Registrar detalhes somente no log do servidor.
        error_log('Erro ao cadastrar imóvel: ' . $e->getMessage());

        $erro = 'Não foi possível cadastrar o imóvel. Tente novamente.';
    }
}

// Recupera mensagem de sucesso após redirecionamento.
if (isset($_SESSION['mensagem_sucesso'])) {
    $sucesso = $_SESSION['mensagem_sucesso'];
    unset($_SESSION['mensagem_sucesso']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Imóvel | DW2 Imobiliária</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        :root {
            --gold: #c99a3b;
            --dark: #151515;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f8f9fa;
        }

        .navbar {
            background: var(--dark);
        }

        .card {
            border: none;
            border-radius: 10px;
        }

        .card-header {
            padding: 18px;
        }

        .btn-gold {
            background: var(--gold);
            border: 1px solid var(--gold);
            color: var(--dark);
            font-weight: 700;
        }

        .btn-gold:hover {
            background: #b8892f;
            border-color: #b8892f;
            color: var(--dark);
        }

        .form-label {
            font-weight: 600;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 .2rem rgba(201, 154, 59, .15);
        }

        .section-title {
            font-size: 1rem;
            font-weight: 700;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin: 20px 0 10px;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">

        <a class="navbar-brand fw-bold" href="dashboard.php">
            DW2 Dashboard
        </a>

        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard.php">
                Voltar ao Dashboard
            </a>
        </div>

    </div>
</nav>

<main class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-10">

            <div class="card shadow-sm">

                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Cadastrar Novo Imóvel</h5>
                </div>

                <div class="card-body p-4">

                    <?php if ($sucesso): ?>
                        <div class="alert alert-success" role="alert">
                            <?= escapar($sucesso) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($erro): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= escapar($erro) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= escapar($_SESSION['csrf_token']) ?>"
                        >

                        <h6 class="section-title">Informações do imóvel</h6>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label">Código *</label>
                                <input
                                    type="text"
                                    name="codigo"
                                    class="form-control"
                                    maxlength="30"
                                    required
                                    value="<?= escapar($dados['codigo']) ?>"
                                    placeholder="Ex: CASA001"
                                >
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Finalidade *</label>
                                <select name="finalidade" class="form-select" required>
                                    <option value="venda" <?= $dados['finalidade'] === 'venda' ? 'selected' : '' ?>>Venda</option>
                                    <option value="aluguel" <?= $dados['finalidade'] === 'aluguel' ? 'selected' : '' ?>>Aluguel</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tipo *</label>
                                <select name="tipo" class="form-select" required>
                                    <?php
                                    $tipos = [
                                        'casa' => 'Casa',
                                        'apartamento' => 'Apartamento',
                                        'condominio' => 'Condomínio',
                                        'terreno' => 'Terreno',
                                        'na_planta' => 'Na Planta'
                                    ];

                                    foreach ($tipos as $valor => $nome):
                                    ?>
                                        <option
                                            value="<?= escapar($valor) ?>"
                                            <?= $dados['tipo'] === $valor ? 'selected' : '' ?>
                                        >
                                            <?= escapar($nome) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Título *</label>
                                <input
                                    type="text"
                                    name="titulo"
                                    class="form-control"
                                    maxlength="150"
                                    required
                                    value="<?= escapar($dados['titulo']) ?>"
                                    placeholder="Apartamento 3 quartos no centro"
                                >
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Valor (R$) *</label>
                                <input
                                    type="number"
                                    name="valor"
                                    class="form-control"
                                    required
                                    step="0.01"
                                    min="0.01"
                                    value="<?= escapar($dados['valor']) ?>"
                                >
                            </div>

                            <div class="col-12">
                                <label class="form-label">Descrição</label>
                                <textarea
                                    name="descricao"
                                    class="form-control"
                                    rows="3"
                                    maxlength="5000"
                                    placeholder="Descreva o imóvel..."
                                ><?= escapar($dados['descricao']) ?></textarea>
                            </div>

                        </div>

                        <h6 class="section-title">Localização</h6>

                        <div class="row g-3">

                            <?php
                            $camposEndereco = [
                                'cep' => ['CEP', 'col-md-3', '00000-000'],
                                'endereco' => ['Endereço', 'col-md-4', 'Rua, Avenida...'],
                                'numero' => ['Número', 'col-md-2', '123'],
                                'complemento' => ['Complemento', 'col-md-3', 'Apto 101'],
                                'bairro' => ['Bairro', 'col-md-4', ''],
                                'cidade' => ['Cidade', 'col-md-4', ''],
                                'estado' => ['Estado (UF)', 'col-md-4', 'SP']
                            ];

                            foreach ($camposEndereco as $campo => [$label, $classe, $placeholder]):
                            ?>

                                <div class="<?= escapar($classe) ?>">
                                    <label class="form-label"><?= escapar($label) ?></label>

                                    <input
                                        type="text"
                                        name="<?= escapar($campo) ?>"
                                        class="form-control <?= $campo === 'cep' ? 'cep-mask' : '' ?>"
                                        value="<?= escapar($dados[$campo]) ?>"
                                        placeholder="<?= escapar($placeholder) ?>"
                                        <?= in_array($campo, ['bairro', 'cidade', 'estado'], true) ? 'required' : '' ?>
                                        <?= $campo === 'estado' ? 'maxlength="2"' : '' ?>
                                        <?= $campo === 'cep' ? 'maxlength="9"' : '' ?>
                                    >
                                </div>

                            <?php endforeach; ?>

                        </div>

                        <h6 class="section-title">Características</h6>

                        <div class="row g-3">

                            <?php
                            $caracteristicas = [
                                'quartos' => 'Quartos',
                                'banheiros' => 'Banheiros',
                                'vagas' => 'Vagas'
                            ];

                            foreach ($caracteristicas as $campo => $label):
                            ?>

                                <div class="col-md-4">
                                    <label class="form-label"><?= escapar($label) ?></label>
                                    <input
                                        type="number"
                                        name="<?= escapar($campo) ?>"
                                        class="form-control"
                                        min="0"
                                        step="1"
                                        value="<?= escapar($dados[$campo]) ?>"
                                    >
                                </div>

                            <?php endforeach; ?>

                            <div class="col-md-3">
                                <label class="form-label">Área (m²)</label>
                                <input
                                    type="number"
                                    name="area"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="<?= escapar($dados['area']) ?>"
                                >
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Condomínio (R$)</label>
                                <input
                                    type="number"
                                    name="condominio"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="<?= escapar($dados['condominio']) ?>"
                                >
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">IPTU (R$)</label>
                                <input
                                    type="number"
                                    name="iptu"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="<?= escapar($dados['iptu']) ?>"
                                >
                            </div>

                        </div>

                        <h6 class="section-title">Contato</h6>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label">Telefone</label>
                                <input
                                    type="text"
                                    name="telefone_contato"
                                    class="form-control"
                                    value="<?= escapar($dados['telefone_contato']) ?>"
                                    placeholder="(00) 0000-0000"
                                >
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">WhatsApp</label>
                                <input
                                    type="text"
                                    name="whatsapp"
                                    class="form-control"
                                    value="<?= escapar($dados['whatsapp']) ?>"
                                    placeholder="(00) 00000-0000"
                                >
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">E-mail</label>
                                <input
                                    type="email"
                                    name="email_contato"
                                    class="form-control"
                                    value="<?= escapar($dados['email_contato']) ?>"
                                    placeholder="contato@imobiliaria.com"
                                >
                            </div>

                        </div>

                        <h6 class="section-title">Publicação</h6>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="disponivel" <?= $dados['status'] === 'disponivel' ? 'selected' : '' ?>>
                                        Disponível
                                    </option>
                                    <option value="indisponivel" <?= $dados['status'] === 'indisponivel' ? 'selected' : '' ?>>
                                        Indisponível
                                    </option>
                                </select>
                            </div>

                        </div>

                        <div class="d-grid gap-2 mt-4">

                            <button type="submit" class="btn btn-gold py-2">
                                Salvar Imóvel
                            </button>

                            <a href="dashboard.php" class="btn btn-outline-secondary py-2">
                                Cancelar
                            </a>

                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelector('.cep-mask')?.addEventListener('input', function () {
    let valor = this.value.replace(/\D/g, '').substring(0, 8);

    if (valor.length > 5) {
        valor = valor.substring(0, 5) + '-' + valor.substring(5);
    }

    this.value = valor;
});

document.querySelector('input[name="estado"]')?.addEventListener('input', function () {
    this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '').substring(0, 2);
});
</script>

</body>
</html>
