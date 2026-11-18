<?php
require_once __DIR__ . '/../app/programacao/imoveis/imoveis.php';
$sucesso = '';
$erro = '';
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
        'estado' => trim($_POST['estado'] ?? ''),
        'quartos' => (int) ($_POST['quartos'] ?? 0),
        'banheiros' => (int) ($_POST['banheiros'] ?? 0),
        'vagas' => (int) ($_POST['vagas'] ?? 0),
        'area' => $_POST['area'] ?? null,
        'valor' => (float) ($_POST['valor'] ?? 0),
        'condominio' => $_POST['condominio'] ?? null,
        'iptu' => $_POST['iptu'] ?? null,
        'telefone_contato' => trim($_POST['telefone_contato'] ?? ''),
        'whatsapp' => trim($_POST['whatsapp'] ?? ''),
        'email_contato' => trim($_POST['email_contato'] ?? ''),
        'status' => $_POST['status'] ?? 'disponivel'
    ];
    try {
        $id = cadastrarImovel($pdo, $dados, 1);
        try {
            $contador = salvarUploadsImagensImovel($pdo, $id);
            $sucesso = "Imóvel cadastrado com sucesso! ID: $id. $contador imagem(ns) adicionada(s).";
        } catch (Throwable $e) {
            error_log('Erro ao salvar imagens do imóvel ' . $id . ': ' . $e->getMessage());
            $erro = "Imóvel cadastrado com ID $id, mas não foi possível salvar as imagens: " . $e->getMessage();
        }
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Imóvel | DW2 Imobiliária</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --gold: #c99a3b; --dark: #151515; }
        body { font-family: 'DM Sans', sans-serif; background: #f8f9fa; }
        .btn-gold { background: var(--gold); border: 1px solid var(--gold); color: #151515; font-weight: 700; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: var(--dark);">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">DW2 Dashboard</a>
        <div class="navbar-nav ms-auto"><a class="nav-link" href="dashboard.php">Voltar ao Dashboard</a></div>
    </div>
</nav>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-white"><h5>Cadastrar Novo Imóvel</h5></div>
                <div class="card-body">
                    <?php if ($sucesso): ?><div class="alert alert-success"><?= $sucesso ?></div><?php endif; ?>
                    <?php if ($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label fw-bold">Código *</label><input type="text" name="codigo" class="form-control" required placeholder="Ex: CASA001"></div>
                            <div class="col-md-4"><label class="form-label fw-bold">Finalidade *</label><select name="finalidade" class="form-select" required><option value="venda">Venda</option><option value="aluguel">Aluguel</option></select></div>
                            <div class="col-md-4"><label class="form-label fw-bold">Tipo *</label><select name="tipo" class="form-select" required><option value="casa">Casa</option><option value="apartamento">Apartamento</option><option value="condominio">Condomínio</option><option value="terreno">Terreno</option><option value="na_planta">Na Planta</option></select></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Título *</label><input type="text" name="titulo" class="form-control" required placeholder="Apartamento 3 quartos no centro"></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Valor (R$) *</label><input type="number" name="valor" class="form-control" required step="0.01" min="0"></div>
                            <div class="col-12"><label class="form-label fw-bold">Descrição</label><textarea name="descricao" class="form-control" rows="3" placeholder="Descreva o imóvel..."></textarea></div>
                            <div class="col-md-3"><label class="form-label fw-bold">CEP</label><input type="text" name="cep" class="form-control cep-mask" placeholder="00000-000"></div>
                            <div class="col-md-4"><label class="form-label fw-bold">Endereço</label><input type="text" name="endereco" class="form-control" placeholder="Rua, Avenida..."></div>
                            <div class="col-md-2"><label class="form-label fw-bold">Número</label><input type="text" name="numero" class="form-control" placeholder="123"></div>
                            <div class="col-md-3"><label class="form-label fw-bold">Complemento</label><input type="text" name="complemento" class="form-control" placeholder="Apto 101"></div>
                            <div class="col-md-3"><label class="form-label fw-bold">Bairro</label><input type="text" name="bairro" class="form-control" required></div>
                            <div class="col-md-3"><label class="form-label fw-bold">Cidade</label><input type="text" name="cidade" class="form-control" required></div>
                            <div class="col-md-2"><label class="form-label fw-bold">Estado (UF)</label><input type="text" name="estado" class="form-control" required maxlength="2" placeholder="SP"></div>
                            <div class="col-md-2"><label class="form-label fw-bold">Quartos</label><input type="number" name="quartos" class="form-control" value="0" min="0"></div>
                            <div class="col-md-2"><label class="form-label fw-bold">Banheiros</label><input type="number" name="banheiros" class="form-control" value="0" min="0"></div>
                            <div class="col-md-2"><label class="form-label fw-bold">Vagas</label><input type="number" name="vagas" class="form-control" value="0" min="0"></div>
                            <div class="col-md-3"><label class="form-label fw-bold">Área (m²)</label><input type="number" name="area" class="form-control" step="0.01" min="0"></div>
                            <div class="col-md-3"><label class="form-label fw-bold">Condomínio (R$)</label><input type="number" name="condominio" class="form-control" step="0.01" min="0"></div>
                            <div class="col-md-3"><label class="form-label fw-bold">IPTU (R$)</label><input type="number" name="iptu" class="form-control" step="0.01" min="0"></div>
                            <div class="col-md-4"><label class="form-label fw-bold">Telefone Contato</label><input type="text" name="telefone_contato" class="form-control" placeholder="(00) 0000-0000"></div>
                            <div class="col-md-4"><label class="form-label fw-bold">WhatsApp</label><input type="text" name="whatsapp" class="form-control" placeholder="(00) 00000-0000"></div>
                            <div class="col-md-4"><label class="form-label fw-bold">E-mail Contato</label><input type="email" name="email_contato" class="form-control" placeholder="contato@imobiliaria.com"></div>
                            <div class="col-md-4"><label class="form-label fw-bold">Status</label><select name="status" class="form-select"><option value="disponivel">Disponível</option><option value="indisponivel">Indisponível</option></select></div>
                            <div class="col-md-12"><label class="form-label fw-bold">Imagens do Imóvel</label>
                                <div class="border rounded p-3">
                                    <input class="form-control" type="file" name="imagens[]" multiple accept="image/jpeg,image/png,image/webp">
                                    <small class="text-muted">Selecione uma ou mais imagens (JPEG, PNG ou WebP), com até 10 MB por arquivo.</small>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-gold py-2">Salvar Imóvel</button>
                            <a href="dashboard.php" class="btn btn-outline-secondary py-2">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelector('.cep-mask')?.addEventListener('input', function(e) {
    let v = e.target.value.replace(/\D/g, '');
    if (v.length > 5) v = v.substring(0, 5) + '-' + v.substring(5);
    e.target.value = v;
});
</script>
</body>
</html>