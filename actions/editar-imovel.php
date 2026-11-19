<?php
require_once __DIR__ . '/../app/programacao/imoveis/imoveis.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Proteção: exige autenticação
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /login.php');
    exit;
}

$sucesso = '';
$erro = '';
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: /dashboard.php');
    exit;
}

// Buscar imóvel
$imovel = buscarImovelCompleto($pdo, $id);
if (!$imovel) {
    $erro = 'Imóvel não encontrado.';
}

// Buscar imagens existentes
$imagensExistentes = [];
if ($imovel) {
    $imagensExistentes = listarImagensImovel($pdo, $id);
}

// Processar upload de novas imagens
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $imovel) {
    // Garantir que pasta existe
    @mkdir('/var/www/public/uploads/imoveis', 0777, true);
    
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
        'latitude' => $_POST['latitude'] ?? null,
        'longitude' => $_POST['longitude'] ?? null,
        'status' => $_POST['status'] ?? 'disponivel'
    ];
    
    // Processar upload de imagens
    $novasImagens = [];
    $contador = 0;
    if (!empty($_FILES['imagens']['name'][0])) {
        $contador = 0;
        foreach ($_FILES['imagens']['tmp_name'] as $key => $tmp_name) {
            // Validar extensão
            $extensao = strtolower(pathinfo($_FILES['imagens']['name'][$key], PATHINFO_EXTENSION));
            if (in_array($extensao, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $nomeArquivo = uniqid() . '.' . $extensao;
                $destino = '/var/www/public/uploads/imoveis/' . $nomeArquivo;
                
                if (move_uploaded_file($tmp_name, $destino)) {
                    // Salvar no banco
                    salvarImagemImovel($pdo, $imovel['id'], $destino);
                    $novasImagens[] = $nomeArquivo;
                    $contador++;
                }
            }
        }
    }
    
    // Atualizar dados do imóvel
    try {
        atualizarImovel($pdo, $id, $dados);
        $sucesso = "Imóvel atualizado com sucesso! $contador nova(s) imagem(ns) adicionada(s).";
        
        // Recarregar dados
        $imovel = array_merge($imovel, $dados);
        $imagensExistentes = array_merge($imagensExistentes, $novasImagens);
    } catch (Exception $e) {
        $erro = "Erro ao atualizar: " . $e->getMessage();
    }
}
?>