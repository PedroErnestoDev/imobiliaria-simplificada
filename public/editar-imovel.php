
<?php
require_once __DIR__ . '/../app/programacao/imoveis/imoveis.php';

$sucesso = '';
$erro = '';

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: dashboard.php');
    exit;
}

$imovel = buscarImovelCompleto($pdo, $id);

if (!$imovel) {
    header('Location: dashboard.php');
    exit;
}

$imagens = listarImagensImovel($pdo, $id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dados = [
        'codigo' => trim($_POST['codigo'] ?? ''),
        'titulo' => trim($_POST['titulo'] ?? ''),
        'descricao' => trim($_POST['descricao'] ?? ''),
        'finalidade' => $_POST['finalidade'] ?? 'venda',
        'tipo' => $_POST['tipo'] ?? 'casa',
        'cep' => trim($_POST['cep'] ?? ''),
        'endereco' => trim($_POST['endereco'] ?? ''),
        'numero' => trim($_POST['numero'] ?? ''),
        'complemento' => trim($_POST['complemento'] ?? ''),
        'bairro' => trim($_POST['bairro'] ?? ''),
        'cidade' => trim($_POST['cidade'] ?? ''),
        'estado' => strtoupper(trim($_POST['estado'] ?? '')),
        'quartos' => (int) ($_POST['quartos'] ?? 0),
        'banheiros' => (int) ($_POST['banheiros'] ?? 0),
        'vagas' => (int) ($_POST['vagas'] ?? 0),
        'area' => $_POST['area'] !== '' ? $_POST['area'] : null,
        'valor' => (float) ($_POST['valor'] ?? 0),
        'condominio' => $_POST['condominio'] !== '' ? $_POST['condominio'] : null,
        'iptu' => $_POST['iptu'] !== '' ? $_POST['iptu'] : null,
        'telefone_contato' => trim($_POST['telefone_contato'] ?? ''),
        'whatsapp' => trim($_POST['whatsapp'] ?? ''),
        'email_contato' => trim($_POST['email_contato'] ?? ''),
        'status' => $_POST['status'] ?? 'disponivel'
    ];

    try {
        $pdo->beginTransaction();

        atualizarImovel($pdo, $id, $dados);

        // Upload das imagens
        if (
            isset($_FILES['imagens']) &&
            isset($_FILES['imagens']['name']) &&
            is_array($_FILES['imagens']['name'])
        ) {
            $diretorio = __DIR__ . '/../public/uploads/imoveis/';

            if (!is_dir($diretorio)) {
                if (!mkdir($diretorio, 0755, true)) {
                    throw new Exception('Não foi possível criar o diretório de imagens.');
                }
            }

            $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

            foreach ($_FILES['imagens']['name'] as $i => $nomeOriginal) {

                if ($_FILES['imagens']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                if ($_FILES['imagens']['error'][$i] !== UPLOAD_ERR_OK) {
                    throw new Exception('Erro durante o envio de uma imagem.');
                }

                $ext = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

                if (!in_array($ext, $extensoesPermitidas, true)) {
                    throw new Exception('Formato de imagem não permitido.');
                }

                $arquivoTemporario = $_FILES['imagens']['tmp_name'][$i];

                // Verificação real do conteúdo da imagem
                $infoImagem = getimagesize($arquivoTemporario);

                if ($infoImagem === false) {
                    throw new Exception('Um dos arquivos enviados não é uma imagem válida.');
                }

                $extensoesMime = [
                    'image/jpeg' => ['jpg', 'jpeg'],
                    'image/png' => ['png'],
                    'image/webp' => ['webp']
                ];

                $mime = $infoImagem['mime'];

                if (
                    !isset($extensoesMime[$mime]) ||
                    !in_array($ext, $extensoesMime[$mime], true)
                ) {
                    throw new Exception('O conteúdo da imagem não corresponde à extensão.');
                }

                $novoNome = 'img_' . $id . '_' . bin2hex(random_bytes(8)) . '.' . $ext;

                if (!move_uploaded_file($arquivoTemporario, $diretorio . $novoNome)) {
                    throw new Exception('Não foi possível salvar uma das imagens.');
                }

                salvarImagemImovel(
                    $pdo,
                    $id,
                    'uploads/imoveis/' . $novoNome,
                    false
                );
            }
        }

        $pdo->commit();

        $sucesso = 'Imóvel atualizado com sucesso!';

        // Atualiza os dados exibidos
        $imovel = buscarImovelCompleto($pdo, $id);
        $imagens = listarImagensImovel($pdo, $id);

    } catch (Throwable $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $erro = 'Erro ao atualizar imóvel: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

    <title>Editar Imóvel | DW2 Imobiliária</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap"
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
            min-height: 100vh;
            overflow-x: hidden;
        }

        .navbar {
            background: var(--dark);
        }

        .page-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .form-card {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            padding: 10px 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.2rem rgba(201, 154, 59, 0.15);
        }

        .btn-gold {
            background: var(--gold);
            border: 1px solid var(--gold);
            color: var(--dark);
            font-weight: 700;
        }

        .btn-gold:hover {
            background: #b3862f;
            border-color: #b3862f;
            color: #fff;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 25px 0 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        @media (max-width: 576px) {
            .page-container {
                margin: 20px auto;
                padding: 0 12px;
            }

            .form-card {
                padding: 18px;
            }
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

    <main class="page-container">

        <div class="mb-4">
            <h2 class="fw-bold">Editar Imóvel</h2>
            <p class="text-muted">
                Atualize as informações do imóvel cadastrado.
            </p>
        </div>

        <?php if ($sucesso): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($sucesso) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($erro): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($erro) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="form-card">

            <form method="POST" action="" enctype="multipart/form-data">

                <h5 class="section-title">Informações principais</h5>

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Código *</label>
                        <input type="text" name="codigo" class="form-control"
                            value="<?= htmlspecialchars($imovel['codigo'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Finalidade *</label>
                        <select name="finalidade" class="form-select" required>
                            <option value="venda" <?= $imovel['finalidade'] === 'venda' ? 'selected' : '' ?>>Venda</option>
                            <option value="aluguel" <?= $imovel['finalidade'] === 'aluguel' ? 'selected' : '' ?>>Aluguel</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tipo *</label>
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
                                <option value="<?= $valor ?>"
                                    <?= $imovel['tipo'] === $valor ? 'selected' : '' ?>>
                                    <?= $nome ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Título *</label>
                        <input type="text" name="titulo" class="form-control"
                            value="<?= htmlspecialchars($imovel['titulo'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Valor (R$) *</label>
                        <input type="number" name="valor" class="form-control"
                            value="<?= htmlspecialchars((string) $imovel['valor']) ?>"
                            required step="0.01" min="0">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="4"><?= htmlspecialchars($imovel['descricao'] ?? '') ?></textarea>
                    </div>

                </div>

                <h5 class="section-title">Localização</h5>

                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label fw-bold">CEP</label>
                        <input type="text" name="cep" class="form-control"
                            value="<?= htmlspecialchars($imovel['cep'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Endereço</label>
                        <input type="text" name="endereco" class="form-control"
                            value="<?= htmlspecialchars($imovel['endereco'] ?? '') ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold">Número</label>
                        <input type="text" name="numero" class="form-control"
                            value="<?= htmlspecialchars($imovel['numero'] ?? '') ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Complemento</label>
                        <input type="text" name="complemento" class="form-control"
                            value="<?= htmlspecialchars($imovel['complemento'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Bairro *</label>
                        <input type="text" name="bairro" class="form-control"
                            value="<?= htmlspecialchars($imovel['bairro'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Cidade *</label>
                        <input type="text" name="cidade" class="form-control"
                            value="<?= htmlspecialchars($imovel['cidade'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Estado (UF) *</label>
                        <input type="text" name="estado" class="form-control"
                            value="<?= htmlspecialchars($imovel['estado'] ?? '') ?>"
                            required maxlength="2">
                    </div>

                </div>

                <h5 class="section-title">Características do imóvel</h5>

                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Quartos</label>
                        <input type="number" name="quartos" class="form-control"
                            value="<?= (int) $imovel['quartos'] ?>" min="0">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Banheiros</label>
                        <input type="number" name="banheiros" class="form-control"
                            value="<?= (int) $imovel['banheiros'] ?>" min="0">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Vagas</label>
                        <input type="number" name="vagas" class="form-control"
                            value="<?= (int) $imovel['vagas'] ?>" min="0">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Área (m²)</label>
                        <input type="number" name="area" class="form-control"
                            value="<?= htmlspecialchars((string) ($imovel['area'] ?? '')) ?>"
                            step="0.01" min="0">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Condomínio (R$)</label>
                        <input type="number" name="condominio" class="form-control"
                            value="<?= htmlspecialchars((string) ($imovel['condominio'] ?? '')) ?>"
                            step="0.01" min="0">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">IPTU (R$)</label>
                        <input type="number" name="iptu" class="form-control"
                            value="<?= htmlspecialchars((string) ($imovel['iptu'] ?? '')) ?>"
                            step="0.01" min="0">
                    </div>

                </div>

                <h5 class="section-title">Contato</h5>

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Telefone</label>
                        <input type="text" name="telefone_contato" class="form-control"
                            value="<?= htmlspecialchars($imovel['telefone_contato'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control"
                            value="<?= htmlspecialchars($imovel['whatsapp'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">E-mail</label>
                        <input type="email" name="email_contato" class="form-control"
                            value="<?= htmlspecialchars($imovel['email_contato'] ?? '') ?>">
                    </div>

                </div>

                <h5 class="section-title">Disponibilidade</h5>

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="disponivel" <?= $imovel['status'] === 'disponivel' ? 'selected' : '' ?>>
                                Disponível
                            </option>

                            <option value="indisponivel" <?= $imovel['status'] === 'indisponivel' ? 'selected' : '' ?>>
                                Indisponível
                            </option>
                        </select>
                    </div>

                </div>

                <h5 class="section-title">Adicionar imagens</h5>

                <div class="border rounded p-3">
                    <input
                        class="form-control"
                        type="file"
                        name="imagens[]"
                        multiple
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    >

                    <small class="text-muted">
                        Selecione uma ou mais imagens (JPEG, PNG ou WebP).
                    </small>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-gold py-2">
                        Atualizar Imóvel
                    </button>

                    <a href="dashboard.php" class="btn btn-outline-secondary py-2">
                        Cancelar
                    </a>
                </div>

            </form>

        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
