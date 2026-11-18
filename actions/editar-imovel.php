<?php
require_once __DIR__ . '/../app/programacao/imoveis/imoveis.php';

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: ../pages/dashboard.php');
    exit;
}

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

    atualizarImovel($pdo, $id, $dados);

    // Upload de imagens
    if (isset($_FILES['imagens']) && $_FILES['imagens']['error'][0] !== UPLOAD_ERR_NO_FILE) {
        $contador = count($_FILES['imagens']['name']);
        for ($i = 0; $i < $contador; $i++) {
            if ($_FILES['imagens']['error'][$i] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['imagens']['name'][$i], PATHINFO_EXTENSION));
                $novoNome = 'img_' . $id . '_' . time() . '_' . $i . '.' . $ext;
                // Caminho absoluto baseado no container Docker
                $diretorio = '/var/www/public/uploads/imoveis/';
                
                // Garantir que o diretório existe
                if (!is_dir($diretorio)) {
                    mkdir($diretorio, 0777, true);
                    // Verificar se foi criado
                    if (!is_dir($diretorio)) {
                        error_log("Não foi possível criar o diretório: $diretorio");
                        continue;
                    }
                }

                $caminhoTemp = $_FILES['imagens']['tmp_name'][$i];
                $caminhoFinal = $diretorio . $novoNome;

                if (move_uploaded_file($caminhoTemp, $caminhoFinal)) {
                    salvarImagemImovel($pdo, $id, 'uploads/imoveis/' . $novoNome, false);
                } else {
                    error_log("Erro ao mover arquivo: $caminhoTemp para $caminhoFinal");
                }
            }
        }
    }

    header('Location: ../pages/dashboard.php?status=atualizado');
    exit;
}

header('Location: ../pages/dashboard.php');
exit;