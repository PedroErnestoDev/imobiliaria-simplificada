<?php
require_once __DIR__ . '/../app/programacao/imoveis/imoveis.php';

$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log para depuração
    error_log("=== DADOS DO FORMULÁRIO ===");
    error_log("codigo: " . $_POST['codigo'] ?? 'vazio');
    error_log("Arquivos recebidos: " . print_r($_FILES, true));
    
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
        error_log("Imóvel cadastrado ID: $id");
    } catch (Exception $e) {
        $erro = "Erro ao cadastrar: " . $e->getMessage();
    }

    // Upload de imagens
    if (isset($_FILES['imagens']) && is_array($_FILES['imagens']['name'])) {
        error_log("Processing images count: " . count($_FILES['imagens']['name']));
        $contador = count($_FILES['imagens']['name']);
        for ($i = 0; $i < $contador; $i++) {
            error_log("Processing image $i: error={$_FILES['imagens']['error'][$i]}, name={$_FILES['imagens']['name'][$i]}");
            if ($_FILES['imagens']['error'][$i] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['imagens']['name'][$i], PATHINFO_EXTENSION));
                $novoNome = 'img_' . $id . '_' . time() . '_' . $i . '.' . $ext;
                // Caminho ABSOLUTO no container Docker
                $diretorio = '/var/www/public/uploads/imoveis/';
                
                // Garantir que o diretório existe com permissões completas
                if (!is_dir($diretorio)) {
                    $criado = mkdir($diretorio, 0777, true);
                    error_log("Diretorio criado: $criado para $diretorio");
                    if (!is_dir($diretorio)) {
                        error_log("Falha ao criar diretorio: $diretorio");
                        continue;
                    }
                }
                
                // Dar permissões completas
                chmod($diretorio, 0777);
                
                $caminhoTemp = $_FILES['imagens']['tmp_name'][$i];
                $caminhoFinal = $diretorio . $novoNome;
                
                error_log("Attempting move: $caminhoTemp -> $caminhoFinal");
                
                if (move_uploaded_file($caminhoTemp, $caminhoFinal)) {
                    error_log("Arquivo movido com sucesso!");
                    // Salvar no banco
                    $stmt = $pdo->prepare("INSERT INTO imagens_imoveis (imovel_id, caminho_imagem, imagem_principal) VALUES (:imovel_id, :caminho, :principal)");
                    if ($stmt->execute([':imovel_id' => $id, ':caminho' => 'uploads/imoveis/' . $novoNome, ':principal' => 0])) {
                        error_log("Imagem salva no banco de dados!");
                    } else {
                        error_log("Erro ao salvar no banco: " . print_r($stmt->errorInfo(), true));
                    }
                } else {
                    error_log("ERRO CRITICO: Não foi possível mover $caminhoTemp para $caminhoFinal");
                    error_log("Erro do PHP: " . error_get_last()['message'] ?? 'sem erro');
                }
            }
        }
    }
    $sucesso = "Imóvel cadastrado com sucesso! ID: $id";
}

header('Location: ../pages/dashboard.php?status=cadastrado');
exit;
